import Image from "next/image";
import Link from "next/link";
import type { SiteLogo } from "@/lib/wp/types";

interface BrandLogoProps {
	className: string;
	logo: SiteLogo | null;
	name: string;
	priority?: boolean;
}

export function BrandLogo({ className, logo, name, priority = false }: BrandLogoProps) {
	const label = logo?.alt || name || "LiftedPath Empowerment Foundation";

	return (
		<Link aria-label={label} className="brand-link" href="/">
			{logo ? (
				<Image
					alt={label}
					className={className}
					height={logo.height}
					priority={priority}
					src={logo.url}
					unoptimized={logo.url.startsWith("http://")}
					width={logo.width}
				/>
			) : (
				<span className={className}>{label}</span>
			)}
		</Link>
	);
}
