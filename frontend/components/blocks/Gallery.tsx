"use client";

import { useCallback, useEffect, useRef, useState, type CSSProperties } from "react";
import Image from "next/image";
import { ChevronLeft, ChevronRight, Play, X } from "lucide-react";
import { BlockPhoto } from "@/components/blocks/parts";
import { flag, imageId, rows, text, videoLink } from "@/lib/blocks/fields";
import type { Block, FeaturedImage, JsonValue } from "@/lib/wp/types";

type Motion = "pending" | "reduce" | "allow";
type TileSize = "standard" | "wide" | "tall";

interface Embed {
	provider: "youtube" | "vimeo";
	src: string;
}

interface GalleryItem {
	kind: "image" | "video";
	image: FeaturedImage | null;
	videoSrc: string;
	embed: Embed | null;
	caption: string;
	tile: TileSize;
	alt: string;
}

function paragraphs(value: string): string[] {
	return value
		.split(/\n+/)
		.map((paragraph) => paragraph.trim())
		.filter((paragraph) => paragraph !== "");
}

function tileOf(value: JsonValue): TileSize {
	const raw = text(value);
	return raw === "wide" || raw === "tall" ? raw : "standard";
}

function columnsOf(value: JsonValue | Block[] | undefined): "2" | "3" | "4" {
	const raw = text(value);
	return raw === "2" || raw === "4" ? raw : "3";
}

interface PackedCell {
	col: number;
	row: number;
	colSpan: number;
	rowSpan: number;
}

function packGallery(tiles: TileSize[], columns: number, mosaic: boolean): PackedCell[] {
	if (columns <= 1) {
		return tiles.map((_, index) => ({ col: 0, row: index, colSpan: 1, rowSpan: 1 }));
	}

	const occupied: boolean[][] = [];
	const taken = (row: number, col: number) => occupied[row]?.[col] ?? false;
	const mark = (row: number, col: number) => {
		while (occupied.length <= row) {
			occupied.push(Array.from({ length: columns }, () => false));
		}
		occupied[row][col] = true;
	};
	const fits = (row: number, col: number, colSpan: number, rowSpan: number) => {
		if (col < 0 || col + colSpan > columns) {
			return false;
		}
		for (let rowOffset = 0; rowOffset < rowSpan; rowOffset += 1) {
			for (let colOffset = 0; colOffset < colSpan; colOffset += 1) {
				if (taken(row + rowOffset, col + colOffset)) {
					return false;
				}
			}
		}
		return true;
	};
	const firstFree = () => {
		for (let row = 0; row < 240; row += 1) {
			for (let col = 0; col < columns; col += 1) {
				if (!taken(row, col)) {
					return { row, col };
				}
			}
		}
		return { row: 0, col: 0 };
	};

	const placed: PackedCell[] = [];
	tiles.forEach((tile, index) => {
		const spot = firstFree();
		const shape = mosaic ? tile : "standard";
		const remaining = tiles.length - index - 1;
		let colSpan = 1;
		let rowSpan = 1;
		if (shape === "wide" && fits(spot.row, spot.col, Math.min(2, columns), 1)) {
			colSpan = Math.min(2, columns);
		}
		if (shape === "tall") {
			const holes = columns - spot.col - 1 + (columns - 1);
			if (fits(spot.row, spot.col, 1, 2) && remaining >= holes) {
				rowSpan = 2;
			}
		}
		for (let rowOffset = 0; rowOffset < rowSpan; rowOffset += 1) {
			for (let colOffset = 0; colOffset < colSpan; colOffset += 1) {
				mark(spot.row + rowOffset, spot.col + colOffset);
			}
		}
		placed.push({ col: spot.col, row: spot.row, colSpan, rowSpan });
	});

	for (let row = 0; row < occupied.length; row += 1) {
		for (let col = 0; col < columns; col += 1) {
			if (taken(row, col)) {
				continue;
			}
			const before = placed.find((cell) => cell.row === row && cell.rowSpan === 1 && cell.col + cell.colSpan === col);
			if (before) {
				before.colSpan += 1;
				mark(row, col);
			}
		}
		for (let col = columns - 1; col >= 0; col -= 1) {
			if (taken(row, col)) {
				continue;
			}
			const after = placed.find((cell) => cell.row === row && cell.rowSpan === 1 && cell.col === col + 1);
			if (after) {
				after.col = col;
				after.colSpan += 1;
				mark(row, col);
			}
		}
	}

	return placed;
}

