import { Suspense } from "react";
import { AboutTabs } from "@/components/blocks/AboutTabs";
import { ActionGrid } from "@/components/blocks/ActionGrid";
import { ContactForm } from "@/components/blocks/ContactForm";
import { CoreContent } from "@/components/blocks/CoreContent";
import { CtaBand } from "@/components/blocks/CtaBand";
import { DonatePanel } from "@/components/blocks/DonatePanel";
import { Faq } from "@/components/blocks/Faq";
import { Goals } from "@/components/blocks/Goals";
import { Hero } from "@/components/blocks/Hero";
import { Marquee } from "@/components/blocks/Marquee";
import { PageHero } from "@/components/blocks/PageHero";
import { Pillars } from "@/components/blocks/Pillars";
import { Process } from "@/components/blocks/Process";
import { ProgramCards } from "@/components/blocks/ProgramCards";
import { SponsorCallout } from "@/components/blocks/SponsorCallout";
import { Stat } from "@/components/blocks/Stat";
import { StatGroup } from "@/components/blocks/StatGroup";
import { Stewardship } from "@/components/blocks/Stewardship";
import { PageMotion } from "@/components/motion/PageMotion";
import { siteHost } from "@/lib/blocks/fields";
import type { Block, FeaturedImage } from "@/lib/wp/types";

export function BlockList({
	address,
	blocks,
	email,
	images,
	paymentsConfigured = false,
	phone,
	phrases,
	providerLabel = "",
}: {
	address: string;
	blocks: Block[];
	email: string;
	images: ReadonlyMap<number, FeaturedImage>;
	paymentsConfigured?: boolean;
	phone: string;
	phrases: readonly string[];
	providerLabel?: string;
}) {
	const host = siteHost();

	return (
		<PageMotion>
			{blocks.map((block, index) => (
				<BlockView
					address={address}
					block={block}
					email={email}
					images={images}
					key={`${block.name}-${index}`}
					paymentsConfigured={paymentsConfigured}
					phone={phone}
					phrases={phrases}
					providerLabel={providerLabel}
					radioKey={String(index)}
					siteHost={host}
				/>
			))}
		</PageMotion>
	);
}

function BlockView({
	address,
	block,
	email,
	images,
	paymentsConfigured,
	phone,
	phrases,
	providerLabel,
	radioKey,
	siteHost: host,
}: {
	address: string;
	block: Block;
	email: string;
	images: ReadonlyMap<number, FeaturedImage>;
	paymentsConfigured: boolean;
	phone: string;
	phrases: readonly string[];
	providerLabel: string;
	radioKey: string;
	siteHost: string;
}) {
	switch (block.name) {
		case "liftedpath/hero":
			return <Hero block={block} images={images} siteHost={host} />;
		case "liftedpath/page-hero":
			return <PageHero block={block} images={images} />;
		case "liftedpath/stat":
			return <Stat block={block} />;
		case "liftedpath/stat-group":
			return <StatGroup block={block} />;
		case "liftedpath/marquee":
			return <Marquee block={block} phrases={phrases} />;
		case "liftedpath/cta-band":
			return <CtaBand block={block} images={images} siteHost={host} />;
		case "liftedpath/about-tabs":
			return <AboutTabs block={block} images={images} radioKey={radioKey} siteHost={host} />;
		case "liftedpath/program-cards":
			return <ProgramCards block={block} images={images} siteHost={host} />;
		case "liftedpath/action-grid":
			return <ActionGrid block={block} siteHost={host} />;
		case "liftedpath/sponsor-callout":
			return <SponsorCallout block={block} siteHost={host} />;
		case "liftedpath/stewardship":
			return <Stewardship block={block} images={images} phone={phone} siteHost={host} />;
		case "liftedpath/process":
			return <Process block={block} siteHost={host} />;
		case "liftedpath/pillars":
			return <Pillars block={block} />;
		case "liftedpath/goals":
			return <Goals block={block} />;
		case "liftedpath/faq":
			return <Faq block={block} radioKey={radioKey} />;
		case "liftedpath/contact-form":
			return <ContactForm address={address} block={block} email={email} phone={phone} />;
		case "liftedpath/donate-panel":
			return (
				<Suspense fallback={null}>
					<DonatePanel block={block} images={images} paymentsConfigured={paymentsConfigured} providerLabel={providerLabel} />
				</Suspense>
			);
		case "core/heading":
		case "core/paragraph":
		case "core/image":
		case "core/columns":
			return <CoreContent block={block} images={images} />;
		default:
			return null;
	}
}
