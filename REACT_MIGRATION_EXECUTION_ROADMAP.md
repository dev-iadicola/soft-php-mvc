# React Migration Execution Roadmap

## Goal

Turn the React/Inertia migration from a high-level idea into a deployable sequence of small, reversible branches.
This document is the tracked execution reference for the ongoing migration.

## Operating Principles

- every branch must remain deployable
- no big-bang rewrite
- public pages, auth and admin can coexist between PHP views and Inertia until parity is reached
- app-specific decisions must stay in app/frontend adapters, not leak into the reusable core
- every branch closes only after build, `phpstan`, `phpunit`, changelog, commit and push

## Current Status

### Completed

- `feature/react/feasibility-assessment`
- `feature/react/frontend-surface-audit`
- `feature/react/architecture-rfc`
- `feature/react/migration-strategy`
- `feature/react/inertia-backend-adapter`
- `feature/react/frontend-toolchain`
- `feature/react/react-ts-bootstrap`
- `feature/react/inertia-first-page`
- `feature/react/guest-layout-shell`
- `feature/react/public-props-contract`
- `feature/react/public-pages-porting`
- `feature/react/public-seo-parity`
- `feature/react/admin-layout-shell`
- `feature/react/admin-form-strategy`
- `feature/react/admin-critical-flows`
- `feature/react/admin-secondary-areas`
- `feature/react/untitledui-integration`
- `feature/react/design-system-foundation`

### Remaining

- `feature/react/qa-exit-criteria`
- `feature/react/legacy-decommission-plan`

## Recommended Next Slices

### Slice A: QA Hardening

Objective:
make the current migration reliable before porting more surfaces.

Outputs:

- CI workflow for frontend build, `phpstan` and `phpunit`
- explicit exit criteria for React branches
- smoke coverage of key public and admin pages where sustainable

### Slice B: Legacy Inventory

Objective:
identify exactly which PHP layouts, views and inline scripts are still active.

Outputs:

- `legacy -> React/Inertia` mapping
- inventory of still-blocking legacy surfaces
- removal priorities ordered by risk

### Slice C: Editorial CRUD Porting

Objective:
port the heaviest editorial/admin flows on top of the now-stable layout, props and design primitives.

Candidates:

- articles CRUD
- projects CRUD
- media manager

### Slice D: Final Legacy Cleanup

Objective:
remove replaced PHP views progressively without damaging the reusable framework core.

Outputs:

- phased removal of replaced views/layouts
- cleanup of obsolete inline scripts and CDN dependencies
- final verification on routing, SEO, auth and admin paths

## Go/No-Go Rule

A React branch is ready to close only if it:

- passes `./dock npm run build`
- passes `php vendor/bin/phpstan analyse --no-ansi`
- passes `vendor/bin/phpunit --no-coverage`
- updates `CHANGELOG.md`
- stays within its declared scope
- does not introduce unnecessary coupling into the framework core
