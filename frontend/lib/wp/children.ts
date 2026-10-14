import { getPaths } from "@/lib/wp/client";

const SLUG = /^[a-z0-9]+(?:-[a-z0-9]+)*$/;

export function childPath(parent: "learning" | "what-we-do", slug: string): string | null {
	if (!SLUG.test(slug)) {
		return null;
	}

	return `/${parent}/${slug}`;
}

/**
 * Build-time paths for learning / what-we-do children.
 * An empty list is fine: dynamicParams still serves the page from WordPress on request.
 * Never throw — a CMS blip must not fail the Vercel build.
 */
export async function childStaticParams(parent: "learning" | "what-we-do") {
	try {
		const index = await getPaths();
		const params: { slug: string }[] = [];

		for (const entry of index.paths) {
			const parts = entry.path.split("/").filter(Boolean);
			if (parts.length === 2 && parts[0] === parent && SLUG.test(parts[1])) {
				params.push({ slug: parts[1] });
			}
		}

		return params;
	} catch {
		return [];
	}
}
