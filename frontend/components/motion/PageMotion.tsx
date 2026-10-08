"use client";

import { useRef, type ReactNode } from "react";
import gsap from "gsap";
import { useGSAP } from "@gsap/react";
import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(useGSAP, ScrollTrigger);

const SECTION = ".liftedpath-section, .liftedpath-stat-group, .liftedpath-cta, .liftedpath-copy";

const REVEAL = [
	".liftedpath-about__photos",
	".liftedpath-about__copy",
	".liftedpath-programs__heading",
	".liftedpath-programs__card",
	".liftedpath-programs__callout",
	".liftedpath-actions__heading",
	".liftedpath-actions__item",
	".liftedpath-process__heading",
	".liftedpath-process__grid > li",
	".liftedpath-pillars__grid > li",
	".liftedpath-goals__list > li",
	".liftedpath-stat",
	".liftedpath-faq__list",
	".liftedpath-stewardship__copy",
	".liftedpath-stewardship__media",
	".liftedpath-contact__layout > *",
	".liftedpath-donate__form",
	".liftedpath-donate__aside",
	".liftedpath-cta__content > *",
	".liftedpath-sponsor__row",
	".liftedpath-copy > *",
	".liftedpath-eyebrow",
	"h2",
	".liftedpath-lead",
].join(", ");

function revealTargets(section: HTMLElement): HTMLElement[] {
	const matches = Array.from(section.querySelectorAll<HTMLElement>(REVEAL));
	const outermost = matches.filter((node) => !matches.some((other) => other !== node && other.contains(node)));
	return outermost.length > 0 ? outermost : [section];
}

function armTree(node: Node, arm: (section: HTMLElement) => void) {
	if (!(node instanceof HTMLElement)) {
		return;
	}
	if (node.matches(SECTION)) {
		arm(node);
	}
	node.querySelectorAll<HTMLElement>(SECTION).forEach(arm);
}

export function PageMotion({ children }: { children: ReactNode }) {
	const root = useRef<HTMLDivElement>(null);

	useGSAP(
		() => {
			const scope = root.current;
			if (!scope) {
				return;
			}

			const motion = gsap.matchMedia();
			motion.add("(prefers-reduced-motion: no-preference)", () => {
				scope.querySelectorAll<HTMLElement>(".liftedpath-hero, .liftedpath-page-hero").forEach((hero) => {
					const lines = hero.querySelectorAll<HTMLElement>(
						".liftedpath-hero__content > *, .liftedpath-page-hero__content > *, .liftedpath-hero__stat",
					);
					if (lines.length === 0) {
						return;
					}
					gsap.fromTo(
						lines,
						{ y: 34, opacity: 0 },
						{
							y: 0,
							opacity: 1,
							duration: 0.9,
							stagger: 0.13,
							delay: 0.15,
							ease: "power3.out",
							clearProps: "transform,opacity",
						},
					);
				});

				const arm = (section: HTMLElement) => {
					if (section.dataset.motion === "armed") {
						return;
					}
					section.dataset.motion = "armed";
					revealTargets(section).forEach((target, index) => {
						gsap.fromTo(
							target,
							{ y: 62, autoAlpha: 0 },
							{
								y: 0,
								autoAlpha: 1,
								duration: 1.05,
								delay: Math.min(index % 4, 3) * 0.075,
								ease: "power3.out",
								clearProps: "transform,opacity,visibility",
								scrollTrigger: {
									trigger: target,
									start: "top 92%",
									once: true,
								},
							},
						);
					});
				};

				Array.from(scope.children).forEach((child) => armTree(child, arm));

				const observer = new MutationObserver((records) => {
					records.forEach((record) => {
						record.addedNodes.forEach((node) => armTree(node, arm));
					});
				});
				observer.observe(scope, { childList: true });

				return () => {
					observer.disconnect();
					scope.querySelectorAll<HTMLElement>("[data-motion]").forEach((node) => {
						delete node.dataset.motion;
					});
				};
			});

			return () => {
				motion.revert();
			};
		},
		{ scope: root },
	);

	return <div ref={root}>{children}</div>;
}
