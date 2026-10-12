export type JsonValue =
	| string
	| number
	| boolean
	| null
	| JsonValue[]
	| { [key: string]: JsonValue };

export interface FeaturedImage {
	id: number;
	url: string;
	alt: string;
	width: number;
	height: number;
	mime?: string;
}

export interface BlockAttributes {
	innerBlocks?: Block[];
	[key: string]: JsonValue | Block[] | undefined;
}

export interface Block {
	name: string;
	html: string;
	attributes: BlockAttributes;
}

export interface Page {
	id: number;
	slug: string;
	path: string;
	title: string;
	status: "publish" | "draft" | "pending" | "future";
	modified: string;
	parentPath: string | null;
	featuredImage: FeaturedImage | null;
	seoTitle: string;
	seoDescription: string;
	seoImage: FeaturedImage | null;
	seoNoindex: boolean;
	heroSummary: string;
	blocks: Block[];
}

export interface PathEntry {
	id: number;
	path: string;
	modified: string;
}

export interface PathIndex {
	paths: PathEntry[];
}

export interface DesignLayout {
	contentSize?: string;
	wideSize?: string;
}

export interface Design {
	version: string;
	palette: Record<string, string>;
	fontSizes: Record<string, string>;
	spacingSizes: Record<string, string>;
	layout: DesignLayout;
}

export interface NavItem {
	label: string;
	path: string | null;
	detail: string;
	children: NavItem[];
}

export interface Navigation {
	primary: NavItem[];
	footer: NavItem[];
}

export interface SiteLogo {
	id: number;
	url: string;
	alt: string;
	width: number;
	height: number;
}

export interface RedirectRule {
	from: string;
	to: string;
	status: 301 | 302;
}

export interface DiscoveryPage {
	path: string;
	modified: string;
	title: string;
	description: string;
	indexable: boolean;
}

export interface Discovery {
	siteUrl: string;
	areaServed: string;
	geoSummary: string;
	sameAs: string[];
	shareImage: FeaturedImage | null;
	googleSiteVerification: string;
	googleAnalytics: string;
	googleTagManager: string;
	googleBusinessVerification: string;
	bingVerification: string;
	redirects: RedirectRule[];
	pages: DiscoveryPage[];
}

export interface SocialLink {
	network: "facebook" | "instagram" | "linkedin" | "youtube" | "tiktok";
	label: string;
	url: string;
}

export interface SiteSettings {
	legalName: string;
	shortName: string;
	tagline: string;
	registrationNumber: string;
	phone: string;
	email: string;
	postalAddress: string;
	copyrightText: string;
	logo: SiteLogo | null;
	logoHeight: number;
	submenuArrows?: boolean;
	footerLogo: SiteLogo | null;
	footerLogoHeight: number;
	footerWordmark: string;
	locationHeading: string;
	marqueePhrases: string[];
	social: SocialLink[];
}

export class WordpressError extends Error {
	readonly code: string;
	readonly status: number;

	constructor(code: string, message: string, status: number) {
		super(message);
		this.name = "WordpressError";
		this.code = code;
		this.status = status;
	}
}
