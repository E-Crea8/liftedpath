import { FieldAnchor } from "@/components/blocks/parts";
import { CountFigure } from "@/components/motion/CountFigure";
import { fieldLink, text } from "@/lib/blocks/fields";
import type { Block } from "@/lib/wp/types";

export function SponsorCallout({ block, siteHost }: { block: Block; siteHost: string }) {
	const figure = text(block.attributes.figure);
	const body = text(block.attributes.text);
	const link = fieldLink(block.attributes.link, siteHost);

	if (!figure && !body && !link) {
		return null;
	}

	return (
		<section className="liftedpath-section liftedpath-sponsor">
			<div className="liftedpath-section__inner">
				<p className="liftedpath-sponsor__row">
					{figure ? <CountFigure value={figure} /> : null}
					{body ? <span>{body}</span> : null}
					{link ? (
						<FieldAnchor className="liftedpath-text-link" link={link}>
							{link.title}
						</FieldAnchor>
					) : null}
				</p>
			</div>
		</section>
	);
}
