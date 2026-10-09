import { pageMetadata, WordPressPage } from "@/components/pages/WordPressPage";

export const instant = false;

export function generateMetadata() {
	return pageMetadata("/impact");
}

export default function ImpactPage() {
	return <WordPressPage path="/impact" />;
}
