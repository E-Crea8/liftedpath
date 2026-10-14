import { wordpressApiHost, wordpressApiUrl } from "@/lib/wp/api-url";

/**
 * Public, secret-free check that the server can resolve and reach WordPress.
 * Used after an outage to confirm WORDPRESS_API_URL without printing secrets.
 */
export async function GET() {
	let api = "";
	let host = "";
	try {
		api = wordpressApiUrl();
		host = wordpressApiHost();
	} catch {
		return Response.json(
			{ ok: false, reason: "missing_config", host: "", status: 0 },
			{ status: 503, headers: { "Cache-Control": "no-store" } },
		);
	}

	const url = new URL(`${api}/liftedpath/v1/settings`);
	url.searchParams.set("lp_fresh", String(Date.now()));

	try {
		const response = await fetch(url, {
			headers: { Accept: "application/json" },
			cache: "no-store",
			signal: AbortSignal.timeout(8000),
		});
		const text = await response.text();
		const json = text.trim().startsWith("{");
		const ok = response.ok && json;
		return Response.json(
			{
				ok,
				host,
				status: response.status,
				json,
				configuredHost: host,
			},
			{ status: ok ? 200 : 503, headers: { "Cache-Control": "no-store" } },
		);
	} catch {
		return Response.json(
			{ ok: false, reason: "unreachable", host, status: 0 },
			{ status: 503, headers: { "Cache-Control": "no-store" } },
		);
	}
}
