# .cursor/commands/create-api-module.md

Create a new backend API module for this SaaS Project Management Platform.

## Input

The user will provide:

- module name
- resource purpose
- main fields
- relationships
- role/permission expectations
- whether the resource is workspace-owned, project-owned, board-owned, or global

Example:

`/create-api-module projects`

---

## Project Context

This project uses:

- Laravel 12
- PHP 8.3
- MySQL
- Redis
- Vue 3
- Pinia
- Vue Router
- Tailwind CSS
- shadcn-vue

This is a workspace-based multi-tenant application.

Important rules:

- controllers must stay thin
- validation must use Form Requests
- authorization must use Policies
- business logic must live in Actions or Services
- API responses must stay consistent
- workspace isolation must always be enforced
- no billing logic
- no plan limit logic

---

## Your Task

Create a complete backend API module following the existing project rules and structure.

Generate or update the following where relevant:

- migration
- model
- relationships
- factory if useful
- controller
- Form Requests
- Policy
- API Resource if needed
- Action / Service classes for non-trivial workflows
- routes
- Feature tests
- Unit tests for complex action logic if needed

---

## Backend Requirements

### Architecture

- keep controllers thin
- place complex workflows in `app/Actions/...` or `app/Services/...`
- follow project domain structure
- match existing naming conventions
- do not create random helper classes

### Validation

- every write endpoint must validate input
- use dedicated Form Requests
- validate enums/status/role-like fields strictly
- validate nested arrays explicitly
- validate foreign key ownership where relevant

### Authorization

- every protected action must be authorized
- use Policy methods for create, view, update, delete, archive, assign, reorder, or manage actions where applicable
- never rely on frontend-only protection

### Multi-Tenancy

- if the module belongs to a workspace, scope all access correctly
- if the module belongs to a project/board/task, ensure the parent chain belongs to the current workspace
- add tests proving cross-workspace access is blocked

### Database

- use proper foreign keys
- add indexes for common filters and joins
- use transactions for multi-step writes
- use clear migration names
- use proper column types and nullability

### API Design

- use resource-oriented endpoints
- use correct HTTP verbs
- return correct status codes
- paginate list endpoints when appropriate
- use API Resources when response shape matters

---

## Testing Requirements

Add meaningful tests for:

- successful create/read/update/delete flows as relevant
- validation failures
- authorization failures
- workspace isolation failures
- role-based access if relevant
- action/service behavior if the workflow is non-trivial

Use:

- Feature tests for endpoints
- Unit tests for isolated action logic where valuable
- factories for realistic data setup

---

## Output Expectations

Produce code that is:

- production-style
- clean
- maintainable
- testable
- consistent with Laravel conventions
- consistent with project rules

Also explain:

1. what files were added or updated
2. how workspace isolation is enforced
3. what tests were added
4. any assumptions made

---

## Definition of Done

The module is only complete when:

- migration exists
- model exists
- relationships are correct
- controller exists
- Form Requests exist
- Policy exists
- Action/Service exists for non-trivial logic
- routes exist
- tests exist
- validation works
- authorization works
- tenant scoping works
- code follows project structure rules
