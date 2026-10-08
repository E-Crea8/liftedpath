import { sanitizeHtml } from "@/lib/wp/sanitize";
import type {
	Block,
	BlockAttributes,
	Design,
	DesignLayout,
	FeaturedImage,
	JsonValue,
	NavItem,
	Discovery,
	DiscoveryPage,
	Navigation,
	Page,
	PathEntry,
	PathIndex,
	RedirectRule,
	SiteLogo,
	SiteSettings,
} from "@/lib/wp/types";
import { WordpressError } from "@/lib/wp/types";

const PUBLIC_PATH = /^\/$|^\/[a-z0-9]+(?:-[a-z0-9]+)*(?:\/[a-z0-9]+(?:-[a-z0-9]+)*)*$/;
const BLOCK_NAME = /^[a-z0-9-]+\/[a-z0-9-]+$/;
const MODIFIED = /^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/;
const DESIGN_VERSION = /^[a-f0-9]{64}$/;

export function assertPublicPath(path: string): string {
	if (!PUBLIC_PATH.test(path)) {
		throw new WordpressError("invalid_path", "The path is not a page path.", 400);
	}

	return path;
}

export function mapPage(value: unknown, mode: "public" | "preview" = "public"): Page {
	const record = recordOrThrow(value, "The page payload is not an object.");
	const path = requiredString(record.path, "The page path is missing.");
	assertPublicPath(path);

	const status = requiredString(record.status, "The page status is missing.");
	const previewStatus = status === "publish" || status === "draft" || status === "pending" || status === "future";
	if (mode === "public" ? status !== "publish" : !previewStatus) {
		throw new WordpressError("invalid_response", "The page is not published.", 502);
	}

	const parentPath = mapParentPath(record.parent_path);

	return {
		id: requiredId(record.id),
		slug: requiredString(record.slug, "The page slug is missing."),
		path,
		title: requiredString(record.title, "The page title is missing."),
		status: status as Page["status"],
		modified: requiredModified(record.modified),
		parentPath,
		featuredImage: mapFeaturedImage(record.featured_image),
		seoTitle: optionalString(record.seo_title),
		seoDescription: optionalString(record.seo_description),
		seoImage: mapFeaturedImage(record.seo_image ?? null),
		seoNoindex: requiredBoolean(record.seo_noindex, "The page index flag is missing."),
		heroSummary: optionalString(record.hero_summary),
		blocks: mapBlocks(record.blocks),
	};
}

export function mapPaths(value: unknown): PathIndex {
	const record = recordOrThrow(value, "The path index is not an object.");
	if (!Array.isArray(record.paths)) {
		throw new WordpressError("invalid_response", "The path index has no paths array.", 502);
	}

	return {
		paths: record.paths.map(mapPathEntry),
	};
}

export function mapNavigation(value: unknown): Navigation {
	const record = recordOrThrow(value, "The navigation payload is not an object.");

	return {
		primary: mapNavItems(record.primary, 0),
		footer: mapNavItems(record.footer, 0),
	};
}

export function mapDiscovery(value: unknown): Discovery {
	const record = recordOrThrow(value, "The search payload is not an object.");
	const siteUrl = requiredString(record.site_url, "The public site URL is missing.");
	if (!isHttpUrl(siteUrl)) {
		throw new WordpressError("invalid_response", "The public site URL is not an HTTP URL.", 502);
	}

	return {
		siteUrl: siteUrl.replace(/\/$/, ""),
		areaServed: optionalString(record.area_served),
		geoSummary: optionalString(record.geo_summary),
		sameAs: httpList(record.same_as),
		shareImage: mapFeaturedImage(record.share_image ?? null),
		googleSiteVerification: matchingToken(record.google_site_verification, /^[A-Za-z0-9_-]{8,200}$/),
		googleAnalytics: matchingToken(record.google_analytics, /^G-[A-Z0-9]{4,32}$/),
		googleTagManager: matchingToken(record.google_tag_manager, /^GTM-[A-Z0-9]{4,32}$/),
		googleBusinessVerification: matchingToken(record.google_business_verification, /^[A-Za-z0-9_-]{8,200}$/),
		bingVerification: matchingToken(record.bing_verification, /^[A-Za-z0-9_-]{8,200}$/),
		redirects: mapRedirects(record.redirects),
		pages: mapDiscoveryPages(record.pages),
	};
}

export function mapRedirects(value: unknown): RedirectRule[] {
	if (!Array.isArray(value)) {
		throw new WordpressError("invalid_response", "The redirect list is not an array.", 502);
	}

	return value.map(mapRedirect);
}

