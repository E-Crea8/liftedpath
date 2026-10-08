import type { CSSProperties } from "react";
import type { Design } from "@/lib/wp/types";

const PALETTE: Record<string, string> = {
	paper: "--color-paper",
	teal: "--color-teal",
	deep: "--color-deep",
	gold: "--color-gold",
	bronze: "--color-bronze",
	sand: "--color-sand",
	surface: "--color-surface",
	warm: "--color-warm",
	cool: "--color-cool",
	muted: "--color-muted",
	line: "--color-line",
};

const FONT_SIZES: Record<string, string> = {
	display: "--text-display",
	heading: "--text-heading",
	"heading-3": "--text-h3",
	stat: "--text-stat",
	"body-large": "--text-body-lg",
	body: "--text-body",
	eyebrow: "--text-eyebrow",
	nav: "--text-nav",
	button: "--text-button",
	caption: "--text-caption",
};

const COLOR = /^#[0-9A-Fa-f]{6}$/;
const LENGTH = /^(?:[0-9.]+px|[0-9.]+rem)$/;
const CLAMP = /^clamp\([0-9.]+rem,\s*[0-9.]+vw,\s*[0-9.]+rem\)$/;

export function designCssVariables(design: Design): CSSProperties {
	const style: Record<string, string> = {};

	for (const [slug, variable] of Object.entries(PALETTE)) {
		const value = design.palette[slug];
		if (value && COLOR.test(value)) {
			style[variable] = value;
		}
	}

	for (const [slug, variable] of Object.entries(FONT_SIZES)) {
		const value = design.fontSizes[slug];
		if (value && isSize(value)) {
			style[variable] = value;
		}
	}

	for (const [slug, value] of Object.entries(design.spacingSizes)) {
		if (/^[0-9]+$/.test(slug) && /^[0-9.]+px$/.test(value)) {
			style[`--space-${slug}`] = value;
		}
	}

	if (design.layout.contentSize && /^[0-9.]+px$/.test(design.layout.contentSize)) {
		style["--content-max"] = design.layout.contentSize;
	}

	return style as CSSProperties;
}

function isSize(value: string): boolean {
	return LENGTH.test(value) || CLAMP.test(value);
}
