import { BlockIcon } from "@/components/blocks/icons";
import { rows, text } from "@/lib/blocks/fields";
import type { Block } from "@/lib/wp/types";

export function Faq({ block, radioKey }: { block: Block; radioKey: string }) {
	const eyebrow = text(block.attributes.eyebrow);
	const heading = text(block.attributes.heading);
	const items = rows(block.attributes, "items")
		.map((row) => ({ question: text(row.question), answer: text(row.answer) }))
		.filter((item) => item.question || item.answer);

	if (!heading && !eyebrow && items.length === 0) {
		return null;
	}

	return (
		<section className="liftedpath-section liftedpath-faq">
			<div className="liftedpath-section__inner">
				{eyebrow ? <p className="liftedpath-eyebrow">{eyebrow}</p> : null}
				{heading ? <h2>{heading}</h2> : null}
				{items.length > 0 ? (
					<div className="liftedpath-faq__list">
						{items.map((item, index) => (
							<details className="liftedpath-faq__item" key={`${item.question}-${index}`} name={`liftedpath-faq-${radioKey}`}>
								<summary>
									<span>{item.question || item.answer}</span>
									<span className="liftedpath-faq__chevron">
										<BlockIcon name="chevron" />
									</span>
								</summary>
								{item.answer && item.question ? <p>{item.answer}</p> : null}
							</details>
						))}
					</div>
				) : null}
			</div>
		</section>
	);
}
