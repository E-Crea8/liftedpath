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
 * The same issue waits an hour before another attempt from this server.
 */
export function notifyAdmin(alert: AdminAlert): void {
	const key = `${alert.code}:${alert.path}`;
	const now = Date.now();
	const previous = sentAt.get(key) ?? 0;
	if (now - previous < HOUR) {
		return;
	}

	sentAt.set(key, now);
	void postAdminAlert(alert);
}

export async function postAdminAlert(alert: AdminAlert): Promise<void> {
	const secret = process.env.REVALIDATE_SECRET?.trim() ?? "";
	const base = process.env.WORDPRESS_API_URL?.replace(/\/$/, "") ?? "";
	if (!secret || !base) {
		return;
	}

	const body = {
		secret,
		code: clip(alert.code.toLowerCase().replace(/[^a-z0-9_]/g, ""), 40) || "unavailable",
		path: clip(alert.path, 180) || "/",
		message: clip(alert.message.replace(/\s+/g, " ").trim(), 280) || "WordPress could not be read.",
		kept: alert.kept,
	};

	try {
		await fetch(`${base}/liftedpath/v1/alert`, {
			method: "POST",
			headers: {
				Accept: "application/json",
				"Content-Type": "application/json",
			},
			body: JSON.stringify(body),
			cache: "no-store",
			signal: AbortSignal.timeout(4000),
		});
	} catch {
		// The public page is already decided. Mail can wait until WordPress answers.
	}
}

function clip(value: string, max: number): string {
	return value.slice(0, max);
}
