# .cursor/commands/add-tests.md

Add meaningful tests for a feature or module in this SaaS Project Management Platform.

## Input

The user will provide:

- feature name or module name
- optionally the exact files/classes/endpoints to test
- optionally whether to focus on feature tests, unit tests, or both

Example:

`/add-tests projects`
`/add-tests move-task action`
`/add-tests workspace invitations`

---

## Project Context

This project uses:

- Laravel 12
- PHPUnit
- Laravel testing utilities
- model factories
- RefreshDatabase

The application is workspace-based and multi-tenant.

Important testing priorities:

- validation
- authorization
- workspace isolation
- business workflow correctness
- regression safety

---

## Your Task

Review the target feature/module and add meaningful missing tests.

Prefer testing real behavior over artificial coverage.

You may add:

- Feature tests for endpoints
- Unit tests for actions/services
- Policy/authorization-focused tests
- Workspace isolation tests
- Regression tests for identified edge cases

---

## Testing Standards

### Feature Tests

Use Feature tests for:

- API endpoints
- request/response behavior
- validation failures
- authorization failures
- multi-tenant access restrictions
- high-level workflow behavior

### Unit Tests

Use Unit tests for:

- action classes
- service classes
- ordering logic
- workflow logic
- isolated domain decisions

### Test Setup

- use factories
- create realistic relationships
- keep setup minimal but meaningful
- avoid fragile repetitive setup
- tests must be independent

---

## Required Coverage Areas

When relevant, add tests for:

### Validation
- missing required fields
- invalid values
- invalid nested arrays
- invalid foreign key ownership assumptions

### Authorization
- allowed roles can perform the action
- blocked roles cannot perform the action
- non-members cannot access the resource

### Workspace Isolation
- user from workspace A cannot view workspace B resource
- user from workspace A cannot mutate workspace B resource
- nested resources also respect workspace boundaries

### Workflow Integrity
- multi-step behavior completes correctly
- side effects happen correctly
- transactions protect against partial writes if relevant

### Side Effects
- activity logs created when expected
- notifications created when expected

---

## Test Naming Rules

Use clear test names like:

- `test_user_can_create_project`
- `test_member_cannot_delete_project`
- `test_user_cannot_access_other_workspace_tasks`
- `test_task_move_updates_column_and_position`

Avoid vague names like:

- `test_project`
- `test_case_1`
- `test_action`

---

## Output Expectations

Produce:

1. the added test files or updates
2. a short explanation of what scenarios are covered
3. any important gaps still remaining
4. any assumptions made about the current implementation

Do not add weak tests that only confirm trivial implementation details.
Prefer behavioral confidence.

---

## Definition of Done

The test work is only complete when:

- meaningful scenarios are covered
- validation is tested where relevant
- authorization is tested where relevant
- workspace isolation is tested where relevant
- behavior is tested, not just implementation details
- tests are readable and maintainable
