import { formFailure, forwardForm, visitorAddress, visitorLocation } from "@/lib/forms";

export async function POST(request: Request) {
	let body: unknown;
	try {
		body = await request.json();
	} catch {
		return Response.json({ message: "The message could not be read." }, { status: 400 });
	}

	try {
		const payload = await forwardForm("contact", body, visitorAddress(request), visitorLocation(request));
		return Response.json(payload);
	} catch (error) {
		return formFailure(error, "The message could not be sent.");
	}
}
