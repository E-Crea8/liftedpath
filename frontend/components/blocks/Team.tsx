import type { CSSProperties } from "react";
import { BlockPhoto } from "@/components/blocks/parts";
import { imageId, rows, text } from "@/lib/blocks/fields";
import type { Block, FeaturedImage, JsonValue } from "@/lib/wp/types";

function hexColor(value: JsonValue | Block[] | undefined): string {
	const color = text(value);
	return /^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/.test(color) ? color : "";
}

export function Team({
	block,
	images,
}: {
	block: Block;
	images: ReadonlyMap<number, FeaturedImage>;
}) {
	const eyebrow = text(block.attributes.eyebrow);
	const heading = text(block.attributes.heading);
	const members = rows(block.attributes, "members")
		.map((row) => ({
			image: imageId(row.image),
			name: text(row.name),
			position: text(row.position),
		}))
		.filter((member) => member.image || member.name || member.position)
		.slice(0, 4);
	const columns = String(Math.min(4, Math.max(1, members.length)));
	const style = {
		...(hexColor(block.attributes.overlay_color) ? { "--team-overlay": hexColor(block.attributes.overlay_color) } : {}),
		...(hexColor(block.attributes.name_color) ? { "--team-name": hexColor(block.attributes.name_color) } : {}),
		...(hexColor(block.attributes.position_color) ? { "--team-role": hexColor(block.attributes.position_color) } : {}),
	} as CSSProperties;

	if (!heading && !eyebrow && members.length === 0) {
		return null;
	}

	return (
		<section className="liftedpath-section liftedpath-team" style={style}>
			<div className="liftedpath-section__inner">
				{eyebrow || heading ? (
					<div className="liftedpath-team__heading">
						{eyebrow ? <p className="liftedpath-eyebrow">{eyebrow}</p> : null}
						{heading ? <h2>{heading}</h2> : null}
					</div>
				) : null}
				{members.length > 0 ? (
					<ul className="liftedpath-team__grid" data-columns={columns}>
						{members.map((member, index) => {
							const photo = member.image !== null && images.has(member.image) ? member.image : null;
							const initial = member.name ? member.name.charAt(0).toUpperCase() : "";

							return (
								<li key={`${member.name}-${index}`}>
									<article className="liftedpath-team__card">
										<div className="liftedpath-team__media">
											{photo ? (
												<BlockPhoto alt={member.name || undefined} className="liftedpath-team__photo" id={photo} images={images} />
											) : (
												<span aria-hidden="true" className="liftedpath-team__fallback">
													{initial}
												</span>
											)}
										</div>
										<div className="liftedpath-team__info">
											{member.name ? <h3>{member.name}</h3> : null}
											{member.position ? <p className="liftedpath-team__role">{member.position}</p> : null}
										</div>
									</article>
								</li>
							);
						})}
					</ul>
				) : null}
			</div>
		</section>
	);
}
