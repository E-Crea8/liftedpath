"use client";

import { useEffect, useRef, useState, useSyncExternalStore } from "react";
import type { FeaturedImage } from "@/lib/wp/types";

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

export function HeroBackground({
	image,
	imageClass,
	videoClass,
	videoUrl,
	muted,
	loop,
	autoplay,
}: {
	image: FeaturedImage | null;
	imageClass: string;
	videoClass: string;
	videoUrl: string;
	muted: boolean;
	loop: boolean;
	autoplay: boolean;
}) {
	const reduce = usePrefersReducedMotion();
	const videoRef = useRef<HTMLVideoElement>(null);
	const [failed, setFailed] = useState(false);
	const [playing, setPlaying] = useState(false);
	const showVideo = videoUrl !== "" && autoplay && !reduce && !failed;

	useEffect(() => {
		setFailed(false);
		setPlaying(false);
	}, [videoUrl]);

	useEffect(() => {
		const node = videoRef.current;
		if (!node || !showVideo) {
			return;
		}

		let cancelled = false;
		node.muted = muted;
		const attempt = node.play();

		if (attempt !== undefined) {
			attempt
				.then(() => {
					if (!cancelled) {
						setPlaying(true);
					}
				})
				.catch((error: unknown) => {
					if (cancelled) {
						return;
					}
					const name = error instanceof DOMException ? error.name : "";
					if (name === "AbortError") {
						return;
					}
					setFailed(true);
				});
		}

		return () => {
			cancelled = true;
		};
	}, [showVideo, muted, videoUrl]);

	return (
		<>
			{image ? (
				<img
					alt={image.alt}
					className={imageClass}
					height={image.height}
					src={image.url}
					width={image.width}
				/>
			) : null}
			{showVideo ? (
				<video
					autoPlay
					className={videoClass}
					loop={loop}
					muted={muted}
					onError={() => setFailed(true)}
					onPlaying={() => setPlaying(true)}
					playsInline
					poster={image?.url}
					preload="auto"
					ref={videoRef}
					src={videoUrl}
					style={{ opacity: playing ? 1 : 0 }}
				/>
			) : null}
		</>
	);
}
