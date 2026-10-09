import type { SocialLink } from "@/lib/wp/types";

export function SocialIcon({ network }: { network: SocialLink["network"] }) {
	return (
		<svg
			aria-hidden="true"
			fill="none"
			height={18}
			stroke="currentColor"
			strokeLinecap="round"
			strokeLinejoin="round"
			strokeWidth={2}
			viewBox="0 0 24 24"
			width={18}
			xmlns="http://www.w3.org/2000/svg"
		>
			{network === "facebook" ? (
				<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" />
			) : null}
			{network === "instagram" ? (
				<>
					<rect height="20" rx="5" ry="5" width="20" x="2" y="2" />
					<path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" />
					<line x1="17.5" x2="17.51" y1="6.5" y2="6.5" />
				</>
			) : null}
			{network === "linkedin" ? (
				<>
					<path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z" />
					<rect height="12" width="4" x="2" y="9" />
					<circle cx="4" cy="4" r="2" />
				</>
			) : null}
			{network === "youtube" ? (
				<>
					<path d="M2.5 17a24.12 24.12 0 0 1 0-10 2 2 0 0 1 1.4-1.4 49.56 49.56 0 0 1 16.2 0A2 2 0 0 1 21.5 7a24.12 24.12 0 0 1 0 10 2 2 0 0 1-1.4 1.4 49.55 49.55 0 0 1-16.2 0A2 2 0 0 1 2.5 17" />
					<path d="m10 15 5-3-5-3z" />
				</>
			) : null}
			{network === "tiktok" ? (
				<path d="M21 7.917v4.034A9.95 9.95 0 0 1 16 10v4.5a6.5 6.5 0 1 1-8-6.326V12.5a2.5 2.5 0 1 0 4 2V3h4.083A6.005 6.005 0 0 0 21 7.917" />
			) : null}
		</svg>
	);
}
