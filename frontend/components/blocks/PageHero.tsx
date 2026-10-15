import Link from "next/link";
import { HeroBackground } from "@/components/blocks/HeroBackground";
import { ChevronIcon } from "@/components/blocks/icons";
import { BlockPhoto } from "@/components/blocks/parts";
import { flag, imageId, text, videoLink } from "@/lib/blocks/fields";
import type { Block, FeaturedImage } from "@/lib/wp/types";

export function PageHero({ block, images }: { block: Block; images: ReadonlyMap<number, FeaturedImage> }) {
	const photo = imageId(block.attributes.image);
	const videoId = imageId(block.attributes.video);
	const uploaded = videoId !== null ? images.get(videoId) : undefined;
	const videoUrl = videoLink(block.attributes.video_url) || (uploaded?.mime?.startsWith("video/") ? uploaded.url : "");
	const hasVideo = videoUrl !== "";
	const muted = flag(block.attributes.video_muted, true);
	const loop = flag(block.attributes.video_loop, true);
	const autoplay = flag(block.attributes.video_autoplay, true);
	const still = photo !== null ? (images.get(photo) ?? null) : null;
	const variant = text(block.attributes.variant) === "work" ? "work" : "default";
	const eyebrow = text(block.attributes.eyebrow);
	const heading = text(block.attributes.heading);
	const intro = text(block.attributes.intro);
	const crumb = text(block.attributes.breadcrumb_label) || "Home";
	const current = text(block.attributes.breadcrumb_current);
	const hasPhoto = photo !== null && images.has(photo);

	if (!heading && !eyebrow && !intro && !hasPhoto && !hasVideo) {
		return null;
	}

	return (
		<section className={`liftedpath-page-hero${variant === "work" ? " liftedpath-page-hero--work" : ""}`}>
			{hasVideo ? <link as="video" href={videoUrl} rel="preload" /> : null}
			{hasVideo ? (
				<HeroBackground
					autoplay={autoplay}
					image={still}
					imageClass="liftedpath-page-hero__photo"
					loop={loop}
					muted={muted}
					videoClass="liftedpath-page-hero__video"
					videoUrl={videoUrl}
				/>
			) : (
				<BlockPhoto className="liftedpath-page-hero__photo" id={photo} images={images} priority />
			)}
			<div aria-hidden="true" className="liftedpath-page-hero__shade" />
			<div className="liftedpath-page-hero__content">
				{eyebrow && variant !== "work" ? <p className="liftedpath-page-hero__eyebrow">{eyebrow}</p> : null}
				{heading ? <h1>{heading}</h1> : null}
				{intro ? <p className="liftedpath-page-hero__intro">{intro}</p> : null}
				<p className="liftedpath-page-hero__crumbs">
					<Link href="/">{crumb}</Link>
					{current ? (
						<>
							<ChevronIcon />
							<span>{current}</span>
						</>
					) : null}
				</p>
			</div>
		</section>
	);
}
