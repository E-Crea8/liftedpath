import {
	breadcrumbList,
	faqItemsFromBlocks,
	peopleFromBlocks,
	personId,
} from "@/lib/seo/page-graph";
import { jsonLdScript } from "@/lib/seo/json";
import type { Block, FeaturedImage } from "@/lib/wp/types";

export function PageSchema({
	blocks = [],
	description,
	images = new Map(),
	path,
	siteUrl,
	title,
}: {
	blocks?: Block[];
	description: string;
	images?: Map<number, FeaturedImage>;
	path: string;
	siteUrl: string;
	title: string;
}) {
	if (!siteUrl) {
		return null;
	}

	const origin = siteUrl.replace(/\/$/, "");
	const url = new URL(path, `${origin}/`).toString();
	const organizationId = `${origin}/#organization`;
	const webpageId = `${url}#webpage`;
	const graph: Array<Record<string, unknown>> = [
		{
			"@type": "WebPage",
			"@id": webpageId,
			url,
			name: title,
			isPartOf: { "@id": `${origin}/#website` },
			about: { "@id": organizationId },
			...(description ? { description } : {}),
		},
	];

	if (path !== "/") {
		graph.push(breadcrumbList(origin, path, title));
	}

	const faqItems = faqItemsFromBlocks(blocks);
	if (faqItems.length > 0) {
		graph.push({
			"@type": "FAQPage",
			"@id": `${url}#faq`,
			url,
			mainEntity: faqItems.map((item) => ({
				"@type": "Question",
				name: item.question,
				acceptedAnswer: {
					"@type": "Answer",
					text: item.answer,
				},
			})),
			isPartOf: { "@id": webpageId },
			about: { "@id": organizationId },
		});
	}

	const people = peopleFromBlocks(blocks, images);
	if (people.length > 0) {
		const founders = people.filter((person) => person.isFounder).map((person) => ({
			"@id": personId(origin, person.name),
		}));
		const employees = people.map((person) => ({
			"@id": personId(origin, person.name),
		}));

		graph.push({
			"@type": "NGO",
			"@id": organizationId,
			...(founders.length > 0 ? { founder: founders.length === 1 ? founders[0] : founders } : {}),
			employee: employees,
		});

		for (const person of people) {
			const node: Record<string, unknown> = {
				"@type": "Person",
				"@id": personId(origin, person.name),
				name: person.name,
				worksFor: { "@id": organizationId },
			};
			if (person.position) {
				node.jobTitle = person.position;
			}
			if (person.imageUrl) {
				node.image = person.imageUrl;
			}
			graph.push(node);
		}
	}

	return (
		<script
			type="application/ld+json"
			dangerouslySetInnerHTML={{
				__html: jsonLdScript({
					"@context": "https://schema.org",
					"@graph": graph,
				}),
			}}
		/>
	);
}
