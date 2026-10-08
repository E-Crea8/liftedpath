import { createFileRoute } from "@tanstack/react-router";
import { LiftedSite } from "@/components/LiftedSite";

export const Route = createFileRoute("/impact")({
  head: () => ({ meta: [
    { title: "Impact | LiftedPath Empowerment Foundation" },
    { name: "description", content: "Discover LiftedPath Empowerment Foundation's impact in Nigeria." },
    { property: "og:title", content: "Impact | LiftedPath Empowerment Foundation" },
    { property: "og:description", content: "Discover LiftedPath Empowerment Foundation's impact in Nigeria." },
    { property: "og:type", content: "website" },
    { name: "twitter:card", content: "summary_large_image" },
  ] }),
  component: () => <LiftedSite design="one" page="impact" />,
});
