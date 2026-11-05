import { BlockPhoto } from "@/components/blocks/parts";
import { imageId, text } from "@/lib/blocks/fields";
import type { Block, FeaturedImage } from "@/lib/wp/types";

function paragraphs(value: string): string[] {
	return value
		.split(/\n+/)
		.map((paragraph) => paragraph.trim())
		.filter((paragraph) => paragraph !== "");
}

export function AboutStory({
	block,
	images,
}: {
	block: Block;
	images: ReadonlyMap<number, FeaturedImage>;
}) {
	const eyebrow = text(block.attributes.eyebrow);
	const heading = text(block.attributes.heading);
	const body = paragraphs(text(block.attributes.body));
	const photo = imageId(block.attributes.image);
	const alt = text(block.attributes.image_alt);
	const hasPhoto = photo !== null && images.has(photo);

	if (!eyebrow && !heading && body.length === 0 && !hasPhoto) {
		return null;
	}

	return (
		<section className="liftedpath-section liftedpath-story">
			<div className="liftedpath-section__inner liftedpath-story__layout">
				<div className="liftedpath-story__copy">
					{eyebrow ? <p className="liftedpath-eyebrow">{eyebrow}</p> : null}
					{heading ? <h2>{heading}</h2> : null}
					{body.map((paragraph) => (
						<p key={paragraph}>{paragraph}</p>
					))}
				</div>
				{hasPhoto ? (
					<div className="liftedpath-story__photo">
						<BlockPhoto
							alt={alt || undefined}
							className="liftedpath-story__image"
							id={photo}
							images={images}
							sizes="(max-width: 900px) 92vw, 540px"
						/>
					</div>
				) : null}
			</div>
		</section>
	);
}
