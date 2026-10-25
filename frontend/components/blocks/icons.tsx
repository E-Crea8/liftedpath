type IconName =
	| "target"
	| "heart"
	| "link"
	| "sparkles"
	| "megaphone"
	| "phone"
	| "mail"
	| "map-pin"
	| "chevron"
	| "check"
	| "graduation-cap"
	| "book-open"
	| "wrench"
	| "users";

export function BlockIcon({ name }: { name: IconName }) {
	return (
		<svg
			aria-hidden="true"
			fill="none"
			focusable="false"
			height={24}
			stroke="currentColor"
			strokeLinecap="round"
			strokeLinejoin="round"
			strokeWidth="2"
			viewBox="0 0 24 24"
			width={24}
		>
			{name === "target" ? (
				<>
					<circle cx="12" cy="12" r="10" />
					<circle cx="12" cy="12" r="6" />
					<circle cx="12" cy="12" r="2" />
				</>
			) : null}
			{name === "heart" ? (
				<path d="M2 9.5a5.5 5.5 0 0 1 9.591-3.676.56.56 0 0 0 .818 0A5.49 5.49 0 0 1 22 9.5c0 2.29-1.5 4-3 5.5l-5.492 5.313a2 2 0 0 1-3 .019L5 15c-1.5-1.5-3-3.2-3-5.5" />
			) : null}
			{name === "link" ? (
				<>
					<path d="M9 17H7A5 5 0 0 1 7 7h2" />
					<path d="M15 7h2a5 5 0 1 1 0 10h-2" />
					<line x1="8" x2="16" y1="12" y2="12" />
				</>
			) : null}
			{name === "sparkles" ? (
				<>
					<path d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z" />
					<path d="M20 2v4" />
					<path d="M22 4h-4" />
					<circle cx="4" cy="20" r="2" />
				</>
			) : null}
			{name === "megaphone" ? (
				<>
					<path d="M11 6a13 13 0 0 0 8.4-2.8A1 1 0 0 1 21 4v12a1 1 0 0 1-1.6.8A13 13 0 0 0 11 14H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2z" />
					<path d="M6 14a12 12 0 0 0 2.4 7.2 2 2 0 0 0 3.2-2.4A8 8 0 0 1 10 14" />
					<path d="M8 6v8" />
				</>
			) : null}
			{name === "chevron" ? <path d="m6 9 6 6 6-6" /> : null}
			{name === "check" ? <path d="M20 6 9 17l-5-5" /> : null}
			{name === "phone" ? (
				<path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384" />
			) : null}
			{name === "mail" ? (
				<>
					<rect height="16" rx="2" width="20" x="2" y="4" />
					<path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
				</>
			) : null}
			{name === "map-pin" ? (
				<>
					<path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0" />
					<circle cx="12" cy="10" r="3" />
				</>
			) : null}
			{name === "graduation-cap" ? (
				<>
					<path d="M21.42 10.922a1 1 0 0 0-.019-1.838L12.83 5.18a2 2 0 0 0-1.66 0L2.6 9.08a1 1 0 0 0 0 1.832l8.57 3.908a2 2 0 0 0 1.66 0z" />
					<path d="M22 10v6" />
					<path d="M6 12.5V16a6 3 0 0 0 12 0v-3.5" />
				</>
			) : null}
			{name === "book-open" ? (
				<>
					<path d="M12 7v14" />
					<path d="M3 18a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h5a4 4 0 0 1 4 4 4 4 0 0 1 4-4h5a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1h-6a3 3 0 0 0-3 3 3 3 0 0 0-3-3z" />
				</>
			) : null}
			{name === "wrench" ? (
				<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z" />
			) : null}
			{name === "users" ? (
				<>
					<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
					<circle cx="9" cy="7" r="4" />
					<path d="M22 21v-2a4 4 0 0 0-3-3.87" />
					<path d="M16 3.13a4 4 0 0 1 0 7.75" />
				</>
			) : null}
		</svg>
	);
}

export function ArrowIcon() {
	return (
		<svg
			aria-hidden="true"
			className="liftedpath-button__icon"
			fill="none"
			focusable="false"
			height="18"
			stroke="currentColor"
			strokeLinecap="round"
			strokeLinejoin="round"
			strokeWidth="2"
			viewBox="0 0 24 24"
			width="18"
		>
			<path d="M7 17 17 7" />
			<path d="M7 7h10v10" />
		</svg>
	);
}

export function ChevronIcon() {
	return (
		<svg
			aria-hidden="true"
			fill="none"
			focusable="false"
			height="15"
			stroke="currentColor"
			strokeWidth="2"
			viewBox="0 0 24 24"
			width="15"
		>
			<path d="m9 18 6-6-6-6" />
		</svg>
	);
}
