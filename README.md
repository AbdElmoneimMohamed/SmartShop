# SmartShop Mini

A minimal e-commerce demo with AI-powered product recommendations and a simulated checkout, built with Laravel, traditional Controllers, a Repository layer, Livewire (for interactive islands only), Tailwind CSS, and Alpine.js.

## Stack

- **Laravel 12** / **PHP 8.4**, running via **Laravel Sail** (Docker)
- **Controllers own routing and rendering** (`HomeController`, `ProductController`, `CartController`, `AuthController`) — classic MVC, not Livewire full-page components
- **Repository layer** (`App\Repositories\*` + `Contracts\*Interface`) sits between controllers and Eloquent for `Product` and `User`, bound in `AppServiceProvider`
- **Livewire 4** only for the specific interactive islands that need it: `AddToCartButton`, `CartItems` (qty steppers/checkout), and `CartBadge` (live nav count) — everything else is plain Blade
- **Tailwind CSS v4** + **Alpine.js** (bundled with Livewire) for interactivity — no component library
- **MySQL 8** for storage
- **Pint**, **PHPStan** (max level, via Larastan), **blade-formatter**, and **Pest** for the code quality gate

The starter template this project began from ([`SimpleDevTools/start-here`](https://github.com/SimpleDevTools/start-here)) ships with Flux Pro, a paid Livewire component library requiring a license, and used Livewire full-page components for auth. Since the task calls for plain Tailwind + Alpine.js and evaluates "routing, controllers, models, validation," Flux/Flux Pro were removed, auth was rebuilt with a traditional `AuthController` + Form Requests (`LoginRequest`/`RegisterRequest`, Breeze-style rate limiting), and Home/Product/Cart moved from Livewire full-page components to Controllers rendering plain Blade views, with Livewire demoted to just the three components listed above. `laravel/fortify` was removed as a dependency once nothing referenced it. Two-factor auth, password reset, email verification, and the profile/settings pages that came with the starter were also stripped out, since the task only asks for Register/Login/Logout and a single customer role — keeping the surface area minimal per the task's own emphasis on "code quality... not quantity."

## Setup

Requires Docker Desktop.

```bash
git clone <this-repo> smartshop-mini
cd smartshop-mini
make local-setup
```

`make local-setup` copies `.env.example` to `.env`, installs Composer dependencies, brings up the Sail containers (app + MySQL), generates the app key, and runs `migrate:fresh --seed`.

Then build frontend assets:

```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run build   # or `npm run dev` for hot-reload while working on it
```

Visit `http://localhost`.

**Seeded test user:** `user@example.com` / `password`

### Makefile reference

| Command | Description |
|---|---|
| `make start` / `make stop` / `make down` | Start/stop/tear down the Sail containers |
| `make migrate` | `migrate:fresh --seed` |
| `make seed` | Re-run seeders without touching the schema |
| `make test` | Run the full quality gate (`composer test`: Pint, PHPStan, blade-formatter, Pest) |
| `make pint` / `make stan` | Run Pint or PHPStan individually |
| `make ssh` | Shell into the app container |

## AI recommendations

**Provider: Google Gemini** (`gemini-2.0-flash` by default), via a plain `Http` call in `App\Services\RecommendationService` — no SDK dependency. Chosen for its free tier and low latency, which suits a lightweight demo without requiring billing setup. Swapping providers only means changing `askGemini()` in that one class.

Set `GEMINI_API_KEY` in `.env` to enable it (get a free key at [aistudio.google.com](https://aistudio.google.com/apikey)). **Without a key, the app still works end-to-end** — recommendations silently fall back to 3 random products from the catalog, exactly as the task specifies.

### How it works

- The last 3 viewed products are tracked in the session (`viewed_products`), updated on each Product Detail page visit.
- The Home page asks for recommendations based on those; the Product Detail page asks for products similar to the one currently being viewed.
- Both flows share one method (`RecommendationService::recommend()`), which builds a prompt like:

  > Based on these viewed products, suggest 3 similar ones from this product list:
  >
  > Viewed products:
  > - Ad porro: Et magnam voluptas eum dicta eum...
  >
  > Candidate products (id: name):
  > 4: Ratione sed dolorum
  > 7: Rerum sit ut voluptate
  > ...
  >
  > Respond with a JSON array of exactly 3 product IDs from the candidate list above, ordered by relevance. Only use IDs that appear in the candidate list.

- The request uses Gemini's `responseSchema` (structured JSON output — an array of integers) rather than parsing free-text, so the response is either a clean list of IDs or the call fails outright — no brittle regex/string parsing.
- **Fallback logic:** any failure (missing API key, network error, timeout, malformed response, IDs that don't match any candidate) falls back to `collect($candidates)->shuffle()->take(3)`. The user never sees an error — they just get 3 products instead of a personalized 3.
- **Caching (bonus):** results are cached for 10 minutes, keyed by a hash of the viewed-product IDs + candidate catalog IDs, so repeat views don't re-hit the API.

## Cart

Entirely session-based (`App\Services\CartService`) — no database table. `CartController` just renders the page shell; the actual list, quantity steppers, and checkout live in the `CartItems` Livewire component. Steppers are wired through Alpine (`@click="$wire.increment(id)"`), calling back into the component to mutate the session cart. A small nested `<livewire:cart-badge />` component in the nav listens for a `cart-updated` browser event (dispatched by both `CartItems` and `AddToCartButton`) so the cart count badge stays live across pages without a full reload.

Checkout simulates a payment gateway (~90% success rate, no real Stripe/charge) — on success the cart is cleared and an "Order confirmed" message is shown; on the simulated failure path, the cart is preserved and an error banner is shown instead, so the failure path is actually reachable and testable, not just a happy-path stub.

## Database

- `users` — Laravel default (`AuthController` + `UserRepository`, no third-party auth package)
- `products` — `id`, `uuid`, `name`, `description`, `price`, `image`, timestamps
- No `user_interactions` table — "last viewed" is tracked in the session only, per the task's suggested flow, rather than persisted.

Seed with `make seed` or `./vendor/bin/sail artisan db:seed` (25 products via `ProductFactory`, plus the one test user).

## Code quality

```bash
make test
```

Runs the same `composer test` script the starter ships with: `composer-license-checker` → PHPStan (max level) → Pint (`--test`) → blade-formatter (`-c`) → Pest. All of it passes clean on this codebase.

## Bonus

- ✅ Feature test for the recommendation service (`tests/Feature/RecommendationServiceTest.php`), covering both the successful-AI-response path and the fallback-on-failure path via `Http::fake()`.
- ✅ Response caching to avoid repeated AI calls (see above).
- ⬜ Filament/Nova dashboard — skipped; out of scope for the time budget and lowest-priority bonus per the task brief.
