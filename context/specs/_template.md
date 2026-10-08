# Unit NN: [Feature Name]

## Goal

One or two sentences. What exists when this unit is finished, and how someone can see it.

## Design

Visual and structural decisions for this unit. Refer to tokens in `context/ui-context.md` by name. Describe desktop and 390px behavior. Skip this section when the unit has no UI.

## Implementation

### [Component or file]

What to build, the WordPress block name or REST shape, and the editor control that owns each visible value.

### [Next piece]

What to build.

## Dependencies

- Package or plugin, and why this unit needs it.

## Verify when done

- [ ] The visible result named in `context/specs/00-build-plan.md` is true
- [ ] Editor-controlled values for this unit are fields, blocks, menus, media, or global styles
- [ ] No invariant in `context/architecture.md` was broken
- [ ] Desktop and 390px checked, when the unit has UI
- [ ] Reduced motion checked, when the unit animates
- [ ] Theme activates with no PHP fatal, when the unit touches PHP
- [ ] `npm run build` passes, when the unit touches the frontend
- [ ] `context/progress-tracker.md` updated
