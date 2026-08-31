# Agent Guide - Restaurant Management System

## Project Overview

This is a **Laravel 10 REST API backend** for a restaurant management system. It is in a **very early stage** -- the database schema has been scaffolded via migrations but most tables have no columns defined yet. Models are bare shells. One controller exists with empty CRUD stubs.

**Key facts:**
- **Framework:** Laravel 10.10 (PHP ^8.1)
- **Auth:** Laravel Sanctum (token-based API auth)
- **Database:** MySQL (default config)
- **Testing:** PHPUnit 10
- **Build tooling:** Vite 5, laravel-vite-plugin

---

## Directory Structure

```
resturant_laravel_project/
├── app/
│   ├── Console/Kernel.php              # Scheduler & artisan commands
│   ├── Exceptions/Handler.php          # Exception handling
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Controller.php          # Base controller
│   │   │   └── Api/
│   │   │       └── FoodCategoryController.php  # Stub (empty CRUD)
│   │   ├── Kernel.php                  # HTTP middleware stack
│   │   └── Middleware/                 # 9 middleware files (all default Laravel)
│   ├── Models/                         # 8 models (all bare)
│   └── Providers/                      # 5 service providers (all default)
├── config/                             # Standard Laravel config files
├── database/
│   ├── factories/UserFactory.php       # Only factory
│   ├── migrations/                     # 32 migrations
│   └── seeders/
│       ├── DatabaseSeeder.php          # Empty
│       └── RoleSeeder.php              # Has a bug (see Known Issues)
├── routes/
│   ├── api.php                         # Only default Sanctum /user route
│   ├── web.php                         # Welcome + /test view route
│   ├── console.php
│   └── channels.php
├── resources/                          # Views, CSS, JS (Vite)
├── tests/                              # Unit + Feature test suites
└── vendor/                             # Composer dependencies (gitignored)
```

---

## Models

All models are in `app/Models/`. They all use `HasFactory` but have **no `$fillable`, `$guarded`, relationships, accessors, or mutators defined**.

| Model | Table (implied) | Status |
|---|---|---|
| `User` | `users` | Fully defined (default Laravel + Sanctum tokens) |
| `Role` | `roles` | Bare shell |
| `Restaurant` | `restaurants` | Bare shell |
| `RestaurantBranch` | `restaurant_branches` | Bare shell |
| `RestaurantTable` | `restaurant_tables` | Bare shell |
| `FoodCategory` | `food_categories` | Bare shell |
| `Food` | `foods` | Bare shell |
| `Order` | `orders` | Bare shell |

---

## Database Migrations

### Default Laravel Migrations (fully defined)
- `2014_10_12_000000_create_users_table.php` -- name, email, password, etc.
- `2014_10_12_100000_create_password_reset_tokens_table.php`
- `2019_08_19_000000_create_failed_jobs_table.php`
- `2019_12_14_000001_create_personal_access_tokens_table.php`

### Custom Restaurant Migrations (ALL EMPTY -- only `id` + `timestamps`)
These 28 migrations need their columns defined:

| Table | Migration File | Purpose |
|---|---|---|
| `roles` | `2026_08_31_145648` | User roles (admin, manager, waiter, customer) |
| `user_roles` | `2026_08_31_145700` | Pivot: users <-> roles |
| `restaurants` | `2026_08_31_150201` | Restaurant info |
| `restaurant_branches` | `2026_08_31_150201` | Branch locations per restaurant |
| `restaurant_tables` | `2026_08_31_150213` | Tables per branch |
| `staff_positions` | `2026_08_31_150353` | Job positions (chef, waiter, etc.) |
| `employees` | `2026_08_31_150402` | Employee records |
| `customers` | `2026_08_31_150414` | Customer profiles |
| `customer_addresses` | `2026_08_31_150420` | Delivery addresses per customer |
| `food_categories` | `2026_08_31_150436` | Menu categories |
| `foods` | `2026_08_31_150446` | Menu items |
| `food_variants` | `2026_08_31_150454` | Size/variant options per food |
| `food_addons` | `2026_08_31_150501` | Addon groups per food |
| `food_addon_items` | `2026_08_31_150506` | Individual addon options |
| `promotions` | `2026_08_31_150513` | Discount/coupon codes |
| `orders` | `2026_08_31_150522` | Order records |
| `order_items` | `2026_08_31_150529` | Line items per order |
| `order_item_addons` | `2026_08_31_150535` | Addons selected per order item |
| `delivery_drivers` | `2026_08_31_150541` | Driver profiles |
| `deliveries` | `2026_08_31_150547` | Delivery records linked to orders |
| `order_status_history` | `2026_08_31_150552` | Status change audit trail |
| `payments` | `2026_08_31_150558` | Payment records |
| `suppliers` | `2026_08_31_150619` | Ingredient suppliers |
| `ingredients` | `2026_08_31_150625` | Raw ingredients catalog |
| `inventory` | `2026_08_31_150629` | Stock levels per branch |
| `inventory_transactions` | `2026_08_31_150635` | Stock in/out movements |
| `recipes` | `2026_08_31_150639` | Recipe definitions per food |
| `recipe_items` | `2026_08_31_150644` | Ingredients per recipe |

