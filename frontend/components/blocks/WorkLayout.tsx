"use client";

import { useState } from "react";
import { ArrowIcon, BlockIcon } from "@/components/blocks/icons";
import { BlockPhoto, FieldAnchor, GoldButton } from "@/components/blocks/parts";
import { fieldLink, flag, imageId, rows, telHref, text } from "@/lib/blocks/fields";
import type { Block, FeaturedImage } from "@/lib/wp/types";

type WorkNav = {
	title: string;
	subtitle: string;
	link: ReturnType<typeof fieldLink>;
};

function WorkNavLink({ className, item }: { className: string; item: WorkNav }) {
	const body = (
		<>
			<span>
				{item.title ? <strong>{item.title}</strong> : null}
				{item.subtitle ? <small>{item.subtitle}</small> : null}
			</span>
			<ArrowIcon />
		</>
	);

	if (item.link) {
		return (
			<FieldAnchor className={className} link={item.link}>
				{body}
			</FieldAnchor>
		);
	}

	return <span className={className}>{body}</span>;
}

export function WorkLayout({
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
	const [open, setOpen] = useState<number | null>(0);

	const values = rows(block.attributes, "values")
		.map((row) => text(row.phrase))
		.filter(Boolean);

	const sidebarHeading = text(block.attributes.sidebar_heading);
	const navItems = rows(block.attributes, "nav_links")
		.map((row) => ({
			title: text(row.title),
			subtitle: text(row.subtitle),
			link: fieldLink(row.link, siteHost),
		}))
		.filter((item) => item.title || item.subtitle);
	const showBack = flag(block.attributes.show_back, false);
	const backLabel = text(block.attributes.back_label);
	const backLink = fieldLink(block.attributes.back_link, siteHost);

	const contactImage = imageId(block.attributes.contact_image);
	const contactHeading = text(block.attributes.contact_heading);
	const contactText = text(block.attributes.contact_text);
	const contactButton = fieldLink(block.attributes.contact_button, siteHost);
	const contactPhone = text(block.attributes.contact_phone) || phone;
	const phoneLink = telHref(contactPhone);
	const hasContactPhoto = contactImage !== null && images.has(contactImage);

	const leadImage = imageId(block.attributes.lead_image);
	const leadCaption = text(block.attributes.lead_caption);
	const leadEyebrow = text(block.attributes.lead_eyebrow);
	const leadHeading = text(block.attributes.lead_heading);
	const leadIntro = text(block.attributes.lead_intro);
	const hasLeadPhoto = leadImage !== null && images.has(leadImage);

	const audienceKicker = text(block.attributes.audience_kicker);
	const audienceHeading = text(block.attributes.audience_heading);
	const audienceText = text(block.attributes.audience_text);
	const support = rows(block.attributes, "support")
		.map((row) => ({ title: text(row.title), text: text(row.text) }))
		.filter((item) => item.title || item.text);

	const areasKicker = text(block.attributes.areas_kicker);
	const areasHeading = text(block.attributes.areas_heading);
	const areas = rows(block.attributes, "areas")
		.map((row) => ({
			title: text(row.title),
			subtitle: text(row.subtitle),
			text: text(row.text),
			link: fieldLink(row.link, siteHost),
		}))
		.filter((item) => item.title || item.text);

	const whyKicker = text(block.attributes.why_kicker);
	const whyHeading = text(block.attributes.why_heading);
	const whyText = text(block.attributes.why_text);
	const principles = rows(block.attributes, "principles")
		.map((row) => text(row.text))
		.filter(Boolean);

	const faqKicker = text(block.attributes.faq_kicker);
	const faqHeading = text(block.attributes.faq_heading);
	const faqItems = rows(block.attributes, "faq_items")
		.map((row) => ({ question: text(row.question), answer: text(row.answer) }))
		.filter((item) => item.question || item.answer);

	const endEyebrow = text(block.attributes.end_eyebrow);
	const endHeading = text(block.attributes.end_heading);
	const endText = text(block.attributes.end_text);
	const endButton = fieldLink(block.attributes.end_button, siteHost);

	const empty =
		values.length === 0 &&
		!sidebarHeading &&
		!leadHeading &&
		!audienceHeading &&
		!areasHeading &&
		!whyHeading &&
		!faqHeading &&
		!endHeading &&
		!contactHeading;

	if (empty) {
		return null;
	}

	return (
		<>
			{values.length > 0 ? (
				<div aria-label="Our approach" className="liftedpath-work-values">
					{values.flatMap((phrase, index) => {
						const nodes = [];
						if (index > 0) {
							nodes.push(
								<span aria-hidden="true" className="liftedpath-work-values__icon" key={`sparkle-${index}`}>
									<BlockIcon name="sparkles" />
								</span>,
							);
						}
						nodes.push(<span key={`${phrase}-${index}`}>{phrase}</span>);
						return nodes;
					})}
				</div>
			) : null}

			<section className="liftedpath-section liftedpath-work">
				<div className="liftedpath-section__inner liftedpath-work__layout">
					<aside className="liftedpath-work__sidebar">
						{sidebarHeading || navItems.length > 0 || (showBack && backLink) ? (
							<nav aria-label={sidebarHeading || "Explore our work"} className="liftedpath-work__nav">
								{sidebarHeading ? <h2>{sidebarHeading}</h2> : null}
								{navItems.length > 0 ? (
									<div>
										{navItems.map((item, index) => (
											<WorkNavLink className="liftedpath-work__nav-link" item={item} key={`${item.title}-${index}`} />
										))}
									</div>
								) : null}
								{showBack && backLink ? (
									<FieldAnchor className="liftedpath-work__back" link={{ ...backLink, title: backLabel || backLink.title || "All our work" }}>
										{backLabel || backLink.title || "All our work"}
										<ArrowIcon />
									</FieldAnchor>
								) : null}
							</nav>
						) : null}

						{contactHeading || contactText || contactButton || contactPhone ? (
							<div className={`liftedpath-work__contact${hasContactPhoto ? " liftedpath-work__contact--photo" : ""}`}>
								{hasContactPhoto ? <BlockPhoto className="liftedpath-work__contact-photo" decorative id={contactImage} images={images} /> : null}
								{hasContactPhoto ? <span aria-hidden="true" className="liftedpath-work__contact-shade" /> : null}
								<div className="liftedpath-work__contact-inner">
									<span aria-hidden="true" className="liftedpath-work__contact-icon">
										<BlockIcon name="heart" />
									</span>
									{contactHeading ? <h3>{contactHeading}</h3> : null}
									{contactText ? <p>{contactText}</p> : null}
									<GoldButton link={contactButton} />
									{contactPhone ? (
										phoneLink ? (
											<a className="liftedpath-work__phone" href={phoneLink}>
												<BlockIcon name="phone" />
												{contactPhone}
											</a>
										) : (
											<span className="liftedpath-work__phone">
												<BlockIcon name="phone" />
												{contactPhone}
											</span>
										)
									) : null}
								</div>
							</div>
						) : null}
					</aside>

					<article className="liftedpath-work__article">
						{hasLeadPhoto || leadHeading || leadIntro ? (
							<div className="liftedpath-work__lead">
								{hasLeadPhoto ? (
									<figure>
										<BlockPhoto className="liftedpath-work__lead-photo" id={leadImage} images={images} priority />
										{leadCaption ? <figcaption>{leadCaption}</figcaption> : null}
									</figure>
								) : null}
								{leadEyebrow ? <span className="liftedpath-eyebrow">{leadEyebrow}</span> : null}
								{leadHeading ? <h2>{leadHeading}</h2> : null}
								{leadIntro ? <p className="liftedpath-work__intro">{leadIntro}</p> : null}
							</div>
						) : null}

						{audienceHeading || audienceText || support.length > 0 ? (
							<section className="liftedpath-work__block">
								{audienceKicker ? <span className="liftedpath-work__kicker">{audienceKicker}</span> : null}
								{audienceHeading ? <h2>{audienceHeading}</h2> : null}
								{audienceText ? <p>{audienceText}</p> : null}
								{support.length > 0 ? (
									<div className="liftedpath-work__support">
										{support.map((item, index) => (
											<div key={`${item.title}-${index}`}>
												<span aria-hidden="true" className="liftedpath-work__support-icon">
													<BlockIcon name={index === 0 ? "heart" : "sparkles"} />
												</span>
												{item.title ? <h3>{item.title}</h3> : null}
												{item.text ? <p>{item.text}</p> : null}
											</div>
										))}
									</div>
								) : null}
							</section>
						) : null}

						{areas.length > 0 ? (
							<section className="liftedpath-work__block">
								{areasKicker ? <span className="liftedpath-work__kicker">{areasKicker}</span> : null}
								{areasHeading ? <h2>{areasHeading}</h2> : null}
								<div className="liftedpath-work__child-list">
									{areas.map((item, index) => (
										<div className="liftedpath-work__child" key={`${item.title}-${index}`}>
											<span className="liftedpath-work__child-number">{String(index + 1).padStart(2, "0")}</span>
											<div>
												<WorkNavLink
													className="liftedpath-work__nav-link"
													item={{ title: item.title, subtitle: item.subtitle, link: item.link }}
												/>
												{item.text ? <p>{item.text}</p> : null}
											</div>
										</div>
									))}
								</div>
							</section>
						) : null}

						{whyHeading || whyText || principles.length > 0 ? (
							<section className="liftedpath-work__block">
								{whyKicker ? <span className="liftedpath-work__kicker">{whyKicker}</span> : null}
								{whyHeading ? <h2>{whyHeading}</h2> : null}
								{whyText ? <p>{whyText}</p> : null}
								{principles.length > 0 ? (
									<ul className="liftedpath-work__principles">
										{principles.map((item) => (
											<li key={item}>
												<BlockIcon name="check" />
												{item}
											</li>
										))}
									</ul>
								) : null}
							</section>
						) : null}

						{faqHeading || faqItems.length > 0 ? (
							<section className="liftedpath-work__block liftedpath-work__questions">
								{faqKicker ? <span className="liftedpath-work__kicker">{faqKicker}</span> : null}
								{faqHeading ? <h2>{faqHeading}</h2> : null}
								{faqItems.length > 0 ? (
									<div className="liftedpath-faq__list">
										{faqItems.map((item, index) => {
											const expanded = open === index;
											return (
												<div className="liftedpath-faq__item" key={`${item.question}-${index}`}>
													<button
														aria-controls={`liftedpath-work-answer-${index}`}
														aria-expanded={expanded}
														className="liftedpath-work__faq-button"
														id={`liftedpath-work-question-${index}`}
														onClick={() => setOpen(expanded ? null : index)}
														type="button"
													>
														<span>{item.question || item.answer}</span>
														<span className={`liftedpath-faq__chevron${expanded ? " is-open" : ""}`}>
															<BlockIcon name="chevron" />
														</span>
													</button>
													<div
														aria-labelledby={`liftedpath-work-question-${index}`}
														hidden={!expanded}
														id={`liftedpath-work-answer-${index}`}
														role="region"
													>
														{item.answer && item.question ? <p>{item.answer}</p> : null}
													</div>
												</div>
											);
										})}
									</div>
								) : null}
							</section>
						) : null}
					</article>
				</div>
			</section>

			{endHeading || endText || endButton ? (
				<section className="liftedpath-section liftedpath-work-end">
					<div className="liftedpath-section__inner liftedpath-work-end__inner">
						<div>
							{endEyebrow ? <span className="liftedpath-eyebrow">{endEyebrow}</span> : null}
							{endHeading ? <h2>{endHeading}</h2> : null}
							{endText ? <p>{endText}</p> : null}
						</div>
						<GoldButton link={endButton} />
					</div>
				</section>
			) : null}
		</>
	);
}
