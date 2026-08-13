# Repository Guidelines

## Project Structure & Module Organization

This repository wraps a Laravel application in `system/`. Core PHP code is in `system/app`, with domain modules under `system/app/Modules/*` and panel code under `system/app/Panels`. Routes live in `system/routes`, configuration in `system/config`, migrations and seeders in `system/database`, and Pest tests in `system/tests/Feature` and `system/tests/Unit`. Blade views and frontend source assets are in `system/resources`; compiled public assets are in top-level `assets/build`. Template exploration belongs in `template-integration/`.

## Build, Test, and Development Commands

Run commands from `system/` unless noted.

- `composer run setup`: installs PHP and JS dependencies, creates `.env`, generates the app key, migrates, and builds assets.
- `composer run dev`: starts Laravel, the queue listener, and Vite together for local development.
- `npm run dev`: starts only the Vite dev server.
- `npm run build`: builds production frontend assets.
- `composer run test` or `php artisan test`: clears config and runs the Pest test suite.
- `php artisan migrate --seed`: applies schema changes and seeders for local data.

## Coding Style & Naming Conventions

Follow existing Laravel conventions and sibling files before adding new patterns. PHP requires explicit return types, typed parameters, curly braces for all control structures, and constructor property promotion where appropriate. Prefer Eloquent models and relationships over raw `DB::` queries. Use Form Request classes for validation. Format PHP with Laravel Pint (`vendor/bin/pint`) when touching PHP files. Use descriptive names such as `isEligibleForTrial` rather than abbreviated names.

## Testing Guidelines

Tests use Pest with Laravel helpers. Place feature coverage in `system/tests/Feature` and isolated logic tests in `system/tests/Unit`; module-specific tests may live under each module’s `Tests` directory when that pattern already exists. Name tests after behavior, for example `UserImpersonationTest.php`. Run focused tests while developing: `php artisan test tests/Feature/FrontendMenusTest.php`.

## Commit & Pull Request Guidelines

Recent history uses short, imperative commit subjects, for example `Update .gitignore to remove demo-html from system and add it to template-integration`. Keep commits scoped and mention the area changed. Pull requests should include a concise summary, tests run, linked issues when applicable, and screenshots for Blade/UI changes.

## Security & Configuration Tips

Keep secrets in `system/.env`; never commit credentials, API keys, or generated local database files. Review `system/config/services.php`, `system/config/filesystems.php`, and payment or notification config before changing integrations. Avoid editing compiled files in `assets/build` directly; update source files and rebuild instead.
