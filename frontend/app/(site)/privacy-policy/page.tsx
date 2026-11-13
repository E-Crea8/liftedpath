import { pageMetadata } from "@/components/pages/WordPressPage";
import { PrivacyPolicyDocument } from "@/components/pages/PrivacyPolicyDocument";

export const instant = false;

export function generateMetadata() {
	return pageMetadata("/privacy-policy");
}

export default function PrivacyPolicyPage() {
	return <PrivacyPolicyDocument />;
}
