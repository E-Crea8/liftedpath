"use client";

import { useEffect, useRef, useState } from "react";

interface Parts {
	grouped: boolean;
	prefix: string;
	suffix: string;
	target: number;
}

function partsOf(value: string): Parts | null {
	const match = value.trim().match(/^(\D*?)(\d[\d,]*)(.*)$/);
	if (!match) {
		return null;
	}

	const target = Number.parseInt(match[2].replace(/,/g, ""), 10);
	if (!Number.isFinite(target)) {
		return null;
	}

	return {
		grouped: match[2].includes(","),
		prefix: match[1],
		suffix: match[3],
		target,
	};
}

function format(parts: Parts, current: number): string {
	const number = parts.grouped ? current.toLocaleString("en-US") : String(current);
	return `${parts.prefix}${number}${parts.suffix}`;
}

export function CountFigure({ className, value }: { className?: string; value: string }) {
	const ref = useRef<HTMLElement>(null);
	const [display, setDisplay] = useState(value);

	useEffect(() => {
		const node = ref.current;
		const parts = partsOf(value);
		if (!node || !parts) {
			setDisplay(value);
			return;
		}

		if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
			setDisplay(value);
			return;
		}

		let frame = 0;
		const observer = new IntersectionObserver(
			([entry]) => {
				if (!entry?.isIntersecting) {
					return;
				}

				observer.disconnect();
				const start = performance.now();
				const tick = (now: number) => {
					const progress = Math.min((now - start) / 1500, 1);
					const eased = 1 - (1 - progress) ** 3;
					setDisplay(format(parts, Math.round(parts.target * eased)));
					if (progress < 1) {
						frame = requestAnimationFrame(tick);
					}
				};
				setDisplay(format(parts, 0));
				frame = requestAnimationFrame(tick);
			},
			{ threshold: 0.2 },
		);
		observer.observe(node);

		return () => {
			observer.disconnect();
			cancelAnimationFrame(frame);
		};
	}, [value]);

	return (
		<strong className={className} ref={ref}>
			{display}
		</strong>
	);
}
