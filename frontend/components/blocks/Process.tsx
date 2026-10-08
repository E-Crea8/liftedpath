import { GoldButton } from "@/components/blocks/parts";
import { fieldLink, rows, stepNumber, text } from "@/lib/blocks/fields";
import type { Block } from "@/lib/wp/types";

export function Process({ block, siteHost }: { block: Block; siteHost: string }) {
	const eyebrow = text(block.attributes.eyebrow);
	const heading = text(block.attributes.heading);
	const button = fieldLink(block.attributes.button, siteHost);
	const steps = rows(block.attributes, "steps")
		.map((row) => ({
			title: text(row.title),
			body: text(row.text),
		}))
		.filter((step) => step.title || step.body);

	if (!heading && !eyebrow && !button && steps.length === 0) {
		return null;
	}

	return (
		<section className="liftedpath-section liftedpath-process">
			<div className="liftedpath-section__inner">
				<div className="liftedpath-process__heading">
					<div>
						{eyebrow ? <p className="liftedpath-eyebrow">{eyebrow}</p> : null}
						{heading ? <h2>{heading}</h2> : null}
					</div>
					<GoldButton link={button} />
				</div>
				{steps.length > 0 ? (
					<ol className="liftedpath-process__grid">
						{steps.map((step, index) => (
							<li key={`${step.title}-${index}`}>
								<span>{stepNumber(index)}</span>
								{step.title ? <h3>{step.title}</h3> : null}
								{step.body ? <p>{step.body}</p> : null}
							</li>
						))}
					</ol>
				) : null}
			</div>
		</section>
	);
}