function gridTrack(cell: PackedCell, axis: "col" | "row"): string {
	const start = (axis === "col" ? cell.col : cell.row) + 1;
	const span = axis === "col" ? cell.colSpan : cell.rowSpan;
	return `${start} / span ${span}`;
}

function still(id: number | null, images: ReadonlyMap<number, FeaturedImage>): FeaturedImage | null {
	if (!id) {
		return null;
	}
	const media = images.get(id);
	if (!media || media.mime?.startsWith("video/")) {
		return null;
	}
	return media;
}

function film(id: number | null, images: ReadonlyMap<number, FeaturedImage>): string {
	if (!id) {
		return "";
	}
	const media = images.get(id);
	if (!media || (media.mime && !media.mime.startsWith("video/"))) {
		return "";
	}
	return media.url;
}

function youtubeId(parsed: URL, host: string): string {
	if (host === "youtu.be") {
		return parsed.pathname.split("/").filter(Boolean)[0] ?? "";
	}
	const watch = parsed.searchParams.get("v");
	if (watch) {
		return watch;
	}
	const parts = parsed.pathname.split("/").filter(Boolean);
	if (parts[0] === "embed" || parts[0] === "shorts") {
		return parts[1] ?? "";
	}
	return "";
}

function remoteVideo(url: string): { file: string; embed: Embed | null } | null {
	if (!url) {
		return null;
	}

	let parsed: URL;
	try {
		parsed = new URL(url);
	} catch {
		return null;
	}

	if (parsed.protocol !== "http:" && parsed.protocol !== "https:") {
		return null;
	}

	const host = parsed.hostname.replace(/^www\./, "").replace(/^m\./, "");
	if (host === "youtu.be" || host === "youtube.com" || host === "youtube-nocookie.com") {
		const id = youtubeId(parsed, host);
		if (!/^[\w-]{6,}$/.test(id)) {
			return null;
		}
		return {
			file: "",
			embed: {
				provider: "youtube",
				src: `https://www.youtube-nocookie.com/embed/${encodeURIComponent(id)}?rel=0`,
			},
		};
	}

	if (host === "vimeo.com" || host === "player.vimeo.com") {
		const parts = parsed.pathname.split("/").filter((part) => /^\d+$/.test(part));
		const id = parts[parts.length - 1];
		if (!id) {
			return null;
		}
		return {
			file: "",
			embed: { provider: "vimeo", src: `https://player.vimeo.com/video/${id}?dnt=1` },
		};
	}

	return { file: url, embed: null };
}

function withAutoplay(src: string, play: boolean): string {
	if (!play) {
		return src;
	}
	const url = new URL(src);
	url.searchParams.set("autoplay", "1");
	return url.toString();
}

function readItems(block: Block, images: ReadonlyMap<number, FeaturedImage>): GalleryItem[] {
	const items: GalleryItem[] = [];

	for (const row of rows(block.attributes, "items")) {
		const kind = text(row.media_kind) === "video" ? "video" : "image";
		const photo = still(imageId(row.image), images);
		const caption = text(row.caption);
		const tile = tileOf(row.tile);

		if (kind === "image") {
			if (!photo) {
				continue;
			}
			items.push({
				kind,
				image: photo,
				videoSrc: "",
				embed: null,
				caption,
				tile,
				alt: photo.alt,
			});
		} else {
			const remote = remoteVideo(videoLink(row.video_url));
			const embed = remote?.embed ?? null;
			const videoSrc = embed ? "" : remote?.file || film(imageId(row.video), images);
			if (!embed && !videoSrc) {
				continue;
			}
			items.push({
				kind,
				image: photo,
				videoSrc,
				embed,
				caption,
				tile,
				alt: photo?.alt ?? "",
			});
		}

		if (items.length >= 24) {
			break;
		}
	}

	return items;
}

function itemName(item: GalleryItem): string {
	const label = item.caption || item.alt;
	if (item.kind === "video") {
		return label ? `Play ${label}` : "Play video";
	}
	return label ? `View ${label}` : "View photograph";
}

function useMotion(): Motion {
	const [motion, setMotion] = useState<Motion>("pending");

	useEffect(() => {
		const query = window.matchMedia("(prefers-reduced-motion: reduce)");
		const apply = () => setMotion(query.matches ? "reduce" : "allow");
		apply();
		query.addEventListener("change", apply);
		return () => query.removeEventListener("change", apply);
	}, []);

	return motion;
}

