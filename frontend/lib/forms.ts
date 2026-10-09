export interface FormConfig {
	provider: "" | "stripe" | "paystack" | "flutterwave";
	providerLabel: string;
	paymentsConfigured: boolean;
}

const EMPTY_CONFIG: FormConfig = {
	provider: "",
	providerLabel: "",
	paymentsConfigured: false,
};

export async function getFormConfig(): Promise<FormConfig> {
	try {
		const payload = await readJson("forms", { method: "GET" }, false);
		if (!isRecord(payload)) {
			return EMPTY_CONFIG;
		}
		const provider = payload.provider;
		const providerLabel = payload.providerLabel;
		return {
			provider: provider === "stripe" || provider === "paystack" || provider === "flutterwave" ? provider : "",
			providerLabel: typeof providerLabel === "string" ? providerLabel : "",
			paymentsConfigured: payload.paymentsConfigured === true,
		};
	} catch {
		return EMPTY_CONFIG;
	}
}

export async function forwardForm(path: string, body: unknown, visitor: string) {
	const payload = await readJson(path, {
		method: "POST",
		headers: {
			"Content-Type": "application/json",
			"X-LiftedPath-Visitor": visitor.slice(0, 200),
		},
		body: JSON.stringify(body),
	}, true);

	return payload;
}

export async function confirmPayment(reference: string, sessionId: string) {
	await readJson(
		"payments/confirm",
		{
			method: "POST",
			headers: { "Content-Type": "application/json" },
			body: JSON.stringify({ reference, sessionId }),
		},
		true,
	);
}

export async function paymentStatus(reference: string) {
	const payload = await readJson(`payments/status?reference=${encodeURIComponent(reference)}`, { method: "GET" }, false);
	if (isRecord(payload) && (payload.status === "paid" || payload.status === "pending" || payload.status === "failed")) {
		return payload.status;
	}
	return "unknown";
}

export function visitorAddress(request: Request) {
	const forwarded = request.headers.get("x-forwarded-for") ?? "";
	const hop = forwarded.split(",")[0]?.trim() ?? "";
	return hop || "local";
}

export function formFailure(error: unknown, fallback: string) {
	const status = typeof error === "object" && error !== null && "status" in error && typeof error.status === "number" ? error.status : 500;
	const message = error instanceof Error && error.message ? error.message : fallback;
	const level = status === 400 || status === 409 || status === 429 ? "warning" : "error";
	return Response.json({ message, level }, { status });
}

async function readJson(path: string, init: RequestInit, secret: boolean): Promise<unknown> {
	const base = process.env.WORDPRESS_API_URL?.replace(/\/$/, "");
	if (!base) {
		throw new Error("WORDPRESS_API_URL is not set.");
	}

	const headers = new Headers(init.headers);
	headers.set("Accept", "application/json");
	if (secret) {
		headers.set("X-LiftedPath-Form-Secret", process.env.FORM_SECRET ?? "");
	}

	const response = await fetch(`${base}/liftedpath/v1/${path}`, {
		...init,
		headers,
		cache: "no-store",
	});
	const text = await response.text();
	let payload: unknown = {};
	const body = text.trim();
	if (body) {
		try {
			payload = JSON.parse(body) as unknown;
		} catch {
			const error = new Error("The form could not be completed. Please try again.") as Error & { status: number };
			error.status = response.ok ? 502 : response.status;
			throw error;
		}
	}
	if (!response.ok) {
		const message = isRecord(payload) && typeof payload.message === "string" ? payload.message : "WordPress could not complete the form.";
		const error = new Error(message) as Error & { status: number };
		error.status = response.status;
		throw error;
	}
	return payload;
}

function isRecord(value: unknown): value is Record<string, unknown> {
	return typeof value === "object" && value !== null && !Array.isArray(value);
}
