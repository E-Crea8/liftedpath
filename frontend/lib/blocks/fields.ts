import type { Block, BlockAttributes, FeaturedImage, JsonValue } from "@/lib/wp/types";

export interface FieldLink {
	title: string;
	href: string;
	external: boolean;
	blank: boolean;
}

const MEDIA_KEY = /(?:^|_)(?:image|video)$/;

const BACKGROUND_COLORS: Record<string, string> = {
	paper: "#fcfdfd",
	surface: "#e7ecec",
	teal: "#043c42",
	deep: "#02272b",
	gold: "#b78d4b",
	bronze: "#a57d3c",
	sand: "#d6b886",
};

export function siteHost(): string {
	const raw = process.env.WORDPRESS_API_URL;
	if (!raw) {
		return "";
	}

	try {
		return new URL(raw).host;
	} catch {
		return "";
	}
}

export function text(value: JsonValue | Block[] | undefined): string {
	return typeof value === "string" ? value.trim() : "";
}

export function rows(attributes: BlockAttributes, name: string): Array<Record<string, JsonValue>> {
	const raw = attributes[name];
	const total = typeof raw === "number" && Number.isInteger(raw) && raw > 0 ? raw : 0;
	const list: Array<Record<string, JsonValue>> = [];

	for (let index = 0; index < total; index += 1) {
		const prefix = `${name}_${index}_`;
		const row: Record<string, JsonValue> = {};
		for (const [key, value] of Object.entries(attributes)) {
			if (!key.startsWith(prefix) || value === undefined || Array.isArray(value)) {
				continue;
			}
			row[key.slice(prefix.length)] = value;
		}
		list.push(row);
	}

	return list;
}

export function numeric(value: JsonValue | Block[] | undefined): number {
	if (typeof value === "number" && Number.isFinite(value)) {
		return value;
	}
	if (typeof value === "string" && value.trim() !== "" && Number.isFinite(Number(value))) {
		return Number(value);
	}
	return 0;
}

export function videoLink(value: JsonValue | Block[] | undefined): string {
	const link = text(value);
	if (/^https?:\/\//i.test(link)) {
		return link;
	}
	return "";
}

export function flag(value: JsonValue | Block[] | undefined, fallback: boolean): boolean {
	if (value === true || value === 1 || value === "1") {
		return true;
	}
	if (value === false || value === 0 || value === "0") {
		return false;
	}
	return fallback;
}

export function imageId(value: JsonValue | Block[] | undefined): number | null {
	if (typeof value === "number" && Number.isInteger(value) && value > 0) {
		return value;
	}
	if (typeof value === "string" && /^\d+$/.test(value)) {
		const id = Number(value);
		return id > 0 ? id : null;
	}
	return null;
}

export function imageIds(blocks: Block[]): number[] {
	const ids: number[] = [];

	const visit = (block: Block) => {
		for (const [key, value] of Object.entries(block.attributes)) {
			if (key === "innerBlocks" || (typeof value !== "number" && typeof value !== "string")) {
				continue;
			}
			if (!MEDIA_KEY.test(key) && !(block.name === "core/image" && key === "id")) {
				continue;
			}
			const id = imageId(value);
			if (id) {
				ids.push(id);
			}
		}

		block.attributes.innerBlocks?.forEach(visit);
	};

	blocks.forEach(visit);
	return ids;
}

export function fieldLink(value: JsonValue | Block[] | undefined, host: string): FieldLink | null {
	if (!value || typeof value !== "object" || Array.isArray(value)) {
		return null;
	}

	const title = text(value.title);
	const url = text(value.url);
	if (!title || !url) {
		return null;
	}

	const blank = value.target === "_blank";
	let href = url;
	let external = false;

	if (url.startsWith("/")) {
		const path = url.replace(/\/+$/, "") || "/";
		href = path;
	} else {
		try {
			const parsed = new URL(url);
			if (parsed.protocol !== "http:" && parsed.protocol !== "https:") {
				return null;
			}
			if (host && parsed.host.toLowerCase() === host.toLowerCase()) {
				href = `${parsed.pathname.replace(/\/+$/, "") || "/"}${parsed.search}`;
			} else {
				external = true;
				href = parsed.toString();
			}
		} catch {
			return null;
		}
	}

	return { title, href, external, blank };
}