function QuietVideo({ paused, poster, src }: { paused: boolean; poster?: string; src: string }) {
	const ref = useRef<HTMLVideoElement>(null);

	useEffect(() => {
		const node = ref.current;
		if (!node) {
			return;
		}
		if (paused) {
			node.pause();
			return;
		}
		const attempt = node.play();
		if (attempt) {
			attempt.catch(() => undefined);
		}
	}, [paused, src]);

	return (
		<video
			aria-hidden="true"
			className="liftedpath-gallery__video"
			loop
			muted
			playsInline
			poster={poster}
			preload="metadata"
			ref={ref}
			src={src}
			tabIndex={-1}
		/>
	);
}

function DialogFilm({ label, motion, poster, src }: { label: string; motion: Motion; poster?: string; src: string }) {
	const ref = useRef<HTMLVideoElement>(null);

	useEffect(() => {
		const node = ref.current;
		if (!node || motion !== "allow") {
			return;
		}
		const attempt = node.play();
		if (attempt) {
			attempt.catch(() => undefined);
		}
	}, [motion, src]);

	return (
		<video
			aria-label={label}
			className="liftedpath-gallery__frame"
			controls
			playsInline
			poster={poster}
			preload="metadata"
			ref={ref}
			src={src}
		/>
	);
}

function TileVisual({
	enlarge,
	images,
	inline,
	item,
	motion,
	paused,
}: {
	enlarge: boolean;
	images: ReadonlyMap<number, FeaturedImage>;
	inline: boolean;
	item: GalleryItem;
	motion: Motion;
	paused: boolean;
}) {
	const poster = item.image?.url;

	if (item.kind === "video" && item.embed && inline) {
		return (
			<iframe
				allow="autoplay; fullscreen; picture-in-picture"
				allowFullScreen
				className="liftedpath-gallery__embed"
				src={withAutoplay(item.embed.src, motion === "allow")}
				title={item.caption || item.alt || "Video"}
			/>
		);
	}

	if (item.kind === "video" && item.videoSrc && (!enlarge || motion === "allow")) {
		if (enlarge) {
			return <QuietVideo paused={paused} poster={poster} src={item.videoSrc} />;
		}
		return (
			<video
				autoPlay={motion === "allow"}
				className="liftedpath-gallery__video"
				controls
				loop={motion === "allow"}
				muted={motion === "allow"}
				playsInline
				poster={poster}
				preload={motion === "reduce" ? "none" : "metadata"}
				src={motion === "pending" ? undefined : item.videoSrc}
			/>
		);
	}

	if (!item.image) {
		return null;
	}

	return (
		<BlockPhoto
			alt={item.alt || item.caption}
			className="liftedpath-gallery__photo"
			decorative={enlarge}
			id={item.image.id}
			images={images}
		/>
	);
}

function GalleryTile({
	enlarge,
	images,
	index,
	item,
	motion,
	onOpen,
	paused,
	showCaptions,
}: {
	enlarge: boolean;
	images: ReadonlyMap<number, FeaturedImage>;
	index: number;
	item: GalleryItem;
	motion: Motion;
	onOpen: (index: number) => void;
	paused: boolean;
	showCaptions: boolean;
}) {
	const [inline, setInline] = useState(false);
	const name = itemName(item);
	const showPlay = item.kind === "video" && !(inline && !enlarge && item.embed);
	const caption =
		showCaptions && item.caption ? (
			<span className="liftedpath-gallery__caption" {...(enlarge ? { "aria-hidden": true } : {})}>
				{item.caption}
			</span>
		) : null;
	const media = (
		<span className="liftedpath-gallery__media">
			<TileVisual enlarge={enlarge} images={images} inline={inline && !enlarge} item={item} motion={motion} paused={paused} />
		</span>
	);

	if (enlarge) {
		return (
			<button aria-haspopup="dialog" aria-label={name} className="liftedpath-gallery__tile" onClick={() => onOpen(index)} type="button">
				{media}
				{showPlay ? (
					<span aria-hidden="true" className="liftedpath-gallery__play">
						<Play aria-hidden="true" fill="currentColor" size={16} strokeWidth={0} />
					</span>
				) : null}
				{caption}
			</button>
		);
	}

	if (item.kind === "video" && item.embed && !inline) {
		return (
			<figure className="liftedpath-gallery__tile">
				{media}
				<button aria-label={name} className="liftedpath-gallery__play" onClick={() => setInline(true)} type="button">
					<Play aria-hidden="true" fill="currentColor" size={16} strokeWidth={0} />
				</button>
				{caption}
			</figure>
		);
	}

	return (
		<figure className="liftedpath-gallery__tile">
			{media}
			{caption}
		</figure>
	);
}

