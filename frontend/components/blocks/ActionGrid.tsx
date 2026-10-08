"use client";

import { useRef, type CSSProperties } from "react";
import gsap from "gsap";
import { useGSAP } from "@gsap/react";
import { Heart, Link2, Megaphone, Sparkles } from "lucide-react";
import { FieldAnchor, TextCue } from "@/components/blocks/parts";
import { fieldLink, rows, text } from "@/lib/blocks/fields";
import type { Block, JsonValue } from "@/lib/wp/types";

gsap.registerPlugin(useGSAP);

const ICONS = [Heart, Link2, Sparkles, Megaphone] as const;
const FLOAT_ROTATION = [4, -3, 8, -5];
const FLOAT_DURATION = [3.6, 4.3, 3.2, 4];
const HOVER_ROTATION = [0, -12, 20, -12];

function hoverColor(value: JsonValue | undefined): string {
	const color = text(value);
	return /^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/.test(color) ? color : "";
}

function titleWeight(value: JsonValue | undefined): string {
	const weight = text(value);
	return weight === "400" || weight === "500" || weight === "600" || weight === "700" ? weight : "";
}

export function ActionGrid({ block, siteHost }: { block: Block; siteHost: string }) {
	const grid = useRef<HTMLDivElement>(null);
	const eyebrow = text(block.attributes.eyebrow);
	const heading = text(block.attributes.heading);
	const intro = text(block.attributes.intro);
	const weight = titleWeight(block.attributes.title_weight);
	const items = rows(block.attributes, "items")
		.map((row) => ({
			title: text(row.title),
			body: text(row.text),
			link: fieldLink(row.link, siteHost),
			hover: hoverColor(row.hover_color),
		}))
		.filter((item) => item.title || item.body);

	useGSAP(
		() => {
			const root = grid.current;
			if (!root) {
				return;
			}

			const motion = gsap.matchMedia();
			const cleanups: Array<() => void> = [];

			motion.add("(prefers-reduced-motion: no-preference)", () => {
				root.querySelectorAll<HTMLElement>(".liftedpath-actions__item").forEach((card, index) => {
					const wash = card.querySelector<HTMLElement>(".liftedpath-actions__wash");
					const icon = card.querySelector<HTMLElement>(".liftedpath-actions__icon");
					const symbol = icon?.querySelector("svg");
					if (!wash || !icon || !symbol) {
						return;
					}

					gsap.to(icon, {
						y: index % 2 === 0 ? -9 : 9,
						x: index === 1 ? 7 : 0,
						rotation: FLOAT_ROTATION[index] ?? 4,
						duration: FLOAT_DURATION[index] ?? 3.6,
						ease: "sine.inOut",
						repeat: -1,
						yoyo: true,
						delay: index * 0.3,
					});

					const over = () => {
						gsap.to(wash, { opacity: 1, duration: 0.5, ease: "power2.out", overwrite: true });
						gsap.to(symbol, {
							rotation: HOVER_ROTATION[index] ?? 0,
							scale: index === 0 || index === 2 ? 1.12 : 1.07,
							x: index === 1 ? 9 : 0,
							y: index === 3 ? -5 : 0,
							duration: 0.7,
							ease: index === 0 ? "back.out(2)" : "power2.out",
							overwrite: true,
						});
					};
					const out = () => {
						gsap.to(wash, { opacity: 0, duration: 0.55, ease: "power2.out", overwrite: true });
						gsap.to(symbol, {
							rotation: 0,
							scale: 1,
							x: 0,
							y: 0,
							duration: 0.7,
							ease: "power2.out",
							overwrite: true,
						});
					};

					card.addEventListener("pointerenter", over);
					card.addEventListener("pointerleave", out);
					card.addEventListener("focus", over);
					card.addEventListener("blur", out);
					cleanups.push(() => {
						card.removeEventListener("pointerenter", over);
						card.removeEventListener("pointerleave", out);
						card.removeEventListener("focus", over);
						card.removeEventListener("blur", out);
					});
				});
			});

			return () => {
				cleanups.forEach((cleanup) => cleanup());
				motion.revert();
			};
		},
		{ scope: grid, dependencies: [items.length], revertOnUpdate: true },
	);

	if (!heading && !eyebrow && !intro && items.length === 0) {
		return null;
	}

	return (
		<section className="liftedpath-section liftedpath-actions">
			<div className="liftedpath-section__inner">
				{eyebrow || heading || intro ? (
					<div className="liftedpath-actions__heading">
						{eyebrow ? <p className="liftedpath-eyebrow">{eyebrow}</p> : null}
						{heading ? <h2>{heading}</h2> : null}
						{intro ? <p>{intro}</p> : null}
					</div>
				) : null}
				<div
					className="liftedpath-actions__grid"
					ref={grid}
					style={weight ? ({ "--action-title-weight": weight } as CSSProperties) : undefined}
				>
					{items.map((item, index) => {
						const Icon = ICONS[index] ?? Heart;
						const body = (
							<>
								<span aria-hidden="true" className="liftedpath-actions__wash" />
								<span className="liftedpath-actions__icon">
									<Icon absoluteStrokeWidth={false} strokeWidth={1.35} />
								</span>
								{item.title ? <h3>{item.title}</h3> : null}
								{item.body ? <p>{item.body}</p> : null}
								{item.link ? <TextCue label={item.link.title || "Read more"} /> : null}
							</>
						);
						const style = item.hover ? ({ "--action-tint": item.hover } as CSSProperties) : undefined;

						if (!item.link) {
							return (
								<article className="liftedpath-actions__item" key={`${item.title}-${index}`} style={style}>
									{body}
								</article>
							);
						}

						return (
							<FieldAnchor
								className="liftedpath-actions__item"
								key={`${item.title}-${index}`}
								link={item.link}
								style={style}
							>
								{body}
							</FieldAnchor>
						);
					})}
				</div>
			</div>
		</section>
	);
}
