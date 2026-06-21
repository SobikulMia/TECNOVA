# TechNova — Premium Gadget Dropshipping E-commerce Platform

A clean, minimalist, Laravel + Tailwind CSS e-commerce storefront built for a Bangladesh-based gadget dropshipping brand. Architected so a 3rd-party Warehouse API (product sync, inventory, order push) can be plugged in later with minimal changes.

---

## 1. Tech Stack

- **Backend:** Laravel 11, Repository Pattern, Service Layer
- **Frontend:** Blade Templates + Tailwind CSS + Vanilla JS (no SPA framework — fast, simple)
- **Database:** MySQL (default) — works with SQLite too for quick local testing
- **Fonts:** Inter (body) + Poppins (display), via Google Fonts
- **Colors:** Royal Navy Blue `#1E3A8A`, Slate Black, White, Vibrant Teal accent `#14B8A6`, Coral `#FB7158`

---

## 2. Project Structure (What's Where)

```
app/
  Console/Commands/        → warehouse:sync-inventory, warehouse:sync-orders
  Console/Kernel.php       → Task scheduler (classic structure)
  Http/Controllers/        → HomeController, ProductController, CartController,
                              CheckoutController, OrderController (all thin — logic
                              lives in Services/Repositories)
  Http/Requests/           → PlaceOrderRequest (checkout validation)
  Models/                  → Category, Product, Order, OrderItem
  Repositories/            → ProductRepository, OrderRepository (+ Contracts/ interfaces)
  Services/
    WarehouseApiService.php → ⭐ THE FILE YOU'LL EDIT to plug in your warehouse API
    OrderService.php        → Order placement business logic
    CartService.php         → Session-based cart logic

database/
  migrations/              → categories, products, orders, order_items + framework tables
  factories/                → ProductFactory (dummy data generator)
  seeders/                  → CategorySeeder, ProductSeeder, DatabaseSeeder

resources/views/
  layouts/app.blade.php    → Master layout (navbar, footer, Tailwind, fonts)
  home/index.blade.php     → Homepage (hero, featured grid, trust badges)
  products/index.blade.php → Shop listing with category filter + search
  products/show.blade.php  → Product detail (gallery, specs table, stock indicator)
  cart/index.blade.php     → Cart page (AJAX qty update/remove)
  checkout/index.blade.php → Express 1-page checkout (AJAX submit)
  orders/confirmation.blade.php → Thank-you page

routes/web.php             → All storefront routes
public/images/logo.svg     → TechNova logo (also favicon.svg)
```

---

## 3. Local Setup (Step by Step)

### Requirements
- PHP >= 8.2
- Composer
- Node.js >= 18 + npm
- MySQL (or SQLite for quick testing)

### Steps

```bash
# 1. Unzip the project, then move into it
cd technova

# 2. Install PHP dependencies
composer install

# 3. Install JS dependencies
npm install

# 4. Copy the environment file
cp .env.example .env

# 5. Generate the app encryption key
php artisan key:generate

# 6. Configure your database in .env
#    DB_DATABASE=technova
#    DB_USERNAME=root
#    DB_PASSWORD=yourpassword
#    (Or switch DB_CONNECTION=sqlite and skip DB_HOST/PORT/etc. for quick local testing)

# 7. Create the database (MySQL example)
mysql -u root -p -e "CREATE DATABASE technova CHARACTER SET utf8mb4;"

# 8. Run migrations
php artisan migrate

# 9. Seed dummy data (8 categories + 32 sample products)
php artisan db:seed

# 10. Link storage (for future uploaded product images)
php artisan storage:link

# 11. Build frontend assets
npm run build
#    or for active development with hot reload:
npm run dev

# 12. Serve the app
php artisan serve
```

Visit **http://localhost:8000** — you should see the TechNova homepage with seeded dummy products.

---

## 4. How the Warehouse API Integration Works (Read This First)

Everything is designed around **one single integration point**: `app/Services/WarehouseApiService.php`.

When you get your warehouse provider's API docs, you only need to touch **3 things**:

1. **`.env`** — fill in:
   ```
   WAREHOUSE_API_BASE_URL=https://your-warehouse-provider.com/api
   WAREHOUSE_API_KEY=your-real-api-key
   ```

