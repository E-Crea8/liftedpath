import { rows, stepNumber, text } from "@/lib/blocks/fields";
import type { Block } from "@/lib/wp/types";

export function Goals({ block }: { block: Block }) {
	const eyebrow = text(block.attributes.eyebrow);
	const heading = text(block.attributes.heading);
	const intro = text(block.attributes.intro);
	const goals = rows(block.attributes, "goals")
		.map((row) => ({ title: text(row.title), body: text(row.text) }))
		.filter((goal) => goal.title || goal.body);

	if (!heading && !eyebrow && !intro && goals.length === 0) {
		return null;
	}

	return (
		<section className="liftedpath-section liftedpath-goals">
			<div className="liftedpath-section__inner liftedpath-goals__layout">
				<div>
					{eyebrow ? <p className="liftedpath-eyebrow">{eyebrow}</p> : null}
					{heading ? <h2>{heading}</h2> : null}
					{intro ? <p className="liftedpath-lead">{intro}</p> : null}
				</div>
				{goals.length > 0 ? (
					<ol className="liftedpath-goals__list">
						{goals.map((goal, index) => (
							<li key={`${goal.title}-${index}`}>
								<span className="liftedpath-goals__index">{stepNumber(index)}</span>
								{goal.title ? <h3>{goal.title}</h3> : null}
								{goal.body ? <p>{goal.body}</p> : null}
							</li>
						))}
					</ol>
				) : null}
			</div>
		</section>
	);
}
