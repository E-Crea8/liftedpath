import { createHash } from "node:crypto";
import { unstable_cache } from "next/cache";
import { after } from "next/server";
import { notifyAdmin } from "@/lib/wp/alert";
import type { Design, Discovery, FeaturedImage, Navigation, Page, SiteSettings } from "@/lib/wp/types";
import { WordpressError } from "@/lib/wp/types";

/**
 * Last successful public read. The content webhook does not use this tag, so a
 * failed refresh keeps the previous copy. The WordPress fetch itself stays outside
 * this cache and still runs on the visitor's request.
 *
 * Durable copies must NOT use a timed `revalidate`. With `revalidate: 30`, Next
 * re-ran this factory on cold isolates while `pending` was empty, threw
 * "no saved copy to store", and `recallCopy` treated that throw as a miss even
 * when the Data Cache still held a good copy → SiteHold + Vercel error spam.
 * `revalidate: false` means the factory runs only on a real miss (holdCopy with
 * pending set, or first-ever read). Successful WP reads still return live data;
 * this cache is only the outage fallback.
 */
const pending = new Map<string, unknown>();
const memory = new Map<string, unknown>();

const persist = unstable_cache(
	async (key: string): Promise<unknown> => {
		const value = pending.get(key) ?? memory.get(key);
		if (value === undefined) {
			// Cache miss only. Throw so Next does not store null and block a
			// later holdCopy write. With revalidate:false this never runs as a
			// timed refresh of an existing entry.
			throw new Error("LiftedPath has no saved copy to store.");
		}
		return value;
	},
	["liftedpath-saved-copy"],
	{ revalidate: false, tags: ["liftedpath-saved-copy"] },
);

export function holdCopy(key: string, value: unknown): void {
	memory.set(key, value);
	pending.set(key, value);
	void persist(key)
		.catch(() => {
			// A failed write keeps the previous saved copy.
		})
		.finally(() => {
			pending.delete(key);
		});
}

export async function recallCopy<T>(key: string, accept: (value: unknown) => value is T): Promise<T | null> {
	const local = memory.get(key);
	if (accept(local)) {
		return local;
	}

	const inflight = pending.get(key);
	if (accept(inflight)) {
		return inflight;
	}

	try {
		const saved = await persist(key);
		if (accept(saved)) {
			memory.set(key, saved);
			return saved;
		}
	} catch {
		// This deployment has not stored a copy yet.
	}

	return null;
}

export function reportReadFailure(error: unknown, where: string, kept: boolean): void {
	if (error instanceof WordpressError && (error.status === 404 || error.code === "page_not_found")) {
		return;
	}

	const alert = {
		code: error instanceof WordpressError ? error.code : "unavailable",
		path: where,
		message: error instanceof Error && error.message ? error.message : "WordPress could not be read.",
		kept,
	};

	try {
		after(() => notifyAdmin(alert));
	} catch {
		notifyAdmin(alert);
	}
}

export function mediaKey(ids: number[]): string {
	return `media:${createHash("sha256").update(ids.join(",")).digest("hex").slice(0, 20)}`;
}

export const STANDBY_DESIGN: Design = {
	version: "standby",
	palette: {},
	fontSizes: {},
	spacingSizes: {},
	layout: { contentSize: "1120px" },
};

export const STANDBY_NAVIGATION: Navigation = {
	primary: [],
	footer: [],
};

export const STANDBY_SETTINGS: SiteSettings = {
	legalName: "LiftedPath Empowerment Foundation",
	shortName: "LiftedPath",
	tagline: "",
	registrationNumber: "",
	phone: "",
	email: "",
	postalAddress: "",
	copyrightText: "",
	logo: null,
	logoHeight: 104,
	submenuArrows: false,
	footerLogo: null,
	footerLogoHeight: 95,
	footerWordmark: "LiftedPath",
	locationHeading: "",
	marqueePhrases: [],
	social: [],
};

export const STANDBY_DISCOVERY: Discovery = {
	siteUrl: "https://www.liftedpathfoundation.org",
	areaServed: "",
	geoSummary: "",
	sameAs: [],
	shareImage: null,
	googleSiteVerification: "",
	googleAnalytics: "",
	googleTagManager: "",
	googleBusinessVerification: "",
	bingVerification: "",
	redirects: [],
	pages: [],
};

