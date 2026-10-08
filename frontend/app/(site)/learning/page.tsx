import { pageMetadata, WordPressPage } from "@/components/pages/WordPressPage";

export function generateMetadata() {
	return pageMetadata("/learning");
}

export default function LearningPage() {
	return <WordPressPage path="/learning" />;
}
