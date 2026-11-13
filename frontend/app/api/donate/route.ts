import { formFailure, forwardForm, visitorAddress, visitorLocation } from "@/lib/forms";

export async function POST(request: Request) {
	let body: unknown;
	try {
		body = await request.json();
	} catch {
		return Response.json({ message: "The donation could not be read." }, { status: 400 });
	}

	try {
		const payload = await forwardForm("donate", body, visitorAddress(request), visitorLocation(request));
		return Response.json(payload);
	} catch (error) {
		return formFailure(error, "The donation could not be started.");
	}
}