2. **`app/Services/WarehouseApiService.php`** — there are 3 methods with `TODO` comments:
   - `fetchProducts()` — pulls the catalog from the warehouse
   - `updateStock()` — pulls live inventory levels
   - `pushOrderToWarehouse($order)` — sends a placed order for fulfillment

   Each TODO tells you exactly what to change (endpoint path, payload shape) based on your provider's real API.

3. **`app/Console/Commands/SyncWarehouseInventory.php`** — adjust the loop that maps the warehouse's response shape to local DB fields, if your provider's response format differs from the example.

**Nothing else needs to change.** Controllers, models, and views already read from the local database — once the sync commands populate/update that database from the real API, the entire storefront reflects it automatically.

The scheduler (`routes/console.php` and `app/Console/Kernel.php`, both included for compatibility) already runs:
- `warehouse:sync-inventory` every 15 minutes
- `warehouse:sync-orders` every 5 minutes (retries any orders that failed to push)

To enable the scheduler in production, add this single cron entry on your server:
```
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

---

## 5. Deploying to Make the Site Live

I can't deploy this for you directly — I don't have hosting or server access — but here's exactly how to do it yourself.

### Option A: Shared Hosting (cPanel) — common with Bangladeshi hosts (Hostinger, Exonhost, BDIX hosts, etc.)

1. Buy hosting that supports **PHP 8.2+** and lets you run **Composer/SSH** (many BD shared hosts now support this — confirm before buying).
2. Upload the entire project via FTP/File Manager to a folder **outside** `public_html` (e.g. `technova_app/`).
3. Point your domain's document root to `technova_app/public` (cPanel → "Domains" → set document root), OR copy the contents of `public/` into `public_html` and adjust `index.php`'s paths to point up one level — ask your host's support if unsure, this step varies by host.
4. Via SSH/Terminal in cPanel:
   ```bash
   cd technova_app
   composer install --optimize-autoloader --no-dev
   npm install && npm run build
   cp .env.example .env
   php artisan key:generate
   # edit .env with your real DB credentials (create DB via cPanel MySQL Databases)
   php artisan migrate --force
   php artisan db:seed --force
   php artisan storage:link
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
5. Set up the cron job (cPanel → Cron Jobs) as shown above for the scheduler.
6. Set proper permissions: `storage/` and `bootstrap/cache/` need to be writable (`chmod -R 775`).

### Option B: VPS (DigitalOcean, Vultr, Linode, etc.) — more control, recommended for scaling

1. Provision an Ubuntu VPS, install PHP 8.2, MySQL, Nginx, Composer, Node.js.
2. Clone/upload the project to `/var/www/technova`.
3. Run the same `composer install` / `npm run build` / `migrate` / `seed` steps as above.
4. Configure Nginx to point to `technova/public` as the document root.
5. Add an SSL certificate (Let's Encrypt / Certbot — free).
6. Add the cron entry for `schedule:run`.
7. Use a process manager (Supervisor) if you later add real queue workers.

### Option C: Laravel-specific platforms (Laravel Forge, Laravel Cloud)

Easiest path if you want managed deployment — connect your Git repo, Forge/Cloud handles the server provisioning, SSL, and cron setup for you. Costs more than raw VPS/shared hosting but saves setup time.

---

## 6. What's Dummy / Placeholder Right Now

- **Products & categories**: seeded with `ProductFactory` — replace via `php artisan db:seed` after editing the factory, or build an admin panel (not included in this build — ask if you want one added).
- **Product images**: all point to `public/images/placeholder-product.svg` until you upload real photos.
- **Warehouse sync**: fully wired in structure, but inert until you add real API credentials — it logs "not configured" and skips safely.
- **bKash/Nagad**: currently just recorded as the customer's chosen payment method; no live payment gateway is wired in. Ask if you'd like real bKash Merchant API integration added next.

---

## 7. Admin / Order Management

This build is the **customer-facing storefront only**, as scoped in the brief. There's no admin panel yet for managing products/orders — they're managed via `php artisan db:seed`, Tinker, or directly in the database for now. Happy to build a clean admin dashboard (Laravel + Tailwind, matching this design system) as a next step if useful.
