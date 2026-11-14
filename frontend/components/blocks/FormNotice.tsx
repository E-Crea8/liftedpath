"use client";

export type NoticeLevel = "success" | "warning" | "error";

export function FormNotice({
	level,
	message,
	onClose,
}: {
	level: NoticeLevel;
	message: string;
	onClose: () => void;
}) {
	if (!message) {
		return null;
	}

	return (
		<div className={`liftedpath-notice liftedpath-notice--${level}`} role={level === "error" ? "alert" : "status"}>
			<p>{message}</p>
			<button aria-label="Close notice" className="liftedpath-notice__close" onClick={onClose} type="button">
				<span aria-hidden="true">×</span>
			</button>
		</div>
	);
}

export async function readFormResult(response: Response): Promise<{
	ok: boolean;
	level: NoticeLevel;
	message: string;
	checkoutUrl: string;
	accessCode: string;
	reference: string;
}> {
	const text = await response.text();
	let payload: {
		message?: unknown;
		level?: unknown;
		checkoutUrl?: unknown;
		accessCode?: unknown;
		reference?: unknown;
	} = {};
	let parsed = text.trim() === "";

	if (!parsed) {
		try {
			const value = JSON.parse(text) as unknown;
			if (value && typeof value === "object" && !Array.isArray(value)) {
				payload = value as {
					message?: unknown;
					level?: unknown;
					checkoutUrl?: unknown;
					accessCode?: unknown;
					reference?: unknown;
				};
			}
			parsed = true;
		} catch {
			parsed = false;
		}
	}

	if (!parsed || !response.ok) {
		const message = typeof payload.message === "string" && payload.message ? payload.message : "The form could not be completed. Please try again.";
		const level = payload.level === "warning" ? "warning" : "error";
		return { ok: false, level, message, checkoutUrl: "", accessCode: "", reference: "" };
	}

	return {
		ok: true,
		level: "success",
		message: typeof payload.message === "string" ? payload.message : "",
		checkoutUrl: typeof payload.checkoutUrl === "string" ? payload.checkoutUrl : "",
		accessCode: typeof payload.accessCode === "string" ? payload.accessCode : "",
		reference: typeof payload.reference === "string" ? payload.reference : "",
	};
}
