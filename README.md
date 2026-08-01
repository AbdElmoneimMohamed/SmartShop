# SmartShop Mini

A minimal e-commerce demo with AI-powered product recommendations and a simulated checkout, built with Laravel, traditional Controllers, a Repository layer, Livewire (for interactive islands only), Tailwind CSS, and Alpine.js.


## Modules

`app/` is organized by feature module rather than by type, so everything belonging to one part of the domain lives together:

```
app/Modules/
├── Cart/      Http\Controllers\CartController, Livewire\{AddToCartButton,CartBadge,CartItems}, Services\{CartService,CartItem}
├── Product/   Http\Controllers\{HomeController,ProductController}, Models\Product, Repositories\*, Services\RecommendationService
├── Auth/      Http\Controllers\AuthController, Http\Requests\{LoginRequest,RegisterRequest}, Models\User, Repositories\*
└── Shared/    Cross-cutting infrastructure used by more than one module — currently just AI (see below)
```

`app/Http/Controllers/Controller.php`, `app/Concerns/LogsModelActivity.php`, `app/Providers`, and `app/helpers.php` stay at the top level since they're app-wide, not feature-specific. Routes, views, migrations, and tests are unchanged from Laravel's default locations — only the PHP classes under `app/` were reorganized.

## Setup

Requires Docker Desktop.

```bash
git clone https://github.com/AbdElmoneimMohamed/SmartShop.git
cd smartshop
make local-setup
```

`make local-setup` copies `.env.example` to `.env`, installs Composer dependencies, brings up the Sail containers (app + MySQL), generates the app key, runs `migrate:fresh --seed`, and installs + builds the frontend assets (`npm install` && `npm run build`) — no separate frontend step needed.

Visit `http://localhost`.

**Seeded test user:** `user@example.com` / `password`

While working on frontend changes, run `make npm-dev` in a separate terminal for Vite's hot-reload instead of rebuilding manually each time.

### Makefile reference

| Command                                  | Description                                                                       |
|------------------------------------------|-----------------------------------------------------------------------------------|
| `make start` / `make stop` / `make down` | Start/stop/tear down the Sail containers                                          |
| `make migrate`                           | `migrate:fresh --seed`                                                            |
| `make seed`                              | Re-run seeders without touching the schema                                        |
| `make npm-install`                       | Install frontend dependencies                                                     |
| `make npm-build`                         | Build frontend assets for production                                              |
| `make npm-dev`                           | Run Vite in dev mode with hot-reload                                              |
| `make test`                              | Run the full quality gate (`composer test`: Pint, PHPStan, blade-formatter, Pest) |
| `make pint` / `make stan`                | Run Pint or PHPStan individually                                                  |
| `make ssh`                               | Shell into the app container                                                      |

## AI recommendations

**Provider: Google Gemini** (`gemini-2.0-flash` by default) — but the AI call is deliberately layered so the provider is swappable without touching `RecommendationService`:

Set `GEMINI_API_KEY` in `.env` to enable it (get a free key at [aistudio.google.com](https://aistudio.google.com/apikey)). **Without a key, the app still works end-to-end** — recommendations silently fall back to 3 random products from the catalog, exactly as the task specifies.

### How it works

- The last 3 viewed products are tracked in the session (`viewed_products`), updated on each Product Detail page visit.
- The Home page asks for recommendations based on those; the Product Detail page asks for products similar to the one currently being viewed.
- Both flows share one method (`RecommendationService::recommend()`), which builds a prompt like:

  > Based on these viewed products, suggest 3 similar ones from this product list:
  >
  > Viewed products:
  > - Fjallraven Foldsack No. 1 Backpack: Your perfect pack for everyday use...
  >
  > Candidate products (id: name):
  > 4: Mens Cotton Jacket
  > 7: White Gold Plated Princess Ring
  > ...
  >
  > Respond with a JSON array of exactly 3 product IDs from the candidate list above, ordered by relevance. Only use IDs that appear in the candidate list.

- `GeminiClient` uses Gemini's `responseSchema` (structured JSON output — an array of integers) rather than parsing free-text, so the response is either a clean list of IDs or the call fails outright — no brittle regex/string parsing.
- **Fallback logic:** any failure (missing API key, network error, timeout, malformed response, IDs that don't match any candidate) falls back to `collect($candidates)->shuffle()->take(3)`. The user never sees an error — they just get 3 products instead of a personalized 3.
- **Caching:** results are cached for 10 minutes, keyed by a hash of the viewed-product IDs + candidate catalog IDs, so repeat views don't re-hit the API.

## Cart

Entirely session-based (`App\Modules\Cart\Services\CartService`) — no database table. `CartController` just renders the page shell; the actual list, quantity steppers, and checkout live in the `CartItems` Livewire component. Steppers are wired through Alpine (`@click="$wire.increment(id)"`), calling back into the component to mutate the session cart. A small nested `<livewire:cart-badge />` component in the nav listens for a `cart-updated` browser event (dispatched by both `CartItems` and `AddToCartButton`) so the cart count badge stays live across pages without a full reload.

The three Cart Livewire components live under `App\Modules\Cart\Livewire`, outside Livewire's default `App\Livewire` auto-discovery path, so they're registered explicitly via `Livewire::component(...)` in `AppServiceProvider::boot()` — the `<livewire:add-to-cart-button>`-style tags in Blade didn't need to change.

Checkout simulates a payment gateway (~90% success rate, no real Stripe/charge) — on success the cart is cleared and an "Order confirmed" message is shown; on the simulated failure path, the cart is preserved and an error banner is shown instead, so the failure path is actually reachable and testable, not just a happy-path stub.

## Database

- `users` — Laravel default (`AuthController` + `UserRepository`, no third-party auth package)
- `products` — `id`, `uuid`, `name`, `description`, `price`, `image`, timestamps
- No `user_interactions` table — "last viewed" is tracked in the session only, per the task's suggested flow, rather than persisted.

Seed with `make seed` or `./vendor/bin/sail artisan db:seed` (25 products via `ProductFactory`, plus the one test user). `ProductFactory` picks randomly from a curated catalog of 20 real e-commerce products — clothing, jewelry, and electronics — with real names, descriptions, prices, and product photos, so the storefront and AI recommendations have something genuinely product-like to work with instead of Lorem Ipsum and random stock photography.

## Code quality

```bash
make test
```

Runs the same `composer test` script the starter ships with: `composer-license-checker` → PHPStan (max level) → Pint (`--test`) → blade-formatter (`-c`) → Pest. All of it passes clean on this codebase.
