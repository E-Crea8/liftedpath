import { jsonLdScript } from "@/lib/seo/json";

export function PageSchema({
	description,
	path,
	siteUrl,
	title,
}: {
	description: string;
	path: string;
	siteUrl: string;
	title: string;
}) {
	if (!siteUrl) {
		return null;
	}

	const url = new URL(path, siteUrl).toString();
	const page: Record<string, unknown> = {
		"@context": "https://schema.org",
		"@type": "WebPage",
		"@id": `${url}#webpage`,
		url,
		name: title,
		isPartOf: { "@id": `${siteUrl}/#website` },
		about: { "@id": `${siteUrl}/#organization` },
	};

	if (description) {
		page.description = description;
	}

	return <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: jsonLdScript(page) }} />;
}
