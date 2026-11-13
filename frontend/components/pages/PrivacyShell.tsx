"use client";

import { useEffect, useRef, useState, type ReactNode } from "react";
import { ArrowIcon } from "@/components/blocks/icons";

type TocItem = {
	id: string;
	label: string;
};

export function PrivacyShell({ children }: { children: ReactNode }) {
	const root = useRef<HTMLDivElement>(null);
	const [toc, setToc] = useState<TocItem[]>([]);

	useEffect(() => {
		const scope = root.current;
		if (!scope) {
			return;
		}

		const headings = Array.from(scope.querySelectorAll<HTMLElement>(".liftedpath-privacy__content h2"));
		const items = headings.map((heading, index) => {
			const id = heading.id || `privacy-section-${index + 1}`;
			heading.id = id;
			return {
				id,
				label: (heading.textContent || "").replace(/\s+/g, " ").trim(),
			};
		}).filter((item) => item.label !== "");

		setToc(items);
	}, []);

	return (
		<div className="liftedpath-section liftedpath-privacy" ref={root}>
			<div className="liftedpath-section__inner liftedpath-privacy__layout">
				<aside aria-label="Privacy Policy sections" className="liftedpath-privacy__sidebar">
					<nav className="liftedpath-privacy__nav">
						<h2>Privacy Policy</h2>
						<div>
							{toc.map((item) => (
								<a className="liftedpath-privacy__nav-link" href={`#${item.id}`} key={item.id}>
									<span>
										<strong>{item.label}</strong>
									</span>
									<ArrowIcon />
								</a>
							))}
						</div>
					</nav>
				</aside>
				<div className="liftedpath-privacy__content">{children}</div>
			</div>
		</div>
	);
}