export function mapSettings(value: unknown): SiteSettings {
	const record = recordOrThrow(value, "The settings payload is not an object.");

	return {
		legalName: optionalString(record.legal_name),
		shortName: optionalString(record.short_name),
		tagline: optionalString(record.tagline),
		registrationNumber: optionalString(record.registration_number),
		phone: optionalString(record.phone),
		email: optionalString(record.email),
		postalAddress: optionalString(record.postal_address),
		copyrightText: optionalString(record.copyright_text),
		logo: mapLogo(record.logo),
		logoHeight: boundedNumber(record.logo_height, 104, 64, 160),
		footerLogo: mapLogo(record.footer_logo),
		footerLogoHeight: boundedNumber(record.footer_logo_height, 95, 48, 200),
		footerWordmark: optionalString(record.footer_wordmark),
		locationHeading: optionalString(record.location_heading),
		marqueePhrases: stringList(record.marquee_phrases),
	};
}

export function mapMedia(value: unknown): FeaturedImage[] {
	if (!Array.isArray(value)) {
		throw new WordpressError("invalid_response", "The media payload is not a list.", 502);
	}

	const images: FeaturedImage[] = [];
	for (const item of value) {
		const image = mapMediaItem(item);
		if (image) {
			images.push(image);
		}
	}
	return images;
}

export function mapDesign(value: unknown): Design {
	const record = recordOrThrow(value, "The design payload is not an object.");
	const version = requiredString(record.version, "The design version is missing.");
	if (!DESIGN_VERSION.test(version)) {
		throw new WordpressError("invalid_response", "The design version is not a SHA-256 hash.", 502);
	}

	return {
		version,
		palette: stringRecord(record.palette),
		fontSizes: stringRecord(record.fontSizes),
		spacingSizes: stringRecord(record.spacingSizes),
		layout: mapLayout(record.layout),
	};
}

function mapPathEntry(value: unknown): PathEntry {
	const record = recordOrThrow(value, "A path entry is not an object.");
	const path = requiredString(record.path, "A path entry is missing its path.");
	assertPublicPath(path);

	return {
		id: requiredId(record.id),
		path,
		modified: requiredModified(record.modified),
	};
}

function mapParentPath(value: unknown): string | null {
	if (value === null) {
		return null;
	}

	if (typeof value !== "string") {
		throw new WordpressError("invalid_response", "The parent path is not a string.", 502);
	}

	return assertPublicPath(value);
}

function mapFeaturedImage(value: unknown): FeaturedImage | null {
	if (value === null) {
		return null;
	}

	const record = recordOrThrow(value, "The featured image is not an object.");
	const url = requiredString(record.url, "The featured image URL is missing.");
	if (!isHttpUrl(url)) {
		throw new WordpressError("invalid_response", "The featured image URL is not an HTTP URL.", 502);
	}

	return {
		id: requiredId(record.id),
		url,
		alt: optionalString(record.alt),
		width: requiredNumber(record.width, "The featured image width is missing."),
		height: requiredNumber(record.height, "The featured image height is missing."),
	};
}

function mapBlocks(value: unknown): Block[] {
	if (!Array.isArray(value)) {
		throw new WordpressError("invalid_response", "The page blocks are not an array.", 502);
	}

	return value.map(mapBlock);
}

function mapBlock(value: unknown): Block {
	const record = recordOrThrow(value, "A block is not an object.");
	const name = requiredString(record.name, "A block name is missing.");
	if (!BLOCK_NAME.test(name)) {
		throw new WordpressError("invalid_response", "A block name is not a block type.", 502);
	}

	if (typeof record.html !== "string") {
		throw new WordpressError("invalid_response", "A block is missing its HTML.", 502);
	}

	if (!isRecord(record.attributes)) {
		throw new WordpressError("invalid_response", "Block attributes are not an object.", 502);
	}

	const attributes: BlockAttributes = {};
	for (const [key, item] of Object.entries(record.attributes)) {
		if (key.startsWith("_") || key === "innerBlocks") {
			continue;
		}
		attributes[key] = asJson(item);
	}

	if (Array.isArray(record.attributes.innerBlocks)) {
		attributes.innerBlocks = record.attributes.innerBlocks.map(mapBlock);
	} else if (record.attributes.innerBlocks !== undefined) {
		throw new WordpressError("invalid_response", "Nested blocks are not an array.", 502);
	}

	return {
		name,
		html: sanitizeHtml(record.html),
		attributes,
	};
}

