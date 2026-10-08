"use client";

import { useState, type FormEvent } from "react";
import { ArrowIcon, BlockIcon } from "@/components/blocks/icons";
import { rows, telHref, text } from "@/lib/blocks/fields";
import type { Block } from "@/lib/wp/types";

const REASONS = [
	{ value: "volunteer", label: "Volunteer my time" },
	{ value: "sponsor", label: "Sponsor a child or program" },
	{ value: "partner", label: "Partner as a school or organisation" },
	{ value: "in-kind", label: "Make an in-kind donation" },
	{ value: "other", label: "Something else" },
] as const;

export function ContactForm({
	block,
	phone,
	email,
	address,
}: {
	block: Block;
	phone: string;
	email: string;
	address: string;
}) {
	const eyebrow = text(block.attributes.eyebrow);
	const heading = text(block.attributes.heading);
	const intro = text(block.attributes.intro);
	const formEyebrow = text(block.attributes.form_eyebrow);
	const formHeading = text(block.attributes.form_heading);
	const buttonLabel = text(block.attributes.button_label) || "Send message";
	const note = text(block.attributes.note);
	const reasons = rows(block.attributes, "reasons")
		.map((row) => ({ value: text(row.reason), label: text(row.label) }))
		.filter((reason) => REASONS.some((item) => item.value === reason.value) && reason.label);
	const choices = reasons.length > 0 ? reasons : REASONS.map((item) => ({ ...item }));
	const [status, setStatus] = useState<"idle" | "sending" | "sent" | "error">("idle");
	const [message, setMessage] = useState("");

	if (!heading && !eyebrow && !intro && !formHeading && !formEyebrow) {
		return null;
	}

	async function onSubmit(event: FormEvent<HTMLFormElement>) {
		event.preventDefault();
		const formEl = event.currentTarget;
		const form = new FormData(formEl);
		const reason = String(form.get("reason") ?? "");
		const reasonLabel = choices.find((item) => item.value === reason)?.label ?? reason;
		setStatus("sending");
		setMessage("");

		try {
			const response = await fetch("/api/contact", {
				method: "POST",
				headers: { "Content-Type": "application/json", Accept: "application/json" },
				body: JSON.stringify({
					fullName: String(form.get("fullName") ?? ""),
					email: String(form.get("email") ?? ""),
					reason,
					reasonLabel,
					message: String(form.get("message") ?? ""),
					website: String(form.get("website") ?? ""),
				}),
			});
			const payload = (await response.json()) as { message?: string };
			if (!response.ok) {
				setStatus("error");
				setMessage(payload.message || "The message could not be sent.");
				return;
			}
			setStatus("sent");
			setMessage("Thank you. Your message is with the foundation.");
			formEl.reset();
		} catch {
			setStatus("error");
			setMessage("The message could not be sent.");
		}
	}

	return (
		<section className="liftedpath-section liftedpath-contact">
			<div className="liftedpath-section__inner liftedpath-contact__layout">
				<div>
					{eyebrow ? <p className="liftedpath-eyebrow">{eyebrow}</p> : null}
					{heading ? <h2>{heading}</h2> : null}
					{intro ? <p className="liftedpath-lead">{intro}</p> : null}
					{phone ? (
						<a className="liftedpath-contact__row" href={telHref(phone)}>
							<BlockIcon name="phone" />
							<span>
								<small>Phone</small>
								{phone}
							</span>
						</a>
					) : null}
					{email ? (
						<a className="liftedpath-contact__row" href={`mailto:${email}`}>
							<BlockIcon name="mail" />
							<span>
								<small>Email</small>
								{email}
							</span>
						</a>
					) : null}
					{address ? (
						<div className="liftedpath-contact__row">
							<BlockIcon name="map-pin" />
							<span>
								<small>Office</small>
								{address}
							</span>
						</div>
					) : null}
				</div>
				<form className="liftedpath-contact__panel" onSubmit={onSubmit}>
					{formEyebrow ? <p className="liftedpath-eyebrow">{formEyebrow}</p> : null}
					{formHeading ? <h2>{formHeading}</h2> : null}
					<label className="liftedpath-field">
						Full name
						<input autoComplete="name" name="fullName" placeholder="Your full name" required type="text" />
					</label>
					<label className="liftedpath-field">
						Email
						<input autoComplete="email" name="email" placeholder="you@example.com" required type="email" />
					</label>
					<label className="liftedpath-field">
						I would like to
						<select name="reason">
							{choices.map((reason) => (
								<option key={reason.value} value={reason.value}>
									{reason.label}
								</option>
							))}
						</select>
					</label>
					<label className="liftedpath-field">
						Message
						<textarea name="message" placeholder="Tell us a little about how you would like to help" required rows={5} />
					</label>
					<label aria-hidden="true" className="liftedpath-honeypot">
						Website
						<input aria-hidden="true" autoComplete="off" name="website" tabIndex={-1} type="text" />
					</label>
					<button className="liftedpath-button" disabled={status === "sending"} type="submit">
						{status === "sending" ? "Sending" : buttonLabel}
						<ArrowIcon />
					</button>
					{note ? <p className="liftedpath-form-note">{note}</p> : null}
					{message ? (
						<p className={status === "error" ? "liftedpath-form-status liftedpath-form-status--error" : "liftedpath-form-status"} role={status === "error" ? "alert" : "status"}>
							{message}
						</p>
					) : null}
				</form>
			</div>
		</section>
	);
}
