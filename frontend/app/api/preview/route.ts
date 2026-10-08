import { draftMode } from "next/headers";
import { redirect } from "next/navigation";
import { exchangePreviewToken } from "@/lib/wp/client";
import { assertPublicPath } from "@/lib/wp/map";
import { WordpressError } from "@/lib/wp/types";

export async function GET(request: Request) {
	const token = new URL(request.url).searchParams.get("token") ?? "";
	let path: string;

	try {
		path = await exchangePreviewToken(token);
	} catch (error) {
		const unavailable = error instanceof WordpressError && error.status >= 500;
		return new Response(unavailable ? "Preview is not configured." : "This preview link is not valid.", {
			status: unavailable ? 503 : 401,
			headers: { "Cache-Control": "no-store" },
		});
	}

	const draft = await draftMode();
	draft.enable();
	redirect(path);
}

export async function POST(request: Request) {
	const host = request.headers.get("x-forwarded-host") ?? request.headers.get("host");
	const origin = request.headers.get("origin");

	if (origin && host) {
		let originHost = "";
		try {
			originHost = new URL(origin).host;
		} catch {
			originHost = "";
		}

		if (originHost !== host) {
			return new Response("This preview link is not valid.", {
				status: 403,
				headers: { "Cache-Control": "no-store" },
			});
		}
	}

	const draft = await draftMode();
	draft.disable();
	redirect(previewReturnPath(request, host));
}

function previewReturnPath(request: Request, host: string | null): string {
	const referer = request.headers.get("referer");
	if (!referer || !host) {
		return "/";
	}

	try {
		const url = new URL(referer);
		if (url.host !== host || url.pathname === "/api/preview") {
			return "/";
		}

		return assertPublicPath(url.pathname);
	} catch {
		return "/";
	}
}
