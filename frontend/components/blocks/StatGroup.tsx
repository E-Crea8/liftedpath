import { Stat } from "@/components/blocks/Stat";
import type { Block } from "@/lib/wp/types";

export function StatGroup({ block }: { block: Block }) {
	const stats = (block.attributes.innerBlocks ?? []).filter((child) => child.name === "liftedpath/stat");
	if (stats.length === 0) {
		return null;
	}

	return (
		<section className="liftedpath-stat-group">
			<div className="liftedpath-stat-group__grid">
				{stats.map((child, index) => (
					<Stat block={child} key={`${child.name}-${index}`} />
				))}
			</div>
		</section>
	);
}
