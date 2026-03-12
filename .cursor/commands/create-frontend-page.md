# .cursor/commands/create-frontend-page.md

Create a new frontend page for this SaaS Project Management Platform.

## Input

The user will provide:

- page name
- page purpose
- route path
- data to display
- actions available on the page
- whether the page is dashboard, list, board, settings, detail, or form based

Example:

`/create-frontend-page projects`

---

## Project Context

This project uses:

- Vue 3
- Composition API
- Pinia
- Vue Router
- Tailwind CSS
- shadcn-vue

UI style must follow:

- Linear-style boards
- Stripe-style dashboards
- modern SaaS design
- minimal color palette
- clean typography
- clean spacing
- crisp cards and tables

Important rules:

- pages must stay modular
- reusable UI must be extracted into components
- use shadcn-vue wherever appropriate
- keep the interface calm and product-like
- support loading, empty, success, and error states
- do not generate cluttered admin-template UI

---

## Your Task

Create or update the frontend page and its supporting structure.

Generate or update where relevant:

- page component
- route entry
- page-level components
- reusable child components
- local composables if needed
- store integration if needed
- loading state UI
- empty state UI
- error state UI
- dialogs/drawers if needed

---

## UI Requirements

### Design Style

Follow the project UI standards:

- Stripe-style dashboard structure for analytics/list pages
- Linear-style board/task experience for board/task pages
- whitespace-first layout
- subtle borders
- minimal colors
- Inter-style typography feel
- shadcn-vue components
- Lucide icons

### Layout

- use the existing app shell/layout
- keep page spacing consistent with project standards
- use `p-6`, `space-y-6`, `gap-6` style spacing rhythm
- avoid over-nesting containers

### Components

Extract reusable components when patterns repeat, such as:

- page header
- filter bar
- stat card
- table wrapper
- empty state
- form section
- task card
- side drawer
- detail panel

### UX States

Every page must include:

- loading state
- empty state
- error state
- success feedback where needed
- confirmation for destructive actions where needed

### Forms and Actions

If the page includes create/edit actions:

- use dialogs, drawers, or sheets appropriately
- show inline validation messages
- disable submit during requests
- provide success/failure feedback

---

## State Management Rules

- use local state when possible
- use Pinia only when the page needs shared or cross-page state
- avoid creating unnecessary global stores
- extract reusable logic into composables when appropriate

---

## Output Expectations

Produce code that is:

- modular
- clean
- responsive
- consistent with project UI
- easy to extend
- aligned with existing project structure

Also explain:

1. what files were added or updated
2. what components were extracted
3. what UI states were included
4. whether store/composable changes were required
5. any assumptions made

---

## Definition of Done

The page is only complete when:

- route exists
- page component exists
- supporting components exist where needed
- UI follows Linear + Stripe direction
- loading/empty/error states exist
- interactions are clear
- code follows project structure rules
- component reuse is respected
