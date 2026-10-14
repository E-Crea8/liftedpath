import { Suspense, type CSSProperties, type ReactNode } from "react";
import { AboutStory } from "@/components/blocks/AboutStory";
import { BlockBackgroundVideo } from "@/components/blocks/BlockBackground";
import { AboutTabs } from "@/components/blocks/AboutTabs";
import { Beliefs } from "@/components/blocks/Beliefs";
import { ActionGrid } from "@/components/blocks/ActionGrid";
import { ContactForm } from "@/components/blocks/ContactForm";
import { CoreContent } from "@/components/blocks/CoreContent";
import { CtaBand } from "@/components/blocks/CtaBand";
import { DonatePanel } from "@/components/blocks/DonatePanel";
import { Faq } from "@/components/blocks/Faq";
import { Gallery } from "@/components/blocks/Gallery";
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
import { Team } from "@/components/blocks/Team";
import { PageMotion } from "@/components/motion/PageMotion";
import { blockHidden, blockSurface, headingWeight, layoutClass, siteHost } from "@/lib/blocks/fields";
import type { ContactNotices, DonateNotices } from "@/lib/forms";
import type { Block, FeaturedImage } from "@/lib/wp/types";

export function BlockList({
	address,
	blocks,
	contactNotices,
	donateNotices,
	email,
	images,
	paymentsConfigured = false,
	phone,
	phrases,
	providerLabel = "",
}: {
	address: string;
	blocks: Block[];
	contactNotices?: ContactNotices;
	donateNotices?: DonateNotices;
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
					contactNotices={contactNotices}
					donateNotices={donateNotices}
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
	contactNotices,
	donateNotices,
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
	contactNotices?: ContactNotices;
	donateNotices?: DonateNotices;
	email: string;
	images: ReadonlyMap<number, FeaturedImage>;
	paymentsConfigured: boolean;
	phone: string;
	phrases: readonly string[];
	providerLabel: string;
	radioKey: string;
	siteHost: string;
}) {
	if (blockHidden(block)) {
		return null;
	}

	let view: ReactNode = null;

	switch (block.name) {
		case "liftedpath/hero":
			view = <Hero block={block} images={images} siteHost={host} />;
			break;
		case "liftedpath/page-hero":
			view = <PageHero block={block} images={images} />;
			break;
		case "liftedpath/stat":
			view = <Stat block={block} />;
			break;
		case "liftedpath/stat-group":
			view = <StatGroup block={block} />;
			break;
		case "liftedpath/marquee":
			view = <Marquee block={block} phrases={phrases} />;
			break;
		case "liftedpath/cta-band":
			view = <CtaBand block={block} images={images} siteHost={host} />;
			break;
		case "liftedpath/about-tabs":
			view = <AboutTabs block={block} images={images} radioKey={radioKey} siteHost={host} />;
			break;
		case "liftedpath/about-story":
			view = <AboutStory block={block} images={images} />;
			break;
		case "liftedpath/beliefs":
			view = <Beliefs block={block} />;
			break;
		case "liftedpath/program-cards":
			view = <ProgramCards block={block} images={images} siteHost={host} />;
			break;
		case "liftedpath/action-grid":
			view = <ActionGrid block={block} siteHost={host} />;
			break;
		case "liftedpath/sponsor-callout":
			view = <SponsorCallout block={block} siteHost={host} />;
			break;
		case "liftedpath/stewardship":
			view = <Stewardship block={block} images={images} phone={phone} siteHost={host} />;
			break;
		case "liftedpath/process":
			view = <Process block={block} siteHost={host} />;
			break;
		case "liftedpath/pillars":
			view = <Pillars block={block} />;
			break;
		case "liftedpath/goals":
			view = <Goals block={block} />;
			break;
		case "liftedpath/team":
			view = <Team block={block} images={images} />;
			break;
		case "liftedpath/gallery":
			view = <Gallery block={block} images={images} />;
			break;
		case "liftedpath/faq":
			view = <Faq block={block} radioKey={radioKey} />;
			break;
		case "liftedpath/contact-form":
			view = <ContactForm address={address} block={block} email={email} notices={contactNotices} phone={phone} />;
			break;
		case "liftedpath/donate-panel":
			view = (
				<Suspense fallback={null}>
					<DonatePanel block={block} images={images} notices={donateNotices} paymentsConfigured={paymentsConfigured} providerLabel={providerLabel} />
				</Suspense>
			);
			break;
		case "core/heading":
		case "core/paragraph":
		case "core/image":
		case "core/columns":
			view = <CoreContent block={block} images={images} />;
			break;
		case "core/block": {
			const nested = Array.isArray(block.attributes.innerBlocks) ? block.attributes.innerBlocks : [];
			if (nested.length === 0) {
				return null;
			}
			return (
				<>
					{nested.map((child, childIndex) => (
						<BlockView
							address={address}
							block={child}
							contactNotices={contactNotices}
							donateNotices={donateNotices}
							email={email}
							images={images}
							key={`${child.name}-${childIndex}`}
							paymentsConfigured={paymentsConfigured}
							phone={phone}
							phrases={phrases}
							providerLabel={providerLabel}
							radioKey={`${radioKey}-${childIndex}`}
							siteHost={host}
						/>
					))}
				</>
			);
		}
		default:
			return null;
	}

	const layout = layoutClass(block);
	const surface = blockSurface(block, images);
	const weight = headingWeight(block.attributes.heading_weight);
	if (!layout && !surface) {
		return view;
	}

	const style = {
		...(surface?.style ?? {}),
		"--heading-weight": weight,
	} as CSSProperties;

	return (
		<div className={[layout || "liftedpath-layout", surface?.className].filter(Boolean).join(" ")} style={style}>
			{surface?.videoUrl ? <BlockBackgroundVideo src={surface.videoUrl} /> : null}
			{surface && surface.kind !== "color" ? (
				<span aria-hidden="true" className="liftedpath-block-bg-shade" style={{ backgroundColor: surface.shade }} />
			) : null}
			{view}
		</div>
	);
}
