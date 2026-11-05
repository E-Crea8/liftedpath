import Script from "next/script";
import { areaServedNodes, enrichPostalAddress, organizationKnowsAbout } from "@/lib/seo/page-graph";
import { jsonLdScript } from "@/lib/seo/json";
import { getDiscovery, getSettings } from "@/lib/wp/client";
import type { Discovery, SiteSettings } from "@/lib/wp/types";

export async function SiteIdentity() {
	const settings = await getSettings();
	const discovery = await getDiscovery();
	const graph = organizationGraph(settings, discovery);

	return (
		<>
			<script type="application/ld+json" dangerouslySetInnerHTML={{ __html: jsonLdScript(graph) }} />
			{discovery.googleTagManager ? <TagManager id={discovery.googleTagManager} /> : null}
			{!discovery.googleTagManager && discovery.googleAnalytics ? <Analytics id={discovery.googleAnalytics} /> : null}
		</>
	);
}

function TagManager({ id }: { id: string }) {
	return (
		<>
			<Script id="liftedpath-gtm" strategy="afterInteractive">
				{`(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','${id}');`}
			</Script>
			<noscript>
				<iframe
					className="hidden"
					height="0"
					src={`https://www.googletagmanager.com/ns.html?id=${id}`}
					title="Google Tag Manager"
					width="0"
				/>
			</noscript>
		</>
	);
}

function Analytics({ id }: { id: string }) {
	return (
		<>
			<Script src={`https://www.googletagmanager.com/gtag/js?id=${id}`} strategy="afterInteractive" />
			<Script id="liftedpath-ga" strategy="afterInteractive">
				{`window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','${id}');`}
			</Script>
		</>
	);
}

function organizationGraph(settings: SiteSettings, discovery: Discovery) {
	const origin = discovery.siteUrl.replace(/\/$/, "");
	const organizationId = `${origin}/#organization`;
	const organization: Record<string, unknown> = {
		"@type": "NGO",
		"@id": organizationId,
		name: settings.legalName || settings.shortName || "LiftedPath",
		url: `${origin}/`,
		knowsAbout: organizationKnowsAbout(),
	};

	if (settings.shortName) {
		organization.alternateName = settings.shortName;
	}
	if (discovery.geoSummary || settings.tagline) {
		organization.description = discovery.geoSummary || settings.tagline;
	}
	if (settings.email) {
		organization.email = settings.email;
	}
	if (settings.phone) {
		organization.telephone = settings.phone;
	}

	const address = enrichPostalAddress(settings.postalAddress);
	if (address) {
		organization.address = address;
	}

	organization.areaServed = areaServedNodes(discovery.areaServed);

	if (settings.registrationNumber) {
		organization.identifier = {
			"@type": "PropertyValue",
			propertyID: "Registration number",
			value: settings.registrationNumber,
		};
	}
	const sameAs = [...discovery.sameAs];
	for (const link of settings.social) {
		if (!sameAs.includes(link.url)) {
			sameAs.push(link.url);
		}
	}
	if (sameAs.length > 0) {
		organization.sameAs = sameAs;
	}
	if (settings.logo) {
		organization.logo = settings.logo.url;
		organization.image = settings.logo.url;
	}

	organization.foundingLocation = {
		"@type": "Place",
		name: "Lagos, Nigeria",
		address: {
			"@type": "PostalAddress",
			addressLocality: "Lagos",
			addressRegion: "Lagos State",
			addressCountry: "NG",
		},
	};

	return {
		"@context": "https://schema.org",
		"@graph": [
			organization,
			{
				"@type": "WebSite",
				"@id": `${origin}/#website`,
				url: `${origin}/`,
				name: settings.shortName || settings.legalName || "LiftedPath",
				description: settings.tagline || discovery.geoSummary || undefined,
				publisher: { "@id": organizationId },
				inLanguage: "en-NG",
			},
		],
	};
}
