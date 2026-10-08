const BLOCKED_ELEMENT =
	/<\s*(script|style|iframe|object|embed)\b[^>]*>[\s\S]*?<\s*\/\s*\1\s*>/gi;
const BLOCKED_TAG =
	/<\s*\/?\s*(script|style|iframe|object|embed|link|meta|base)\b[^>]*\/?\s*>/gi;
const EVENT_HANDLER = /\s+on[a-z]+\s*=\s*("[^"]*"|'[^']*'|[^\s>]+)/gi;
const UNSAFE_URL =
	/(\s(?:href|src|xlink:href)\s*=\s*)(["'])\s*(?:javascript|data):[^"']*\2/gi;

export function sanitizeHtml(html: string): string {
	return html
		.replace(BLOCKED_ELEMENT, "")
		.replace(BLOCKED_TAG, "")
		.replace(EVENT_HANDLER, "")
		.replace(UNSAFE_URL, "$1$2$2");
}
