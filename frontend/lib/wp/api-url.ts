/**
 * WordPress REST root for server fetches.
 *
 * Production must talk to the CMS host, never the public Next.js domain.
 * A missing or mis-pointed WORDPRESS_API_URL is what puts SiteHold on every page.
 */
export const PRODUCTION_WORDPRESS_API_URL = "https://cms.liftedpathfoundation.org/wp-json";

const PUBLIC_SITE_HOSTS = new Set(["www.liftedpathfoundation.org", "liftedpathfoundation.org"]);

export function wordpressApiUrl(): string {
	const configured = (process.env.WORDPRESS_API_URL ?? "").trim().replace(/\/$/, "");
	const production = process.env.VERCEL_ENV === "production" || process.env.NODE_ENV === "production";

	if (!configured) {
		if (production) {
			return PRODUCTION_WORDPRESS_API_URL;
		}
		throw new Error("WORDPRESS_API_URL is not set.");
	}

	try {
		const host = new URL(configured).hostname.toLowerCase();
		if (PUBLIC_SITE_HOSTS.has(host) || host === "liftedpath.local") {
			if (production) {
				return PRODUCTION_WORDPRESS_API_URL;
			}
		}
	} catch {
		if (production) {
			return PRODUCTION_WORDPRESS_API_URL;
		}
	}

	return configured;
}

export function wordpressApiHost(): string {
	try {
		return new URL(wordpressApiUrl()).host;
	} catch {
		return "";
	}
}
