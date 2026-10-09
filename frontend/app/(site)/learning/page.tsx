import { pageMetadata, WordPressPage } from "@/components/pages/WordPressPage";

export const instant = false;

export function generateMetadata() {
	return pageMetadata("/learning");
}

export default function LearningPage() {
	return <WordPressPage path="/learning" />;
}
