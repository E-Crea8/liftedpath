import { BlockList } from "@/components/blocks/BlockList";
import { PrivacyShell } from "@/components/pages/PrivacyShell";
import { PageSchema } from "@/components/seo/PageSchema";
import { SiteHold } from "@/components/layout/SiteHold";
import { PageMotion } from "@/components/motion/PageMotion";
import { WordPressPage } from "@/components/pages/WordPressPage";
import { imageIds } from "@/lib/blocks/fields";
import { getDiscovery, getMedia, getPage, getSettings } from "@/lib/wp/client";
import type { FeaturedImage } from "@/lib/wp/types";
import { notFound, unstable_rethrow } from "next/navigation";
import { WordpressError } from "@/lib/wp/types";

export async function PrivacyPolicyDocument() {
	let page;
	try {
		page = await getPage("/privacy-policy");
	} catch (error) {
		unstable_rethrow(error);
		if (isMissingPage(error)) {
			notFound();
		}
		return <SiteHold />;
	}

	const settings = await getSettings();
	const discovery = await getDiscovery();
	const media = await getMedia(imageIds(page.blocks));
	const images = new Map<number, FeaturedImage>(media.map((image) => [image.id, image]));
	const hero = page.blocks.filter((block) => block.name === "liftedpath/page-hero");
	const body = page.blocks.filter((block) => block.name !== "liftedpath/page-hero");

	if (body.length === 0) {
		return <WordPressPage path="/privacy-policy" />;
	}

	const shared = {
		address: settings.postalAddress,
		email: settings.email,
		images,
		motion: false as const,
		phone: settings.phone,
		phrases: settings.marqueePhrases,
	};

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
			<PageMotion>
				{hero.length > 0 ? <BlockList {...shared} blocks={hero} /> : null}
				<PrivacyShell>
					<BlockList {...shared} blocks={body} />
				</PrivacyShell>
			</PageMotion>
		</>
	);
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
