import { pageMetadata, WordPressPage } from "@/components/pages/WordPressPage";
import { getFormConfig } from "@/lib/forms";

export const instant = false;

export function generateMetadata() {
	return pageMetadata("/donate");
}

export default async function DonatePage() {
	const form = await getFormConfig();
	return <WordPressPage form={form} path="/donate" />;
}
