import { llmsText } from "@/lib/seo/llms";
import { getDiscovery, getSettings } from "@/lib/wp/client";

export async function GET() {
	const settings = await getSettings();
	const discovery = await getDiscovery();

	return new Response(llmsText(settings, discovery), {
		headers: {
			"Content-Type": "text/plain; charset=utf-8",
			"Cache-Control": "public, max-age=0, must-revalidate",
		},
	});
}
