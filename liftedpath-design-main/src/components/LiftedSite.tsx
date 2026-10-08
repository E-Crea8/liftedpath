import { useEffect, useRef, useState } from "react";
import { ArrowDown, ArrowRight, ArrowUpRight, ChevronDown, ChevronRight, Heart, Link2, Mail, MapPin, Megaphone, Menu, Phone, Sparkles, Target, X } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Link } from "@tanstack/react-router";
import lightLogo from "@/assets/liftedpath-logo-light.png.asset.json";
import students from "@/assets/students.jpg.asset.json";
import classroom from "@/assets/classroom.jpg.asset.json";
import scholarship from "@/assets/scholarship.jpg.asset.json";
import skills from "@/assets/skills.jpg.asset.json";
import mentorship from "@/assets/mentorship.jpg.asset.json";
import orphanage from "@/assets/orphanage.jpg.asset.json";
import season from "@/assets/season.jpg.asset.json";

/** Sizes a footer wordmark so its text always stretches edge to edge. */
function useFitToViewport() {
  const ref = useRef<HTMLDivElement>(null);
  useEffect(() => {
    const node = ref.current;
    if (!node) return;
    let frame = 0;
    const fit = () => {
      cancelAnimationFrame(frame);
      frame = requestAnimationFrame(() => {
        const target = document.documentElement.clientWidth;
        node.style.width = "max-content";
        node.style.fontSize = "100px";
        const natural = node.getBoundingClientRect().width;
        node.style.width = "";
        if (natural > 0) node.style.fontSize = `${(target / natural) * 100}px`;
      });
    };
    fit();
    window.addEventListener("resize", fit);
    document.fonts?.ready.then(fit).catch(() => {});
    return () => { window.removeEventListener("resize", fit); cancelAnimationFrame(frame); };
  }, []);
  return ref;
}

