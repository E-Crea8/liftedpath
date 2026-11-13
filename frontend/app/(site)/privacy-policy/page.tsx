import { pageMetadata, WordPressPage } from "@/components/pages/WordPressPage";

export const instant = false;

export function generateMetadata() {
	return pageMetadata("/privacy-policy");
}

export default function PrivacyPolicyPage() {
	return <WordPressPage path="/privacy-policy" />;
}
