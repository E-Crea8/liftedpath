import { wordpressApiUrl } from "@/lib/wp/api-url";

const HOUR = 60 * 60 * 1000;
const sentAt = new Map<string, number>();

export interface AdminAlert {
	code: string;
	path: string;
	message: string;
	kept: boolean;
}

/**
 * Tell WordPress to email the administration address. A failed call leaves the page up.
 * The same issue waits an hour before another attempt from this server, and only after a
 * successful POST so a failed mail attempt can retry.
 */
export function notifyAdmin(alert: AdminAlert): void {
	const key = `${alert.code}:${alert.path}`;
	const now = Date.now();
	const previous = sentAt.get(key) ?? 0;
	if (now - previous < HOUR) {
		return;
	}

	void postAdminAlert(alert).then((sent) => {
		if (sent) {
			sentAt.set(key, Date.now());
		}
	});
}

export async function postAdminAlert(alert: AdminAlert): Promise<boolean> {
	const secret = process.env.REVALIDATE_SECRET?.trim() ?? "";
	let base = "";
	try {
		base = wordpressApiUrl();
	} catch {
		return false;
	}
	if (!secret || !base) {
		return false;
	}

	const body = {
		secret,
		code: clip(alert.code.toLowerCase().replace(/[^a-z0-9_]/g, ""), 40) || "unavailable",
		path: clip(alert.path, 180) || "/",
		message: clip(alert.message.replace(/\s+/g, " ").trim(), 280) || "WordPress could not be read.",
		kept: alert.kept,
	};

	try {
		const response = await fetch(`${base}/liftedpath/v1/alert`, {
			method: "POST",
			headers: {
				Accept: "application/json",
				"Content-Type": "application/json",
			},
			body: JSON.stringify(body),
			cache: "no-store",
			signal: AbortSignal.timeout(4000),
		});
		return response.ok;
	} catch {
		// The public page is already decided. Mail can wait until WordPress answers.
		return false;
	}
}

function clip(value: string, max: number): string {
	return value.slice(0, max);
}
