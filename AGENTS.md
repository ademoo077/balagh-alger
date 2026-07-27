# AGENTS.md — Balagh Alger

## What this is

Custom PHP 8.x MVC citizen reporting platform for Algerian municipalities. No Laravel, no Symfony — hand-rolled framework with manual autoloading, PDO MySQL, and Bootstrap 5.3 UI. Bilingual (French/Arabic) with RTL support.

## Critical: PDO::EMULATE_PREPARES = false

`app/Config/database.php` sets `PDO::ATTR_EMULATE_PREPARES => false`. This means:

- **Never use `LIMIT ?` or `OFFSET ?` with bind params.** Real prepared statements require int-typed bound values for LIMIT/OFFSET, but PDO sends them as strings, causing MySQL errors. Interpolate these directly into the SQL string instead: `"LIMIT {$perPage} OFFSET {$offset}"`.
- This applies everywhere: `FeedController`, `Notification`, `Gamification`, any paginated query.
- SELECT ... WHERE with string/int params (`WHERE id = ?`) is fine — only LIMIT/OFFSET is affected.

## Entry point

`public/index.php` — loads `.env` via `putenv()`, requires helpers manually (no Composer autoloading for app code), boots Session, dispatches routes from `app/Routes/web.php`.

## Autoloading

`spl_autoload_register` in `public/index.php:39` — maps `App\Controllers\Foo` to `APP_PATH/Controllers/Foo.php`. Only app classes are autoloaded. Helpers are `require_once` explicitly.

## Routes

`app/Routes/web.php` — flat array of `"METHOD /path"` => `['Controller', 'action']`. No middleware stack; auth checks are done inside controller actions via `$this->auth()`, `$this->requireRole(...)`, `$this->requireStaff()`.

## Layouts

- **Admin**: `app/Views/layouts/main.php` — sidebar + top navbar, `data-bs-theme="dark"` default
- **Citizen**: `app/Views/layouts/citizen.php` — mobile-first bottom nav
- Both have FOUT-prevention inline scripts for theme persistence via `localStorage('balagh-theme')`

## CSRF

- Tokens generated in `app/Helpers/Csrf.php`, stored in session
- Views include `<input name="_token">` and `<meta name="csrf-token">`
- Controllers must call `$this->checkCsrf($redirectUrl)` at the top of every POST handler — this was a bug that was fixed; new POST handlers must not forget it

## I18n

- `app/Helpers/I18n::init()` loads `lang/fr.json` or `lang/ar.json` based on session/cookie
- `__('key')` shortcut available globally for translations
- Arabic triggers RTL via `dir="rtl"` on `<html>`, with CSS overrides in `main.php` inline `<style>`

## Timezone & locale

Timezone: `Africa/Algiers` (set in `app/Config/app.php` and applied in `public/index.php:49`). Locale: French by default.

## Database

MySQL 8, database `balagh_alger`. Connection via `App\Helpers\Database::getConnection()` — singleton PDO wrapper with `__callStatic` proxy. Config: `app/Config/database.php`, env vars `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`.

## No test framework

There are no tests, no PHPUnit, no test runner. No lint/typecheck tools configured. If you add tests, confirm with the user first.

## CSS architecture

- `public/assets/css/app.css` — admin design system (CSS variables, dark mode, sidebar, components)
- `public/assets/css/citizen.css` — citizen-facing styles
- Dark mode via `[data-bs-theme="dark"]` CSS variable overrides — always use CSS variables (`var(--bg-card)`, `var(--text-primary)`, etc.), never hardcoded colors for backgrounds/text in admin UI
- Decorative gradient badges and colored icons may use hardcoded colors intentionally

## JS architecture

- `public/assets/js/app.js` — admin: theme toggle, sidebar, command palette, notifications polling, DataTables init
- `public/assets/js/citizen.js` — citizen interface
- `public/assets/js/i18n.js` — client-side translations
- jQuery loaded from CDN for DataTables; vanilla JS preferred otherwise

## Known gotchas

- `Helper::generateTrackingCode()` still uses `mt_rand()` — should be `random_int()` for security
- `Notification::getRecent()` and `Gamification::getLeaderboard()` had LIMIT bind param bugs (fixed) — search for any other `LIMIT ?` patterns
- `Badge.php` previously used `FOUND_ROWS()` which always returned 0 under real prepares — use `COUNT(*)` with `prepare/fetchColumn` instead
- `SettingController::update()` must whitelist keys from DB to prevent mass assignment — never blindly merge `$_POST` into settings
- Views reference `$_SERVER['REQUEST_URI']` directly for active-state matching — trailing slashes matter
- Two controller namespaces: short names (`ReportController`) for web routes, fully-qualified (`App\Controllers\Api\ReportController`) for API routes
