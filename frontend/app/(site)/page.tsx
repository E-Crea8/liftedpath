import { pageMetadata, WordPressPage } from "@/components/pages/WordPressPage";

export const instant = false;

export function generateMetadata() {
	return pageMetadata("/");
}

export default function HomePage() {
	return <WordPressPage path="/" />;
}
