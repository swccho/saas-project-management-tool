# .cursor/commands/improve-ui.md

Improve the UI of a page, flow, or component in this SaaS Project Management Platform.

## Input

The user will provide:

- page name, feature name, or component name
- optionally what feels wrong today
- optionally whether the goal is layout polish, hierarchy, usability, consistency, or full redesign

Example:

`/improve-ui dashboard`
`/improve-ui project board`
`/improve-ui task details drawer`

---

## Project Context

This project uses:

- Vue 3
- Tailwind CSS
- shadcn-vue
- Lucide icons

Selected UI direction:

- Linear-style boards
- Stripe-style dashboards
- minimal color palette
- modern typography
- clean startup SaaS feel

The UI must feel:

- premium
- calm
- minimal
- modern
- structured
- product-like

It must not feel:

- cluttered
- noisy
- generic admin-template like
- overly colorful
- inconsistent

---

## Your Task

Improve the target UI while preserving the product direction.

You may update:

- page layout
- hierarchy
- spacing
- cards
- tables
- dialogs
- drawers
- forms
- buttons
- badges
- empty states
- loading states
- interaction feedback

Extract reusable components where helpful.

---

## UI Standards to Follow

### Visual Direction

Always prefer:

- neutral surfaces
- subtle borders
- subtle shadows
- clean spacing
- modern typography
- strong but quiet hierarchy
- one primary action per area
- compact professional interfaces

### Components

Use shadcn-vue components wherever appropriate.

Prefer:

- Card
- Button
- Input
- Table
- Badge
- Dialog
- Sheet
- Tabs
- DropdownMenu
- Select
- Avatar
- Toast

### Layout and Spacing

Use consistent spacing rhythm such as:

- `p-6`
- `space-y-6`
- `gap-6`

Avoid:

- cramped layouts
- excessive nested containers
- random padding differences
- oversized hero-like app sections

### UX

Include or improve:

- loading states
- empty states
- validation messages
- destructive confirmations
- clearer actions
- reduced visual noise
- better scanability

### Page-Type Rules

For dashboards:
- use Stripe-style stat cards and structured sections
- avoid too many competing widgets

For boards:
- use Linear-style clean columns and calm task cards
- avoid over-decorated task cards

For forms:
- group fields logically
- keep actions obvious
- keep error states clear

For settings:
- use calm card sections
- separate destructive actions clearly

---

## Refactoring Expectations

When improving UI:

- extract repeated UI into reusable components
- do not duplicate styles unnecessarily
- keep code modular
- improve readability of component templates
- follow existing file structure rules

---

## Output Expectations

Produce:

1. improved UI implementation
2. a summary of what changed visually and structurally
3. any extracted reusable components
4. any UX issues fixed
5. any assumptions made

Do not redesign into a different visual style.
Do not add decorative noise.
Do not break the existing product direction.

---

## Definition of Done

The UI improvement is only complete when:

- it follows Linear + Stripe direction
- spacing and hierarchy are improved
- shadcn-vue is used well
- the interface feels more product-like
- reusable patterns are respected
- loading/empty/error/feedback states are handled where relevant
- the result looks cleaner, calmer, and more premium
