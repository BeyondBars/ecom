# Ladies First Couture: shop API and back office

The backend and admin for the Ladies First Couture shop, built on
[htb-backend-base](https://github.com/BeyondBars/htb-backend-base) (Laravel 13 API in `api/`, Vue 3 admin in
`frontend/`). The customer storefront is a separate repo,
[ecommerce-frontend](https://github.com/BeyondBars/ecommerce-frontend), which talks to this API.

## What's in it

| Area | API | Admin screen |
| --- | --- | --- |
| Catalogue | Categories (nested), brands, products with images, options (Size/Color...), specifications, stock, sale prices | Products, Categories, Brands |
| Checkout | Server-side cart pricing, coupons, standard/express shipping, Cash on Delivery, Razorpay with signature check and webhook | - |
| Orders | Customer history, cancel, returns; admin status flow, tracking, COD collection, returns, refunds, notes | Orders |
| Marketing | Coupons, home page banners, blog with moderated comments, newsletter | Coupons, Banners, Blog, Inbox |
| Customers | Address book, wishlist, product reviews (moderated, verified purchase), back-in-stock alerts, contact form | Reviews, Inbox, Users & Customers |
| Store | Contact details, shipping fees, free-shipping threshold, COD limit, return window, social links | Settings |
| Reports | Revenue/orders vs previous period, daily chart, best sellers, to-do counts | Dashboard |

Order emails (confirmation, new-order alert for staff, shipped, delivered, cancelled, returns, refunds,
back in stock) are registered on the Email Templates page, so they can be previewed and test-sent.

### Roles

Defined in `api/app/Support/Enums` and synced with `php artisan abilities:refresh`:

- **Super Admin**: everything, including settings, users and email templates.
- **Staff**: orders, catalogue, coupons, reviews, content and reports.
- **Customer** (`user`): shops on the storefront; only sees their own orders, addresses and wishlist.

### Storefront API

Public: `GET shop/products` (filters `filter[category]`, `[brand]`, `[search]`, `[min_price]`, `[max_price]`,
`[on_sale]`, `[in_stock]`, `[featured]`; sorts `price`, `-price`, `-created_at`, `-rating`), `shop/products/{slug}`,
`.../related`, `.../reviews`, `shop/categories`, `shop/brands`, `shop/banners`, `shop/blog`, `shop/settings`,
`POST shop/cart/quote`, `shop/newsletter`, `shop/contact`, `shop/products/{slug}/stock-alerts`.

Signed in: `shop/addresses`, `POST shop/checkout`, `shop/orders` (+ `cancel`, `return`,
`payments/razorpay`), `shop/wishlist`, `POST shop/products/{slug}/reviews`, `POST shop/blog/{slug}/comments`.

Customers sign up with `POST shop/register` (emails a link to the storefront's `/register/verify` page), then
the starter's `GET /verify` and `POST /account-setup`. Sign-in, password reset and profile use the starter's
`/auth`, `/password/forgot`, `/password/reset`, `/user` and `users/{id}`; password-reset emails for customers link to
the storefront. Customers can close their account with `DELETE shop/account`. See `api/routes/shop.php` for the
full list.

### Shop setup notes

- Run `php artisan storage:link` once so uploaded images are served (`docker compose exec fpm php artisan storage:link`).
- The `scheduler` service runs `orders:release-unpaid` every 5 minutes: online orders reserve stock when placed,
  and unpaid ones are cancelled after 30 minutes so the stock goes back on sale. Outside Docker, run
  `php artisan schedule:run` from cron every minute.
- Razorpay: set `RAZORPAY_KEY_ID`, `RAZORPAY_KEY_SECRET` and `RAZORPAY_WEBHOOK_SECRET`. Without keys the
  storefront only offers Cash on Delivery. Refunds are issued in the Razorpay dashboard and recorded on the order.
- The storefront must run on the same site as the API for cookie sign-in (e.g. `shop.example.com` and
  `api.example.com` with `SESSION_DOMAIN=.example.com`), and be listed in `SANCTUM_STATEFUL_DOMAINS` and
  `STOREFRONT_URL`.
- `migrate --seed` adds demo products, categories, coupons (WELCOME10, FLAT200, FREESHIP), banners and a blog post
  matching the storefront's sample data.

### Staying in sync with the base

`main` contains htb-backend-base's history, so starter fixes can be merged in:

```sh
git remote add upstream git@github.com:BeyondBars/htb-backend-base.git
git fetch upstream
git merge upstream/main
```

Shop code is kept apart from starter files where possible (`routes/shop.php`, `ShopServiceProvider`,
the `ShopCustomer` trait on `User`, `config/shop.php`), so these merges stay small.

---

## About the starter

A starter for apps that need a solid sign-in system: Laravel 13 API (PHP 8.4, `api/`, Sanctum SPA cookie
auth, Bouncer roles) plus a Vue 3 + Pinia + Vite + Tailwind CSS 4 SPA (`frontend/`), run together with
`docker-compose.yml`.

Included: sign-up with email verification, sign-in, Google/Facebook login, 2-step verification by email or
SMS, password reset, terms acceptance, user management, roles and an account page. Nothing app-specific.

## Building an app on this starter

Create the app from this repo and keep it linked, so auth fixes made here can be pulled in later:

```sh
git clone git@github.com:BeyondBars/<this-repo>.git my-app
cd my-app
git remote rename origin upstream
git remote add origin git@github.com:BeyondBars/my-app.git
git push -u origin main
```

Pull starter changes when you want them:

```sh
git fetch upstream
git merge upstream/main          # everything
git cherry-pick <commit-hash>    # one specific fix
```

To keep merges painless:

- Fix auth and shared-shell problems **here**, then pull them into the app. Keep starter commits focused
  (one fix per commit) so they can be cherry-picked.
- In the app, extend rather than edit starter files where you can. App-specific user data goes in its own
  table rather than new columns on `users`; app pages go in new route files under `frontend/src/router/routes/`.
- Rebrand by changing the `--color-brand-*` values in `frontend/src/assets/css/app.css` and replacing
  `frontend/src/assets/img/logo/logo.png` and `white-logo.png`. Everything else uses those.

## Email templates

Admins can see every email the app sends under **Email Templates**, preview each one with sample data
and send a test copy (subject prefixed with `[TEST]`). Access needs the `manage email templates` ability,
which super admins get; on an existing database run `php artisan abilities:refresh` to add it.

When the app adds an email, register it in `api/app/Providers/EmailTemplateServiceProvider.php`:

```php
$templates->register(new EmailTemplate(
    key: 'order_shipped',
    name: 'Order Shipped',
    type: EmailTemplateType::Transactional,   // or Marketing
    triggeredBy: 'order.shipped',
    description: 'Tells the customer their order is on its way.',
    sample: fn (User $recipient) => new OrderShippedNotification(Order::factory()->make()),
));
```

`sample` returns the real notification built with sample data, so previews match what users get. Test
copies go to real inboxes, so never put real tokens or signed links in a sample.
`tests/Feature/EmailTemplateTest.php` renders and test-sends every registered template.

## Getting started

Requirements: Docker with the Compose plugin.

```sh
cp api/.env.example api/.env
docker compose build
docker compose run --rm fpm composer install
docker compose run --rm fpm php artisan key:generate
docker compose up -d
docker compose exec fpm php artisan migrate --seed
docker compose exec fpm php artisan storage:link
```

| Service             | URL                   |
| ------------------- | --------------------- |
| SPA                 | http://localhost:8080 |
| API                 | http://localhost:8000 |
| Emails (MailDev)    | http://localhost:8025 |
| MySQL (from host)   | `localhost:33061` (see `DB_*` in `api/.env`) |

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