function mapNavItems(value: unknown, depth: number): NavItem[] {
	if (!Array.isArray(value)) {
		throw new WordpressError("invalid_response", "A navigation list is not an array.", 502);
	}

	if (depth > 6) {
		throw new WordpressError("invalid_response", "A navigation list is too deep.", 502);
	}

	return value.map((item) => mapNavItem(item, depth));
}

function mapNavItem(value: unknown, depth: number): NavItem {
	const record = recordOrThrow(value, "A navigation item is not an object.");

	return {
		label: requiredString(record.label, "A navigation label is missing."),
		path: mapNavPath(record.path),
		detail: optionalString(record.detail),
		children: mapNavItems(record.children, depth + 1),
	};
}

function mapNavPath(value: unknown): string | null {
	if (value === null) {
		return null;
	}

	if (typeof value !== "string") {
		throw new WordpressError("invalid_response", "A navigation path is not a string.", 502);
	}

	if (value.startsWith("http://") || value.startsWith("https://")) {
		if (!isHttpUrl(value)) {
			throw new WordpressError("invalid_response", "A navigation URL is not an HTTP URL.", 502);
		}
		return value;
	}

	return assertPublicPath(value);
}

function boundedNumber(value: unknown, fallback: number, min: number, max: number): number {
	const number = typeof value === "number" ? value : typeof value === "string" && value.trim() !== "" ? Number(value) : fallback;
	if (!Number.isFinite(number)) {
		return fallback;
	}

	return Math.min(max, Math.max(min, Math.round(number)));
}

function mapLogo(value: unknown): SiteLogo | null {
	if (value === null || value === undefined) {
		return null;
	}

	const record = recordOrThrow(value, "The logo is not an object.");
	const url = requiredString(record.url, "The logo URL is missing.");
	if (!isHttpUrl(url)) {
		throw new WordpressError("invalid_response", "The logo URL is not an HTTP URL.", 502);
	}

	const width = requiredNumber(record.width, "The logo width is missing.");
	const height = requiredNumber(record.height, "The logo height is missing.");
	if (width <= 0 || height <= 0) {
		throw new WordpressError("invalid_response", "The logo has no dimensions.", 502);
	}

	return {
		id: requiredId(record.id),
		url,
		alt: optionalString(record.alt),
		width,
		height,
	};
}

function mapLayout(value: unknown): DesignLayout {
	if (!isRecord(value)) {
		return {};
	}

	const layout: DesignLayout = {};
	if (typeof value.contentSize === "string") {
		layout.contentSize = value.contentSize;
	}
	if (typeof value.wideSize === "string") {
		layout.wideSize = value.wideSize;
	}
	return layout;
}

function stringList(value: unknown): string[] {
	if (!Array.isArray(value)) {
		throw new WordpressError("invalid_response", "The marquee phrases are not a list.", 502);
	}

	const phrases: string[] = [];
	for (const item of value) {
		if (typeof item !== "string") {
			throw new WordpressError("invalid_response", "A marquee phrase is not text.", 502);
		}
		const phrase = item.trim();
		if (phrase) {
			phrases.push(phrase);
		}
	}
	return phrases;
}

function mapMediaItem(value: unknown): FeaturedImage | null {
	if (!isRecord(value)) {
		return null;
	}

	const url = typeof value.source_url === "string" ? value.source_url : "";
	if (!url.startsWith("http://") && !url.startsWith("https://")) {
		return null;
	}

	const mime = typeof value.mime_type === "string" ? value.mime_type : "";
	const details = isRecord(value.media_details) ? value.media_details : null;
	let width = details && typeof details.width === "number" ? details.width : 0;
	let height = details && typeof details.height === "number" ? details.height : 0;
	const isVideo = mime.startsWith("video/");
	if (!Number.isInteger(width) || !Number.isInteger(height) || width <= 0 || height <= 0) {
		if (!isVideo) {
			return null;
		}
		width = 1920;
		height = 1080;
	}

	if (typeof value.id !== "number" || !Number.isInteger(value.id) || value.id <= 0) {
		return null;
	}

	return {
		id: value.id,
		url,
		alt: typeof value.alt_text === "string" ? value.alt_text : "",
		width,
		height,
		mime,
	};
}

function stringRecord(value: unknown): Record<string, string> {
	if (!isRecord(value)) {
		return {};
	}

	const record: Record<string, string> = {};
	for (const [key, item] of Object.entries(value)) {
		if (typeof item === "string") {
			record[key] = item;
		}
	}
	return record;
}

