import { postAdminAlert } from "@/lib/wp/alert";

const recent = new Map<string, number>();
const TEN_MINUTES = 10 * 60 * 1000;

export async function POST(request: Request) {
	let body: unknown;
	try {
		body = await request.json();
	} catch {
		return alertFailure(400, "The alert could not be read.");
	}

	if (!isRecord(body) || body.code !== "render") {
		return alertFailure(400, "The alert could not be read.");
	}

	const path = typeof body.path === "string" ? body.path : "/";
	const message = typeof body.message === "string" ? body.message : "The public page could not be rendered.";
	const key = path.slice(0, 180);
	const now = Date.now();
	const previous = recent.get(key) ?? 0;
	if (now - previous < TEN_MINUTES) {
		return Response.json({ sent: false, held: true }, { headers: { "Cache-Control": "no-store" } });
	}

	recent.set(key, now);
	await postAdminAlert({
		code: "render",
		path: key,
		message,
		kept: false,
	});

	return Response.json({ sent: true }, { headers: { "Cache-Control": "no-store" } });
}

function alertFailure(status: number, message: string) {
	return Response.json({ code: "invalid_body", message }, { status, headers: { "Cache-Control": "no-store" } });
}

function isRecord(value: unknown): value is Record<string, unknown> {
	return typeof value === "object" && value !== null && !Array.isArray(value);
}
