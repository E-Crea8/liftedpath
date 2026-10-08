import { createFileRoute } from "@tanstack/react-router";
import { LiftedSite } from "@/components/LiftedSite";

export const Route = createFileRoute("/")({
  head: () => ({ meta: [
    { title: "Home | LiftedPath Empowerment Foundation" },
    { name: "description", content: "Discover LiftedPath Empowerment Foundation's mission to expand access to education and opportunity in Nigeria." },
    { property: "og:title", content: "Home | LiftedPath Empowerment Foundation" },
    { property: "og:description", content: "Discover LiftedPath Empowerment Foundation's mission to expand access to education and opportunity in Nigeria." },
    { property: "og:type", content: "website" },
    { name: "twitter:card", content: "summary_large_image" },
  ] }),
  component: () => <LiftedSite design="one" page="home" />,
});
