import Link from "next/link";
import { ArrowIcon } from "@/components/blocks/icons";

export function MissingPage() {
	return (
		<section className="missing-page missing-page--lost">
			<div className="column missing-page__layout">
				<p aria-hidden="true" className="missing-page__mark">
					<span className="missing-page__code">404</span>
					<span className="missing-page__trail">
						<span />
						<span />
						<span />
					</span>
				</p>
				<div className="missing-page__copy">
					<h1>Oops! You&apos;re lost.</h1>
					<p className="missing-page__lead">The page you are looking for was not found.</p>
					<div className="missing-page__actions">
						<Link className="liftedpath-button" href="/">
							Back to home
							<ArrowIcon />
						</Link>
						<Link className="missing-page__quiet" href="/contact">
							Contact us
						</Link>
					</div>
				</div>
			</div>
		</section>
	);
}
