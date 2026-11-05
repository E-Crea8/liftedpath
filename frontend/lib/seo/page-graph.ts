import { rows, text } from "@/lib/blocks/fields";
import type { Block, FeaturedImage } from "@/lib/wp/types";

export interface FaqSchemaItem {
	question: string;
	answer: string;
}

export interface PersonSchemaItem {
	name: string;
	position: string;
	imageUrl?: string;
	isFounder: boolean;
}

const KNOWS_ABOUT = [
	"education empowerment",
	"scholarships and school sponsorship",
	"vocational training",
	"mentorship and coaching",
	"school partnerships in Nigeria",
	"government and institutional education partnerships",
	"orphanage outreach",
	"community initiatives in Lagos, Nigeria",
	"out-of-school children support",
	"inclusive education",
	"skills for employability",
	"access to quality education in Nigeria",
	"TVET and youth skills development",
	"transparent education philanthropy",
];

export function organizationKnowsAbout(): string[] {
	return [...KNOWS_ABOUT];
}

export function faqItemsFromBlocks(blocks: Block[]): FaqSchemaItem[] {
	const items: FaqSchemaItem[] = [];

	for (const block of walkBlocks(blocks)) {
		if (block.name === "liftedpath/faq") {
			for (const row of rows(block.attributes, "items")) {
				const question = text(row.question);
				const answer = text(row.answer);
				if (question && answer) {
					items.push({ question, answer });
				}
			}
			continue;
		}

		if (block.name === "liftedpath/work-layout") {
			for (const row of rows(block.attributes, "faq_items")) {
				const question = text(row.question);
				const answer = text(row.answer);
				if (question && answer) {
					items.push({ question, answer });
				}
			}
		}
	}

	return items;
}

export function peopleFromBlocks(blocks: Block[], images: Map<number, FeaturedImage>): PersonSchemaItem[] {
	const people: PersonSchemaItem[] = [];

	for (const block of walkBlocks(blocks)) {
		if (block.name !== "liftedpath/team") {
			continue;
		}

		for (const row of rows(block.attributes, "members")) {
			const name = text(row.name);
			if (!name) {
				continue;
			}
			const position = text(row.position);
			const imageId = typeof row.image === "number" ? row.image : Number(row.image);
			const image = Number.isFinite(imageId) ? images.get(imageId) : undefined;
			people.push({
				name,
				position,
				imageUrl: image?.url || undefined,
				isFounder: /founder/i.test(position),
			});
		}
	}

	return people;
}

export function personId(siteUrl: string, name: string): string {
	return `${siteUrl}/#person-${slugifyPerson(name)}`;
}

export function enrichPostalAddress(postalAddress: string): Record<string, string> | null {
	const street = postalAddress.trim();
	if (!street) {
		return null;
	}

	const address: Record<string, string> = {
		"@type": "PostalAddress",
		streetAddress: street,
	};

	if (/lagos/i.test(street)) {
		address.addressLocality = "Lagos";
		address.addressRegion = "Lagos State";
	}
	if (/nigeria/i.test(street)) {
		address.addressCountry = "NG";
	}

	return address;
}

export function areaServedNodes(areaServed: string): Array<Record<string, string>> {
	const nodes: Array<Record<string, string>> = [
		{
			"@type": "Country",
			name: "Nigeria",
		},
		{
			"@type": "City",
			name: "Lagos",
		},
	];

	const custom = areaServed.trim();
	if (custom && !/^lagos/i.test(custom) && !/^nigeria$/i.test(custom)) {
		nodes.unshift({
			"@type": "AdministrativeArea",
			name: custom,
		});
	}

	return nodes;
}

export function breadcrumbList(siteUrl: string, path: string, title: string): Record<string, unknown> {
	const origin = siteUrl.replace(/\/$/, "");
	const items: Array<Record<string, unknown>> = [
		{
			"@type": "ListItem",
			position: 1,
			name: "Home",
			item: `${origin}/`,
		},
	];

	const segments = path.split("/").filter(Boolean);
	let cursor = "";
	segments.forEach((segment, index) => {
		cursor += `/${segment}`;
		const isLast = index === segments.length - 1;
		items.push({
			"@type": "ListItem",
			position: index + 2,
			name: isLast ? title : labelForSegment(segment),
			item: `${origin}${cursor}`,
		});
	});

	return {
		"@type": "BreadcrumbList",
		"@id": `${origin}${path === "/" ? "" : path}#breadcrumb`,
		itemListElement: items,
	};
}

function walkBlocks(blocks: Block[]): Block[] {
	const list: Block[] = [];
	for (const block of blocks) {
		list.push(block);
		const nested = block.attributes.innerBlocks;
		if (Array.isArray(nested) && nested.length > 0) {
			list.push(...walkBlocks(nested));
		}
	}
	return list;
}

function slugifyPerson(name: string): string {
	return name
		.toLowerCase()
		.replace(/[^a-z0-9]+/g, "-")
		.replace(/^-+|-+$/g, "")
		.replace(/-{2,}/g, "-");
}

function labelForSegment(segment: string): string {
	return segment
		.split("-")
		.map((part) => part.charAt(0).toUpperCase() + part.slice(1))
		.join(" ");
}
