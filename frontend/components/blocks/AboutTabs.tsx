import { Fragment } from "react";
import { BlockIcon } from "@/components/blocks/icons";
import { CountFigure } from "@/components/motion/CountFigure";
import { BlockPhoto, GoldButton, TextLink } from "@/components/blocks/parts";
import { fieldLink, imageId, rows, text } from "@/lib/blocks/fields";
import type { Block, FeaturedImage } from "@/lib/wp/types";

export function AboutTabs({
	block,
	images,
	radioKey,
	siteHost,
}: {
	block: Block;
	images: ReadonlyMap<number, FeaturedImage>;
	radioKey: string;
	siteHost: string;
}) {
	const primary = imageId(block.attributes.primary_image);
	const secondary = imageId(block.attributes.secondary_image);
	const figure = text(block.attributes.figure);
	const figureLabel = text(block.attributes.figure_label);
	const eyebrow = text(block.attributes.eyebrow);
	const heading = text(block.attributes.heading);
	const intro = text(block.attributes.intro);
	const primaryLink = fieldLink(block.attributes.primary_link, siteHost);
	const secondaryLink = fieldLink(block.attributes.secondary_link, siteHost);
	const panels = rows(block.attributes, "tabs")
		.map((row) => ({
			label: text(row.label),
			title: text(row.title),
			body: text(row.body),
		}))
		.filter((panel) => panel.label || panel.title || panel.body)
		.slice(0, 6);
	const hasPrimary = primary !== null && images.has(primary);
	const hasSecondary = secondary !== null && images.has(secondary);

	if (
		!heading &&
		!eyebrow &&
		!intro &&
		!figure &&
		!figureLabel &&
		!hasPrimary &&
		!hasSecondary &&
		!primaryLink &&
		!secondaryLink &&
		panels.length === 0
	) {
		return null;
	}

	const group = `liftedpath-about-${radioKey}`;

	return (
		<section className="liftedpath-section liftedpath-about">
			<div className="liftedpath-section__inner liftedpath-about__layout">
				{hasPrimary || hasSecondary || figure || figureLabel ? (
					<div className={hasPrimary || hasSecondary ? "liftedpath-about__photos" : "liftedpath-about__photos liftedpath-about__photos--badge"}>
						<BlockPhoto className="liftedpath-about__photo" id={primary} images={images} />
						<BlockPhoto className="liftedpath-about__photo liftedpath-about__photo--small" id={secondary} images={images} />
						{figure || figureLabel ? (
							<p className="liftedpath-about__figure">
								{figure ? <CountFigure value={figure} /> : null}
								{figureLabel ? <small>{figureLabel}</small> : null}
							</p>
						) : null}
					</div>
				) : null}
				<div className="liftedpath-about__copy">
					{eyebrow ? <p className="liftedpath-eyebrow">{eyebrow}</p> : null}
					{heading ? <h2>{heading}</h2> : null}
					{intro ? <p className="liftedpath-about__intro">{intro}</p> : null}
					{panels.length > 0 ? (
						<div className="liftedpath-about__switcher">
							<div aria-label={eyebrow || "About"} className="liftedpath-about__tabs" role="radiogroup">
								{panels.map((panel, index) => (
									<Fragment key={`${panel.label}-${index}`}>
										<input
											className={`liftedpath-about__input is-${index}`}
											defaultChecked={index === 0}
											id={`${group}-${index}`}
											name={group}
											type="radio"
										/>
										<label className={`liftedpath-about__tab tab-${index}`} htmlFor={`${group}-${index}`}>
											{panel.label}
										</label>
									</Fragment>
								))}
							</div>
							{panels.map((panel, index) => (
								<div className={`liftedpath-about__panel panel-${index}`} key={`${panel.title}-${index}`}>
									<span className="liftedpath-about__icon">
										<BlockIcon name="target" />
									</span>
									<div>
										{panel.title ? <h3>{panel.title}</h3> : null}
										{panel.body ? <p>{panel.body}</p> : null}
									</div>
								</div>
							))}
						</div>
					) : null}
					{primaryLink || secondaryLink ? (
						<div className="liftedpath-about__actions">
							<GoldButton link={primaryLink} />
							<TextLink link={secondaryLink} />
						</div>
					) : null}
				</div>
			</div>
		</section>
	);
}