function NeighborStill({ item }: { item: GalleryItem }) {
	if (item.image) {
		return (
			<span className="liftedpath-gallery__neighbor-still">
				<Image
					alt=""
					aria-hidden="true"
					height={item.image.height}
					src={item.image.url}
					unoptimized={item.image.url.startsWith("http://")}
					width={item.image.width}
				/>
			</span>
		);
	}

	if (item.videoSrc) {
		return (
			<span className="liftedpath-gallery__neighbor-still">
				<video aria-hidden="true" muted playsInline preload="metadata" src={item.videoSrc} tabIndex={-1} />
			</span>
		);
	}

	return <span className="liftedpath-gallery__neighbor-still" />;
}

function neighborName(item: GalleryItem, direction: "Previous" | "Next"): string {
	const label = item.caption || item.alt;
	return label ? `${direction}: ${label}` : direction;
}

function GalleryDialog({
	active,
	items,
	motion,
	onClose,
	onStep,
}: {
	active: number | null;
	items: GalleryItem[];
	motion: Motion;
	onClose: () => void;
	onStep: (delta: number) => void;
}) {
	const dialogRef = useRef<HTMLDialogElement>(null);
	const item = active === null ? null : items[active];

	useEffect(() => {
		const dialog = dialogRef.current;
		if (!dialog) {
			return;
		}
		if (active === null) {
			if (dialog.open) {
				dialog.close();
			}
			return;
		}
		if (!dialog.open) {
			dialog.showModal();
		}
	}, [active]);

	useEffect(() => {
		if (active === null) {
			return;
		}
		const onKey = (event: KeyboardEvent) => {
			if (event.target instanceof HTMLVideoElement || items.length < 2) {
				return;
			}
			if (event.key !== "ArrowRight" && event.key !== "ArrowLeft") {
				return;
			}
			event.preventDefault();
			onStep(event.key === "ArrowRight" ? 1 : -1);
		};
		window.addEventListener("keydown", onKey);
		return () => window.removeEventListener("keydown", onKey);
	}, [active, items.length, onStep]);

	const label = item ? item.caption || item.alt || (item.kind === "video" ? "Video" : "Photograph") : "Gallery";
	const previous = active === null ? null : items[(active - 1 + items.length) % items.length];
	const next = active === null ? null : items[(active + 1) % items.length];

	return (
		<dialog
			aria-label={label}
			className="liftedpath-gallery__dialog"
			onClick={(event) => {
				if (event.target === event.currentTarget) {
					onClose();
				}
			}}
			onClose={onClose}
			ref={dialogRef}
		>
			{item && active !== null ? (
			<>
			<div className="liftedpath-gallery__dialog-bar">
				<div className="liftedpath-gallery__nav">
					{items.length > 1 ? (
						<>
							<button aria-label="Previous" className="liftedpath-gallery__icon-button" onClick={() => onStep(-1)} type="button">
								<ChevronLeft aria-hidden="true" size={18} />
							</button>
							<button aria-label="Next" className="liftedpath-gallery__icon-button" onClick={() => onStep(1)} type="button">
								<ChevronRight aria-hidden="true" size={18} />
							</button>
						</>
					) : null}
				</div>
				<p className="liftedpath-gallery__count">
					{active + 1} of {items.length}
				</p>
				<button aria-label="Close" className="liftedpath-gallery__icon-button" onClick={onClose} type="button">
					<X aria-hidden="true" size={18} />
				</button>
			</div>
			<div className="liftedpath-gallery__stage">
				{previous && items.length > 1 ? (
					<button aria-label={neighborName(previous, "Previous")} className="liftedpath-gallery__neighbor" onClick={() => onStep(-1)} type="button">
						<NeighborStill item={previous} />
					</button>
				) : null}
				<div className="liftedpath-gallery__current">
					{item.embed ? (
						<iframe
							allow="autoplay; fullscreen; picture-in-picture"
							allowFullScreen
							className="liftedpath-gallery__frame"
							key={item.embed.src}
							src={withAutoplay(item.embed.src, motion === "allow")}
							title={label}
						/>
					) : null}
					{!item.embed && item.videoSrc ? (
						<DialogFilm key={item.videoSrc} label={label} motion={motion} poster={item.image?.url} src={item.videoSrc} />
					) : null}
					{!item.embed && !item.videoSrc && item.image ? (
						<Image
							alt={item.alt || item.caption}
							className="liftedpath-gallery__frame"
							height={item.image.height}
							src={item.image.url}
							unoptimized={item.image.url.startsWith("http://")}
							width={item.image.width}
						/>
					) : null}
				</div>
				{next && items.length > 1 ? (
					<button aria-label={neighborName(next, "Next")} className="liftedpath-gallery__neighbor" onClick={() => onStep(1)} type="button">
						<NeighborStill item={next} />
					</button>
				) : null}
			</div>
			{item.caption ? <p className="liftedpath-gallery__dialog-caption">{item.caption}</p> : null}
			</>
			) : null}
		</dialog>
	);
}

