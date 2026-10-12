"use client";

import { ChevronDown, ChevronRight, Menu, X } from "lucide-react";
import gsap from "gsap";
import Link from "next/link";
import { usePathname } from "next/navigation";
import { useId, useEffect, useRef, useState } from "react";
import { BrandLogo } from "@/components/layout/BrandLogo";
import { DonateButton } from "@/components/layout/DonateButton";
import type { NavItem, SiteSettings } from "@/lib/wp/types";

interface HeaderProps {
	items: NavItem[];
	settings: SiteSettings;
}

export function Header({ items, settings }: HeaderProps) {
	const pathname = usePathname();
	const dialogRef = useRef<HTMLDialogElement>(null);
	const [menuPath, setMenuPath] = useState<string | null>(null);
	const [scrolled, setScrolled] = useState(false);
	const open = menuPath === pathname;

	useEffect(() => {
		const onScroll = () => {
			setScrolled(window.scrollY > 35);
		};

		onScroll();
		window.addEventListener("scroll", onScroll, { passive: true });
		return () => window.removeEventListener("scroll", onScroll);
	}, []);

	useEffect(() => {
		const dialog = dialogRef.current;

		if (!dialog) {
			return;
		}

		if (open && !dialog.open) {
			dialog.showModal();
		}

		if (!open && dialog.open) {
			dialog.close();
		}
	}, [open]);

	function closeMenu() {
		setMenuPath(null);
	}

	return (
		<header className="site-header" data-scrolled={scrolled ? "true" : "false"}>
			<BrandLogo className="brand-logo" logo={settings.logo} name={settings.legalName || settings.shortName} priority />
			<nav aria-label="Main" className="desktop-nav">
				{items.map((item, index) =>
					item.children.length > 0 ? (
						<DesktopBranch item={item} key={itemKey(item, index)} pathname={pathname} showArrows={settings.submenuArrows === true} />
					) : (
						<MenuLink item={item} key={itemKey(item, index)} pathname={pathname} />
					),
				)}
			</nav>
			<div className="header-donate">
				<DonateButton />
			</div>
			<button
				aria-controls="site-menu"
				aria-expanded={open}
				aria-label="Open menu"
				className="menu-button"
				onClick={() => setMenuPath(pathname)}
				type="button"
			>
				<Menu aria-hidden="true" size={20} strokeWidth={2} />
			</button>
			<dialog
				aria-label="Main"
				className="site-menu"
				id="site-menu"
				onClose={closeMenu}
				ref={dialogRef}
			>
				<form method="dialog">
					<button aria-label="Close menu" className="menu-button" type="submit">
						<X aria-hidden="true" size={20} strokeWidth={2} />
					</button>
				</form>
				<nav>
					{items.map((item, index) =>
						item.children.length > 0 ? (
							<MobileBranch
								item={item}
								key={itemKey(item, index)}
								onNavigate={closeMenu}
								pathname={pathname}
								showArrows={settings.submenuArrows === true}
							/>
						) : (
							<MenuLink item={item} key={itemKey(item, index)} onNavigate={closeMenu} pathname={pathname} />
						),
					)}
					<Link href="/donate" onClick={closeMenu}>
						Donate Now
					</Link>
				</nav>
			</dialog>
		</header>
	);
}

function DesktopBranch({ item, pathname, showArrows }: { item: NavItem; pathname: string; showArrows: boolean }) {
	const [open, setOpen] = useState(false);
	const [childKey, setChildKey] = useState<string | null>(null);
	const panelId = useId();
	const panelRef = useRef<HTMLDivElement>(null);

	useEffect(() => {
		const panel = panelRef.current;
		if (!panel || !open || window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
			return;
		}

		const tween = gsap.fromTo(
			panel.querySelectorAll(":scope > .nav-nested"),
			{ y: 7, opacity: 0 },
			{ y: 0, opacity: 1, stagger: 0.04, duration: 0.28, ease: "power2.out", clearProps: "transform,opacity" },
		);

		return () => {
			tween.kill();
		};
	}, [open]);

	function close() {
		setOpen(false);
		setChildKey(null);
	}

	return (
		<div
			className="nav-branch"
			onBlur={(event) => {
				if (!event.currentTarget.contains(event.relatedTarget)) {
					close();
				}
			}}
			onKeyDown={(event) => {
				if (event.key === "Escape") {
					event.stopPropagation();
					close();
				}
			}}
			onMouseEnter={() => setOpen(true)}
			onMouseLeave={close}
		>
			<div className="nav-parent" data-current={containsPath(item, pathname) ? "true" : "false"}>
				<MenuLink item={item} pathname={pathname} />
				<button
					aria-controls={panelId}
					aria-expanded={open}
					aria-label={`Show ${item.label} menu`}
					className="nav-toggle"
					onClick={() => setOpen((value) => !value)}
					onFocus={() => setOpen(true)}
					type="button"
				>
					<ChevronDown
						aria-hidden="true"
						className={open ? "nav-chevron-open" : undefined}
						size={15}
						strokeWidth={2}
					/>
				</button>
			</div>
			<div className="nav-panel" data-open={open ? "true" : "false"} id={panelId} inert={!open} ref={panelRef}>
				{item.children.map((child, index) => (
					<DesktopChild
						branchKey={branchKey(child)}
						item={child}
						key={`${branchKey(child)}-${index}`}
						onOpen={setChildKey}
						open={childKey === branchKey(child)}
						pathname={pathname}
						showArrows={showArrows}
					/>
				))}
			</div>
		</div>
	);
}

