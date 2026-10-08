import Link from "next/link";
import { confirmPayment, paymentStatus } from "@/lib/forms";

export const instant = false;

export const metadata = {
	title: "Gift confirmation",
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
	const status = await readStatus(reference, sessionId);

	return (
		<section className="liftedpath-section liftedpath-return">
			<div className="liftedpath-section__inner">
				<p className="liftedpath-eyebrow">Your gift</p>
				<h1>{heading(status)}</h1>
				<p className="liftedpath-lead">{detail(status)}</p>
				<p>
					<Link className="liftedpath-button" href="/donate">
						Back to giving
					</Link>
				</p>
			</div>
		</section>
	);
}

async function readStatus(reference: string, sessionId: string) {
	if (!REFERENCE.test(reference)) {
		return "unknown";
	}

	try {
		await confirmPayment(reference, sessionId);
	} catch {
		// The status request below still reports what WordPress stored.
	}

	try {
		return await paymentStatus(reference);
	} catch {
		return "unknown";
	}
}

function first(value: string | string[] | undefined) {
	const item = Array.isArray(value) ? value[0] : value;
	return typeof item === "string" ? item : "";
}

function heading(status: string) {
	if (status === "paid") {
		return "Thank you. This gift is confirmed.";
	}
	if (status === "pending") {
		return "This gift is still being confirmed.";
	}
	if (status === "failed") {
		return "This gift was not completed.";
	}
	return "This return link does not match a gift.";
}

function detail(status: string) {
	if (status === "paid") {
		return "The foundation has the confirmation from the payment provider.";
	}
	if (status === "pending") {
		return "The payment provider has not confirmed it yet. You can leave this page.";
	}
	if (status === "failed") {
		return "No gift was recorded as paid.";
	}
	return "Start again from the giving page if you still want to give.";
}
