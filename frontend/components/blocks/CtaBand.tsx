import { BlockPhoto, GoldButton } from "@/components/blocks/parts";
import { fieldLink, imageId, text } from "@/lib/blocks/fields";
import type { Block, FeaturedImage } from "@/lib/wp/types";

export function CtaBand({
	block,
	images,
	siteHost,
}: {
	block: Block;
	images: ReadonlyMap<number, FeaturedImage>;
	siteHost: string;
}) {
	const tone = text(block.attributes.tone);
	const mode = tone === "teal" || tone === "paper" ? tone : "photo";
	const photo = mode === "photo" ? imageId(block.attributes.image) : null;
	const eyebrow = text(block.attributes.eyebrow);
	const heading = text(block.attributes.heading);
	const body = text(block.attributes.text);
	const button = fieldLink(block.attributes.button, siteHost);
	const hasPhoto = photo !== null && images.has(photo);

	if (!heading && !eyebrow && !body && !hasPhoto && !button) {
		return null;
	}

	return (
		<section className={`liftedpath-cta liftedpath-cta--${mode}`}>
			<BlockPhoto
				className="liftedpath-cta__photo"
				id={photo}
				images={images}
				sizes="(max-width: 1280px) 100vw, 1280px"
			/>
			<div className="liftedpath-cta__content">
				{eyebrow ? <p className="liftedpath-cta__eyebrow">{eyebrow}</p> : null}
				{heading ? <h2>{heading}</h2> : null}
				{body ? <p>{body}</p> : null}
				<GoldButton link={button} />
			</div>
		</section>
	);
}
