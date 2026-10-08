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
