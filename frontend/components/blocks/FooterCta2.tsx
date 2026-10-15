import { BlockPhoto, GoldButton } from "@/components/blocks/parts";
import { fieldLink, imageId, numeric, text } from "@/lib/blocks/fields";
import type { Block, FeaturedImage } from "@/lib/wp/types";
import type { CSSProperties } from "react";

export function FooterCta2({
	block,
	images,
	siteHost,
}: {
	block: Block;
	images: ReadonlyMap<number, FeaturedImage>;
	siteHost: string;
}) {
	const photo = imageId(block.attributes.image);
	const hasPhoto = photo !== null && images.has(photo);
	const rawOpacity = numeric(block.attributes.overlay_opacity);
	const opacity = rawOpacity > 0 || block.attributes.overlay_opacity === 0 || block.attributes.overlay_opacity === "0" ? Math.min(100, Math.max(0, rawOpacity || 0)) : 72;
	const eyebrow = text(block.attributes.eyebrow);
	const heading = text(block.attributes.heading);
	const body = text(block.attributes.text);
	const button = fieldLink(block.attributes.button, siteHost);

	if (!heading && !eyebrow && !body && !hasPhoto && !button) {
		return null;
	}

	const style = hasPhoto ? ({ "--footer-cta-overlay": String(opacity / 100) } as CSSProperties) : undefined;

	return (
		<section className={`liftedpath-section liftedpath-footer-cta${hasPhoto ? " liftedpath-footer-cta--photo" : ""}`} style={style}>
			{hasPhoto ? <BlockPhoto className="liftedpath-footer-cta__photo" decorative id={photo} images={images} /> : null}
			{hasPhoto ? <span aria-hidden="true" className="liftedpath-footer-cta__shade" /> : null}
			<div className="liftedpath-section__inner liftedpath-footer-cta__inner">
				<div className="liftedpath-footer-cta__copy">
					{eyebrow ? <span className="liftedpath-footer-cta__eyebrow">{eyebrow}</span> : null}
					{heading ? <h2>{heading}</h2> : null}
					{body ? <p>{body}</p> : null}
				</div>
				<GoldButton link={button} />
			</div>
		</section>
	);
}
