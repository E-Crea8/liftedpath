import { pageMetadata, WordPressPage } from "@/components/pages/WordPressPage";

export const instant = false;

export function generateMetadata() {
	return pageMetadata("/contact");
}

export default function ContactPage() {
	return <WordPressPage path="/contact" />;
}
