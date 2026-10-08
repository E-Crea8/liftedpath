import { createFileRoute } from "@tanstack/react-router";
import { LiftedSite } from "@/components/LiftedSite";

export const Route = createFileRoute("/what-we-do/$slug")({
  head: ({ params }) => {
    const title = `${params.slug.split("-").map(word => word[0]?.toUpperCase() + word.slice(1)).join(" ")} | LiftedPath`;
    const description = "Explore LiftedPath's guidance and community initiatives supporting education and opportunity.";
    return { meta: [{ title }, { name: "description", content: description }, { property: "og:title", content: title }, { property: "og:description", content: description }, { property: "og:type", content: "website" }, { name: "twitter:card", content: "summary_large_image" }] };
  },
  component: Page,
});

function Page() { const { slug } = Route.useParams(); return <LiftedSite design="one" page="programs" programSlug={slug} />; }