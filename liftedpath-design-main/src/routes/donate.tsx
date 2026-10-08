import { createFileRoute } from "@tanstack/react-router";
import { LiftedSite } from "@/components/LiftedSite";

export const Route = createFileRoute("/donate")({
  head: () => ({ meta: [
    { title: "Donate | LiftedPath Empowerment Foundation" },
    { name: "description", content: "Discover LiftedPath Empowerment Foundation's donate in Nigeria." },
    { property: "og:title", content: "Donate | LiftedPath Empowerment Foundation" },
    { property: "og:description", content: "Discover LiftedPath Empowerment Foundation's donate in Nigeria." },
    { property: "og:type", content: "website" },
    { name: "twitter:card", content: "summary_large_image" },
  ] }),
  component: () => <LiftedSite design="one" page="donate" />,
});
