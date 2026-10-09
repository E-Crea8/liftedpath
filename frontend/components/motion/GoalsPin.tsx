"use client";

import { useRef, type ReactNode } from "react";
import gsap from "gsap";
import { useGSAP } from "@gsap/react";
import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(useGSAP, ScrollTrigger);

export function GoalsPin({ children }: { children: ReactNode }) {
	const section = useRef<HTMLElement>(null);

	useGSAP(
		() => {
			const root = section.current;
			if (!root) {
				return;
			}

			const motion = gsap.matchMedia();
			motion.add("(min-width: 761px) and (prefers-reduced-motion: no-preference)", () => {
				const intro = root.querySelector<HTMLElement>(".liftedpath-goals__intro");
				const fifth = root.querySelector<HTMLElement>(".liftedpath-goals__list > li:nth-child(5)");
				if (!intro || !fifth) {
					return;
				}

				const pinStart = () => {
					const header = document.querySelector("header");
					const height = header ? header.getBoundingClientRect().height : 89;
					return height + 21;
				};

				ScrollTrigger.create({
					trigger: intro,
					pin: intro,
					pinSpacing: false,
					start: () => `top ${pinStart()}px`,
					endTrigger: fifth,
					end: () => `top ${pinStart() + intro.offsetHeight}px`,
					invalidateOnRefresh: true,
				});
			});

			return () => {
				motion.revert();
			};
		},
		{ scope: section },
	);

	return (
		<section className="liftedpath-section liftedpath-goals" ref={section}>
			{children}
		</section>
	);
}
