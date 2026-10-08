import type { MetadataRoute } from "next";
import { getDiscovery } from "@/lib/wp/client";

export default async function robots(): Promise<MetadataRoute.Robots> {
	const discovery = await getDiscovery();

	return {
		rules: {
			userAgent: "*",
			allow: "/",
			disallow: ["/api/"],
		},
		sitemap: new URL("/sitemap.xml", discovery.siteUrl).toString(),
	};
}
