import { createFileRoute } from "@tanstack/react-router";
import { LiftedSite } from "@/components/LiftedSite";

export const Route = createFileRoute("/what-we-do")({
  head: () => ({ meta: [
    { title: "What We Do | LiftedPath Empowerment Foundation" },
    { name: "description", content: "Explore LiftedPath Learning, Path Guide and Community Initiatives, from education pathways to mentorship and outreach." },
    { property: "og:title", content: "What We Do | LiftedPath Empowerment Foundation" },
    { property: "og:description", content: "Explore LiftedPath Learning, Path Guide and Community Initiatives, from education pathways to mentorship and outreach." },
    { property: "og:type", content: "website" },
    { name: "twitter:card", content: "summary_large_image" },
  ] }),
  component: () => <LiftedSite design="one" page="programs" />,
});
