import type { MetadataRoute } from "next";
import { getDiscovery } from "@/lib/wp/client";

export default async function sitemap(): Promise<MetadataRoute.Sitemap> {
	const discovery = await getDiscovery();

	return discovery.pages
		.filter((page) => page.indexable)
		.map((page) => ({
			url: new URL(page.path, discovery.siteUrl).toString(),
			lastModified: page.modified,
		}));
}
