import type { NextConfig } from "next";

function uploadRemotePatterns(): NonNullable<NextConfig["images"]>["remotePatterns"] {
	const raw = process.env.WORDPRESS_API_URL;
	if (!raw) {
		return [];
	}

	try {
		const url = new URL(raw);
		if (url.protocol !== "http:" && url.protocol !== "https:") {
			return [];
		}

		return [
			{
				protocol: url.protocol === "https:" ? "https" : "http",
				hostname: url.hostname,
				...(url.port ? { port: url.port } : {}),
				pathname: "/wp-content/uploads/**",
			},
		];
	} catch {
		return [];
	}
}

const nextConfig: NextConfig = {
	cacheComponents: true,
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
