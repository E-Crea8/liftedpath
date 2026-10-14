import type { NextConfig } from "next";

function uploadRemotePatterns(): NonNullable<NextConfig["images"]>["remotePatterns"] {
	const raw = process.env.WORDPRESS_API_URL?.trim() || "https://cms.liftedpathfoundation.org/wp-json";

	try {
		const url = new URL(raw);
		if (url.protocol !== "http:" && url.protocol !== "https:") {
			return [];
		}

		const hosts = new Set([url.hostname, "cms.liftedpathfoundation.org"]);

		return [...hosts].map((hostname) => ({
			protocol: (url.protocol === "https:" || hostname === "cms.liftedpathfoundation.org" ? "https" : "http") as "http" | "https",
			hostname,
			...(url.port && hostname === url.hostname ? { port: url.port } : {}),
			pathname: "/wp-content/uploads/**",
		}));
	} catch {
		return [
			{
				protocol: "https",
				hostname: "cms.liftedpathfoundation.org",
				pathname: "/wp-content/uploads/**",
			},
		];
	}
}

const nextConfig: NextConfig = {
	cacheComponents: true,
	cacheLife: {
		max: {
			stale: 0,
			revalidate: 30,
			expire: 120,
		},
	},
	images: {
		remotePatterns: uploadRemotePatterns(),
	},
	partialPrefetching: true,
	turbopack: {
		rules: {
			"*.css": {
				loaders: ["@tailwindcss/turbopack"],
				as: "*.css",
			},
		},
	},
};

export default nextConfig;
