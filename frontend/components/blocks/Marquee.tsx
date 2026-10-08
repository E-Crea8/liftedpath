import { Fragment } from "react";
import { rows, text } from "@/lib/blocks/fields";
import type { Block } from "@/lib/wp/types";

export function Marquee({ block, phrases }: { block: Block; phrases: readonly string[] }) {
	const fromBlock = rows(block.attributes, "phrases")
		.map((row) => text(row.phrase))
		.filter((phrase) => phrase !== "");
	const line = fromBlock.length > 0 ? fromBlock : phrases;

	if (line.length === 0) {
		return null;
	}

	return (
		<div className="liftedpath-marquee">
			<div className="liftedpath-marquee__track">
				<PhraseGroup phrases={line} />
				<PhraseGroup hidden phrases={line} />
			</div>
		</div>
	);
}

function PhraseGroup({ hidden = false, phrases }: { hidden?: boolean; phrases: readonly string[] }) {
	return (
		<p aria-hidden={hidden ? true : undefined} className="liftedpath-marquee__group">
			{phrases.map((phrase, index) => (
				<Fragment key={`${phrase}-${index}`}>
					<span className="liftedpath-marquee__phrase">{phrase}</span>
					<span aria-hidden="true" className="liftedpath-marquee__star">
						✦
					</span>
				</Fragment>
			))}
		</p>
	);
}
