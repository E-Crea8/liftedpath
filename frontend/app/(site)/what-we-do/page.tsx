import { pageMetadata, WordPressPage } from "@/components/pages/WordPressPage";

export const instant = false;

export function generateMetadata() {
	return pageMetadata("/what-we-do");
}

export default function WhatWeDoPage() {
	return <WordPressPage path="/what-we-do" />;
}
