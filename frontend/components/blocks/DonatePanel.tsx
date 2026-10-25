"use client";

import { useState, type FormEvent } from "react";
import { useSearchParams } from "next/navigation";
import { FormNotice, readFormResult, type NoticeLevel } from "@/components/blocks/FormNotice";
import { ArrowIcon, BlockIcon } from "@/components/blocks/icons";
import { BlockPhoto } from "@/components/blocks/parts";
import { imageId, numeric, rows, text } from "@/lib/blocks/fields";
import type { DonateNotices } from "@/lib/forms";
import type { Block, FeaturedImage } from "@/lib/wp/types";

const DESIGNATIONS = [
	{ value: "greatest-need", label: "Wherever the need is greatest" },
	{ value: "education", label: "Education Sponsorship / Scholarship" },
	{ value: "skills", label: "Skills Acquisition & Vocational Training" },
	{ value: "mentorship", label: "Mentorship & Coaching" },
	{ value: "orphanage", label: "Orphanage Visitations" },
	{ value: "season", label: "Share the Joy of the Season" },
] as const;

const AMOUNTS = [
	{ amount: 15000, title: "School essentials" },
	{ amount: 50000, title: "Education support" },
	{ amount: 120000, title: "Skills & tools" },
	{ amount: 300000, title: "Family care" },
];

type Currency = "NGN" | "USD" | "GBP";