function DesktopChild({
	branchKey: key,
	item,
	onOpen,
	open,
	pathname,
	showArrows,
}: {
	branchKey: string;
	item: NavItem;
	onOpen: (key: string | null) => void;
	open: boolean;
	pathname: string;
	showArrows: boolean;
}) {
	const panelId = useId();
	const panelRef = useRef<HTMLDivElement>(null);
	const nested = item.children.length > 0;

	useEffect(() => {
		const panel = panelRef.current;
		if (!panel || !open || window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
			return;
		}

		const tween = gsap.fromTo(
			panel.querySelectorAll("a"),
			{ x: 9, opacity: 0 },
			{ x: 0, opacity: 1, duration: 0.27, stagger: 0.035, ease: "power2.out", clearProps: "transform,opacity" },
		);

		return () => {
			tween.kill();
		};
	}, [open]);

	return (
		<div
			className="nav-nested"
			onFocus={() => {
				if (nested) {
					onOpen(key);
				}
			}}
			onMouseEnter={() => {
				if (nested) {
					onOpen(key);
				}
			}}
			onMouseLeave={() => onOpen(null)}
		>
			<div className="nav-row">
				<MenuLink item={item} pathname={pathname} />
				{nested ? (
					<button
						aria-controls={panelId}
						aria-expanded={open}
						aria-label={`Show ${item.label} menu`}
						className="nav-toggle nav-toggle-ink"
						onClick={() => onOpen(open ? null : key)}
						type="button"
					>
						<ChevronRight
							aria-hidden="true"
							className={open ? "nav-chevron-open" : undefined}
							size={17}
							strokeWidth={2}
						/>
					</button>
				) : null}
			</div>
			{nested ? (
				<div
					className="nav-panel nav-subpanel"
					data-open={open ? "true" : "false"}
					id={panelId}
					inert={!open}
					ref={panelRef}
				>
					{item.children.map((child, index) => (
						<SubmenuLink item={child} key={itemKey(child, index)} pathname={pathname} showArrows={showArrows} />
					))}
				</div>
			) : null}
		</div>
	);
}

function MobileBranch({
	item,
	onNavigate,
	pathname,
	showArrows,
}: {
	item: NavItem;
	onNavigate: () => void;
	pathname: string;
	showArrows: boolean;
}) {
	const [open, setOpen] = useState(false);
	const panelId = useId();

	return (
		<div className="mobile-branch">
			<div className="mobile-parent">
				<MenuLink item={item} onNavigate={onNavigate} pathname={pathname} />
				<button
					aria-controls={panelId}
					aria-expanded={open}
					aria-label={`Show ${item.label} menu`}
					className="nav-toggle"
					onClick={() => setOpen((value) => !value)}
					type="button"
				>
					<ChevronDown
						aria-hidden="true"
						className={open ? "nav-chevron-open" : undefined}
						size={20}
						strokeWidth={2}
					/>
				</button>
			</div>
			<div className="mobile-children" data-open={open ? "true" : "false"} id={panelId} inert={!open}>
				<div>
					{item.children.map((child, index) =>
						child.children.length > 0 ? (
							<MobileBranch
								item={child}
								key={itemKey(child, index)}
								onNavigate={onNavigate}
								pathname={pathname}
								showArrows={showArrows}
							/>
						) : (
							<SubmenuLink
								item={child}
								key={itemKey(child, index)}
								onNavigate={onNavigate}
								pathname={pathname}
								showArrows={showArrows}
							/>
						),
					)}
				</div>
			</div>
		</div>
	);
}

function SubmenuLink({
	item,
	onNavigate,
	pathname,
	showArrows,
}: {
	item: NavItem;
	onNavigate?: () => void;
	pathname: string;
	showArrows: boolean;
}) {
	const detail = item.detail.trim();

	if (!item.path) {
		return <span>{item.label}</span>;
	}

	const current = item.path === pathname;
	const body = (
		<>
			<span>
				<strong>{item.label}</strong>
				{detail ? <small>{detail}</small> : null}
			</span>
			{showArrows ? <ChevronRight aria-hidden="true" size={17} strokeWidth={2} /> : null}
		</>
	);

	if (item.path.startsWith("http://") || item.path.startsWith("https://")) {
		return (
			<a className="nav-detail" href={item.path} rel="noreferrer" target="_blank">
				{body}
			</a>
		);
	}

	return (
		<Link
			aria-current={current ? "page" : undefined}
			className="nav-detail"
			href={item.path}
			onClick={onNavigate}
		>
			{body}
		</Link>
	);
}

function MenuLink({
	item,
	onNavigate,
	pathname,
}: {
	item: NavItem;
	onNavigate?: () => void;
	pathname: string;
}) {
	if (!item.path) {
		return <span>{item.label}</span>;
	}

	const current = item.path === pathname;

	if (item.path.startsWith("http://") || item.path.startsWith("https://")) {
		return (
			<a href={item.path} rel="noreferrer" target="_blank">
				{item.label}
			</a>
		);
	}

	return (
		<Link aria-current={current ? "page" : undefined} href={item.path} onClick={onNavigate}>
			{item.label}
		</Link>
	);
}

function containsPath(item: NavItem, pathname: string): boolean {
	if (item.path === pathname) {
		return true;
	}

	return item.children.some((child) => containsPath(child, pathname));
}

function itemKey(item: NavItem, index: number): string {
	return `${branchKey(item)}-${index}`;
}

function branchKey(item: NavItem): string {
	return `${item.path ?? "group"}-${item.label}`;
}
