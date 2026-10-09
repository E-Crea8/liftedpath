import { Mail, Phone } from "lucide-react";
import Link from "next/link";
import { BrandLogo } from "@/components/layout/BrandLogo";
import { CopyrightLine } from "@/components/layout/CopyrightLine";
import { FooterWordmark } from "@/components/layout/FooterWordmark";
import { SocialIcon } from "@/components/layout/SocialIcon";
import type { NavItem, SiteSettings } from "@/lib/wp/types";

interface FooterProps {
	items: NavItem[];
	settings: SiteSettings;
}

export function Footer({ items, settings }: FooterProps) {
	const phoneHref = settings.phone.replace(/[^\d+]/g, "");
	const name = settings.legalName || settings.shortName || "LiftedPath";
	const logo = settings.footerLogo ?? settings.logo;
	const wordmark = settings.footerWordmark || settings.shortName;
	const hasLocation = settings.postalAddress !== "" || settings.phone !== "" || settings.email !== "";
	const hasSocial = phoneHref !== "" || settings.email !== "" || settings.social.length > 0;

	return (
		<footer className="site-footer">
			<div className="column footer-grid">
				<div className="footer-brand">
					<BrandLogo className="footer-logo" logo={logo} name={name} />
					{settings.tagline ? <p className="footer-tagline">{settings.tagline}</p> : null}
					{hasSocial ? (
						<div className="footer-social">
							{phoneHref ? (
								<a aria-label={`Call ${name}`} href={`tel:${phoneHref}`}>
									<Phone aria-hidden="true" size={18} strokeWidth={2} />
								</a>
							) : null}
							{settings.email ? (
								<a aria-label={`Email ${name}`} href={`mailto:${settings.email}`}>
									<Mail aria-hidden="true" size={18} strokeWidth={2} />
								</a>
							) : null}
							{settings.social.map((link) => (
								<a
									aria-label={`${name} on ${link.label}`}
									href={link.url}
									key={link.network}
									rel="noreferrer"
									target="_blank"
								>
									<SocialIcon network={link.network} />
								</a>
							))}
						</div>
					) : null}
				</div>
				{items.map((group, index) => (
					<nav aria-label={group.label} key={`${group.label}-${index}`}>
						<h2>{group.path ? <FooterAnchor item={group} /> : group.label}</h2>
						<FooterList items={group.children} />
					</nav>
				))}
				{hasLocation ? (
					<div className="footer-location">
						{settings.locationHeading ? <h2>{settings.locationHeading}</h2> : null}
						{settings.postalAddress ? <p>{settings.postalAddress}</p> : null}
						{settings.phone ? (
							phoneHref ? (
								<a href={`tel:${phoneHref}`}>{settings.phone}</a>
							) : (
								<p>{settings.phone}</p>
							)
						) : null}
						{settings.email ? <a href={`mailto:${settings.email}`}>{settings.email}</a> : null}
					</div>
				) : null}
			</div>
			<div className="column footer-meta">
				{settings.copyrightText ? <CopyrightLine text={settings.copyrightText} /> : null}
				{settings.registrationNumber ? <p>{settings.registrationNumber}</p> : null}
			</div>
			{wordmark ? <FooterWordmark text={wordmark} /> : null}
		</footer>
	);
}

function FooterList({ items }: { items: NavItem[] }) {
	if (items.length === 0) {
		return null;
	}

	return (
		<ul>
			{items.map((item, index) => (
				<li key={`${item.path ?? "group"}-${item.label}-${index}`}>
					{item.path ? <FooterAnchor item={item} /> : item.label}
					{item.children.length > 0 ? <FooterList items={item.children} /> : null}
				</li>
			))}
		</ul>
	);
}

function FooterAnchor({ item }: { item: NavItem }) {
	if (!item.path) {
		return item.label;
	}

	if (item.path.startsWith("http://") || item.path.startsWith("https://")) {
		return (
			<a href={item.path} rel="noreferrer" target="_blank">
				{item.label}
			</a>
		);
	}

	return <Link href={item.path}>{item.label}</Link>;
}
