<!-- LOVABLE:BEGIN -->
> [!IMPORTANT]
> This project is connected to [Lovable](https://lovable.dev). Avoid rewriting
> published git history — force pushing, or rebasing/amending/squashing commits
> that are already pushed — as it rewrites history on Lovable's side and the
> user will likely lose their project history.
>
> Commits you push to the connected branch sync back to Lovable and show up in
> the editor, so keep the branch in a working state.
<!-- LOVABLE:END -->

- Serve only LiftedPath Original at the unprefixed routes with no design selector or Design 02 routes; this preserves the sole supported design without altering its presentation.
- Use CDN asset pointers for site photos and logo while keeping a cropped square favicon in public; this preserves source clarity without committing large media.
- Define LiftedPath's supplied swatches once as semantic CSS tokens; this keeps the Original design color-consistent.
- Preserve Original's staggered ScrollTrigger content reveals and continuous action-icon plus hover animations, with reduced-motion fallbacks; this retains existing behavior and accessible motion preferences.
- Keep What We Do navigation hierarchical, with overview links and individually routed learning, guidance, and community items; this prevents desktop and mobile navigation from diverging.
- Pin the About goals introduction with GSAP ScrollTrigger only on motion-enabled desktop until goal five, leaving mobile and the final two goals to scroll naturally; this keeps the explanation in view without trapping the section.