type Page = "home" | "about" | "programs" | "learning" | "impact" | "contact" | "donate";
type Design = "one" | "two";
const programs = [
  { title: "Education Sponsorship / Scholarship", kicker: "Stronger People", line: "Opportunity Today = Possibility Tomorrow", description: "For a student with determination but limited financial resources, tuition support can mean the difference between continuing an education and having to put that dream on hold. Where possible we pay schools and institutions directly on a beneficiary's behalf.", image: scholarship.url, caption: "Scholarships open doors." },
  { title: "Skills Acquisition & Vocational Training", kicker: "Stronger People", line: "Learn. Build. Grow.", description: "Practical, income-generating skills give young people a livelihood they own. We fund vocational placements and the tools, materials and follow-up coaching that turn training into real work.", image: skills.url, caption: "Skills build confidence." },
  { title: "Mentorship & Coaching", kicker: "Brighter Communities", line: "Guidance. Support. Success.", description: "Support is more than money. Our mentors and coaches walk with students and trainees through study habits, career choices and confidence, helping them stay focused on their goals.", image: mentorship.url, caption: "Mentorship creates direction." },
  { title: "Orphanage Visitations", kicker: "Brighter Communities", line: "Love. Care. Hope.", description: "LiftedPath has consistently visited orphanage homes to share care, encouragement, and support — providing food items and essentials while creating moments of connection that remind children they are seen, valued, and remembered.", image: orphanage.url, caption: "Orphanage support builds brighter days." },
  { title: "Share the Joy of the Season", kicker: "Greater Impact", line: "Food. Gifts. Smiles.", description: "Each festive season we reach families facing financial hardship with essential food items and financial assistance, so they can meet immediate needs and experience the joy of the season with dignity.", image: season.url, caption: "Together we share joy." },
];
const learning = [
  { slug: "lift-root", title: "LIFT-ROOT", subtitle: "Early Education", description: "A strong beginning opens a lifetime of possibility. We help young learners access the essentials that support their first steps in education.", image: scholarship.url },
  { slug: "lift-grow", title: "LIFT-GROW", subtitle: "Secondary Education", description: "We support determined students through their secondary years, helping them stay in school and keep moving toward their goals.", image: students.url },
  { slug: "lift-beyond", title: "LIFT-BEYOND", subtitle: "Higher Education", description: "We help young adults overcome financial barriers to further education and pursue the futures they imagine for themselves.", image: classroom.url },
  { slug: "lift-equip", title: "LIFT-EQUIP", subtitle: "Skills & Vocational Development", description: "Practical training and tools build confidence, independence, and pathways into meaningful work.", image: skills.url },
];
const workBranches = [
  { key: "learning", label: "LiftedPath Learning", href: "learning", children: learning.map(item => ({ label: item.title, subtitle: item.subtitle, href: `learning/${item.slug}` })) },
  { key: "guide", label: "Path Guide", href: "what-we-do/path-guide", children: [{ label: "Mentorship & Coaching", href: "what-we-do/mentorship-coaching" }] },
  { key: "community", label: "Community Initiatives", href: "what-we-do/community-initiatives", children: [
    { label: "Lift Beyond Barriers", subtitle: "Special Needs Education", href: "what-we-do/lift-beyond-barriers" },
    { label: "Lift Beyond Borders", subtitle: "Juvenile & Correctional Center Education Outreach", href: "what-we-do/lift-beyond-borders" },
    { label: "Orphanage Outreach", href: "what-we-do/orphanage-outreach" },
    { label: "Share the Joy of the Season", href: "what-we-do/share-the-joy-of-the-season" },
  ] },
];
const workDetails: Record<string, { title: string; subtitle?: string; description: string; image: string }> = {
  "path-guide": { title: "Path Guide", description: "Guidance and encouragement help young people navigate their next steps with confidence.", image: mentorship.url },
  "mentorship-coaching": { title: "Mentorship & Coaching", description: "Our mentors and coaches walk with students and trainees through study habits, career choices and confidence, helping them stay focused on their goals.", image: mentorship.url },
  "community-initiatives": { title: "Community Initiatives", description: "Community-led support creates opportunities for young people and families facing barriers.", image: orphanage.url },
  "lift-beyond-barriers": { title: "Lift Beyond Barriers", subtitle: "Special Needs Education", description: "We believe access to education should include young people with special educational needs.", image: classroom.url },
  "lift-beyond-borders": { title: "Lift Beyond Borders", subtitle: "Juvenile & Correctional Center Education Outreach", description: "Education and guidance can open new pathways for young people in juvenile and correctional settings.", image: students.url },
  "orphanage-outreach": { title: "Orphanage Outreach", description: "We visit orphanage homes to share care, encouragement and essentials, reminding children they are seen, valued and remembered.", image: orphanage.url },
  "share-the-joy-of-the-season": { title: "Share the Joy of the Season", description: "Each festive season we reach families facing financial hardship with food items and financial assistance, sharing the joy of the season with dignity.", image: season.url },
};
const workOverview = [
  { key: "learning", title: "LiftedPath Learning", description: "From early education through higher education and practical skills, our learning pathways meet young people at every stage.", image: classroom.url },
  { key: "guide", title: "Path Guide", description: "Guidance and encouragement help young people navigate their next steps with confidence.", image: mentorship.url },
  { key: "community", title: "Community Initiatives", description: "Community-led support creates opportunities for young people and families facing barriers.", image: orphanage.url },
];
const stats = [
  { value: "25+", title: "Students supported", copy: "Students supported with educational assistance and scholarships." },
  { value: "15", title: "Families reached", copy: "Families reached through our Share the Joy of the Season initiative with food items and financial assistance." },
  { value: "6", title: "School partnerships", copy: "Partnerships with schools to identify students with potential who face financial barriers." },
  { value: "2", title: "Years of orphanage visits", copy: "One orphanage home visited consistently for two years with care and essentials." },
];
function CountUp({ value }: { value: string }) {
  const ref = useRef<HTMLElement>(null);
  const [display, setDisplay] = useState(value);
  useEffect(() => {
    const node = ref.current;
    if (!node || window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;
    const target = Number.parseInt(value, 10);
    if (!Number.isFinite(target)) return;
    const suffix = value.slice(String(target).length);
    let frame = 0;
    const observer = new IntersectionObserver(([entry]) => {
      if (!entry?.isIntersecting) return;
      observer.disconnect();
      const start = performance.now();
      const tick = (now: number) => {
        const progress = Math.min((now - start) / 1500, 1);
        setDisplay(`${Math.round(target * (1 - Math.pow(1 - progress, 3)))}${suffix}`);
        if (progress < 1) frame = requestAnimationFrame(tick);
      };
      setDisplay(`0${suffix}`);
      frame = requestAnimationFrame(tick);
    }, { threshold: .2 });
    observer.observe(node);
    return () => { observer.disconnect(); cancelAnimationFrame(frame); };
  }, [value]);
  return <strong ref={ref}>{display}</strong>;
}
const pillarIcons = [Heart, Link2, Sparkles, Megaphone];
const pillars = [
  { title: "Give", copy: "Financial and in-kind donations that help fund our programs and initiatives." },
  { title: "Partner", copy: "We connect with individuals, schools, organizations and communities through our Community Outreach Representatives to identify people who can benefit from support." },
  { title: "Empower", copy: "Through education, skills development, coaching, mentorship, and practical assistance." },
  { title: "Reachout", copy: "We reach families and communities through initiatives such as Share the Joy of the Season and our orphanage outreach." },
];
const stewardship = [
  { title: "85% of funds go directly to programs", copy: "We prioritize the direct delivery of our programs, ensuring the majority of funds entrusted to LiftedPath create meaningful opportunities for the communities we serve." },
  { title: "Direct payment to institutions", copy: "Where possible we make educational payments directly to schools, training providers and other relevant institutions on behalf of beneficiaries." },
  { title: "Annual audits and reports", copy: "We maintain proper financial oversight through annual financial reviews and reporting, for transparency and accountability." },
  { title: "Donation tracking and receipts", copy: "Every donation is documented and tracked, with records and receipts maintained as a clear account of funds received and their use." },
];
const goals = [
  { title: "Reach More Learners", description: "Extend Educational Support to more students and learners across the communities we serve." },
  { title: "Grow Skills & Opportunities", description: "Create more pathways for people to gain practical, vocational, and life skills that can open doors to new possibilities." },
  { title: "Strengthen Guidance & Mentorship", description: "Build a network of mentors and coaches who can encourage, guide, and walk alongside people on their journey." },
  { title: "Build Stronger Partnerships", description: "Deepen our relationships with schools, communities, organizations, and individuals who share our vision and can help extend our reach." },
  { title: "Reach More Families & Communities", description: "Expand initiatives that bring practical support, care, connection, and opportunity to more people beyond the classroom." },
  { title: "Build for Lasting Impact", description: "Strengthen our programs, systems, partnerships, and resources so the work we begin today can continue creating possibilities for years to come." },
];
const giftOptions = [
  { amount: 15000, title: "School essentials", detail: "Can help provide learning materials and school essentials for a pupil." },
  { amount: 50000, title: "Education support", detail: "Can contribute toward tuition and related costs for a student." },
  { amount: 120000, title: "Skills & tools", detail: "Can help fund practical training and tools for a young person." },
  { amount: 300000, title: "Family care", detail: "Can help provide food and essentials to families facing hardship." },
];
type GiftCurrency = "NGN" | "USD" | "GBP";
const giftCurrencies: Record<GiftCurrency, { symbol: string; ngnPerUnit: number }> = {
  NGN: { symbol: "₦", ngnPerUnit: 1 },
  USD: { symbol: "$", ngnPerUnit: 1500 },
  GBP: { symbol: "£", ngnPerUnit: 2000 },
};
const formatGift = (value: number, currency: GiftCurrency) => `${giftCurrencies[currency].symbol}${Math.round(value).toLocaleString("en-US")}`;
const headings: Record<Page, { title: string; eyebrow: string; intro: string; image: string }> = {
  home: { title: "Breaking Barriers to Education & Opportunity", eyebrow: "Lifting Potentials and Inspiring Purpose", intro: "LiftedPath Empowerment Foundation is a purpose driven non-governmental organization founded on a deep commitment to breaking barriers to education and opportunity for children, teenagers, and young adults.", image: students.url },
  about: { title: "Restoring hope, creating sustainable pathways.", eyebrow: "About us", intro: "Lifting Potential, Inspiring Purpose and Creating Futures.", image: classroom.url },
  programs: { title: "What We Do", eyebrow: "What We Do", intro: "Explore LiftedPath Learning, Path Guide and Community Initiatives — pathways to education, guidance and lasting support.", image: scholarship.url },
  learning: { title: "A pathway for every stage of learning.", eyebrow: "LiftedPath Learning", intro: "From first steps to new skills, we help young people continue learning and create their own futures.", image: classroom.url },
  impact: { title: "Our success so far, and how every naira is accounted for.", eyebrow: "Impact & transparency", intro: "Every contribution is managed with integrity, accountability, and purpose.", image: mentorship.url },
  contact: { title: "Together, we can inspire purpose and create futures.", eyebrow: "Contact", intro: "Volunteer, partner, sponsor a student, or simply ask a question — we would love to hear from you.", image: orphanage.url },
  donate: { title: "Your gift keeps a determined student in school.", eyebrow: "Donate", intro: "Give once or monthly. Where possible we pay schools and training providers directly on a beneficiary's behalf.", image: season.url },
};

export function LiftedSite({ design, page, learningSlug, programSlug }: { design: Design; page: Page; learningSlug?: string; programSlug?: string }) {
  const base = "";
  const path = (p: string) => `${base}${p === "home" ? "" : `/${p}`}` || "/";
  const [menuOpen, setMenuOpen] = useState(false);
  const [whatOpen, setWhatOpen] = useState(false);
  const [activeBranch, setActiveBranch] = useState<string | null>(null);
  const [mobileWhatOpen, setMobileWhatOpen] = useState(false);
  const [mobileBranch, setMobileBranch] = useState<string | null>(null);
  const [frequency, setFrequency] = useState("One-off");
  const [amount, setAmount] = useState(50000);
  const [customAmount, setCustomAmount] = useState("");
  const [currency, setCurrency] = useState<GiftCurrency>("NGN");
  const [hoveredGift, setHoveredGift] = useState<number | null>(null);
  const [openGift, setOpenGift] = useState<number | null>(null);
  const [program, setProgram] = useState("Wherever the need is greatest");
  const [faqOpen, setFaqOpen] = useState<number | null>(null);
  const [scrolled, setScrolled] = useState(false);
  const [focusTab, setFocusTab] = useState<"Mission" | "Vision" | "Commitment">("Mission");
  const shell = useRef<HTMLDivElement>(null);
  const mobileMenu = useRef<HTMLDivElement>(null);
  const submenu = useRef<HTMLDivElement>(null);
  const wordmark = useFitToViewport();
  const detail = learningSlug ? learning.find(item => item.slug === learningSlug) : undefined;
  const workDetail = programSlug ? workDetails[programSlug] : undefined;
  const current = workDetail ? { title: workDetail.title, eyebrow: "What We Do", intro: workDetail.subtitle ?? workDetail.description, image: workDetail.image } : detail ? { title: `${detail.title} — ${detail.subtitle}`, eyebrow: "LiftedPath Learning", intro: detail.description, image: detail.image } : headings[page];
  useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 35);
    onScroll(); window.addEventListener("scroll", onScroll, { passive: true });
    return () => window.removeEventListener("scroll", onScroll);
  }, []);
  useEffect(() => {
    let context: { revert: () => void } | undefined;
    let observer: IntersectionObserver | undefined;
    let active = true;
    const cleanups: Array<() => void> = [];
    Promise.all([import("gsap"), design === "one" || page === "about" ? import("gsap/ScrollTrigger") : Promise.resolve(null)]).then(([{ gsap }, scrollModule]) => {
      if (!active || !shell.current) return;
      if (scrollModule) gsap.registerPlugin(scrollModule.ScrollTrigger);
      context = gsap.context(() => {
        if (!window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
          gsap.fromTo(".hero-reveal", { y: 34, opacity: 0 }, { y: 0, opacity: 1, duration: .9, stagger: .13, ease: "power3.out", delay: .15 });
          if (design === "one" && scrollModule) {
            const revealSelector = ".section-heading, .section-copy, .image-composition, .pillar, .single-photo, .commitment-heading, .commitment-panel, .commitment-grid > img, .program-card, .program-donate, .goals-image, .goal-list > div, .steps-grid > div, .focus-collage, .focus-copy, .stats-grid > div, .belief-grid > div, .feature-image, .feature-copy, .learning-card, .contact-details, .contact-form, .donate-form, .donate-aside, .steward-grid > div, .faq-list, .cta-band .container > div";
            shell.current?.querySelectorAll<HTMLElement>(".motion-section").forEach((section) => {
              const matches = Array.from(section.querySelectorAll<HTMLElement>(revealSelector));
              const targets = matches.filter(node => !matches.some(other => other !== node && other.contains(node)));
              if (!targets.length) targets.push(section);
              targets.forEach((target, index) => {
                gsap.fromTo(target, { y: 62, autoAlpha: 0 }, {
                  y: 0, autoAlpha: 1, duration: 1.05, delay: Math.min(index % 4, 3) * .075,
                  ease: "power3.out", clearProps: "transform,opacity,visibility",
                  scrollTrigger: { trigger: target, start: "top 92%", once: true },
                });
              });
            });
          } else {
            observer = new IntersectionObserver(entries => entries.forEach(entry => {
              if (entry.isIntersecting) {
                gsap.fromTo(entry.target, { y: 40, opacity: 0 }, { y: 0, opacity: 1, duration: .85, ease: "power2.out" });
                observer?.unobserve(entry.target);
              }
            }), { threshold: .08 });
            shell.current?.querySelectorAll(".motion-section:not(.goals-section)").forEach(el => observer?.observe(el));
          }
          if (page === "about" && scrollModule) {
            const goalsSection = shell.current?.querySelector<HTMLElement>(".goals-section");
            const intro = goalsSection?.querySelector<HTMLElement>(".goals-intro");
            const fifthGoal = goalsSection?.querySelector<HTMLElement>(".goal-list > div:nth-child(5)");
            if (goalsSection && intro && fifthGoal) {
              // Release the pinned heading when goal five reaches it, so goals five and six leave together with the section.
              const media = gsap.matchMedia();
              media.add("(min-width: 761px) and (prefers-reduced-motion: no-preference)", () => {
                scrollModule.ScrollTrigger.create({
                  trigger: intro, pin: intro, pinSpacing: false,
                  start: "top 110px", endTrigger: fifthGoal, end: () => `top ${110 + intro.offsetHeight}px`,
                  invalidateOnRefresh: true,
                });
              });
              cleanups.push(() => media.revert());
              if (design === "two") {
                goalsSection.querySelectorAll<HTMLElement>(".goal-list > div").forEach(goal => {
                  gsap.fromTo(goal, { y: 32, autoAlpha: 0 }, {
                    y: 0, autoAlpha: 1, duration: .8, ease: "power2.out",
                    clearProps: "transform,opacity,visibility",
                    scrollTrigger: { trigger: goal, start: "top 92%", once: true },
                  });
                });
              }
            }
          }
          if (design === "one") {
            shell.current?.querySelectorAll<HTMLElement>(".process-section a.pillar").forEach((card, index) => {
              const wash = card.querySelector<HTMLElement>(".pillar-hover-wash");
              const icon = card.querySelector<HTMLElement>(".pillar-num");
              const symbol = icon?.querySelector("svg");
              if (!wash || !icon || !symbol) return;
              gsap.to(icon, {
                y: index % 2 === 0 ? -9 : 9, x: index === 1 ? 7 : 0,
                rotation: [4, -3, 8, -5][index] ?? 4,
                duration: [3.6, 4.3, 3.2, 4][index] ?? 3.6, ease: "sine.inOut",
                repeat: -1, yoyo: true, delay: index * .3,
              });
              const over = () => {
                gsap.to(wash, { opacity: 1, duration: .5, ease: "power2.out", overwrite: true });
                gsap.to(symbol, {
                  rotation: [0, -12, 20, -12][index] ?? 0,
                  scale: index === 0 || index === 2 ? 1.12 : 1.07,
                  x: index === 1 ? 9 : 0, y: index === 3 ? -5 : 0,
                  duration: .7, ease: index === 0 ? "back.out(2)" : "power2.out", overwrite: true,
                });
              };
              const out = () => {
                gsap.to(wash, { opacity: 0, duration: .55, ease: "power2.out", overwrite: true });
                gsap.to(symbol, { rotation: 0, scale: 1, x: 0, y: 0, duration: .7, ease: "power2.out", overwrite: true });
              };
              card.addEventListener("pointerenter", over);
              card.addEventListener("pointerleave", out);
              card.addEventListener("focus", over);
              card.addEventListener("blur", out);
              cleanups.push(() => {
                card.removeEventListener("pointerenter", over); card.removeEventListener("pointerleave", out);
                card.removeEventListener("focus", over); card.removeEventListener("blur", out);
              });
            });
          }
        }
      }, shell);
    });
    return () => { active = false; observer?.disconnect(); cleanups.forEach(cleanup => cleanup()); context?.revert(); };
  }, [page, design, learningSlug]);
  useEffect(() => {
    const panel = mobileMenu.current;
    if (!panel || !menuOpen || window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;
    let active = true;
    import("gsap").then(({ gsap }) => {
      if (!active) return;
      gsap.fromTo(panel.querySelectorAll(".mobile-link:not(.mobile-nested-link)"), { x: 16, opacity: 0 }, { x: 0, opacity: 1, duration: .32, stagger: .045, delay: .1, ease: "power2.out", clearProps: "transform,opacity" });
    });
    return () => { active = false; };
  }, [menuOpen]);
  useEffect(() => {
    const panel = submenu.current;
    if (!panel || !whatOpen || window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;
    let active = true;
    import("gsap").then(({ gsap }) => {
      if (!active) return;
      gsap.fromTo(panel.querySelectorAll(":scope > .dropdown-nested"), { y: 7, opacity: 0 }, { y: 0, opacity: 1, stagger: .04, duration: .28, ease: "power2.out", clearProps: "transform,opacity" });
    });
    return () => { active = false; };
  }, [whatOpen]);
  useEffect(() => {
    const panel = submenu.current?.querySelector<HTMLElement>(`.dropdown-nested[data-branch="${activeBranch}"] .dropdown-children`);
    if (!panel || !activeBranch || window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;
    let active = true;
    import("gsap").then(({ gsap }) => {
      if (!active) return;
      gsap.fromTo(panel.querySelectorAll("a"), { x: 9, opacity: 0 }, { x: 0, opacity: 1, duration: .27, stagger: .035, ease: "power2.out", clearProps: "transform,opacity" });
    });
    return () => { active = false; };
  }, [activeBranch]);
  useEffect(() => {
    const panel = mobileMenu.current?.querySelector<HTMLElement>(`.mobile-branch[data-branch="${mobileBranch}"] .mobile-learning`);
    if (!panel || !menuOpen || !mobileWhatOpen || !mobileBranch || window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;
    let active = true;
    import("gsap").then(({ gsap }) => {
      if (!active) return;
      gsap.fromTo(panel.querySelectorAll("a"), { x: 12, opacity: 0 }, { x: 0, opacity: 1, duration: .32, stagger: .045, ease: "power2.out", clearProps: "transform,opacity" });
    });
    return () => { active = false; };
  }, [mobileBranch, mobileWhatOpen, menuOpen]);
  const LinkButton = ({ to, children, light = false }: { to: string; children: React.ReactNode; light?: boolean }) => <Button asChild variant={light ? "siteOutline" : "site"} size="site"><a href={path(to)}>{children}<ArrowUpRight size={17} /></a></Button>;
  return <div ref={shell} className={`site design-${design} ${scrolled ? "is-scrolled" : ""}`}>
    {design === "two" && <div className="announcement"><span>STRONGER PEOPLE <span className="star">✦</span> BRIGHTER COMMUNITIES <span className="star">✦</span> GREATER IMPACT <span className="star">✦</span> STRONGER PEOPLE <span className="star">✦</span> BRIGHTER COMMUNITIES</span></div>}
    <header className="site-header">
       <a href={path("home")} className="brand" aria-label="LiftedPath home"><img src={lightLogo.url} alt="LiftedPath Empowerment Foundation" /></a>
      <nav className="desktop-nav" aria-label="Main navigation">
        <a className={page === "home" ? "active" : ""} href={path("home")}>Home</a>
        <a className={page === "about" ? "active" : ""} href={path("about")}>About Us</a>
          <div className="nav-dropdown" onMouseEnter={() => setWhatOpen(true)} onMouseLeave={() => { setWhatOpen(false); setActiveBranch(null); }} onBlur={e => { if (!e.currentTarget.contains(e.relatedTarget)) { setWhatOpen(false); setActiveBranch(null); } }} onKeyDown={e => { if (e.key === "Escape") { setWhatOpen(false); setActiveBranch(null); e.currentTarget.querySelector<HTMLButtonElement>(".nav-parent-row .nav-button")?.focus(); } }}>
            <div className="nav-parent-row"><Link className={page === "programs" || page === "learning" ? "active" : ""} to="/what-we-do">What We Do</Link><Button variant="nav" onClick={() => setWhatOpen(!whatOpen)} onFocus={() => setWhatOpen(true)} aria-label="Show What We Do menu" aria-expanded={whatOpen} aria-controls="what-we-do-menu"><ChevronDown size={15} className={whatOpen ? "chevron-open" : ""} /></Button></div>
            <div id="what-we-do-menu" ref={submenu} className={`dropdown-panel ${whatOpen ? "open" : ""}`}>
              {workBranches.map(branch => <div key={branch.key} data-branch={branch.key} className="dropdown-nested" onMouseEnter={() => setActiveBranch(branch.key)} onMouseLeave={() => setActiveBranch(null)} onFocus={() => setActiveBranch(branch.key)}>
                <div className="dropdown-parent-row"><a href={`${base}/${branch.href}`}>{branch.label}</a><Button variant="nav" className="dropdown-child-toggle" onClick={() => setActiveBranch(activeBranch === branch.key ? null : branch.key)} aria-label={`Show ${branch.label} submenu`} aria-expanded={activeBranch === branch.key} aria-controls={`desktop-${branch.key}-menu`}><ChevronRight size={17} className={activeBranch === branch.key ? "branch-chevron-open" : ""} /></Button></div>
                <div id={`desktop-${branch.key}-menu`} className={`dropdown-panel dropdown-children ${activeBranch === branch.key ? "open" : ""}`}>
                  {branch.children.map(item => <a key={item.href} href={`${base}/${item.href}`}><span><strong>{item.label}</strong>{"subtitle" in item && item.subtitle && <small>{item.subtitle}</small>}</span><ChevronRight size={17} /></a>)}
                </div>
              </div>)}
            </div>
        </div>
        <a className={page === "impact" ? "active" : ""} href={path("impact")}>Impact</a>
        <a className={page === "contact" ? "active" : ""} href={path("contact")}>Contact Us</a>
      </nav>
      <div className="header-actions"><Button asChild variant="site" size="site"><a href={path("donate")}>Donate Now <ArrowUpRight size={16}/></a></Button><Button variant="navIcon" className="mobile-trigger" onClick={() => setMenuOpen(!menuOpen)} aria-label={menuOpen ? "Close menu" : "Open menu"} aria-expanded={menuOpen}>{menuOpen ? <X /> : <Menu />}</Button></div>
    </header>
    <div ref={mobileMenu} className={`mobile-panel ${menuOpen ? "mobile-visible" : ""}`} aria-hidden={!menuOpen}>
      <nav aria-label="Mobile navigation">
         {[["Home", "home"], ["About Us", "about"]].map(([label, to]) => <a className="mobile-link" key={to} href={path(to ?? "home")}>{label}<ArrowUpRight size={20}/></a>)}
         <div className="mobile-parent-row mobile-link"><Link to="/what-we-do">What We Do</Link><Button variant="nav" onClick={() => setMobileWhatOpen(!mobileWhatOpen)} aria-label="Show What We Do menu" aria-expanded={mobileWhatOpen} aria-controls="mobile-what-menu"><ChevronDown size={20} className={mobileWhatOpen ? "chevron-open" : ""}/></Button></div>
          <div id="mobile-what-menu" className={`mobile-what-menu ${mobileWhatOpen ? "expanded" : ""}`} inert={!mobileWhatOpen}>
             <div className="mobile-what-inner">{workBranches.map(branch => <div className="mobile-branch" data-branch={branch.key} key={branch.key}><div className="mobile-learning-parent"><a className="mobile-link mobile-nested-link" href={`${base}/${branch.href}`}>{branch.label}</a><Button variant="nav" onClick={() => setMobileBranch(mobileBranch === branch.key ? null : branch.key)} aria-label={`Show ${branch.label} submenu`} aria-expanded={mobileBranch === branch.key} aria-controls={`mobile-${branch.key}-menu`}><ChevronDown size={18} className={mobileBranch === branch.key ? "chevron-open" : ""}/></Button></div>
              <div id={`mobile-${branch.key}-menu`} className={`mobile-learning ${mobileBranch === branch.key ? "expanded" : ""}`} inert={mobileBranch !== branch.key}><div className="mobile-learning-inner">{branch.children.map(item => <a className="mobile-link mobile-nested-link" href={`${base}/${item.href}`} key={item.href}><span><strong>{item.label}</strong>{"subtitle" in item && item.subtitle && <small>{item.subtitle}</small>}</span><ChevronRight size={17}/></a>)}</div></div>
             </div>)}</div>
           </div>
         {[["Impact", "impact"], ["Contact Us", "contact"], ["Donate Now", "donate"]].map(([label, to]) => <a className="mobile-link" key={to} href={path(to ?? "home")}>{label}<ArrowUpRight size={20}/></a>)}
      </nav>
    </div>
    <main>
      <section className={`hero ${page === "home" ? "hero-home" : "hero-inner"}`}>
        <img className="hero-photo" src={current.image} alt={page === "home" ? "Students supported by LiftedPath" : current.eyebrow} />
        <div className="hero-shade" />
        <div className="hero-content container">
          <span className="eyebrow hero-reveal">{design === "one" && page === "home" && <span className="hero-avatars"><img src={classroom.url} alt=""/><img src={scholarship.url} alt=""/><img src={mentorship.url} alt=""/><img src={orphanage.url} alt=""/></span>}{current.eyebrow}</span>
          <h1 className="hero-reveal">{current.title}</h1>
          <p className="hero-reveal">{current.intro}</p>
          {page === "home" ? <div className="hero-reveal"><LinkButton to="donate">Donate Now</LinkButton></div> : <div className="breadcrumb hero-reveal"><a href={path("home")}>Home</a><ChevronRight size={15}/><span>{current.eyebrow}</span></div>}
        </div>
        {page === "home" && design === "one" && <div className="hero-stat"><strong>25+</strong><span>Students supported</span><small>Students supported with educational assistance and scholarships.</small></div>}
        {page === "home" && design === "two" && <a href="#home-content" className="scroll-cue">SCROLL TO EXPLORE <ArrowDown size={17}/></a>}
      </section>
      {page === "home" && <>
        {design === "one" ? <div className="ticker"><div>BRIGHTER COMMUNITIES <span>✦</span> GREATER IMPACT <span>✦</span> OPPORTUNITY TODAY, POSSIBILITY TOMORROW <span>✦</span> LEARN. BUILD. GROW. <span>✦</span> LIFTING POTENTIAL <span>✦</span> INSPIRING PURPOSE <span>✦</span> CREATING FUTURES <span>✦</span> BRIGHTER COMMUNITIES</div></div> : <div className="intro-strip" id="home-content"><span>OUR PURPOSE</span><p>Opportunity changes everything. <em>And it starts with access.</em></p><ArrowDown size={28}/></div>}
        <section className="section about-section container motion-section" id={design === "one" ? "home-content" : undefined}>
          <div className="image-composition"><img src={classroom.url} alt="Students learning together"/><img src={mentorship.url} alt="Mentorship in action"/><span>2+ <small>Years of impact</small></span></div>
          <div className="section-copy"><span className="eyebrow">About our foundation</span><h2>A purpose-driven foundation breaking barriers to education</h2><p>What began as a small initiative two years ago has grown into a structured mission to restore hope and create sustainable pathways for individuals who have the passion to succeed but lack the resources to pursue their dreams.</p><div className="about-tabs" role="tablist" aria-label="About our foundation">{(["Mission", "Vision", "Commitment"] as const).map(tab => <Button key={tab} id={`about-${design}-${tab.toLowerCase()}`} role="tab" aria-controls={`about-${design}-panel`} aria-selected={focusTab === tab} variant={focusTab === tab ? "site" : "nav"} onClick={() => setFocusTab(tab)}>{tab}</Button>)}</div><div className="about-detail" role="tabpanel" id={`about-${design}-panel`} aria-labelledby={`about-${design}-${focusTab.toLowerCase()}`}><span className="mission-icon"><Target size={18}/></span><div><h3>Our {focusTab}</h3><p>{focusTab === "Mission" ? "To empower supported individuals by providing access to education, vocational training, and essential support systems, while fostering hope, dignity, and long-term self-reliance through community driven initiatives." : focusTab === "Vision" ? "To create a world where every young person has access to the education, skills, and opportunities needed to discover their purpose and reach their potential." : "We manage every contribution with integrity, accountability, and purpose, directing resources toward meaningful opportunities for the communities we serve."}</p></div></div><div className="about-actions"><LinkButton to="about">More About Us</LinkButton>{design === "one" && <a href={path("impact")}>Impact Report <ArrowUpRight size={17}/></a>}</div></div>
        </section>
        <section className="section programs-section motion-section"><div className="container"><div className="section-heading split-heading"><div><span className="eyebrow">What We Do</span><h2>Initiatives that empower</h2></div><p>Five programs, one mission: access to education, skills and the support systems that build long-term self-reliance.</p></div><div className="program-grid">{programs.map((item,i) => <a href={path("what-we-do")} className="program-card" key={item.title}><div className="program-image"><img src={item.image} alt={item.title}/></div><div className="program-info"><small>{item.kicker}</small><h3>{item.title}</h3><p>{item.line}</p><span className="text-link">{design === "one" ? "Read More" : "Explore program"} <ArrowUpRight size={17}/></span></div></a>)}{design === "one" && <div className="program-donate"><Heart size={32}/><div><h3>Sponsor a child or a whole program.</h3><p>Every naira is tracked, receipted and paid directly to institutions where possible.</p></div><Button asChild variant="site" size="site"><a href={path("donate")}>Donate Now <ArrowUpRight size={17}/></a></Button></div>}</div></div></section>
         <section className="section process-section motion-section"><div className="container"><div className="section-heading"><span className="eyebrow">How we work</span><h2>Our actions for lasting change</h2><p>We give, partner, empower and reach out — turning compassion into sustainable pathways for young people and families facing financial and social barriers</p></div><div className="pillar-grid">{pillars.map((item,i) => <a href={path(i === 0 ? "donate" : i === 3 ? "impact" : "what-we-do")} className="pillar" key={item.title}>{design === "one" && <span className="pillar-hover-wash" aria-hidden="true"/>}<span className="pillar-num">{design === "one" ? (() => { const Icon = pillarIcons[i] ?? Heart; return <Icon strokeWidth={1.35}/>; })() : `0${i+1}`}</span><h3>{item.title}</h3><p>{item.copy}</p><span className="text-link">{i === 0 ? "Donate" : "Read More"} <ArrowUpRight size={17}/></span></a>)}</div>{design === "one" && <div className="sponsor-strip"><b>₦50,000</b><span>Keeps one determined student in school for a full term.</span><a href={path("donate")}>Sponsor A Student</a></div>}</div></section>
        <section className="section stewardship-section container motion-section"><div className="section-copy"><span className="eyebrow">Why choose us</span><h2>Thoughtful Actions with Measurable Impact</h2><p>At LiftedPath Empowerment Foundation, we are committed to responsible stewardship of every resource entrusted to us.</p><div className="compact-list">{stewardship.slice(0,3).map((item,i)=><div key={item.title}><b>0{i+1}</b><span><strong>{item.title}</strong><small>{item.copy}</small></span></div>)}</div><LinkButton to="impact">Learn More</LinkButton></div><div className="single-photo"><img src={skills.url} alt="Vocational skills training"/>{design === "one" ? <><div className="call-note"><Phone size={18}/><span><strong>Call Us</strong><small>+234 806 812 8785</small></span></div><img className="steward-inset" src={orphanage.url} alt="Community outreach"/></> : <div className="photo-note"><strong>85%</strong><span>of funds go directly to programs</span></div>}</div></section>
        {/* Our Commitment section hidden for now — re-enable by removing `false &&` */}{false && design === "one" && <section className="section commitment-section motion-section"><div className="container"><span className="eyebrow">Our Commitment</span><div className="commitment-heading"><div><h2>Thoughtful Actions with Measurable Impact</h2><p>Every contribution is managed with integrity, accountability, and purpose.</p></div><LinkButton to="contact">Contact Us</LinkButton></div><div className="commitment-grid"><div className="commitment-panel"><strong>85%</strong><span className="commitment-icon"><Target size={18}/></span><h3>Funds To Programs</h3><p>We prioritize the direct delivery of our programs, ensuring the majority of funds entrusted to LiftedPath create meaningful opportunities for the communities we serve.</p></div><img src={orphanage.url} alt="Community outreach"/><div className="commitment-panel"><div className="impact-avatars"><img src={students.url} alt=""/><img src={classroom.url} alt=""/><img src={scholarship.url} alt=""/><img src={mentorship.url} alt=""/></div><h3>Real Community Impact</h3><p>Every donation is documented and tracked, with records and receipts maintained as a clear account of funds received and their use.</p><ul><li>10 families reached</li><li>2 school partnerships</li><li>2 years of orphanage visits</li></ul></div></div><div className="section-endline"><span>Let’s lift young people together.</span><a href={path("contact")}>Get In Touch</a></div></div></section>}
        <section className="section steps-section motion-section"><div className="container"><div className="section-heading"><div><span className="eyebrow">Our process</span><h2>From planning to action, every step counts</h2></div>{design === "one" && <LinkButton to="about">Learn More</LinkButton>}</div><div className="steps-grid">{["Identify real needs", "Fund & pay directly", "Mentor & follow up"].map((x,i)=><div key={x}><span>0{i+1}</span><h3>{x}</h3><p>{["Through school partnerships and our Community Outreach Representatives.", "Financial education support paid directly to schools and training providers.", "Coaching and mentorship keep every beneficiary on track with their goals."][i]}</p></div>)}</div></div></section>
          {design === "one" ? <section className="impact-summary motion-section"><div className="container stats-grid">{stats.map(item => <div key={item.title}><CountUp value={item.value}/><h3>{item.title}</h3><p>{item.copy}</p></div>)}</div></section> : <Cta path={path} image={students.url}/>}
      </>}
      {page === "about" && <>
        <section className="section container about-inner motion-section"><div className="section-copy"><span className="eyebrow">Lifting potential, inspiring purpose</span><h2>A purpose-driven foundation breaking barriers to education</h2><p>LiftedPath Empowerment Foundation is a purpose driven non-governmental organization founded on a deep commitment to breaking barriers to education and opportunity for children, teenagers, and young adults.</p><p>What began as a small initiative two years ago has grown into a structured mission to restore hope and create sustainable pathways for individuals who have the passion to succeed but lack the resources to pursue their dreams.</p></div><div className="single-photo"><img src={classroom.url} alt="Young people learning in a classroom"/></div></section>
        <section className="section belief-band motion-section"><div className="container belief-grid"><div><span className="eyebrow">01 / Our mission</span><h2>Empower individuals to thrive.</h2><p>To empower supported individuals by providing access to education, vocational training, and essential support systems, while fostering hope, dignity, and long-term self-reliance through community driven initiatives.</p></div><div><span className="eyebrow">02 / Our vision</span><h2>Opportunity for every young person.</h2><p>To create a world where every young person, especially those facing financial and social barriers, has access to the education, skills, and opportunities needed to discover their purpose, reach their potential, and contribute meaningfully to society.</p></div></div></section>
        <section className="section container motion-section"><div className="section-heading"><span className="eyebrow">Our pillars</span><h2>Stronger people. Brighter communities. Greater impact.</h2></div><div className="pillar-grid">{pillars.map((item,i)=><div className="pillar" key={item.title}><span className="pillar-num">0{i+1}</span><h3>{item.title}</h3><p>{item.copy}</p></div>)}</div></section>
        <section className="section goals-section motion-section"><div className="container goals-layout"><div className="goals-intro"><span className="eyebrow">Future goals</span><h2>Where we are headed next.</h2><p>We are building lasting pathways for more young people and families.</p></div><div className="goal-list">{goals.map((goal,i)=><div key={goal.title}><span>0{i+1}</span><strong>{goal.title}</strong><p>{goal.description}</p><ArrowUpRight size={20}/></div>)}</div></div></section><Cta path={path} image={students.url}/>
      </>}
       {page === "programs" && <><section className="section container"><div className="section-heading motion-section"><span className="eyebrow">What We Do</span><h2>{workDetail ? workDetail.title : "Education, guidance and community support."}</h2><p>{workDetail ? workDetail.subtitle ?? workDetail.description : "Explore the three areas of our work and the initiatives within each one."}</p></div>{workDetail ? <article className="feature-row motion-section"><div className="feature-image"><img src={workDetail.image} alt={workDetail.title}/></div><div className="feature-copy"><span className="eyebrow">What We Do</span><h2>{workDetail.title}</h2>{workDetail.subtitle && <h3>{workDetail.subtitle}</h3>}<p>{workDetail.description}</p><LinkButton to="contact">Get in touch</LinkButton></div></article> : workOverview.map((item,i) => { const branch = workBranches.find(entry => entry.key === item.key); return <article className={`feature-row motion-section ${i%2 ? "reverse" : ""}`} key={item.key}><div className="feature-image"><img src={item.image} alt={item.title}/></div><div className="feature-copy"><span className="eyebrow">0{i+1} / What We Do</span><h2>{item.title}</h2><p>{item.description}</p><div className="work-overview-links">{branch?.children.map(child => <a href={`${base}/${child.href}`} key={child.href}><span><strong>{child.label}</strong>{"subtitle" in child && child.subtitle && <small>{child.subtitle}</small>}</span><ArrowUpRight size={18}/></a>)}</div><LinkButton to={branch?.href ?? "what-we-do"}>Explore {item.title}</LinkButton></div></article>; })}</section><Cta path={path} image={students.url}/></>}
      {page === "learning" && <><section className="section container"><div className="section-heading motion-section"><span className="eyebrow">LiftedPath Learning</span><h2>{detail ? `${detail.title}: ${detail.subtitle}` : "Learning grows with every step."}</h2><p>{detail ? detail.description : "Four pathways that meet learners where they are and help them move forward."}</p></div>{detail ? <article className="feature-row motion-section"><div className="feature-image"><img src={detail.image} alt={detail.subtitle}/></div><div className="feature-copy"><span className="eyebrow">Education pathway</span><h2>{detail.title}</h2><h3>{detail.subtitle}</h3><p>{detail.description}</p><p>LiftedPath supports learning through educational assistance, training opportunities and the guidance young people need to keep going.</p><LinkButton to="contact">Get in touch</LinkButton></div></article> : <div className="learning-grid">{learning.map((item,i)=><a href={`${path("learning")}/${item.slug}`} className="learning-card motion-section" key={item.slug}><div><img src={item.image} alt={item.subtitle}/></div><span>0{i+1} / LIFTEDPATH LEARNING</span><h3>{item.title}</h3><p>{item.subtitle}</p><ArrowUpRight size={25}/></a>)}</div>}</section><Cta path={path} image={students.url}/></>}
      {page === "impact" && <><section className="section container motion-section"><div className="section-heading"><span className="eyebrow">Our success so far</span><h2>Progress made together.</h2><p>At LiftedPath Empowerment Foundation, we are committed to responsible stewardship of every resource entrusted to us.</p></div><div className="stats-grid">{stats.map(item=><div key={item.title}><CountUp value={item.value}/><h3>{item.title}</h3><p>{item.copy}</p></div>)}</div></section><section className="section belief-band motion-section"><div className="container"><span className="eyebrow">Financial transparency</span><h2>Responsible stewardship of every resource entrusted to us.</h2><p>Every contribution is managed with integrity, accountability, and purpose.</p><div className="steward-grid">{stewardship.map((item,i)=><div key={item.title}><span>0{i+1}</span><h3>{item.title}</h3><p>{item.copy}</p></div>)}</div></div></section><section className="section container motion-section"><div className="section-heading"><span className="eyebrow">Questions</span><h2>What donors ask us.</h2></div><div className="faq-list">{[["Where does my donation actually go?", "85% of funds go directly to programs. Where possible, educational payments are made directly to schools and training providers."], ["Can I give in kind instead of cash?", "Yes. We welcome essential supplies and other in-kind support. Get in touch so we can coordinate what's needed."], ["Can I sponsor one specific student?", "Yes. Contact us to discuss education sponsorship and how support can be directed."], ["How do I volunteer?", "Send us a message through the contact page and tell us how you'd like to help."]].map(([q,a],i)=><div key={q}><Button variant="faq" onClick={()=>setFaqOpen(faqOpen===i?null:i)} aria-expanded={faqOpen===i}>{q}<ChevronDown className={faqOpen===i ? "chevron-open" : ""}/></Button>{faqOpen===i && <p>{a}</p>}</div>)}</div></section><Cta path={path} image={students.url}/></>}
      {page === "contact" && <section className="section container contact-layout motion-section"><div className="contact-details"><span className="eyebrow">Let's talk</span><h2>Every great change begins with a conversation.</h2><p>Volunteer, partner, sponsor a student, or simply ask a question — we would love to hear from you.</p><a href="tel:+2348068128785"><Phone size={23}/><span><small>Phone</small>+234 806 812 8785</span></a><a href="mailto:liftedpathfoundation@gmail.com"><Mail size={23}/><span><small>Email</small>liftedpathfoundation@gmail.com</span></a><div className="address"><MapPin size={23}/><span><small>Office</small>19, George Tienne Crescent, Hilltop Estate, Aboru, Iyana Ipaja, Lagos, Nigeria.</span></div></div><form className="contact-form" onSubmit={e=>{e.preventDefault(); const data = new FormData(e.currentTarget); window.location.href=`mailto:liftedpathfoundation@gmail.com?subject=${encodeURIComponent(`LiftedPath enquiry: ${data.get("interest")}`)}&body=${encodeURIComponent(`Name: ${data.get("name")}\nEmail: ${data.get("email")}\n\n${data.get("message")}`)}`;}}><span className="eyebrow">Get in touch</span><h2>Send us a message</h2><label>Full name<input required name="name" placeholder="Your full name"/></label><label>Email<input required name="email" type="email" placeholder="you@example.com"/></label><label>I would like to<select name="interest"><option>Volunteer my time</option><option>Sponsor a child or program</option><option>Partner as a school or organisation</option><option>Make an in-kind donation</option><option>Something else</option></select></label><label>Message<textarea required name="message" rows={5} placeholder="Tell us a little about how you'd like to help"/></label><Button variant="site" size="site" type="submit">Send message <ArrowUpRight size={17}/></Button><small>Opens your email app to send your message.</small></form></section>}
      {page === "donate" && <section className="section container donate-layout motion-section"><div className="donate-form"><span className="eyebrow">Make a difference</span><h2>Choose your impact.</h2><p>Your generosity can keep learning within reach. To arrange your gift, select an amount and contact our team.</p><h3>Frequency</h3><div className="segmented">{["One-off","Monthly"].map(x=><Button key={x} variant={frequency===x ? "selected" : "choice"} onClick={()=>setFrequency(x)}>{x}</Button>)}</div><div className="gift-heading"><h3>Choose an impact</h3><label className="gift-currency-label" htmlFor="gift-currency">Currency<select id="gift-currency" value={currency} onChange={e=>{setCurrency(e.target.value as GiftCurrency);setCustomAmount("");setHoveredGift(null);setOpenGift(amount)}}><option value="NGN">NGN · ₦</option><option value="USD">USD · $</option><option value="GBP">GBP · £</option></select></label></div><div className="amount-grid" onMouseLeave={()=>setHoveredGift(null)}>{giftOptions.map(item=><div className="gift-option" key={item.amount}><Button variant={amount===item.amount && !customAmount ? "amountSelected" : "amount"} aria-pressed={amount===item.amount && !customAmount} aria-describedby={`gift-tip-${item.amount}`} onMouseEnter={()=>setHoveredGift(item.amount)} onFocus={()=>setHoveredGift(item.amount)} onBlur={()=>setHoveredGift(null)} onClick={()=>{setAmount(item.amount);setCustomAmount("");setOpenGift(item.amount)}}><strong>{formatGift(item.amount / giftCurrencies[currency].ngnPerUnit, currency)}</strong><span>{item.title}</span></Button></div>)}</div><div className="gift-detail" aria-live="polite"><Heart size={16}/><span>{giftOptions.find(item=>item.amount===(hoveredGift ?? openGift ?? (customAmount ? null : amount)))?.detail ?? "Select an amount to see how your gift could help."}</span></div><p className="gift-rate-note">{currency === "NGN" ? "Select a gift amount or enter your own below." : `Indicative conversion: ${formatGift(giftCurrencies[currency].ngnPerUnit, "NGN")} = ${formatGift(1, currency)}. Confirm the final amount with our team before giving.`}</p><label>Other amount ({currency})<input type="number" min="1" value={customAmount} onChange={e=>{setCustomAmount(e.target.value);setOpenGift(null)}} placeholder={`Enter amount in ${currency}`}/></label><label>Direct my gift to<select value={program} onChange={e=>setProgram(e.target.value)}><option>Wherever the need is greatest</option>{programs.map(x=><option key={x.title}>{x.title}</option>)}</select></label><Button variant="site" size="site" asChild><a href={`mailto:liftedpathfoundation@gmail.com?subject=${encodeURIComponent("Donation enquiry")}&body=${encodeURIComponent(`I would like to arrange a ${frequency.toLowerCase()} donation of ${formatGift(Number(customAmount) || amount / giftCurrencies[currency].ngnPerUnit, currency)} ${currency} for ${program}. Please let me know the next steps and confirm the final amount.`)}`}>Arrange my gift <ArrowUpRight size={17}/></a></Button><small className="donate-note">This opens your email app to coordinate a donation. No payment is collected on this website.</small></div><aside className="donate-aside"><img src={scholarship.url} alt="Student education"/><div><Heart size={26}/><strong>85% of funds go directly to programs.</strong><p>Every donation is documented and receipted, and where possible we pay schools and training providers directly.</p></div></aside></section>}
    </main>
      <footer className="footer">{design === "one" ? <div className="container"><div className="footer-top"><div className="footer-brand"><a href={path("home")}><img src={lightLogo.url} alt="LiftedPath Empowerment Foundation"/></a><p>Lifting Potential, Inspiring Purpose and Creating Futures.</p><div className="footer-social"><a href="tel:+2348068128785" aria-label="Call LiftedPath"><Phone size={18}/></a><a href="mailto:liftedpathfoundation@gmail.com" aria-label="Email LiftedPath"><Mail size={18}/></a></div></div><div><h3>Quick Links</h3><a href={path("home")}>Home</a><a href={path("about")}>About Us</a><a href={path("what-we-do")}>What We Do</a><a href={path("impact")}>Impact</a><a href={path("contact")}>Contact Us</a><a href={path("donate")}>Donate</a></div><div><h3>What We Do</h3>{workBranches.map(item => <a href={`${base}/${item.href}`} key={item.key}>{item.label}</a>)}</div><div><h3>Our Location</h3><p>19, George Tienne Crescent, Hilltop Estate, Aboru, Iyana Ipaja, Lagos, Nigeria.</p><a href="tel:+2348068128785">+234 806 812 8785</a><a href="mailto:liftedpathfoundation@gmail.com">liftedpathfoundation@gmail.com</a></div></div><div className="footer-bottom"><span>Copyright © {new Date().getFullYear()} LiftedPath Empowerment Foundation. All Rights Reserved.</span><span>RC 9872033</span></div><div className="footer-watermark" ref={wordmark}>LiftedPath</div></div> : <div className="container"><div className="footer-top"><div className="footer-brand"><a href={path("home")}><img src={lightLogo.url} alt="LiftedPath Empowerment Foundation"/></a><p>Lifting potential, inspiring purpose and creating futures.</p></div><div><h3>Explore</h3><a href={path("home")}>Home</a><a href={path("about")}>About Us</a><a href={path("what-we-do")}>What We Do</a><a href={path("learning")}>LiftedPath Learning</a><a href={path("impact")}>Our Impact</a></div><div><h3>Get involved</h3><a href={path("donate")}>Donate</a><a href={path("contact")}>Volunteer</a><a href={path("contact")}>Partner with us</a><a href={path("contact")}>Contact Us</a></div><div><h3>Reach us</h3><a href="tel:+2348068128785">+234 806 812 8785</a><a href="mailto:liftedpathfoundation@gmail.com">liftedpathfoundation@gmail.com</a><p>Lagos, Nigeria</p></div></div><div className="footer-bottom"><span>© {new Date().getFullYear()} LiftedPath Empowerment Foundation</span><span>Stronger people. Brighter communities. Greater impact.</span></div><div className="footer-giant" ref={wordmark}>LiftedPath</div></div>}</footer>
  </div>;
}
function Cta({ path, image }: { path: (p:string)=>string; image:string }) { return <section className="cta-band motion-section"><img src={image} alt="LiftedPath students"/><div className="container"><div><span className="eyebrow">Join the journey</span><h2>Let's lift young people together.</h2><Button variant="site" size="site" asChild><a href={path("contact")}>Get in touch <ArrowUpRight size={17}/></a></Button></div></div></section>; }
