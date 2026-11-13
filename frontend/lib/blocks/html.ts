const NAMED: Record<string, string> = {
	amp: "&",
	lt: "<",
	gt: ">",
	quot: '"',
	apos: "'",
	nbsp: " ",
};

export function richText(html: string): string {
	const stripped = html.replace(/<[^>]+>/g, " ");
	return decodeHtml(stripped).replace(/\s+/g, " ").trim();
}

/** Inner markup of the first paragraph, limited to safe inline tags. */
export function paragraphHtml(html: string): string {
	const match = /<p\b[^>]*>([\s\S]*?)<\/p>/i.exec(html);
	const inner = match?.[1] ?? html;
	return sanitizeInline(inner);
}

export function listItems(html: string): string[] {
	return [...html.matchAll(/<li\b[^>]*>([\s\S]*?)<\/li>/gi)]
		.map((match) => richText(match[1] ?? ""))
		.filter((item) => item !== "");
}

function sanitizeInline(value: string): string {
	const cleaned = value
		.replace(/<\/?(script|style|iframe|object|embed)\b[^>]*>/gi, "")
		.replace(/\son\w+\s*=\s*("[^"]*"|'[^']*'|[^\s>]+)/gi, "");

	return cleaned
		.replace(/<\/?([a-z0-9-]+)(\s[^>]*)?>/gi, (full, tag: string, attrs = "") => {
			const name = tag.toLowerCase();
			if (name === "br") {
				return "<br />";
			}
			if (name === "strong" || name === "em") {
				return full.startsWith("</") ? `</${name}>` : `<${name}>`;
			}
			if (name === "a") {
				if (full.startsWith("</")) {
					return "</a>";
				}
				const href = /\shref\s*=\s*(?:"([^"]*)"|'([^']*)')/i.exec(attrs)?.[1]
					?? /\shref\s*=\s*(?:"([^"]*)"|'([^']*)')/i.exec(attrs)?.[2]
					?? "";
				if (!/^(https?:|mailto:|tel:|#\/?)/i.test(href)) {
					return "";
				}
				return `<a href="${href}">`;
			}
			return "";
		})
		.replace(/\s+/g, " ")
		.trim();
}

export function headingLevel(html: string): 1 | 2 | 3 | 4 | 5 | 6 {
	const match = /<h([1-6])\b/i.exec(html);
	const level = match ? Number(match[1]) : 2;
	if (level === 1 || level === 2 || level === 3 || level === 4 || level === 5 || level === 6) {
		return level;
	}
	return 2;
}

export function imageAlt(html: string): string {
	const tag = /<img\b[^>]*>/i.exec(html)?.[0];
	if (!tag) {
		return "";
	}
	const match = /\salt\s*=\s*(?:"([^"]*)"|'([^']*)')/i.exec(tag);
	return decodeHtml(match?.[1] ?? match?.[2] ?? "");
}

function decodeHtml(value: string): string {
	return value
		.replace(/&#(\d+);/g, (_, code: string) => String.fromCodePoint(Number(code)))
		.replace(/&#x([0-9a-f]+);/gi, (_, code: string) => String.fromCodePoint(Number.parseInt(code, 16)))
		.replace(/&([a-z]+);/gi, (entity, name: string) => NAMED[name.toLowerCase()] ?? entity);
}
