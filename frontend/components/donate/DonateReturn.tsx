import Link from "next/link";
import { ArrowIcon } from "@/components/blocks/icons";

type DonateReturnProps = {
	status: "paid" | "pending" | "failed" | "unknown";
	heading: string;
	detail: string;
	reference?: string;
};

const MARK: Record<DonateReturnProps["status"], { label: string; tone: string }> = {
	paid: { label: "Confirmed", tone: "is-paid" },
	pending: { label: "Confirming", tone: "is-pending" },
	failed: { label: "Not completed", tone: "is-failed" },
	unknown: { label: "Donation status", tone: "is-unknown" },
};

export function DonateReturn({ status, heading, detail, reference = "" }: DonateReturnProps) {
	const mark = MARK[status];

	return (
		<section className={`no-hero donate-return donate-return--${status}`}>
			<div className="column donate-return__layout">
				<div className={`donate-return__mark ${mark.tone}`} aria-hidden="true">
					<span className="donate-return__badge">{mark.label}</span>
					<span className="donate-return__glyph">{glyph(status)}</span>
					<span className="donate-return__trail">
						<span />
						<span />
						<span />
					</span>
				</div>

				<div className="donate-return__copy">
					<p className="liftedpath-eyebrow">{eyebrow(status)}</p>
					<h1>{heading}</h1>
					<p className="donate-return__lead">{detail}</p>

					{reference ? (
						<p className="donate-return__reference">
							<span>Reference</span>
							<code>{reference}</code>
						</p>
					) : null}

					<div className="donate-return__actions">
						{status === "paid" ? (
							<>
								<Link className="liftedpath-button" href="/impact">
									See our impact
									<ArrowIcon />
								</Link>
								<Link className="donate-return__quiet" href="/">
									Back to home
								</Link>
							</>
						) : null}

						{status === "pending" ? (
							<>
								<Link className="liftedpath-button" href={reference ? `/donate/complete?reference=${encodeURIComponent(reference)}` : "/donate/complete"}>
									Check again
									<ArrowIcon />
								</Link>
								<Link className="donate-return__quiet" href="/donate">
									Back to giving
								</Link>
							</>
						) : null}

						{status === "failed" || status === "unknown" ? (
							<>
								<Link className="liftedpath-button" href="/donate">
									Try again
									<ArrowIcon />
								</Link>
								<Link className="donate-return__quiet" href="/contact">
									Contact us
								</Link>
							</>
						) : null}
					</div>
				</div>
			</div>
		</section>
	);
}

function eyebrow(status: DonateReturnProps["status"]) {
	if (status === "paid") {
		return "With our thanks";
	}
	if (status === "pending") {
		return "Still confirming";
	}
	if (status === "failed") {
		return "Not completed";
	}
	return "Donation status";
}

function glyph(status: DonateReturnProps["status"]) {
	if (status === "paid") {
		return "✓";
	}
	if (status === "pending") {
		return "…";
	}
	if (status === "failed") {
		return "!";
	}
	return "?";
}
