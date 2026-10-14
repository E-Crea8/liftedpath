import { draftMode } from "next/headers";
import { unstable_rethrow } from "next/navigation";
import { connection } from "next/server";
import { wordpressApiUrl } from "@/lib/wp/api-url";
import { assertPublicPath, mapDesign, mapDiscovery, mapMedia, mapNavigation, mapPage, mapPaths, mapSettings } from "@/lib/wp/map";
import {
	holdCopy,
	isSavedDesign,
	isSavedDiscovery,
	isSavedMedia,
	isSavedNavigation,
	isSavedPage,
	isSavedSettings,
	mediaKey,
	recallCopy,
	reportReadFailure,
	STANDBY_DESIGN,
	STANDBY_DISCOVERY,
	STANDBY_NAVIGATION,
	STANDBY_SETTINGS,
} from "@/lib/wp/safety";
import type { Design, Discovery, FeaturedImage, Navigation, Page, PathIndex, SiteSettings } from "@/lib/wp/types";
import { WordpressError } from "@/lib/wp/types";

/**
 * A finished fetch is still stored in the prerender when the segment is allowed
 * to block. connection() waits for a visitor, so the build cannot keep the page.
 */
async function readOnRequest(): Promise<void> {
	await connection();
}

export async function getPage(path: string): Promise<Page> {
	const publicPath = assertPublicPath(path);

	const draft = await draftMode();
	if (draft.isEnabled) {
		const url = new URL(`${apiUrl()}/liftedpath/v1/preview`);
		url.searchParams.set("path", publicPath);
		return mapPage(await readJson(url, { headers: previewHeaders() }), "preview");
	}

	return published(
		`page:${publicPath}`,
		async () => {
			const url = new URL(`${apiUrl()}/liftedpath/v1/page`);
			url.searchParams.set("path", publicPath);
			return mapPage(await readJson(url));
		},
		isSavedPage,
		null,
		true,
	);
}

export async function exchangePreviewToken(token: string): Promise<string> {
	if (!/^[a-f0-9]{64}$/.test(token)) {
		throw new WordpressError("preview_not_found", "This preview link is not valid.", 401);
	}

	const url = new URL(`${apiUrl()}/liftedpath/v1/preview/exchange`);
	const payload = await readJson(url, {
		method: "POST",
		headers: {
			...previewHeaders(),
			"Content-Type": "application/json",
		},
		body: JSON.stringify({ token }),
		cache: "no-store",
	});

	if (!isRecord(payload) || typeof payload.path !== "string") {
		throw new WordpressError("invalid_response", "The preview response has no path.", 502);
	}

	return assertPublicPath(payload.path);
}

export async function getPaths(): Promise<PathIndex> {
	const url = new URL(`${apiUrl()}/liftedpath/v1/paths`);
	try {
		// Build-time static params. Prefer a plain read; SiteGround sometimes returns an
		// empty HTML 202 on the first hit, so fall back once with lp_fresh.
		return mapPaths(await readJson(url, undefined, false));
	} catch (error) {
		unstable_rethrow(error);
		const retry = new URL(`${apiUrl()}/liftedpath/v1/paths`);
		return mapPaths(await readJson(retry, undefined, true));
	}
}

export async function getNavigation(): Promise<Navigation> {
	return published(
		"navigation",
		async () => {
			const url = new URL(`${apiUrl()}/liftedpath/v1/navigation`);
			return mapNavigation(await readJson(url));
		},
		isSavedNavigation,
		STANDBY_NAVIGATION,
	);
}

export async function getSettings(): Promise<SiteSettings> {
	return published(
		"settings",
		async () => {
			const url = new URL(`${apiUrl()}/liftedpath/v1/settings`);
			return mapSettings(await readJson(url));
		},
		isSavedSettings,
		STANDBY_SETTINGS,
	);
}

export async function getDiscovery(): Promise<Discovery> {
	return published(
		"discovery",
		async () => {
			const url = new URL(`${apiUrl()}/liftedpath/v1/seo`);
			return mapDiscovery(await readJson(url));
		},
		isSavedDiscovery,
		STANDBY_DISCOVERY,
	);
}

export async function getMedia(ids: number[]): Promise<FeaturedImage[]> {
	const unique = [...new Set(ids.filter((id) => Number.isInteger(id) && id > 0))].sort((left, right) => left - right);
	if (unique.length === 0) {
		return [];
	}

	return published(
		mediaKey(unique),
		async () => {
			const images: FeaturedImage[] = [];
			for (let start = 0; start < unique.length; start += 100) {
				const chunk = unique.slice(start, start + 100);
				const url = new URL(`${apiUrl()}/wp/v2/media`);
				url.searchParams.set("include", chunk.join(","));
				url.searchParams.set("per_page", String(chunk.length));
				url.searchParams.set("_fields", "id,source_url,alt_text,media_details,mime_type");
				images.push(...mapMedia(await readJson(url)));
			}
			return images;
		},
		isSavedMedia,
		[],
	);
}

