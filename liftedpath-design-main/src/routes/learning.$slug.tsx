import { createFileRoute } from "@tanstack/react-router";
import { LiftedSite } from "@/components/LiftedSite";

export const Route = createFileRoute("/learning/$slug")({
  head: ({ params }) => ({ meta: [
    { title: `${params.slug.toUpperCase()} | LiftedPath Learning` },
    { name: "description", content: "Explore the LiftedPath Learning education pathway and our commitment to opportunity." },
    { property: "og:title", content: `${params.slug.toUpperCase()} | LiftedPath Learning` },
    { property: "og:description", content: "Explore the LiftedPath Learning education pathway and our commitment to opportunity." },
    { property: "og:type", content: "website" },
    { name: "twitter:card", content: "summary_large_image" },
  ] }),
  component: Page,
});
function Page() { const { slug } = Route.useParams(); return <LiftedSite design="one" page="learning" learningSlug={slug} />; }
