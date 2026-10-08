import { pageMetadata, WordPressPage } from "@/components/pages/WordPressPage";

export function generateMetadata() {
	return pageMetadata("/impact");
}

export default function ImpactPage() {
	return <WordPressPage path="/impact" />;
}
