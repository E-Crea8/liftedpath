import { notFound } from "next/navigation";
import { pageMetadata, WordPressPage } from "@/components/pages/WordPressPage";
import { childPath, childStaticParams } from "@/lib/wp/children";

export const instant = false;

export function generateStaticParams() {
	return childStaticParams("what-we-do");
}

export async function generateMetadata({ params }: { params: Promise<{ slug: string }> }) {
	const { slug } = await params;
	const path = childPath("what-we-do", slug);
	if (!path) {
		return {};
	}

	return pageMetadata(path);
}

export default async function WorkChildPage({ params }: { params: Promise<{ slug: string }> }) {
	const { slug } = await params;
	const path = childPath("what-we-do", slug);
	if (!path) {
		notFound();
	}

	return <WordPressPage path={path} />;
}
