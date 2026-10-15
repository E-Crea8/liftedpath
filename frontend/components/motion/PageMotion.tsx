"use client";

import { useRef, type ReactNode } from "react";
import gsap from "gsap";
import { useGSAP } from "@gsap/react";
import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(useGSAP, ScrollTrigger);

const SECTION = ".liftedpath-section, .liftedpath-stat-group, .liftedpath-cta, .liftedpath-footer-cta, .liftedpath-copy";

const REVEAL = [
	".liftedpath-about__photos",
	".liftedpath-about__copy",
	".liftedpath-story__copy",
	".liftedpath-story__photo",
	".liftedpath-pillars__heading",
	".liftedpath-team__heading",
	".liftedpath-team__card",
	".liftedpath-programs__heading",
	".liftedpath-programs__card",
	".liftedpath-pathway",
	".liftedpath-work__lead figure",
	".liftedpath-work__lead .liftedpath-eyebrow",
	".liftedpath-work__lead h2",
	".liftedpath-work__lead .liftedpath-work__intro",
	".liftedpath-work__block h2",
	".liftedpath-work__block > p",
	".liftedpath-work__support > div",
	".liftedpath-work__child",
	".liftedpath-work__principles",
	".liftedpath-work__questions .liftedpath-faq__list",
	".liftedpath-work-end",
	".liftedpath-footer-cta",
	".liftedpath-footer-cta__inner",
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
	".liftedpath-cta__content",
	".liftedpath-sponsor__row",
	".liftedpath-copy > *",
	".liftedpath-eyebrow",
	"h2",
	".liftedpath-lead",
].join(", ");

function animateColumns(section: HTMLElement, headingSelector: string, cardSelector: string, bitSelector: string) {
	const heading = section.querySelector<HTMLElement>(headingSelector);
	const cards = Array.from(section.querySelectorAll<HTMLElement>(cardSelector));
	const trigger = cards[0]?.parentElement ?? heading ?? section;
	const timeline = gsap.timeline({
		scrollTrigger: {
			trigger,
			start: "top 82%",
			once: true,
		},
	});

	if (heading) {
		timeline.fromTo(
			heading,
			{ y: 46, autoAlpha: 0 },
			{ y: 0, autoAlpha: 1, duration: 0.85, ease: "power3.out", clearProps: "transform,opacity,visibility" },
			0,
		);
	}

	if (cards.length === 0) {
		return;
	}

	timeline.fromTo(
		cards,
		{ y: 72, autoAlpha: 0 },
		{
			y: 0,
			autoAlpha: 1,
			duration: 0.95,
			stagger: 0.13,
			ease: "power3.out",
			clearProps: "transform,opacity,visibility",
		},
		0.12,
	);

	cards.forEach((card, index) => {
		const bits = card.querySelectorAll<HTMLElement>(bitSelector);
		const photo = card.querySelector<HTMLElement>(".liftedpath-team__photo, .liftedpath-team__fallback");
		const at = 0.28 + index * 0.13;

		if (photo) {
			timeline.fromTo(photo, { scale: 1.12 }, { scale: 1, duration: 1.15, ease: "power2.out", clearProps: "transform" }, at);
		}

		if (bits.length > 0) {
			timeline.fromTo(
				bits,
				{ y: 22, autoAlpha: 0 },
				{
					y: 0,
					autoAlpha: 1,
					duration: 0.55,
					stagger: 0.07,
					ease: "power2.out",
					clearProps: "transform,opacity,visibility",
				},
				at,
			);
		}
	});
}

function revealTargets(section: HTMLElement): HTMLElement[] {
	if (section.classList.contains("liftedpath-beliefs")) {
		return [section];
	}

	if (section.classList.contains("liftedpath-pillars") || section.classList.contains("liftedpath-team")) {
		return [];
	}

	const matches = Array.from(section.querySelectorAll<HTMLElement>(REVEAL)).filter(
		(node) => node.closest(".liftedpath-goals__intro") === null,
	);
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
					if (section.classList.contains("liftedpath-pillars")) {
						animateColumns(section, ".liftedpath-pillars__heading", ".liftedpath-pillars__grid > li", ".liftedpath-pillars__index, h3, p");
						return;
					}
					if (section.classList.contains("liftedpath-team")) {
						animateColumns(section, ".liftedpath-team__heading", ".liftedpath-team__card", ".liftedpath-team__info > *");
						return;
					}
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
