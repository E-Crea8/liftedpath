import { CountFigure } from "@/components/motion/CountFigure";
import { text } from "@/lib/blocks/fields";
import type { Block } from "@/lib/wp/types";

export function Stat({ block }: { block: Block }) {
	const figure = text(block.attributes.figure);
	const label = text(block.attributes.label);
	const detail = text(block.attributes.detail);

	if (!figure && !label && !detail) {
		return null;
	}

	return (
		<article className="liftedpath-stat">
			{figure ? <CountFigure className="liftedpath-stat__figure" value={figure} /> : null}
			{label ? <h3>{label}</h3> : null}
			{detail ? <p>{detail}</p> : null}
		</article>
	);
}
