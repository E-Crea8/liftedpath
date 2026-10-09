import type { JSX } from "react";
import { BlockPhoto } from "@/components/blocks/parts";
import { headingLevel, imageAlt, richText } from "@/lib/blocks/html";
import { blockHidden, imageId } from "@/lib/blocks/fields";
import type { Block, FeaturedImage } from "@/lib/wp/types";

const HEADINGS: Record<1 | 2 | 3 | 4 | 5 | 6, keyof JSX.IntrinsicElements> = {
	1: "h1",
	2: "h2",
	3: "h3",
	4: "h4",
	5: "h5",
	6: "h6",
};

export function CoreContent({
	block,
	images,
	nested = false,
}: {
	block: Block;
	images: ReadonlyMap<number, FeaturedImage>;
	nested?: boolean;
}) {
	if (blockHidden(block)) {
		return null;
	}

	if (block.name === "core/columns") {
		const columns = block.attributes.innerBlocks ?? [];
		if (columns.length === 0) {
			return null;
		}
		return (
			<div className="liftedpath-copy">
				<div className="liftedpath-columns">
					{columns.map((column, index) => (
						<div className="liftedpath-column" key={`${column.name}-${index}`}>
							{(column.attributes.innerBlocks ?? []).map((child, childIndex) => (
								<CoreContent
									block={child}
									images={images}
									key={`${child.name}-${childIndex}`}
									nested
								/>
							))}
						</div>
					))}
				</div>
			</div>
		);
	}

	if (block.name === "core/heading") {
		const copy = richText(block.html);
		if (!copy) {
			return null;
		}
		const Heading = HEADINGS[headingLevel(block.html)];
		const node = <Heading>{copy}</Heading>;
		return nested ? node : <div className="liftedpath-copy">{node}</div>;
	}

	if (block.name === "core/paragraph") {
		const copy = richText(block.html);
		if (!copy) {
			return null;
		}
		const node = <p>{copy}</p>;
		return nested ? node : <div className="liftedpath-copy">{node}</div>;
	}

	if (block.name === "core/image") {
		const id = imageId(block.attributes.id);
		if (!id || !images.has(id)) {
			return null;
		}
		const figure = (
			<figure>
				<BlockPhoto alt={imageAlt(block.html)} className="liftedpath-copy__image" id={id} images={images} />
			</figure>
		);
		return nested ? figure : <div className="liftedpath-copy">{figure}</div>;
	}

	return null;
}
