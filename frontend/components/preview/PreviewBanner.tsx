import { draftMode } from "next/headers";

export async function PreviewBanner() {
	const { isEnabled } = await draftMode();
	if (!isEnabled) {
		return null;
	}

	return (
		<aside aria-label="Preview" className="preview-banner" role="status">
			<p>You are previewing unpublished changes.</p>
			<form action="/api/preview" method="post">
				<button type="submit">Exit preview</button>
			</form>
		</aside>
	);
}