export function isSavedPage(value: unknown): value is Page {
	if (!isRecord(value) || typeof value.path !== "string" || typeof value.title !== "string") {
		return false;
	}
	if (typeof value.status !== "string" || typeof value.modified !== "string" || typeof value.seoTitle !== "string") {
		return false;
	}
	if (typeof value.seoDescription !== "string" || typeof value.seoNoindex !== "boolean" || !Array.isArray(value.blocks)) {
		return false;
	}
	return value.blocks.every(
		(block) => isRecord(block) && typeof block.name === "string" && typeof block.html === "string" && isRecord(block.attributes),
	);
}

export function isSavedDesign(value: unknown): value is Design {
	return isRecord(value) && typeof value.version === "string" && isStringRecord(value.palette) && isStringRecord(value.fontSizes) && isStringRecord(value.spacingSizes) && isRecord(value.layout);
}

export function isSavedNavigation(value: unknown): value is Navigation {
	return isRecord(value) && Array.isArray(value.primary) && Array.isArray(value.footer) && value.primary.every(isNavItem) && value.footer.every(isNavItem);
}

export function isSavedSettings(value: unknown): value is SiteSettings {
	if (!isRecord(value)) {
		return false;
	}
	return (
		typeof value.legalName === "string" &&
		typeof value.shortName === "string" &&
		typeof value.tagline === "string" &&
		typeof value.phone === "string" &&
		typeof value.email === "string" &&
		typeof value.postalAddress === "string" &&
		typeof value.logoHeight === "number" &&
		(value.submenuArrows === undefined || typeof value.submenuArrows === "boolean") &&
		typeof value.footerLogoHeight === "number" &&
		typeof value.footerWordmark === "string" &&
		typeof value.copyrightText === "string" &&
		typeof value.registrationNumber === "string" &&
		typeof value.locationHeading === "string" &&
		Array.isArray(value.marqueePhrases) &&
		value.marqueePhrases.every((phrase) => typeof phrase === "string") &&
		Array.isArray(value.social) &&
		value.social.every(isSocialLink) &&
		isLogo(value.logo) &&
		isLogo(value.footerLogo)
	);
}

export function isSavedDiscovery(value: unknown): value is Discovery {
	if (!isRecord(value) || typeof value.siteUrl !== "string" || !/^https?:\/\//i.test(value.siteUrl)) {
		return false;
	}
	if (!Array.isArray(value.pages) || !Array.isArray(value.redirects) || !Array.isArray(value.sameAs)) {
		return false;
	}
	return (
		value.sameAs.every((item) => typeof item === "string") &&
		value.pages.every((page) => isRecord(page) && typeof page.path === "string" && typeof page.indexable === "boolean") &&
		value.redirects.every((rule) => isRecord(rule) && typeof rule.from === "string" && typeof rule.to === "string" && (rule.status === 301 || rule.status === 302))
	);
}

export function isSavedMedia(value: unknown): value is FeaturedImage[] {
	return (
		Array.isArray(value) &&
		value.every(
			(item) =>
				isRecord(item) &&
				typeof item.id === "number" &&
				typeof item.url === "string" &&
				typeof item.alt === "string" &&
				typeof item.width === "number" &&
				typeof item.height === "number",
		)
	);
}

function isNavItem(value: unknown): boolean {
	if (!isRecord(value) || typeof value.label !== "string" || typeof value.detail !== "string" || !Array.isArray(value.children)) {
		return false;
	}
	if (value.path !== null && typeof value.path !== "string") {
		return false;
	}
	return value.children.every(isNavItem);
}

function isSocialLink(value: unknown): boolean {
	if (!isRecord(value) || typeof value.label !== "string" || typeof value.url !== "string") {
		return false;
	}
	return value.network === "facebook" || value.network === "instagram" || value.network === "linkedin" || value.network === "youtube" || value.network === "tiktok";
}

function isLogo(value: unknown): boolean {
	if (value === null) {
		return true;
	}
	return isRecord(value) && typeof value.url === "string" && typeof value.alt === "string" && typeof value.width === "number" && typeof value.height === "number";
}

function isStringRecord(value: unknown): boolean {
	if (!isRecord(value)) {
		return false;
	}
	return Object.values(value).every((item) => typeof item === "string");
}

function isRecord(value: unknown): value is Record<string, unknown> {
	return typeof value === "object" && value !== null && !Array.isArray(value);
}