export function telHref(phone: string): string {
	const digits = phone.replace(/[^\d+]/g, "");
	return digits ? `tel:${digits}` : "";
}

export function stepNumber(index: number): string {
	return String(index + 1).padStart(2, "0");
}

export function blockHidden(block: Block): boolean {
	const visibility = block.attributes.liftedpathShow;
	if (visibility === false || visibility === 0 || visibility === "0") {
		return true;
	}

	return block.name.startsWith("liftedpath/") && !flag(block.attributes.show, true);
}

export function layoutClass(block: Block): string {
	if (!block.name.startsWith("liftedpath/")) {
		return "";
	}

	const align = text(block.attributes.heading_align);
	const place = text(block.attributes.heading_place);
	const order = text(block.attributes.column_order);
	const weight = headingWeight(block.attributes.heading_weight);
	const classes = ["liftedpath-layout", `is-weight-${weight}`];

	if (align === "left" || align === "center" || align === "right") {
		classes.push(`is-heading-${align}`);
	}
	if (place === "below") {
		classes.push("is-heading-below");
	}
	if (order === "reverse") {
		classes.push("is-reversed");
	}

	return classes.join(" ");
}

export function headingWeight(value: JsonValue | Block[] | undefined): string {
	const weight = text(value);
	return weight === "500" || weight === "600" || weight === "700" || weight === "800" ? weight : "700";
}

export interface BlockSurface {
	kind: "color" | "image" | "video";
	className: string;
	style: Record<string, string>;
	shade: string;
	videoUrl: string;
}

function hexColor(value: JsonValue | Block[] | undefined): string {
	const color = text(value);
	return /^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/.test(color) ? color : "";
}

function rgba(hex: string, opacity: number): string {
	let raw = hex.replace("#", "");
	if (raw.length === 3) {
		raw = raw
			.split("")
			.map((part) => part + part)
			.join("");
	}
	const red = Number.parseInt(raw.slice(0, 2), 16);
	const green = Number.parseInt(raw.slice(2, 4), 16);
	const blue = Number.parseInt(raw.slice(4, 6), 16);
	return `rgba(${red}, ${green}, ${blue}, ${Math.round(opacity) / 100})`;
}

function backgroundOpacity(value: JsonValue | Block[] | undefined): number {
	if (value === undefined || value === null || value === "") {
		return 100;
	}

	return Math.max(0, Math.min(100, Math.round(numeric(value))));
}

export function blockSurface(block: Block, images: ReadonlyMap<number, FeaturedImage>): BlockSurface | null {
	if (!block.name.startsWith("liftedpath/")) {
		return null;
	}

	const kind = text(block.attributes.background_kind);
	if (kind !== "color" && kind !== "image" && kind !== "video") {
		return null;
	}

	const variant = text(block.attributes.background_variant) || "paper";
	const chosen = variant === "custom" ? hexColor(block.attributes.background_color) : (BACKGROUND_COLORS[variant] ?? "");
	const color = chosen || BACKGROUND_COLORS.paper;
	const opacity = backgroundOpacity(block.attributes.background_opacity);
	const shade = rgba(color, opacity);
	const className = `has-block-background has-block-background-${kind}`;

	if (kind === "color") {
		return {
			kind,
			className,
			style: { backgroundColor: shade, backgroundImage: "none" },
			shade,
			videoUrl: "",
		};
	}

	if (kind === "image") {
		const id = imageId(block.attributes.background_image);
		const image = id !== null ? images.get(id) : undefined;
		const style: Record<string, string> = { backgroundColor: "transparent" };
		if (image?.url && !image.url.includes('"')) {
			style.backgroundImage = `url("${image.url}")`;
			style.backgroundSize = "cover";
			style.backgroundPosition = "center";
		}
		return { kind, className, style, shade, videoUrl: "" };
	}

	const videoId = imageId(block.attributes.background_video);
	const uploaded = videoId !== null ? images.get(videoId) : undefined;
	const videoUrl = videoLink(block.attributes.background_video_url) || (uploaded?.mime?.startsWith("video/") ? uploaded.url : "");

	return {
		kind,
		className,
		style: { backgroundColor: "transparent" },
		shade,
		videoUrl,
	};
}
