"use client";

import { useEffect, useRef } from "react";

export function CopyrightLine({ text }: { text: string }) {
	const line = useRef<HTMLParagraphElement>(null);

	useEffect(() => {
		const node = line.current;
		if (!node) {
			return;
		}

		const year = String(new Date().getFullYear());
		node.textContent = text.replace(/©\s*\d{4}/, `© ${year}`);
	}, [text]);

	return <p ref={line}>{text}</p>;
}
