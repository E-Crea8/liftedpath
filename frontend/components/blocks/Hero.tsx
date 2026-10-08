import { HeroBackground } from "@/components/blocks/HeroBackground";
import { BlockPhoto, GoldButton } from "@/components/blocks/parts";
import { CountFigure } from "@/components/motion/CountFigure";
import { fieldLink, flag, imageId, rows, text, videoLink } from "@/lib/blocks/fields";
import type { Block, FeaturedImage } from "@/lib/wp/types";

export function Hero({
	block,
	images,
	siteHost,
}: {
	block: Block;
	images: ReadonlyMap<number, FeaturedImage>;
	siteHost: string;
}) {
	const photo = imageId(block.attributes.image);
	const videoId = imageId(block.attributes.video);
	const uploaded = videoId !== null ? images.get(videoId) : undefined;
	const videoUrl = videoLink(block.attributes.video_url) || (uploaded?.mime?.startsWith("video/") ? uploaded.url : "");
	const hasVideo = videoUrl !== "";
	const muted = flag(block.attributes.video_muted, true);
	const loop = flag(block.attributes.video_loop, true);
	const autoplay = flag(block.attributes.video_autoplay, true);
	const still = photo !== null ? (images.get(photo) ?? null) : null;
	const portraits = rows(block.attributes, "portraits")
		.map((row) => imageId(row.image))
		.filter((id): id is number => id !== null);
	const eyebrow = text(block.attributes.eyebrow);
	const heading = text(block.attributes.heading);
	const intro = text(block.attributes.intro);
	const figure = text(block.attributes.figure);
	const label = text(block.attributes.label);
	const detail = text(block.attributes.detail);
	const button = fieldLink(block.attributes.button, siteHost);
	const hasPhoto = photo !== null && images.has(photo);

	if (!heading && !eyebrow && !intro && !hasPhoto && !hasVideo && portraits.length === 0) {
		return null;
	}

	return (
		<section className="liftedpath-hero">
			{hasVideo ? <link as="video" href={videoUrl} rel="preload" /> : null}
			{hasVideo ? (
				<HeroBackground
					autoplay={autoplay}
					image={still}
					imageClass="liftedpath-hero__photo"
					loop={loop}
					muted={muted}
					videoClass="liftedpath-hero__video"
					videoUrl={videoUrl}
				/>
			) : (
				<BlockPhoto className="liftedpath-hero__photo" id={photo} images={images} priority />
			)}
			<div aria-hidden="true" className="liftedpath-hero__shade" />
			<div className="liftedpath-hero__content">
				{eyebrow || portraits.length > 0 ? (
					<p className={portraits.length > 0 ? "liftedpath-hero__eyebrow has-portraits" : "liftedpath-hero__eyebrow"}>
						{portraits.length > 0 ? (
							<span className="liftedpath-hero__portraits">
								{portraits.map((id) => (
									<BlockPhoto
										className="liftedpath-hero__portrait"
										decorative
										id={id}
										images={images}
										key={id}
									/>
								))}
							</span>
						) : null}
						{eyebrow}
					</p>
				) : null}
				{heading ? <h1>{heading}</h1> : null}
				{intro ? <p className="liftedpath-hero__intro">{intro}</p> : null}
				<GoldButton link={button} />
			</div>
			{figure || label || detail ? (
				<aside className="liftedpath-hero__stat">
					{figure ? <CountFigure value={figure} /> : null}
					{label ? <span>{label}</span> : null}
					{detail ? <small>{detail}</small> : null}
				</aside>
			) : null}
		</section>
	);
}
