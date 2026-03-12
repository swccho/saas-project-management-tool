# UI Design Guardian — Reference

Extended page-specific guidance, avoid checklists, and extraction rules. Use when implementing or reviewing specific pages or when the agent needs deeper detail.

---

## Dashboard (Stripe-like)

- Top stat cards with title, value, optional trend, small supporting text
- Structured sections: recent activity, my tasks, active projects, optional small analytics
- Layout: top row stats → middle content grid → bottom detail sections
- Do not overload with too many widgets

---

## Projects Page

- Page header with clear title and primary CTA (create project)
- Filters (status, search) that do not overpower content
- List or card layout with clean project summaries
- Project name and status easily scannable; progress and team secondary but visible

---

## Kanban Board (Linear-like)

- Calm columns (e.g. Backlog, Todo, In Progress, Review, Done)
- Lightweight task cards: title, labels, assignee avatars, due date, priority, minimal metadata
- Strong readability; restrained metadata; subtle drag-and-drop states
- Task cards must not be visually noisy
- Column headers compact; column width balanced

---

## Task Details Drawer

- Right-side drawer/sheet
- Sections: title, metadata, assignees, labels, due date, priority
- Tabs: Description, Attachments, Activity, Subtasks
- Structured and premium; content split clearly; metadata easy to update

---

## Members Page

- Avatar, name, email, role, status, actions
- Structured and trustworthy
- Restrained badges for role/status; dropdowns for actions
- Clean and safe membership management

---

## Settings Page

- Grouped card sections: Workspace Settings, Notifications, Account, Members
- Destructive actions clearly separated
- Form sections not crowded; calm and structured

---

## Things to Avoid (Checklist)

Do not generate UI that is:

- Overdesigned
- Too colorful
- Dashboard-template-like
- Visually cluttered
- Full of unnecessary gradients
- Inconsistent with shadcn-vue
- Too dense
- Too decorative
- Mismatched across pages

Do not create:

- Generic admin template feel
- Loud marketing-style UI
- Old enterprise panel look
- Cramped CRUD interface
- Random collection of components
- Multiple competing focal points on one screen
- Giant hero sections inside app pages
- Oversized full-screen modals unless necessary

---

## Reusable Component Extraction

When the same pattern appears in multiple places:

- Extract to a shared component in the appropriate `components/` location
- Keep props minimal and naming clear
- Document slot/usage if non-obvious
- Do not leave page-specific visual hacks in the design system
