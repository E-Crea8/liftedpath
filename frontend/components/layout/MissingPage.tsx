import Link from "next/link";

export function MissingPage() {
	return (
		<section className="missing-page">
			<div className="column">
				<p className="eyebrow">Missing page</p>
				<h1>This path is not part of the shell yet.</h1>
				<p>
					<Link href="/">Back to the home frame</Link>
				</p>
			</div>
		</section>
	);
}