export async function getDesign(): Promise<Design> {
	return published(
		"design",
		async () => {
			const url = new URL(`${apiUrl()}/liftedpath/v1/design`);
			return mapDesign(await readJson(url));
		},
		isSavedDesign,
		STANDBY_DESIGN,
	);
}

async function published<T>(
	key: string,
	read: () => Promise<T>,
	accept: (value: unknown) => value is T,
	fallback: T | null,
	allowMissing = false,
): Promise<T> {
	await readOnRequest();

	try {
		const value = await read();
		holdCopy(key, value);
		return value;
	} catch (error) {
		unstable_rethrow(error);
		if (allowMissing && error instanceof WordpressError && (error.status === 404 || error.code === "page_not_found")) {
			throw error;
		}

		const saved = await recallCopy(key, accept);
		reportReadFailure(error, key, saved !== null);
		if (saved !== null) {
			return saved;
		}
		if (fallback !== null) {
			return fallback;
		}

		throw new WordpressError("kept_down", "WordPress did not respond, and there is no saved copy of this page yet.", 503);
	}
}

function apiUrl(): string {
	try {
		return wordpressApiUrl();
	} catch {
		throw new WordpressError("missing_config", "WORDPRESS_API_URL is not set.", 500);
	}
}

function previewHeaders(): Record<string, string> {
	const user = process.env.WORDPRESS_PREVIEW_USER?.trim() ?? "";
	const password = (process.env.WORDPRESS_PREVIEW_APP_PASSWORD ?? "").replace(/\s+/g, "");
	const secret = process.env.PREVIEW_SECRET?.trim() ?? "";

	if (!user || !password || !secret) {
		throw new WordpressError("missing_config", "Preview credentials are not set.", 500);
	}

	return {
		Authorization: `Basic ${Buffer.from(`${user}:${password}`, "utf8").toString("base64")}`,
		"X-LiftedPath-Preview-Secret": secret,
	};
}

async function readJson(url: URL, init?: RequestInit, bust = true): Promise<unknown> {
	const headers = new Headers(init?.headers);
	headers.set("Accept", "application/json");
	if (bust) {
		headers.set("Cache-Control", "no-cache");
		url.searchParams.set("lp_fresh", String(Date.now()));
	}

	let response: Response;
	try {
		response = await fetch(url, {
			...init,
			headers,
			cache: "no-store",
		});
	} catch (error) {
		unstable_rethrow(error);
		throw new WordpressError("unavailable", "WordPress did not respond.", 503);
	}

	const text = await response.text();
	const payload = parseJsonBody(text);
	if (payload === undefined) {
		if (bust) {
			url.searchParams.set("lp_fresh", String(Date.now()));
			return readJson(url, init, false);
		}

		const type = response.headers.get("content-type") ?? "unknown";
		throw new WordpressError(
			"invalid_response",
			`WordPress returned a response that is not JSON (${response.status} ${type}: ${responseSnippet(text)}).`,
			502,
		);
	}

	if (!response.ok) {
		throwApiError(response.status, payload);
	}

	return payload;
}

function parseJsonBody(text: string): unknown | undefined {
	const cleaned = text.replace(/^\uFEFF/, "").trim();
	if (!cleaned) {
		return undefined;
	}

	try {
		return JSON.parse(cleaned) as unknown;
	} catch {
		const start = cleaned.search(/[[{]/);
		const prefix = start > 0 ? cleaned.slice(0, start) : "";
		if (start <= 0 || start > 500 || !/warning|notice|deprecated|fatal|<br/i.test(prefix)) {
			return undefined;
		}

		try {
			return JSON.parse(cleaned.slice(start)) as unknown;
		} catch {
			return undefined;
		}
	}
}

function responseSnippet(text: string): string {
	const plain = text.replace(/<[^>]+>/g, " ").replace(/\s+/g, " ").trim();
	return plain.slice(0, 140) || "empty";
}

function throwApiError(status: number, payload: unknown): never {
	if (isRecord(payload) && typeof payload.code === "string" && typeof payload.message === "string") {
		const data = isRecord(payload.data) ? payload.data : {};
		const reported = typeof data.status === "number" ? data.status : status;
		throw new WordpressError(payload.code, payload.message, reported);
	}

	throw new WordpressError("request_failed", "WordPress could not complete the request.", status);
}

function isRecord(value: unknown): value is Record<string, unknown> {
	return typeof value === "object" && value !== null && !Array.isArray(value);
}
