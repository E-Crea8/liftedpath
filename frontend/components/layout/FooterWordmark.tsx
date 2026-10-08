"use client";

import { useEffect, useRef } from "react";

export function FooterWordmark({ text }: { text: string }) {
	const ref = useRef<HTMLParagraphElement>(null);

	useEffect(() => {
		const node = ref.current;
		if (!node) {
			return;
		}

		let frame = 0;
		const fit = () => {
			cancelAnimationFrame(frame);
			frame = requestAnimationFrame(() => {
				const target = document.documentElement.clientWidth;
				node.style.width = "max-content";
				node.style.fontSize = "100px";
				const natural = node.getBoundingClientRect().width;
				node.style.width = "";
				if (natural > 0) {
					node.style.fontSize = `${(target / natural) * 100}px`;
				}
			});
		};

		fit();
		window.addEventListener("resize", fit);
		document.fonts?.ready.then(fit).catch(() => undefined);

		return () => {
			window.removeEventListener("resize", fit);
			cancelAnimationFrame(frame);
		};
	}, [text]);

	return (
		<p className="footer-wordmark" ref={ref}>
			{text}
		</p>
	);
}