---

## Routes

### API Routes (`routes/api.php`)
Currently only the default Sanctum-protected route:
```
GET /api/user  (auth:sanctum)  -> returns authenticated user
```
**No restaurant API routes have been registered yet.**

### Web Routes (`routes/web.php`)
```
GET /       -> welcome view
GET /test   -> test view
```

---

## Controllers

Only one custom controller exists:

- `app/Http/Controllers/Api/FoodCategoryController.php` -- contains empty `index()`, `store()`, `show()`, `update()`, `destroy()` methods.

No other controllers exist.

---

## Middleware

All 9 middleware files are **default Laravel boilerplate** with no customizations:
- `Authenticate`, `EncryptCookies`, `PreventRequestsDuringMaintenance`, `RedirectIfAuthenticated`, `TrimStrings`, `TrustHosts`, `TrustProxies`, `ValidateSignature`, `VerifyCsrfToken`

The `api` middleware group includes `ThrottleRequests` and `SubstituteBindings` but **Sanctum's `EnsureFrontendRequestsAreStateful` is commented out**.

---

## Seeders

- `DatabaseSeeder` -- empty (all code commented out)
- `RoleSeeder` -- inserts 4 roles (Admin, Manager, Waiter, Customer) but **has a bug** (see Known Issues)

---

## Known Issues

1. **RoleSeeder bug:** `database/seeders/RoleSeeder.php` calls `Role::insert(...)` without `use App\Models\Role;` at the top. This will throw `Class 'Role' not found` at runtime. Fix: add `use App\Models\Role;` import.

2. **All 28 custom migrations are empty stubs.** They create tables with only `id` and `timestamps`. No foreign keys, no columns, no indexes defined.

3. **Models have no `$fillable` or `$guarded`.** Mass assignment will fail for all custom models.

4. **Models have no relationships defined.** No Eloquent relationships (belongsTo, hasMany, etc.) exist.

5. **Sanctum stateful middleware is commented out** in the `api` middleware group (`app/Http/Kernel.php`).

---

## Domain Model Relationships (Intended -- Not Yet Implemented)

Based on migration names, the intended relationships are:

```
Restaurant
  └── has many RestaurantBranch
        ├── has many RestaurantTable
        ├── has many Employee (via staff_position)
        └── has many Food / FoodCategory

Food
  ├── belongs to FoodCategory
  ├── has many FoodVariant
  ├── has many FoodAddon -> FoodAddonItem
  └── has many Recipe -> RecipeItem -> Ingredient

Order
  ├── belongs to RestaurantBranch
  ├── belongs to Customer
  ├── has many OrderItem -> Food, OrderItemAddon
  ├── has one Delivery -> DeliveryDriver
  ├── has many OrderStatusHistory
  └── has one Payment

Customer
  └── has many CustomerAddress

Inventory
  ├── belongs to Ingredient
  ├── belongs to RestaurantBranch
  └── has many InventoryTransaction

User
  └── belongs to many Role (via user_roles pivot)
```

---

## Configuration

- **Database:** MySQL on `127.0.0.1:3306`, database `laravel`, user `root` (see `.env`)
- **Cache:** file-based
- **Session:** file-based
- **Queue:** sync (no async workers)
- **Auth:** Sanctum tokens
- **CORS:** Handled by `HandleCors` middleware

---

## Development Commands

```bash
# Start dev server
php artisan serve

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Run tests
php artisan test
# or
./vendor/bin/phpunit

# Lint (Pint)
./vendor/bin/pint

# Create new migration
php artisan make:migration create_xxx_table

# Create new model with migration
php artisan make:model Xxx -m

# Create new controller
php artisan make:controller Api/XxxController --api --model=Xxx

# Clear caches
php artisan config:cache
php artisan route:clear
php artisan view:clear

# Generate app key
php artisan key:generate
```

---

## Testing

- **Framework:** PHPUnit 10
- **Test suites:** `tests/Unit` and `tests/Feature`
- **PHPUnit config:** `phpunit.xml` (uses array cache, sync queue, array mail for testing)
- **No custom tests written yet.** Only default Laravel scaffolding exists in `tests/`.

---

## When Working on This Project

1. **Define migration columns first** before writing model logic. The 28 restaurant tables all need their schema fleshed out.
2. **Add `$fillable` arrays** to all models for mass assignment.
3. **Define Eloquent relationships** on models based on the domain model diagram above.
4. **Create API routes** in `routes/api.php` for each resource.
5. **Implement controller logic** in `app/Http/Controllers/Api/` -- the `FoodCategoryController` is a stub to follow as a pattern.
6. **Fix the RoleSeeder** import bug before seeding.
7. **Enable Sanctum stateful middleware** if building a SPA frontend that authenticates via cookies.
8. Use `php artisan make:model X -mcr` to scaffold model + migration + controller + resource route in one command.
