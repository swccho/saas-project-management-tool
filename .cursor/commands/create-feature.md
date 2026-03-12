# .cursor/commands/create-feature.md

Implement a complete feature for this SaaS Project Management Platform.

## Input

The user will provide:

- feature name
- feature purpose
- affected entities
- backend requirements
- frontend requirements
- roles/permissions involved
- whether activity logs or notifications are needed

Example:

`/create-feature task comments`

---

## Project Context

This is a workspace-based SaaS-style project management system built with:

- Laravel 12
- PHP 8.3
- MySQL
- Redis
- Vue 3
- Pinia
- Vue Router
- Tailwind CSS
- shadcn-vue

Architecture rules:

- thin controllers
- Form Request validation
- Policy authorization
- Actions/Services for business logic
- workspace isolation is mandatory
- clean project structure
- meaningful tests required
- UI must follow Linear + Stripe style

---

## Your Task

Implement the feature end-to-end.

That may include:

### Backend
- migration changes if needed
- models or model updates
- relationships
- controllers
- requests
- policies
- resources
- actions/services
- routes
- jobs/events/listeners if justified
- notifications if needed
- activity log creation if needed

### Frontend
- pages or page updates
- components
- dialogs/drawers/forms
- composables
- store integration if needed
- API integration
- loading/empty/error states
- user feedback states

### Tests
- Feature tests
- Unit tests for non-trivial business logic
- authorization tests
- workspace isolation tests
- regression tests if relevant

---

## Required Implementation Process

### 1. Understand the domain first

Before coding, determine:

- which entity owns the feature
- whether it is workspace-owned or nested under workspace-owned entities
- which users can access it
- whether it affects notifications or activity logs
- whether it affects ordering/workflow logic

### 2. Follow the project architecture

- keep controllers thin
- use dedicated request classes
- use policies
- use actions/services for workflows
- use transactions for multi-step writes
- keep UI modular
- keep state management intentional

### 3. Respect multi-tenancy

- all data access must be scoped properly
- no cross-workspace access
- no trusting frontend workspace assumptions
- add tests proving isolation

### 4. Respect UI standards

- use shadcn-vue
- keep layouts minimal and premium
- follow Linear + Stripe feel
- add proper states
- avoid clutter

---

## Activity Logs and Notifications

When relevant, include:

### Activity Logs
For important user actions such as:
- create
- update
- move
- assign
- comment
- delete/archive

### Notifications
For meaningful user-facing events such as:
- assignments
- mentions
- invitations
- key updates

Do not add spammy notifications or noisy logs.

---

## Testing Requirements

Every feature must be tested meaningfully.

Include tests for:

- happy path behavior
- validation errors
- authorization rules
- workspace isolation
- side effects like notifications/activity logs when relevant

Use:

- Feature tests for endpoints
- Unit tests for actions/services where logic is complex
- factories for realistic setup

---

## Output Expectations

Produce:

- clean implementation
- file-by-file changes
- explanation of architecture choices
- explanation of authorization/scoping
- explanation of tests added
- explanation of any assumptions

Do not produce quick patchy code.
Do not skip tests for important logic.
Do not mix large workflows into controllers.

---

## Definition of Done

A feature is only complete when:

- backend logic exists
- frontend logic exists if required
- validation exists
- authorization exists
- workspace isolation exists
- activity logs exist when appropriate
- notifications exist when appropriate
- tests exist
- UI states exist
- code follows project structure and design rules
