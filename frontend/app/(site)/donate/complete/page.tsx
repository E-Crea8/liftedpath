import { DonateReturn } from "@/components/donate/DonateReturn";
import { confirmPayment, donationWording, getFormConfig, paymentStatus, type DonateNotices } from "@/lib/forms";

export const instant = false;

export const metadata = {
	title: "Donation confirmation",
	description: "Confirmation for your donation to LiftedPath Empowerment Foundation.",
};

const REFERENCE = /^lp_[a-f0-9]{24}$/;

export default async function DonateCompletePage({
	searchParams,
}: {
	searchParams: Promise<Record<string, string | string[] | undefined>>;
}) {
	const params = await searchParams;
	const reference = first(params.reference) || first(params.tx_ref) || first(params.trxref);
	const sessionId = first(params.session_id);
	const [outcome, form] = await Promise.all([readStatus(reference, sessionId), getFormConfig()]);
	const copy = lines(outcome.status, form.donate);
	const status = normalizeStatus(outcome.status);

	return (
		<DonateReturn
			status={status}
			heading={donationWording(outcome.heading || copy.heading)}
			detail={donationWording(outcome.detail || copy.detail)}
			reference={REFERENCE.test(reference) ? reference : ""}
		/>
	);
}

async function readStatus(reference: string, sessionId: string) {
	if (!REFERENCE.test(reference)) {
		return { status: "unknown", heading: "", detail: "" };
	}

	try {
		await confirmPayment(reference, sessionId);
	} catch {
		// Status below still reports what WordPress stored.
	}

	try {
		return await paymentStatus(reference);
	} catch {
		return { status: "unknown", heading: "", detail: "" };
	}
}

function first(value: string | string[] | undefined) {
	const item = Array.isArray(value) ? value[0] : value;
	return typeof item === "string" ? item : "";
}

function normalizeStatus(status: string): "paid" | "pending" | "failed" | "unknown" {
	if (status === "paid" || status === "pending" || status === "failed") {
		return status;
	}
	return "unknown";
}

function lines(status: string, notices: DonateNotices) {
	if (status === "paid") {
		return { heading: notices.paidHeading, detail: notices.paidDetail };
	}
	if (status === "pending") {
		return { heading: notices.pendingHeading, detail: notices.pendingDetail };
	}
	if (status === "failed") {
		return { heading: notices.failedHeading, detail: notices.failedDetail };
	}
	return { heading: notices.unknownHeading, detail: notices.unknownDetail };
}
