import { BlockIcon } from "@/components/blocks/icons";
import { BlockPhoto, GoldButton } from "@/components/blocks/parts";
import { fieldLink, imageId, rows, stepNumber, telHref, text } from "@/lib/blocks/fields";
import type { Block, FeaturedImage } from "@/lib/wp/types";

export function Stewardship({
	block,
	images,
	phone,
	siteHost,
}: {
	block: Block;
	images: ReadonlyMap<number, FeaturedImage>;
	phone: string;
	siteHost: string;
}) {
	const eyebrow = text(block.attributes.eyebrow);
	const heading = text(block.attributes.heading);
	const intro = text(block.attributes.intro);
	const noteLabel = text(block.attributes.note_label) || "Call Us";
	const button = fieldLink(block.attributes.button, siteHost);
	const photo = imageId(block.attributes.image);
	const inset = imageId(block.attributes.inset_image);
	const points = rows(block.attributes, "points")
		.map((row) => ({
			title: text(row.title),
			body: text(row.text),
		}))
		.filter((point) => point.title || point.body);
	const hasPhoto = photo !== null && images.has(photo);
	const hasInset = inset !== null && images.has(inset);
	const tel = telHref(phone);

	if (!heading && !eyebrow && !intro && !hasPhoto && !button && points.length === 0) {
		return null;
	}

	const note =
		phone && tel ? (
			<p className="liftedpath-stewardship__note">
				<span className="liftedpath-stewardship__phone">
					<BlockIcon name="phone" />
				</span>
				<span>
					<strong>{noteLabel}</strong>
					<small>
						<a href={tel}>{phone}</a>
					</small>
				</span>
			</p>
		) : null;

	return (
		<section className="liftedpath-section liftedpath-stewardship">
			<div className="liftedpath-section__inner liftedpath-stewardship__layout">
				<div className="liftedpath-stewardship__copy">
					{eyebrow ? <p className="liftedpath-eyebrow">{eyebrow}</p> : null}
					{heading ? <h2>{heading}</h2> : null}
					{intro ? <p className="liftedpath-stewardship__intro">{intro}</p> : null}
					{points.length > 0 ? (
						<ol className="liftedpath-stewardship__points">
							{points.map((point, index) => (
								<li key={`${point.title}-${index}`}>
									<b>{stepNumber(index)}</b>
									<span>
										{point.title ? <strong>{point.title}</strong> : null}
										{point.body ? <small>{point.body}</small> : null}
									</span>
								</li>
							))}
						</ol>
					) : null}
					<GoldButton link={button} />
					{!hasPhoto && !hasInset ? note : null}
				</div>
				{hasPhoto || hasInset ? (
					<div className="liftedpath-stewardship__media">
						<BlockPhoto className="liftedpath-stewardship__photo" id={photo} images={images} />
						{note}
						<BlockPhoto className="liftedpath-stewardship__inset" id={inset} images={images} />
					</div>
				) : null}
			</div>
		</section>
	);
}
