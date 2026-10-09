import { rows, text } from "@/lib/blocks/fields";
import type { Block } from "@/lib/wp/types";

export function Beliefs({ block }: { block: Block }) {
	const items = rows(block.attributes, "items")
		.map((row) => ({
			eyebrow: text(row.eyebrow),
			heading: text(row.heading),
			body: text(row.text),
		}))
		.filter((item) => item.eyebrow || item.heading || item.body);

	if (items.length === 0) {
		return null;
	}

	return (
		<section className="liftedpath-section liftedpath-beliefs">
			<div className="liftedpath-section__inner liftedpath-beliefs__grid">
				{items.map((item, index) => (
					<div key={`${item.heading}-${index}`}>
						{item.eyebrow ? <p className="liftedpath-eyebrow">{item.eyebrow}</p> : null}
						{item.heading ? <h2>{item.heading}</h2> : null}
						{item.body ? <p>{item.body}</p> : null}
					</div>
				))}
			</div>
		</section>
	);
}