export function DonatePanel({
	block,
	images,
	notices,
	paymentsConfigured,
	providerLabel,
}: {
	block: Block;
	images: ReadonlyMap<number, FeaturedImage>;
	notices?: DonateNotices;
	paymentsConfigured: boolean;
	providerLabel: string;
}) {
	const params = useSearchParams();
	const eyebrow = text(block.attributes.eyebrow);
	const heading = text(block.attributes.heading);
	const intro = text(block.attributes.intro);
	const oneOff = text(block.attributes.one_off_label) || "One-off";
	const monthly = text(block.attributes.monthly_label) || "Monthly";
	const buttonLabel = text(block.attributes.button_label) || "Arrange my gift";
	const note = text(block.attributes.note);
	const asideHeading = text(block.attributes.aside_heading);
	const asideText = text(block.attributes.aside_text);
	const photo = imageId(block.attributes.aside_image);
	const bankHeading = text(block.attributes.bank_heading);
	const bankNumber = text(block.attributes.bank_account_number);
	const bankAccountName = text(block.attributes.bank_account_name);
	const bankName = text(block.attributes.bank_name);
	const bankCurrency = text(block.attributes.bank_currency);
	const bankMobileOrder = text(block.attributes.bank_mobile_order);
	const usdRate = numeric(block.attributes.usd_rate) > 0 ? numeric(block.attributes.usd_rate) : 1500;
	const gbpRate = numeric(block.attributes.gbp_rate) > 0 ? numeric(block.attributes.gbp_rate) : 2000;
	const amounts = rows(block.attributes, "amounts")
		.map((row) => ({ amount: numeric(row.amount), title: text(row.title), detail: text(row.detail) }))
		.filter((item) => item.amount > 0 && item.title);
	const choices = amounts.length > 0 ? amounts : AMOUNTS.map((item) => ({ ...item, detail: "" }));
	const designations = rows(block.attributes, "designations")
		.map((row) => ({ value: text(row.designation), label: text(row.label) }))
		.filter((item) => DESIGNATIONS.some((choice) => choice.value === item.value) && item.label);
	const gifts = designations.length > 0 ? designations : DESIGNATIONS.map((item) => ({ ...item }));
	const [currency, setCurrency] = useState<Currency>("NGN");
	const [amountIndex, setAmountIndex] = useState(0);
	const [custom, setCustom] = useState("");
	const [sending, setSending] = useState(false);
	const [notice, setNotice] = useState<{ level: NoticeLevel; message: string } | null>(null);
	const cancelled = params.get("cancelled") === "1";
	const [showCancelled, setShowCancelled] = useState(cancelled);

	const showBank = Boolean(bankNumber || bankAccountName || bankName || bankCurrency);
	const bankBefore = showBank && bankMobileOrder !== "after";
	const showAside = photo !== null || Boolean(asideHeading || asideText);

	if (!heading && !eyebrow && !intro && !showAside && !showBank) {
		return null;
	}

	function chargeAmount() {
		const typed = Number(custom);
		if (custom.trim() !== "" && Number.isFinite(typed) && typed >= 1) {
			return Math.round(typed);
		}
		return converted(choices[amountIndex]?.amount ?? 0, currency, usdRate, gbpRate);
	}

	async function onSubmit(event: FormEvent<HTMLFormElement>) {
		event.preventDefault();
		const form = new FormData(event.currentTarget);
		const designation = String(form.get("designation") ?? "");
		const designationLabel = gifts.find((item) => item.value === designation)?.label ?? designation;
		setSending(true);
		setNotice(null);

		try {
			const response = await fetch("/api/donate", {
				method: "POST",
				headers: { "Content-Type": "application/json", Accept: "application/json" },
				body: JSON.stringify({
					fullName: String(form.get("fullName") ?? ""),
					email: String(form.get("email") ?? ""),
					frequency: String(form.get("frequency") ?? "one-off"),
					currency,
					amount: chargeAmount(),
					designation,
					designationLabel,
					website: String(form.get("website") ?? ""),
				}),
			});
			const result = await readFormResult(response);
			if (!result.ok) {
				setNotice({ level: result.level, message: result.message || notices?.error || "The gift could not be started." });
				return;
			}
			if (result.checkoutUrl) {
				window.location.assign(result.checkoutUrl);
				return;
			}
		} catch {
			setNotice({ level: "error", message: notices?.error || "The gift could not be started. Please try again." });
		} finally {
			setSending(false);
		}
	}

	return (
		<section className={bankBefore ? "liftedpath-section liftedpath-donate is-bank-before" : "liftedpath-section liftedpath-donate"}>
			<div className="liftedpath-section__inner liftedpath-donate__layout">
				<form className="liftedpath-donate__form" onSubmit={onSubmit}>
					{eyebrow ? <p className="liftedpath-eyebrow">{eyebrow}</p> : null}
					{heading ? <h2>{heading}</h2> : null}
					{intro ? <p className="liftedpath-lead">{intro}</p> : null}
					<h3>Frequency</h3>
					<div className="liftedpath-segment" role="radiogroup" aria-label="Frequency">
						<label>
							<input defaultChecked name="frequency" type="radio" value="one-off" />
							<span>{oneOff}</span>
						</label>
						<label>
							<input name="frequency" type="radio" value="monthly" />
							<span>{monthly}</span>
						</label>
					</div>
					<div className="liftedpath-donate__impact">
						<h3>Choose an impact</h3>
						<div className="liftedpath-segment" role="radiogroup" aria-label="Currency">
							{(["NGN", "USD", "GBP"] as const).map((code) => (
								<label key={code}>
									<input checked={currency === code} name="currency" onChange={() => setCurrency(code)} type="radio" value={code} />
									<span>
										{code} · {symbol(code)}
									</span>
								</label>
							))}
						</div>
					</div>
					<div className="liftedpath-donate__amounts">
						{choices.map((item, index) => (
							<label className="liftedpath-donate__choice" key={`${item.title}-${item.amount}`}>
								<input
									checked={custom.trim() === "" && amountIndex === index}
									name="amount"
									onChange={() => {
										setAmountIndex(index);
										setCustom("");
									}}
									type="radio"
									value={item.amount}
								/>
								<strong>{money(converted(item.amount, currency, usdRate, gbpRate), currency)}</strong>
								<span className="liftedpath-donate__choice-title">{item.title}</span>
							</label>
						))}
					</div>
					{choices[amountIndex] && custom.trim() === "" && choices[amountIndex].detail ? (
						<p className="liftedpath-donate__detail liftedpath-donate__detail--visible">
							<BlockIcon name={giftIcon(choices[amountIndex].title)} />
							<span>{choices[amountIndex].detail}</span>
						</p>
					) : null}
					<p className="liftedpath-donate__rate">
						{currency === "NGN"
							? "Select a gift amount or enter your own below."
							: `Indicative conversion: ${money(currency === "USD" ? usdRate : gbpRate, "NGN")} = ${money(1, currency)}. The amount you confirm is the amount charged.`}
					</p>
					<label className="liftedpath-field">
						Other amount ({currency})
						<input
							inputMode="numeric"
							min={1}
							name="amountCustom"
							onChange={(event) => setCustom(event.target.value)}
							step={1}
							type="number"
							value={custom}
						/>
					</label>
					<label className="liftedpath-field">
						Full name
						<input autoComplete="name" name="fullName" required type="text" />
					</label>
					<label className="liftedpath-field">
						Email
						<input autoComplete="email" name="email" required type="email" />
					</label>
					<label className="liftedpath-field">
						Direct my gift to
						<select name="designation">
							{gifts.map((item) => (
								<option key={item.value} value={item.value}>
									{item.label}
								</option>
							))}
						</select>
					</label>
					<label aria-hidden="true" className="liftedpath-honeypot">
						Website
						<input aria-hidden="true" autoComplete="off" name="website" tabIndex={-1} type="text" />
					</label>
					<button className="liftedpath-button" disabled={sending} type="submit">
						{sending ? "Opening payment" : buttonLabel}
						<ArrowIcon />
					</button>
					{note ? <p className="liftedpath-form-note">{note}</p> : null}
					<p className="liftedpath-form-note">
						{paymentsConfigured
							? notices?.checkoutNote || `You finish this gift on ${providerLabel}. Card details stay with ${providerLabel}.`
							: notices?.unconfigured || "Payments are not connected yet. An administrator can choose Stripe, Paystack, or Flutterwave in Form settings."}
					</p>
					{showCancelled ? (
						<FormNotice
							level="warning"
							message={notices?.cancelled || "The payment page was closed before the gift was confirmed."}
							onClose={() => setShowCancelled(false)}
						/>
					) : null}
					{notice ? <FormNotice level={notice.level} message={notice.message} onClose={() => setNotice(null)} /> : null}
				</form>
				{showBank || showAside ? (
					<aside className="liftedpath-donate__rail">
						{showBank ? (
							<div className="liftedpath-donate__bank">
								{bankHeading ? <h3>{bankHeading}</h3> : null}
								<dl>
									{bankNumber ? (
										<div>
											<dt>Account number</dt>
											<dd>{bankNumber}</dd>
										</div>
									) : null}
									{bankAccountName ? (
										<div>
											<dt>Account name</dt>
											<dd>{bankAccountName}</dd>
										</div>
									) : null}
									{bankName ? (
										<div>
											<dt>Bank</dt>
											<dd>{bankName}</dd>
										</div>
									) : null}
									{bankCurrency ? (
										<div>
											<dt>Currency</dt>
											<dd>{bankCurrency}</dd>
										</div>
									) : null}
								</dl>
							</div>
						) : null}
						{showAside ? (
							<div className="liftedpath-donate__aside">
								<BlockPhoto className="liftedpath-donate__photo" id={photo} images={images} />
								<div>
									<BlockIcon name="graduation-cap" />
									{asideHeading ? <strong>{asideHeading}</strong> : null}
									{asideText ? <p>{asideText}</p> : null}
								</div>
							</div>
						) : null}
					</aside>
				) : null}
			</div>
		</section>
	);
}

function giftIcon(title: string): "graduation-cap" | "book-open" | "wrench" | "users" {
	const label = title.toLowerCase();
	if (label.includes("skill") || label.includes("tool")) {
		return "wrench";
	}
	if (label.includes("family") || label.includes("care") || label.includes("food")) {
		return "users";
	}
	if (label.includes("school") || label.includes("essential") || label.includes("material")) {
		return "book-open";
	}
	return "graduation-cap";
}

function converted(naira: number, currency: Currency, usdRate: number, gbpRate: number) {
	if (currency === "USD") {
		return Math.round(naira / usdRate);
	}
	if (currency === "GBP") {
		return Math.round(naira / gbpRate);
	}
	return Math.round(naira);
}

function symbol(currency: Currency) {
	if (currency === "USD") {
		return "$";
	}
	if (currency === "GBP") {
		return "£";
	}
	return "₦";
}

function money(amount: number, currency: Currency) {
	return `${symbol(currency)}${amount.toLocaleString("en-US")}`;
}
