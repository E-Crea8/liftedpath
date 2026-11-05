import type { Metadata } from "next";
import { notFound, unstable_rethrow } from "next/navigation";
import { BlockList } from "@/components/blocks/BlockList";
import { SiteHold } from "@/components/layout/SiteHold";
import { PageSchema } from "@/components/seo/PageSchema";
import { imageIds } from "@/lib/blocks/fields";
import type { FormConfig } from "@/lib/forms";
import { getDiscovery, getMedia, getPage, getSettings } from "@/lib/wp/client";
import type { FeaturedImage, Page } from "@/lib/wp/types";
import { WordpressError } from "@/lib/wp/types";

export async function pageMetadata(path: string): Promise<Metadata> {
	const page = await readPage(path);
	if (page === "hold") {
		return { title: { absolute: "LiftedPath" } };
	}
	const settings = await getSettings();
	const title = page.seoTitle || page.title;
	const description = page.seoDescription || undefined;
	const image = openGraphImage(page.seoImage);
	const metadata: Metadata = {
		title,
		description,
		alternates: {
			canonical: page.path,
		},
		openGraph: {
			title,
			description,
			url: page.path,
			siteName: settings.shortName || "LiftedPath",
			type: "website",
			images: image,
		},
		twitter: {
			card: image ? "summary_large_image" : "summary",
			title,
			description,
			images: image?.map((item) => item.url),
		},
	};

	if (page.seoNoindex || page.status !== "publish") {
		metadata.robots = { index: false, follow: false };
	}

	return metadata;
}

function openGraphImage(image: FeaturedImage | null) {
	if (!image) {
		return undefined;
	}

	return [
		{
			url: image.url,
			alt: image.alt || undefined,
			width: image.width || undefined,
			height: image.height || undefined,
		},
	];
}

export async function WordPressPage({ form, path }: { form?: FormConfig; path: string }) {
	const page = await readPage(path);
	if (page === "hold") {
		return <SiteHold />;
	}
	const settings = await getSettings();
	const discovery = await getDiscovery();
	const media = await getMedia(imageIds(page.blocks));
	const images = new Map<number, FeaturedImage>(media.map((image) => [image.id, image]));

	return (
		<>
			<PageSchema
				blocks={page.blocks}
				description={page.seoDescription}
				images={images}
				path={page.path}
				siteUrl={discovery.siteUrl}
				title={page.seoTitle || page.title}
			/>
			<BlockList
				address={settings.postalAddress}
				blocks={page.blocks}
				contactNotices={form?.contact}
				donateNotices={form?.donate}
				email={settings.email}
				images={images}
				paymentsConfigured={form?.paymentsConfigured ?? false}
				phone={settings.phone}
				phrases={settings.marqueePhrases}
				providerLabel={form?.providerLabel ?? ""}
			/>
		</>
	);
}

async function readPage(path: string): Promise<Page | "hold"> {
	try {
		return await getPage(path);
	} catch (error) {
		unstable_rethrow(error);
		if (isMissingPage(error)) {
			notFound();
		}
		return "hold";
	}
}

function isMissingPage(error: unknown): boolean {
	if (error instanceof WordpressError) {
		return error.status === 404;
	}

	if (typeof error !== "object" || error === null) {
		return false;
	}

	const record = error as { code?: unknown; message?: unknown; name?: unknown; status?: unknown };
	if (record.status === 404 || record.code === "page_not_found") {
		return true;
	}

	return record.name === "WordpressError" && record.message === "No published page exists at that path.";
}
