import { ArrowUpRight } from "lucide-react";
import Link from "next/link";
import { donateLink } from "@/components/layout/frame-links";

interface DonateButtonProps {
	label?: string;
}

export function DonateButton({ label = donateLink.label }: DonateButtonProps) {
	return (
		<Link className="donate-button" href={donateLink.href}>
			{label}
			<ArrowUpRight aria-hidden="true" size={16} strokeWidth={2} />
		</Link>
	);
}
