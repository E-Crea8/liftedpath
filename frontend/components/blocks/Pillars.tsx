import { rows, stepNumber, text } from "@/lib/blocks/fields";
import type { Block } from "@/lib/wp/types";

export function Pillars({ block }: { block: Block }) {
	const eyebrow = text(block.attributes.eyebrow);
	const heading = text(block.attributes.heading);
	const items = rows(block.attributes, "items")
		.map((row) => ({ title: text(row.title), body: text(row.text) }))
		.filter((item) => item.title || item.body);

	if (!heading && !eyebrow && items.length === 0) {
		return null;
	}

	return (
		<section className="liftedpath-section liftedpath-pillars">
			<div className="liftedpath-section__inner">
				{eyebrow || heading ? (
					<div className="liftedpath-pillars__heading">
						{eyebrow ? <p className="liftedpath-eyebrow">{eyebrow}</p> : null}
						{heading ? <h2>{heading}</h2> : null}
					</div>
				) : null}
				{items.length > 0 ? (
					<ol className="liftedpath-pillars__grid">
						{items.map((item, index) => (
							<li key={`${item.title}-${index}`}>
								<span className="liftedpath-pillars__index">{stepNumber(index)}</span>
								{item.title ? <h3>{item.title}</h3> : null}
								{item.body ? <p>{item.body}</p> : null}
							</li>
						))}
					</ol>
				) : null}
			</div>
		</section>
	);
}
