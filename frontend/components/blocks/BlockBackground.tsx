"use client";

import { useSyncExternalStore } from "react";

function subscribe(onChange: () => void) {
	const query = window.matchMedia("(prefers-reduced-motion: reduce)");
	query.addEventListener("change", onChange);
	return () => query.removeEventListener("change", onChange);
}

function usePrefersReducedMotion(): boolean {
	return useSyncExternalStore(
		subscribe,
		() => window.matchMedia("(prefers-reduced-motion: reduce)").matches,
		() => false,
	);
}

export function BlockBackgroundVideo({ src }: { src: string }) {
	const reduce = usePrefersReducedMotion();
	if (reduce || src === "") {
		return null;
	}

	return <video autoPlay className="liftedpath-block-bg-video" loop muted playsInline preload="metadata" src={src} />;
}
