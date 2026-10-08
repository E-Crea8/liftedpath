import { io } from "next/cache";
import { draftMode } from "next/headers";
import { assertPublicPath, mapDesign, mapDiscovery, mapMedia, mapNavigation, mapPage, mapPaths, mapSettings } from "@/lib/wp/map";
import type { Design, Discovery, FeaturedImage, Navigation, Page, PathIndex, SiteSettings } from "@/lib/wp/types";
import { WordpressError } from "@/lib/wp/types";

/**
 * Cache Components still inlines a finished fetch into the prerender when the
 * segment is allowed to block. io() keeps this read on the visitor's request.
 */
async function readOnRequest(): Promise<void> {
	await io();
}

export async function getPage(path: string): Promise<Page> {
	const publicPath = assertPublicPath(path);

	const draft = await draftMode();
	if (draft.isEnabled) {
		const url = new URL(`${apiUrl()}/liftedpath/v1/preview`);
		url.searchParams.set("path", publicPath);
		return mapPage(await readJson(url, { headers: previewHeaders() }), "preview");
	}

	await readOnRequest();
	const url = new URL(`${apiUrl()}/liftedpath/v1/page`);
	url.searchParams.set("path", publicPath);
	return mapPage(await readJson(url));
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
	await readOnRequest();
	const url = new URL(`${apiUrl()}/liftedpath/v1/paths`);
	return mapPaths(await readJson(url));
}

export async function getNavigation(): Promise<Navigation> {
	await readOnRequest();
	const url = new URL(`${apiUrl()}/liftedpath/v1/navigation`);
	return mapNavigation(await readJson(url));
}

export async function getSettings(): Promise<SiteSettings> {
	await readOnRequest();
	const url = new URL(`${apiUrl()}/liftedpath/v1/settings`);
	return mapSettings(await readJson(url));
}

export async function getDiscovery(): Promise<Discovery> {
	await readOnRequest();
	const url = new URL(`${apiUrl()}/liftedpath/v1/seo`);
	return mapDiscovery(await readJson(url));
}

export async function getMedia(ids: number[]): Promise<FeaturedImage[]> {
	const unique = [...new Set(ids.filter((id) => Number.isInteger(id) && id > 0))].sort((left, right) => left - right);
	if (unique.length === 0) {
		return [];
	}

	await readOnRequest();
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
}

export async function getDesign(): Promise<Design> {
	await readOnRequest();
	const url = new URL(`${apiUrl()}/liftedpath/v1/design`);
	return mapDesign(await readJson(url));
}

function apiUrl(): string {
	const raw = process.env.WORDPRESS_API_URL;
	if (!raw) {
		throw new WordpressError("missing_config", "WORDPRESS_API_URL is not set.", 500);
	}

	return raw.replace(/\/$/, "");
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

async function readJson(url: URL, init?: RequestInit): Promise<unknown> {
	const headers = new Headers(init?.headers);
	headers.set("Accept", "application/json");

	let response: Response;
	try {
		response = await fetch(url, {
			...init,
			headers,
			cache: "no-store",
		});
	} catch {
		throw new WordpressError("unavailable", "WordPress did not respond.", 503);
	}

	let payload: unknown;
	try {
		payload = JSON.parse(await response.text()) as unknown;
	} catch {
		throw new WordpressError("invalid_response", "WordPress returned a response that is not JSON.", 502);
	}

	if (!response.ok) {
		throwApiError(response.status, payload);
	}

	return payload;
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
