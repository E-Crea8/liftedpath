import type { Discovery, SiteSettings } from "@/lib/wp/types";

export function llmsText(settings: SiteSettings, discovery: Discovery): string {
	const name = settings.legalName || settings.shortName || "LiftedPath";
	const lines: string[] = [`# ${name}`, ""];

	if (discovery.geoSummary) {
		lines.push(`> ${discovery.geoSummary}`, "");
	}

	lines.push("## Organization", "");
	pushFact(lines, "Short name", settings.shortName);
	pushFact(lines, "Tagline", settings.tagline);
	pushFact(lines, "Registration", settings.registrationNumber);
	pushFact(lines, "Area served", discovery.areaServed);
	pushFact(lines, "Address", settings.postalAddress);
	pushFact(lines, "Phone", settings.phone);
	pushFact(lines, "Email", settings.email);

	if (discovery.sameAs.length > 0) {
		lines.push("", "Profiles");
		for (const url of discovery.sameAs) {
			lines.push(`- ${url}`);
		}
	}

	lines.push("", "## Pages", "");
	for (const page of discovery.pages) {
		if (!page.indexable) {
			continue;
		}

		const url = new URL(page.path, discovery.siteUrl).toString();
		const description = page.description ? `: ${page.description}` : "";
		lines.push(`- [${page.title}](${url})${description}`);
	}

	lines.push("");
	return lines.join("\n");
}

function pushFact(lines: string[], label: string, value: string) {
	if (value) {
		lines.push(`- ${label}: ${value}`);
	}
}
