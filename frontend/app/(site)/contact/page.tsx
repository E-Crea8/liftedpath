import { pageMetadata, WordPressPage } from "@/components/pages/WordPressPage";

export function generateMetadata() {
	return pageMetadata("/contact");
}

export default function ContactPage() {
	return <WordPressPage path="/contact" />;
}
