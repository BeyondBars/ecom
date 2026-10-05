# Handoff: fixed clone of beyondBars/invoice

Same architecture as the original: Laravel 11 API (`api/`, Sanctum SPA cookie auth, Bouncer roles)
plus a Vue 3 + Pinia + Vite SPA (`frontend/`), run with `docker-compose.yml`.

## Status

### Backend: DONE and verified
- `cd api && php vendor/bin/phpunit` passes 62/62 (34 original tests + 28 new security tests).
- Tests use in-memory SQLite (see phpunit.xml), so they never touch the dev database.
- 2FA is enforced server-side (`2fa` middleware), the account takeover via email change is fixed,
  policies are enforced, sign-up requires a verified email, disabled users can't log in, Socialite
  is safe, rate limits are per user/IP, dead code is removed, and Docker and .env.example work.

### Frontend: about 70% done, NOT yet re-verified
Done: http client (CSRF, 401/403/419 handling), Pinia app store, types, router guards and
middleware (including the 2FA login challenge), lazy routes, 2FA verify and setup screens, the
email verification pages, the accept-terms page, and the login page with social buttons.

Remaining TODO:
1. `src/views/register/AccountSetupAfterVerification.vue`: rewrite the script. `appStore.accountSetup()`
   now logs the user in and returns them; route to `redirect` or `dashboard` afterwards. Remove the
   unused `token` field. Use `appStore.verifySignature()` on load.
2. `src/components/HTBPhoneInput.vue`: remove the dead `onInput`/`emit` code (`emit` is undefined).
3. Run `npm run typecheck` and fix the remaining errors (roughly 100, mostly `stores/users.ts`,
   `stores/roles.ts`, `views/account/Account.vue`, `views/users/*`, `components/forms/tiptap/*`).
   Use the types in `src/types/user.ts` and `src/types/role.ts`; they match the API resources.
   - `Account.vue` should call `PUT /two-factor` via `appStore.updateTwoFactor`, then route to
     `two-factor.setup.<method>` with `query: { change: '1' }`.
   - `stores/users.ts` imports `@/types/users`, which should be `@/types/user`; pagination is under
     `response.data.meta`.
4. `package.json`: remove the unused `vuex`, `vuex-pathify`, `store`, `sweetalert` and `@types/axios`;
   add `sweetalert2` explicitly (it's imported directly).
5. `npm run build` and `npm run typecheck` must both pass with 0 errors.
6. Write the README "Getting started" section (see below).

## Getting started (dev)
```
cp api/.env.example api/.env
docker compose build
docker compose run --rm fpm composer install
docker compose run --rm fpm php artisan key:generate
docker compose up -d
docker compose exec fpm php artisan migrate --seed
```
- SPA: http://localhost:8080
- API: http://localhost:8000
- Emails (MailDev): http://localhost:8025
- Seeded logins: admin@example.com / Secret*12345 (super admin), user@example.com / Secret*12345
- With APP_ENV=local, SMS codes are captured instead of sent and show up in api/storage/logs/laravel.log.

## SECURITY
The original repo committed `ec2-pem.pem` (an RSA private key). Rotate that EC2 key pair and purge
it from git history (git filter-repo / BFG). It is removed here and `*.pem` is gitignored.
