# Docker Dev Environment Design — ciso-office

**Date:** 2026-06-12
**Scope:** Local development only (no production image)
**Foundation:** Laravel Sail

## 1. Overview

A containerized local development environment built on Laravel Sail. Six services
orchestrated by `docker-compose.yml`:

| Service       | Image / build              | Purpose                          | Host ports                       |
|---------------|----------------------------|----------------------------------|----------------------------------|
| `laravel.test`| Sail PHP **8.4** app image | Runs the app (`artisan serve`)   | `${APP_PORT:-9091}` → 80         |
| `mysql`       | `mysql:8`                  | Database `ciso_office`           | `${FORWARD_DB_PORT:-3306}`       |
| `redis`       | `redis:alpine`             | Cache/queue/session-ready        | `${FORWARD_REDIS_PORT:-6379}`    |
| `mailpit`     | `axllent/mailpit`          | Catches outgoing mail + web UI   | `1025` SMTP, `8025` UI           |
| `vite`        | reuses the app image       | `pnpm run dev --host` (HMR)      | `5173`                           |
| `queue`       | reuses the app image       | `artisan queue:listen` (db queue)| —                                |

Key choice: `vite` and `queue` **reuse the Sail app image** (which already bundles
Node + pnpm) rather than maintaining a separate Dockerfile.

## 2. Port mapping (host nginx conflict)

The host already runs nginx on `127.0.0.1:80`, so the container cannot bind host
port 80. The app is therefore exposed on **9091**:

- `APP_PORT=9091`
- `APP_URL=http://localhost:9091`

Port 9091 was verified free at design time.

## 3. How it gets generated

1. `composer require laravel/sail --dev` (on host — adds to `require-dev`).
2. `php artisan sail:install --with=mysql,redis,mailpit` — generates
   `docker-compose.yml`, the `vendor/bin/sail` wrapper, and rewrites `.env` host entries.
3. Customize the generated `docker-compose.yml`: add the `vite` and `queue` services
   and a `node_modules` named volume (see §5).

## 4. PHP version

Sail's `php84` image (PHP 8.4). `composer.json` requires `php ^8.3`, so this is
compatible. (Host is 8.5, but Sail does not ship an 8.5 image yet — 8.4 is the safe,
supported target.)

## 5. The macOS ↔ Linux `node_modules` gotcha

The project is bind-mounted into the containers. `node_modules` installed on macOS
contains platform-specific native binaries (esbuild/rollup) that crash under Linux.

Fix: the `vite` service gets a **named volume** mounted at
`/var/www/html/node_modules`, isolating its Linux-native install from the host's macOS
`node_modules`. On first boot, the `vite` service runs `pnpm install` into that volume.

`vendor/` (pure PHP) is fine bind-mounted, but we run `sail composer install` once
in-container to match the container's PHP.

## 6. Config changes (`.env` + `.env.example`)

Both files updated so the app talks to the service network:

- `APP_PORT=9091`, `APP_URL=http://localhost:9091`
- `DB_HOST=mysql`, `REDIS_HOST=redis`
- `MAIL_MAILER=smtp`, `MAIL_HOST=mailpit`, `MAIL_PORT=1025` (currently `log`)
- Sail vars added: `FORWARD_DB_PORT`, `FORWARD_REDIS_PORT`, `WWWUSER`, `WWWGROUP`

**Trade-off:** `DB_HOST=mysql` means the app expects to run inside Docker. Running
`php artisan serve` directly on the host would need `DB_HOST=127.0.0.1`. Since scope is
Docker local dev, this is intended; the one-line revert is documented in the README.

## 7. Vite config tweak

Add a `server` block to `vite.config.js` for container HMR:

```js
server: { host: '0.0.0.0', port: 5173, strictPort: true, hmr: { host: 'localhost' } }
```

This lets the host browser reach the dev server running in the `vite` container.

## 8. Cleanup

Remove the stray `package-lock.json` (pnpm was chosen; keeping both lockfiles invites
drift). `pnpm-lock.yaml` stays.

## 9. First-run bootstrap (documented in README)

```bash
composer require laravel/sail --dev
php artisan sail:install --with=mysql,redis,mailpit
# (apply the vite/queue + APP_PORT=9091 customizations)
./vendor/bin/sail up -d
./vendor/bin/sail composer install
./vendor/bin/sail artisan key:generate   # if needed
./vendor/bin/sail artisan migrate
# vite service auto-runs pnpm install + pnpm run dev
```

Then: app at `http://localhost:9091`, Vite HMR at `:5173`, Mailpit UI at `:8025`.

## 10. Verification

- `docker compose config` validates the compose file.
- `sail up -d` → all six containers healthy.
- `sail artisan migrate` succeeds against the `mysql` container.
- App loads at `http://localhost:9091`; editing a Tailwind/TipTap asset triggers HMR.
- Sending mail (e.g. Newsletter module) appears in Mailpit at `:8025`.

## 11. Files created / changed

- **New:** `docker-compose.yml`, `vendor/bin/sail` (Sail-generated)
- **Changed:** `composer.json` (+`laravel/sail`), `.env`, `.env.example`,
  `vite.config.js`, `README.md`
- **Removed:** `package-lock.json`