export function Gallery({ block, images }: { block: Block; images: ReadonlyMap<number, FeaturedImage> }) {
	const eyebrow = text(block.attributes.eyebrow);
	const heading = text(block.attributes.heading);
	const intro = paragraphs(text(block.attributes.intro));
	const layout = text(block.attributes.layout) === "grid" ? "grid" : "mosaic";
	const columns = columnsOf(block.attributes.columns);
	const showCaptions = flag(block.attributes.show_captions, true);
	const enlarge = flag(block.attributes.enlarge, true);
	const items = readItems(block, images);
	const mosaic = layout === "mosaic";
	const tiles = items.map((item) => item.tile);
	const packed = {
		1: packGallery(tiles, 1, mosaic),
		2: packGallery(tiles, 2, mosaic),
		3: packGallery(tiles, 3, mosaic),
		4: packGallery(tiles, 4, mosaic),
	};
	const motion = useMotion();
	const [active, setActive] = useState<number | null>(null);
	const step = useCallback((delta: number) => {
		setActive((current) => {
			if (current === null || items.length === 0) {
				return current;
			}
			return (current + delta + items.length) % items.length;
		});
	}, [items.length]);

	if (!eyebrow && !heading && intro.length === 0 && items.length === 0) {
		return null;
	}

	return (
		<section className="liftedpath-section liftedpath-gallery">
			<div className="liftedpath-section__inner">
				{eyebrow || heading || intro.length > 0 ? (
					<div className="liftedpath-gallery__heading">
						{eyebrow ? <p className="liftedpath-eyebrow">{eyebrow}</p> : null}
						{heading ? <h2>{heading}</h2> : null}
						{intro.map((paragraph) => (
							<p className="liftedpath-lead" key={paragraph}>
								{paragraph}
							</p>
						))}
					</div>
				) : null}
				{items.length > 0 ? (
					<ul className="liftedpath-gallery__grid" data-columns={columns} data-layout={layout}>
						{items.map((item, index) => (
							<li
								className={item.tile === "standard" ? undefined : `is-${item.tile}`}
								key={`${item.kind}-${index}`}
								style={
									{
										"--c1": gridTrack(packed[1][index], "col"),
										"--r1": gridTrack(packed[1][index], "row"),
										"--c2": gridTrack(packed[2][index], "col"),
										"--r2": gridTrack(packed[2][index], "row"),
										"--c3": gridTrack(packed[3][index], "col"),
										"--r3": gridTrack(packed[3][index], "row"),
										"--c4": gridTrack(packed[4][index], "col"),
										"--r4": gridTrack(packed[4][index], "row"),
									} as CSSProperties
								}
							>
								<GalleryTile
									enlarge={enlarge}
									images={images}
									index={index}
									item={item}
									motion={motion}
									onOpen={setActive}
									paused={active !== null}
									showCaptions={showCaptions}
								/>
							</li>
						))}
					</ul>
				) : null}
			</div>
			{enlarge && items.length > 0 ? (
				<GalleryDialog active={active} items={items} motion={motion} onClose={() => setActive(null)} onStep={step} />
			) : null}
		</section>
	);
}