function asJson(value: unknown): JsonValue {
	if (value === null || typeof value === "string" || typeof value === "boolean") {
		return value;
	}

	if (typeof value === "number") {
		if (!Number.isFinite(value)) {
			throw new WordpressError("invalid_response", "A block attribute is not JSON.", 502);
		}
		return value;
	}

	if (Array.isArray(value)) {
		return value.map(asJson);
	}

	if (isRecord(value)) {
		const record: Record<string, JsonValue> = {};
		for (const [key, item] of Object.entries(value)) {
			record[key] = asJson(item);
		}
		return record;
	}

	throw new WordpressError("invalid_response", "A block attribute is not JSON.", 502);
}

function isRecord(value: unknown): value is Record<string, unknown> {
	return typeof value === "object" && value !== null && !Array.isArray(value);
}

function recordOrThrow(value: unknown, message: string): Record<string, unknown> {
	if (!isRecord(value)) {
		throw new WordpressError("invalid_response", message, 502);
	}
	return value;
}

function mapDiscoveryPages(value: unknown): DiscoveryPage[] {
	if (!Array.isArray(value)) {
		throw new WordpressError("invalid_response", "The search page list is not an array.", 502);
	}

	return value.map(mapDiscoveryPage);
}

function mapDiscoveryPage(value: unknown): DiscoveryPage {
	const record = recordOrThrow(value, "A search page is not an object.");
	const path = requiredString(record.path, "A search page path is missing.");

	return {
		path: assertPublicPath(path),
		modified: requiredModified(record.modified),
		title: requiredString(record.title, "A search page title is missing."),
		description: optionalString(record.description),
		indexable: requiredBoolean(record.indexable, "A search page index flag is missing."),
	};
}

function mapRedirect(value: unknown): RedirectRule {
	const record = recordOrThrow(value, "A redirect is not an object.");
	const from = assertPublicPath(requiredString(record.from, "A redirect path is missing."));
	const to = requiredString(record.to, "A redirect destination is missing.");
	const status = record.status;

	if (status !== 301 && status !== 302) {
		throw new WordpressError("invalid_response", "A redirect type is not valid.", 502);
	}

	if (to.startsWith("/")) {
		assertPublicPath(to);
	} else if (!isHttpsUrl(to)) {
		throw new WordpressError("invalid_response", "A redirect destination is not a public path.", 502);
	}

	if (from === to) {
		throw new WordpressError("invalid_response", "A redirect destination repeats its path.", 502);
	}

	return { from, to, status };
}

function httpList(value: unknown): string[] {
	if (!Array.isArray(value)) {
		throw new WordpressError("invalid_response", "The profile links are not a list.", 502);
	}

	return value.map((item) => {
		const url = requiredString(item, "A profile link is missing.");
		if (!isHttpUrl(url)) {
			throw new WordpressError("invalid_response", "A profile link is not an HTTP URL.", 502);
		}
		return url;
	});
}

function matchingToken(value: unknown, pattern: RegExp): string {
	const token = optionalString(value);
	if (token === "") {
		return "";
	}
	if (!pattern.test(token)) {
		throw new WordpressError("invalid_response", "A verification code is not valid.", 502);
	}
	return token;
}

function requiredBoolean(value: unknown, message: string): boolean {
	if (typeof value !== "boolean") {
		throw new WordpressError("invalid_response", message, 502);
	}
	return value;
}

function isHttpsUrl(value: string): boolean {
	try {
		const url = new URL(value);
		return url.protocol === "https:" && url.username === "";
	} catch {
		return false;
	}
}

function requiredString(value: unknown, message: string): string {
	if (typeof value !== "string" || value === "") {
		throw new WordpressError("invalid_response", message, 502);
	}
	return value;
}

function optionalString(value: unknown): string {
	if (value === undefined || value === null) {
		return "";
	}
	if (typeof value !== "string") {
		throw new WordpressError("invalid_response", "A text field is not a string.", 502);
	}
	return value;
}

function requiredId(value: unknown): number {
	if (typeof value !== "number" || !Number.isInteger(value) || value <= 0) {
		throw new WordpressError("invalid_response", "An id is not a positive integer.", 502);
	}
	return value;
}

function requiredNumber(value: unknown, message: string): number {
	if (typeof value !== "number" || !Number.isFinite(value) || value < 0) {
		throw new WordpressError("invalid_response", message, 502);
	}
	return value;
}

function requiredModified(value: unknown): string {
	if (typeof value !== "string" || !MODIFIED.test(value)) {
		throw new WordpressError("invalid_response", "A modified time is not a date.", 502);
	}
	return value;
}

function isHttpUrl(value: string): boolean {
	try {
		const url = new URL(value);
		return url.protocol === "http:" || url.protocol === "https:";
	} catch {
		return false;
	}
}
