"use client";

import { useEffect } from "react";
import { SiteHold } from "@/components/layout/SiteHold";

export default function SiteError({ error, reset }: { error: Error & { digest?: string }; reset: () => void }) {
	useEffect(() => {
		const message = [error.message, error.digest ? `Reference ${error.digest}` : ""].filter(Boolean).join(" ");
		void fetch("/api/alert", {
			method: "POST",
			headers: { "Content-Type": "application/json" },
			body: JSON.stringify({
				code: "render",
				path: window.location.pathname,
				message,
				kept: false,
			}),
		});
	}, [error]);

	return (
		<SiteHold
			action={
				<p>
					<button className="liftedpath-button" onClick={() => reset()} type="button">
						Try again
					</button>
				</p>
			}
		/>
	);
}
