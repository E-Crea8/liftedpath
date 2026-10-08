import { confirmPayment, formFailure } from "@/lib/forms";

export async function POST(request: Request) {
	let body: unknown;
	try {
		body = await request.json();
	} catch {
		return Response.json({ message: "The payment could not be read." }, { status: 400 });
	}

	const record = typeof body === "object" && body !== null ? (body as { reference?: unknown; sessionId?: unknown }) : {};
	const reference = typeof record.reference === "string" ? record.reference : "";
	const sessionId = typeof record.sessionId === "string" ? record.sessionId : "";

	try {
		await confirmPayment(reference, sessionId);
		return Response.json({ ok: true });
	} catch (error) {
		return formFailure(error, "The payment could not be confirmed.");
	}
}
