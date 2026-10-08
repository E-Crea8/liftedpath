import { NextResponse } from "next/server";
import type { NextRequest } from "next/server";
import { mapRedirects } from "@/lib/wp/map";
import type { RedirectRule } from "@/lib/wp/types";

export async function proxy(request: NextRequest) {
	if (request.method !== "GET" && request.method !== "HEAD") {
		return NextResponse.next();
	}

	const path = normalizePath(request.nextUrl.pathname);
	const match = (await loadRedirects()).find((item) => item.from === path);

	if (!match || match.to === path) {
		return NextResponse.next();
	}

	if (match.to.startsWith("/")) {
		const url = request.nextUrl.clone();
		url.pathname = match.to;
		url.search = "";
		return NextResponse.redirect(url, match.status);
	}

	return NextResponse.redirect(match.to, match.status);
}

function normalizePath(pathname: string): string {
	if (pathname.length > 1 && pathname.endsWith("/")) {
		return pathname.slice(0, -1);
	}

	return pathname;
}

async function loadRedirects(): Promise<RedirectRule[]> {
	const raw = process.env.WORDPRESS_API_URL?.replace(/\/$/, "");
	if (!raw) {
		return [];
	}

	try {
		const response = await fetch(`${raw}/liftedpath/v1/seo?part=redirects`, {
			cache: "no-store",
			signal: AbortSignal.timeout(1500),
		});
		if (!response.ok) {
			return [];
		}

		const payload: unknown = await response.json();
		if (typeof payload !== "object" || payload === null || Array.isArray(payload)) {
			return [];
		}

		return mapRedirects((payload as { redirects?: unknown }).redirects);
	} catch {
		return [];
	}
}

export const config = {
	matcher: ["/((?!api/|_next/|fonts/|.*\\..*).*)"],
};
