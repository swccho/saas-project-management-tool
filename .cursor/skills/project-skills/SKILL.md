---
name: project-skills
description: Defines technologies, architecture, and coding standards for the SaaS Project Management Platform. Use when generating or modifying backend (Laravel/PHP), frontend (Vue/Pinia), database, UI, tests, or security in this project.
---

# Project Skills — SaaS Project Management Platform

These are the technologies, patterns, and engineering practices used in this project.
All generated code must follow these skills and conventions.

---

# Backend Skills

## PHP & Laravel

- PHP 8.3
- Laravel 12
- Laravel Eloquent ORM
- Laravel Form Request Validation
- Laravel Policies for Authorization
- Laravel API Resources
- Laravel Queues
- Laravel Events & Listeners
- Laravel Sanctum Authentication
- Laravel Feature Testing
- Laravel Unit Testing

## Backend Architecture

- Modular Laravel architecture
- Service layer pattern
- Thin controllers
- Domain-focused services/actions
- RESTful API design
- Transaction-safe database operations
- Multi-tenant architecture using workspaces
- Workspace data isolation

---

# Database Skills

- MySQL database design
- Relational schema modeling
- Foreign key relationships
- Database indexing strategies
- Query optimization
- Transaction-safe writes
- Data integrity constraints

All workspace-related data must always be scoped by:

```
workspace_id
```

---

# Frontend Skills

## Vue

- Vue 3
- Composition API
- Vue Router
- Pinia State Management
- Component-driven architecture
- Composable utilities

## UI System

- Tailwind CSS
- shadcn-vue
- Radix Vue primitives
- Accessible UI components
- Reusable UI components

Common UI components include:

- Sidebar
- Dialog
- Dropdown
- Table
- Card
- Badge
- Avatar
- Tabs
- Popover
- Tooltip
- Toast notifications

---

# UI / UX Skills

- SaaS dashboard layout
- Kanban board UI
- Task detail panels
- Responsive layouts
- Clear loading states
- Empty states
- Confirmation dialogs
- Fast interaction feedback

UI must feel similar to modern tools like:

- Linear
- ClickUp
- Trello
- Notion

---

# State Management

Pinia is used for shared state.

Stores should only exist for:

- authentication
- workspace context
- notifications
- UI preferences
- cross-page filters if needed

Local component state should be preferred when possible.

---

# Collaboration System Skills

The system includes collaboration features such as:

- task assignment
- task comments
- member mentions
- notifications
- activity logs

Generated features should support collaborative workflows.

---

# Performance Skills

- Redis caching
- Redis queues
- Pagination for large lists
- Avoiding N+1 queries
- Efficient API responses
- Lazy loading frontend pages

---

# Testing Skills

Testing is required for important backend logic.

Tests should include:

- Feature tests for API endpoints
- Unit tests for services/actions
- Authorization tests
- Workspace isolation tests
- Validation tests

Testing tools:

- PHPUnit
- Laravel test utilities
- model factories
- database refresh testing

---

# Security Skills

All generated code must include:

- backend validation
- backend authorization
- safe file upload handling
- protection against cross-workspace data access
- proper authentication checks

Never rely on frontend-only protection.

---

# Code Quality Skills

Generated code must follow:

- clean architecture
- readable naming
- small focused classes
- reusable services
- consistent folder structure
- no dead code
- no debug code

Controllers must remain thin.

Business logic must live in services/actions.

---

# Git Workflow Skills

Recommended commit style:

- feat: new feature
- fix: bug fix
- refactor: architecture improvement
- test: new tests
- ui: UI improvements
- docs: documentation changes

Commits should be:

- small
- focused
- descriptive

---

# System Design Skills

This project demonstrates:

- SaaS-style architecture
- Multi-tenant system design
- Collaboration platform design
- Kanban workflow management
- Activity tracking systems
- Scalable full-stack architecture

All generated features should align with these architectural goals.
