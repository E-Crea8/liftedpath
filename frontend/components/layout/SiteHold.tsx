import type { ReactNode } from "react";

export function SiteHold({ action }: { action?: ReactNode }) {
	return (
		<section className="missing-page">
			<div className="column">
				<p className="eyebrow">Please check back</p>
				<h1>This page is being kept online.</h1>
				<p>The latest content is unavailable right now. The site is trying to alert the administrator. This needs to be resolved.</p>
				<p>
					<a href="/">Back to the home page</a>
				</p>
				{action}
			</div>
		</section>
	);
}
