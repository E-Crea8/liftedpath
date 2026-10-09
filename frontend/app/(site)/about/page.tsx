import { pageMetadata, WordPressPage } from "@/components/pages/WordPressPage";

export const instant = false;

export function generateMetadata() {
	return pageMetadata("/about");
}

export default function AboutPage() {
	return <WordPressPage path="/about" />;
}
