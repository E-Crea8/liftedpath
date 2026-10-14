import { ArrowIcon, BlockIcon } from "@/components/blocks/icons";
import { BlockPhoto, FieldAnchor, GoldButton, TextCue } from "@/components/blocks/parts";
import { fieldLink, imageId, rows, text } from "@/lib/blocks/fields";
import type { Block, FeaturedImage } from "@/lib/wp/types";

export function ProgramCards({
	block,
	images,
	siteHost,
}: {
	block: Block;
	images: ReadonlyMap<number, FeaturedImage>;
	siteHost: string;
}) {
	const eyebrow = text(block.attributes.eyebrow);
	const heading = text(block.attributes.heading);
	const intro = text(block.attributes.intro);
	const calloutHeading = text(block.attributes.callout_heading);
	const calloutText = text(block.attributes.callout_text);
	const calloutButton = fieldLink(block.attributes.callout_button, siteHost);
	const pathway = text(block.attributes.card_layout) === "pathway";
	const indexLabel = text(block.attributes.index_label);
	const cards = rows(block.attributes, "cards")
		.map((row) => ({
			image: imageId(row.image),
			kicker: text(row.kicker),
			title: text(row.title),
			line: text(row.line),
			link: fieldLink(row.link, siteHost),
		}))
		.filter((card) => card.image || card.kicker || card.title || card.line);

	if (!heading && !eyebrow && !intro && !calloutHeading && cards.length === 0) {
		return null;
	}

	const callout = calloutHeading ? (
		<div className="liftedpath-programs__callout">
			<span className="liftedpath-programs__mark">
				<BlockIcon name="heart" />
			</span>
			<div>
				<h3>{calloutHeading}</h3>
				{calloutText ? <p>{calloutText}</p> : null}
			</div>
			<GoldButton link={calloutButton} />
		</div>
	) : null;

	return (
		<section className={pathway ? "liftedpath-section liftedpath-programs liftedpath-programs--pathway" : "liftedpath-section liftedpath-programs"}>
			<div className="liftedpath-section__inner">
				{eyebrow || heading || intro ? (
					<div className="liftedpath-programs__heading">
						{eyebrow ? <p className="liftedpath-eyebrow">{eyebrow}</p> : null}
						{heading ? <h2>{heading}</h2> : null}
						{intro ? <p>{intro}</p> : null}
					</div>
				) : null}
				<div className="liftedpath-programs__grid">
					{cards.map((card, index) => {
						const marker = indexLabel ? `${String(index + 1).padStart(2, "0")} / ${indexLabel}` : card.kicker;
						const body = pathway ? (
							<>
								<div className="liftedpath-pathway__media">
									<BlockPhoto className="liftedpath-pathway__image" id={card.image} images={images} />
								</div>
								{marker ? <span>{marker}</span> : null}
								{card.title ? <h3>{card.title}</h3> : null}
								{card.line ? <p>{card.line}</p> : null}
								{card.link ? <ArrowIcon /> : null}
							</>
						) : (
							<>
								<div className="liftedpath-programs__media">
									<BlockPhoto className="liftedpath-programs__image" id={card.image} images={images} />
									{card.kicker ? <span>{card.kicker}</span> : null}
								</div>
								<div className="liftedpath-programs__info">
									{card.title ? <h3>{card.title}</h3> : null}
									{card.line ? <p>{card.line}</p> : null}
									{card.link ? <TextCue label={card.link.title || "Read more"} /> : null}
								</div>
							</>
						);
						const cardClass = pathway ? "liftedpath-pathway" : "liftedpath-programs__card";

						if (!card.link) {
							return (
								<article className={cardClass} key={`${card.title}-${index}`}>
									{body}
								</article>
							);
						}

						return (
							<FieldAnchor className={cardClass} key={`${card.title}-${index}`} link={card.link}>
								{body}
							</FieldAnchor>
						);
					})}
					{pathway ? null : callout}
				</div>
				{pathway ? callout : null}
			</div>
		</section>
	);
}
