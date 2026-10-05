# Invoice

Laravel 11 API (`api/`, Sanctum SPA cookie auth, Bouncer roles) plus a Vue 3 + Pinia + Vite SPA
(`frontend/`), run together with `docker-compose.yml`.

## Getting started

Requirements: Docker with the Compose plugin.

```sh
cp api/.env.example api/.env
docker compose build
docker compose run --rm fpm composer install
docker compose run --rm fpm php artisan key:generate
docker compose up -d
docker compose exec fpm php artisan migrate --seed
```

| Service             | URL                   |
| ------------------- | --------------------- |
| SPA                 | http://localhost:8080 |
| API                 | http://localhost:8000 |
| Emails (MailDev)    | http://localhost:8025 |
| MySQL (from host)   | `localhost:33061` (user `invoice`, password `secret`) |

Seeded logins (password `Secret*12345` for both):

- `admin@example.com`: super admin
- `user@example.com`: standard user

Notes:

- Sign-up, email verification and password reset emails are caught by MailDev.
- With `APP_ENV=local`, 2FA SMS codes are captured instead of sent and show up in
  `api/storage/logs/laravel.log`. Set `TWO_FACTOR_SKIP=true` in `api/.env` to accept any code locally.
- The SPA's `node_modules` live in a Docker volume. After changing `frontend/package.json`, rebuild
  the image and recreate that volume: `docker compose build vue`, then
  `docker compose rm -sf vue && docker volume rm <project>_node_modules && docker compose up -d vue`
  (`docker volume ls` shows the exact name).
- Social login (Google / Facebook) is optional: fill in the `GOOGLE_*` / `FACEBOOK_*` keys in
  `api/.env`. Callback URLs point at the API, e.g. `http://localhost:8000/auth/google/callback`.

## Checks

```sh
# API tests (in-memory SQLite, no .env or database needed)
cd api && php vendor/bin/phpunit

# Frontend type check and production build
cd frontend && npm ci && npm run typecheck && npm run build
```

Inside Docker: `docker compose exec fpm php vendor/bin/phpunit` and
`docker compose exec vue npm run typecheck`.

## Security

The original repository committed `ec2-pem.pem` (an RSA private key). That EC2 key pair must be
rotated and the file purged from the original repository's git history (git filter-repo / BFG).
It is not included here, and `*.pem`, `*.key` and `.env` files are gitignored.
