import Image from "next/image";
import Link from "next/link";
import type { CSSProperties, ReactNode } from "react";
import { ArrowIcon } from "@/components/blocks/icons";
import type { FieldLink } from "@/lib/blocks/fields";
import type { FeaturedImage } from "@/lib/wp/types";

export function FieldAnchor({
	children,
	className,
	link,
	style,
}: {
	children: ReactNode;
	className: string;
	link: FieldLink;
	style?: CSSProperties;
}) {
	if (!link.external && !link.blank) {
		return (
			<Link className={className} href={link.href} style={style}>
				{children}
			</Link>
		);
	}

	return (
		<a
			className={className}
			href={link.href}
			rel={link.blank ? "noopener noreferrer" : undefined}
			style={style}
			target={link.blank ? "_blank" : undefined}
		>
			{children}
		</a>
	);
}

export function GoldButton({ link }: { link: FieldLink | null }) {
	if (!link) {
		return null;
	}

	return (
		<FieldAnchor className="liftedpath-button" link={link}>
			{link.title}
			<ArrowIcon />
		</FieldAnchor>
	);
}

export function TextLink({ link }: { link: FieldLink | null }) {
	if (!link) {
		return null;
	}

	return (
		<FieldAnchor className="liftedpath-text-link" link={link}>
			{link.title}
			<ArrowIcon />
		</FieldAnchor>
	);
}

export function TextCue({ label }: { label: string }) {
	return (
		<span className="liftedpath-text-link">
			{label}
			<ArrowIcon />
		</span>
	);
}

export function BlockPhoto({
	className,
	alt,
	decorative = false,
	id,
	images,
	priority = false,
	sizes,
}: {
	alt?: string;
	className: string;
	decorative?: boolean;
	id: number | null;
	images: ReadonlyMap<number, FeaturedImage>;
	priority?: boolean;
	sizes?: string;
}) {
	if (!id) {
		return null;
	}

	const image = images.get(id);
	if (!image) {
		return null;
	}

	return (
		<Image
			alt={decorative ? "" : (alt ?? image.alt)}
			className={className}
			height={image.height}
			priority={priority}
			sizes={sizes ?? (priority ? "100vw" : "(max-width: 768px) 100vw, min(1120px, 92vw)")}
			src={image.url}
			unoptimized={image.url.startsWith("http://")}
			width={image.width}
		/>
	);
}
