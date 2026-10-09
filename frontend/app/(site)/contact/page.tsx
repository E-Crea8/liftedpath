import { pageMetadata, WordPressPage } from "@/components/pages/WordPressPage";
import { getFormConfig } from "@/lib/forms";

export const instant = false;

export function generateMetadata() {
	return pageMetadata("/contact");
}

export default async function ContactPage() {
	const form = await getFormConfig();
	return <WordPressPage form={form} path="/contact" />;
}
