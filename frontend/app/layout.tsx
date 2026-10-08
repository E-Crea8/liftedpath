import type { Metadata } from "next";
import type { CSSProperties } from "react";
import { Footer } from "@/components/layout/Footer";
import { Header } from "@/components/layout/Header";
import { PreviewBanner } from "@/components/preview/PreviewBanner";
import { SiteIdentity } from "@/components/seo/SiteIdentity";
import { designCssVariables } from "@/lib/tokens";
import { getDesign, getDiscovery, getNavigation, getSettings } from "@/lib/wp/client";
import "@/styles/globals.css";

export const instant = false;

export async function generateMetadata(): Promise<Metadata> {
	const settings = await getSettings();
	const discovery = await getDiscovery();
	const name = settings.shortName || "LiftedPath";
	const google = [discovery.googleSiteVerification, discovery.googleBusinessVerification].filter(
		(code) => code !== "",
	);
	const other: Record<string, string | string[]> = {};

	if (google.length > 0) {
		other["google-site-verification"] = google;
	}
	if (discovery.bingVerification) {
		other["msvalidate.01"] = discovery.bingVerification;
	}

	return {
		metadataBase: new URL(discovery.siteUrl),
		title: {
			default: name,
			template: `%s – ${name}`,
		},
		description: settings.tagline || undefined,
		other: Object.keys(other).length > 0 ? other : undefined,
	};
}

export default async function RootLayout({ children }: LayoutProps<"/">) {
	const design = await getDesign();
	const navigation = await getNavigation();
	const settings = await getSettings();

	return (
		<html
			data-design-version={design.version}
			lang="en"
			style={
				{
					...designCssVariables(design),
					"--logo-height": `${settings.logoHeight}px`,
					"--footer-logo-height": `${settings.footerLogoHeight}px`,
				} as CSSProperties
			}
		>
			<head>
				<link
					as="font"
					crossOrigin="anonymous"
					href="/fonts/plus-jakarta-sans-latin.woff2"
					rel="preload"
					type="font/woff2"
				/>
				<link
					as="font"
					crossOrigin="anonymous"
					href="/fonts/stack-sans-headline-latin.woff2"
					rel="preload"
					type="font/woff2"
				/>
			</head>
			<body>
				<SiteIdentity />
				<a className="skip-link" href="#content">
					Skip to content
				</a>
				<Header items={navigation.primary} settings={settings} />
				<main id="content">{children}</main>
				<Footer items={navigation.footer} settings={settings} />
				<PreviewBanner />
			</body>
		</html>
	);
}
