import { pageMetadata, WordPressPage } from "@/components/pages/WordPressPage";

export function generateMetadata() {
	return pageMetadata("/about");
}

export default function AboutPage() {
	return <WordPressPage path="/about" />;
}
