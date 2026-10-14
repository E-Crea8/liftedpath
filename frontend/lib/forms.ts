import { wordpressApiUrl } from "@/lib/wp/api-url";
import { reportReadFailure } from "@/lib/wp/safety";

export interface ContactNotices {
	success: string;
	error: string;
}

export interface DonateNotices {
	cancelled: string;
	error: string;
	unconfigured: string;
	checkoutNote: string;
	paidHeading: string;
	paidDetail: string;
	pendingHeading: string;
	pendingDetail: string;
	failedHeading: string;
	failedDetail: string;
	unknownHeading: string;
	unknownDetail: string;
}

export interface FormConfig {
	provider: "" | "stripe" | "paystack" | "flutterwave";
	providerLabel: string;
	paymentsConfigured: boolean;
	contact: ContactNotices;
	donate: DonateNotices;
}

const CONTACT_NOTICES: ContactNotices = {
	success: "Thank you. Your message is with the foundation.",
	error: "The message could not be sent. Please try again.",
};

const DONATE_NOTICES: DonateNotices = {
	cancelled: "The payment page was closed before the gift was confirmed.",
	error: "The gift could not be started. Please try again.",
	unconfigured: "Payments are not connected yet. An administrator can choose Stripe, Paystack, or Flutterwave in Form settings.",
	checkoutNote: "",
	paidHeading: "Thank you. This gift is confirmed.",
	paidDetail: "The foundation has the confirmation from the payment provider.",
	pendingHeading: "This gift is still being confirmed.",
	pendingDetail: "The payment provider has not confirmed it yet. You can leave this page.",
	failedHeading: "This gift was not completed.",
	failedDetail: "No gift was recorded as paid.",
	unknownHeading: "This return link does not match a gift.",
	unknownDetail: "Start again from the giving page if you still want to give.",
};

const EMPTY_CONFIG: FormConfig = {
	provider: "",
	providerLabel: "",
	paymentsConfigured: false,
	contact: CONTACT_NOTICES,
	donate: DONATE_NOTICES,
};

export async function getFormConfig(): Promise<FormConfig> {
	try {
		const payload = await readJson("forms", { method: "GET" }, false);
		if (!isRecord(payload)) {
			reportReadFailure(new Error("The forms payload is not an object."), "forms", false);
			return EMPTY_CONFIG;
		}
		const provider = payload.provider;
		const providerLabel = payload.providerLabel;
		const notices = isRecord(payload.notices) ? payload.notices : {};
		const contact = isRecord(notices.contact) ? notices.contact : {};
		const donate = isRecord(notices.donate) ? notices.donate : {};
		return {
			provider: provider === "stripe" || provider === "paystack" || provider === "flutterwave" ? provider : "",
			providerLabel: typeof providerLabel === "string" ? providerLabel : "",
			paymentsConfigured: payload.paymentsConfigured === true,
			contact: {
				success: noticeText(contact.success, CONTACT_NOTICES.success),
				error: noticeText(contact.error, CONTACT_NOTICES.error),
			},
			donate: {
				cancelled: noticeText(donate.cancelled, DONATE_NOTICES.cancelled),
				error: noticeText(donate.error, DONATE_NOTICES.error),
				unconfigured: noticeText(donate.unconfigured, DONATE_NOTICES.unconfigured),
				checkoutNote: noticeText(donate.checkoutNote, DONATE_NOTICES.checkoutNote),
				paidHeading: noticeText(donate.paidHeading, DONATE_NOTICES.paidHeading),
				paidDetail: noticeText(donate.paidDetail, DONATE_NOTICES.paidDetail),
				pendingHeading: noticeText(donate.pendingHeading, DONATE_NOTICES.pendingHeading),
				pendingDetail: noticeText(donate.pendingDetail, DONATE_NOTICES.pendingDetail),
				failedHeading: noticeText(donate.failedHeading, DONATE_NOTICES.failedHeading),
				failedDetail: noticeText(donate.failedDetail, DONATE_NOTICES.failedDetail),
				unknownHeading: noticeText(donate.unknownHeading, DONATE_NOTICES.unknownHeading),
				unknownDetail: noticeText(donate.unknownDetail, DONATE_NOTICES.unknownDetail),
			},
		};
	} catch (error) {
		reportReadFailure(error, "forms", false);
		return EMPTY_CONFIG;
	}
}

export async function forwardForm(path: string, body: unknown, visitor: string, location = "") {
	const headers: Record<string, string> = {
		"Content-Type": "application/json",
		"X-LiftedPath-Visitor": visitor.slice(0, 200),
	};
	if (location.trim()) {
		headers["X-LiftedPath-Location"] = location.trim().slice(0, 180);
	}

	const payload = await readJson(path, {
		method: "POST",
		headers,
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

export async function paymentStatus(reference: string): Promise<{ status: string; heading: string; detail: string }> {
	const payload = await readJson(`payments/status?reference=${encodeURIComponent(reference)}`, { method: "GET" }, false);
	const status = isRecord(payload) && (payload.status === "paid" || payload.status === "pending" || payload.status === "failed") ? payload.status : "unknown";
	return {
		status,
		heading: isRecord(payload) && typeof payload.heading === "string" ? payload.heading : "",
		detail: isRecord(payload) && typeof payload.detail === "string" ? payload.detail : "",
	};
}

function noticeText(value: unknown, fallback: string) {
	return typeof value === "string" && value.trim() ? value : fallback;
}

export function visitorAddress(request: Request) {
	const forwarded = request.headers.get("x-forwarded-for") ?? "";
	const hop = forwarded.split(",")[0]?.trim() ?? "";
	return hop || request.headers.get("x-real-ip")?.trim() || "local";
}

export function visitorLocation(request: Request) {
	const parts = ["x-vercel-ip-city", "x-vercel-ip-country-region", "x-vercel-ip-country"]
		.map((name) => headerText(request, name))
		.filter((part, index, all) => part !== "" && all.indexOf(part) === index);
	return parts.join(", ").slice(0, 180);
}

function headerText(request: Request, name: string) {
	const value = request.headers.get(name)?.trim() ?? "";
	if (!value) {
		return "";
	}
	try {
		return decodeURIComponent(value);
	} catch {
		return value;
	}
}

export function formFailure(error: unknown, fallback: string) {
	const status = typeof error === "object" && error !== null && "status" in error && typeof error.status === "number" ? error.status : 500;
	const message = error instanceof Error && error.message ? error.message : fallback;
	const level = status === 400 || status === 409 || status === 429 ? "warning" : "error";
	return Response.json({ message, level }, { status });
}

async function readJson(path: string, init: RequestInit, secret: boolean): Promise<unknown> {
	const base = wordpressApiUrl();

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
