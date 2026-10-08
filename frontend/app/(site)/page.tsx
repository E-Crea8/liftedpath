import { pageMetadata, WordPressPage } from "@/components/pages/WordPressPage";

export function generateMetadata() {
	return pageMetadata("/");
}

export default function HomePage() {
	return <WordPressPage path="/" />;
}
