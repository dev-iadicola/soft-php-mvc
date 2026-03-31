# React Migration QA Checklist

## Purpose

This checklist defines the minimum exit criteria for every React/Inertia migration branch.
It complements the execution roadmap with concrete validation steps.

## Automatic Gates

Every React branch should pass:

- `./dock npm run build`
- `php vendor/bin/phpstan analyse --no-ansi`
- `vendor/bin/phpunit --no-coverage`

In CI the same gates must run on push and pull request through the dedicated React quality workflow.

## Manual Smoke Checks

### Public Pages

- home renders with guest shell, navigation and CTA
- portfolio renders project and certificate cards
- projects index renders filters and applies technology selection
- project detail renders content, gallery and related items
- blog index renders search, tags and pagination
- article detail renders SEO metadata and related articles
- tech stack renders technology cards

### Admin Pages

- login submits without CSRF regressions
- sign-up bootstrap flow still works only for the first account
- dashboard renders stats, visits and recent messages
- security page enables/disables 2FA correctly
- sessions page lists active sessions and can terminate one session
- secondary admin pages load inside the shared admin layout

## Regression Watchlist

- no asset 404 from Vite manifest integration
- no title/meta mismatch between first HTML render and client-side navigation
- no CSRF regressions on Inertia forms
- no accidental dependency from app UI into reusable framework core
- no inclusion of unrelated local files in React branch commits

## Release Readiness

A React migration slice is ready to close only when:

- the branch scope is respected
- `CHANGELOG.md` is updated
- commit and push are completed
- the legacy replacement path is identified clearly
- any residual risk is written down before moving to the next slice
