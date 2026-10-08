import { createHash, timingSafeEqual } from "node:crypto";
import { revalidatePath, revalidateTag } from "next/cache";
import { assertPublicPath } from "@/lib/wp/map";

const INDEX_TAGS = ["nav", "settings", "design", "sitemap", "media", "seo"] as const;
const FIELDS = new Set(["secret", "action", "path", "tag"]);

export async function POST(request: Request) {
	let body: unknown;
	try {
		body = await request.json();
	} catch {
		return revalidateFailure(400, "invalid_body", "The revalidate request could not be read.");
	}

	if (!isRecord(body)) {
		return revalidateFailure(400, "invalid_body", "The revalidate request could not be read.");
	}

	for (const key of Object.keys(body)) {
		if (!FIELDS.has(key)) {
			return revalidateFailure(400, "invalid_body", "The revalidate request has an unknown field.");
		}
	}

	const expected = process.env.REVALIDATE_SECRET?.trim() ?? "";
	const given = typeof body.secret === "string" ? body.secret.trim() : "";
	if (!expected || !given || !secretsMatch(given, expected)) {
		return revalidateFailure(401, "forbidden", "The revalidate request was not accepted.");
	}

	if (body.action !== "update" && body.action !== "delete") {
		return revalidateFailure(400, "invalid_action", "The revalidate action is not valid.");
	}

	let path: string | null = null;
	if (body.path !== undefined) {
		if (typeof body.path !== "string") {
			return revalidateFailure(400, "invalid_path", "The path is not a page path.");
		}

		try {
			path = assertPublicPath(body.path);
		} catch {
			return revalidateFailure(400, "invalid_path", "The path is not a page path.");
		}
	}

	let tag: (typeof INDEX_TAGS)[number] | null = null;
	if (body.tag !== undefined) {
		if (typeof body.tag !== "string" || !isIndexTag(body.tag)) {
			return revalidateFailure(400, "invalid_tag", "The revalidate tag is not valid.");
		}

		tag = body.tag;
	}

	if (!path && !tag) {
		return revalidateFailure(400, "invalid_body", "The revalidate request needs a path or a tag.");
	}

	const profile = { expire: 0 } as const;
	for (const indexTag of INDEX_TAGS) {
		revalidateTag(indexTag, profile);
	}

	if (tag) {
		revalidateTag(tag, profile);
	}

	if (path) {
		revalidateTag("page", profile);
		revalidateTag(path, profile);
		revalidatePath(path);
	}

	revalidatePath("/", "layout");
	revalidatePath("/sitemap.xml");
	revalidatePath("/robots.txt");
	revalidatePath("/llms.txt");

	return Response.json(
		{ revalidated: true },
		{ headers: { "Cache-Control": "no-store" } },
	);
}

function secretsMatch(given: string, expected: string): boolean {
	const givenHash = createHash("sha256").update(given).digest();
	const expectedHash = createHash("sha256").update(expected).digest();
	return timingSafeEqual(givenHash, expectedHash);
}

function isIndexTag(value: string): value is (typeof INDEX_TAGS)[number] {
	return INDEX_TAGS.some((tag) => tag === value);
}

function isRecord(value: unknown): value is Record<string, unknown> {
	return typeof value === "object" && value !== null && !Array.isArray(value);
}

function revalidateFailure(status: number, code: string, message: string) {
	return Response.json(
		{ code, message },
		{ status, headers: { "Cache-Control": "no-store" } },
	);
}
