<?php

use App\Models\CMS\Page;
use App\Models\CMS\Locale;
use App\Models\CMS\Country;
use Illuminate\Support\Str;
use App\Models\CMS\Currency;
use App\Models\Catalog\Offer;
use App\Services\CartService;
use App\Models\Catalog\Category;
use Illuminate\Support\Collection;

if (!function_exists('setting')) {
    function setting($key, $default = null)
    {
        static $settings = null;

        if ($settings === null) {
            $settings = cache()->rememberForever('app_settings', function () {
                return \App\Models\Setting::pluck('value', 'key')->toArray();
            });
        }

        $value = $settings[$key] ?? $default;

        if (is_string($value) && Str::startsWith($value, ['settings/'])) {
            return 'storage/' . ltrim($value, '/');
        }

        return $value;
    }
}


if (!function_exists('cart')) {
    function cart()
    {
        return (new CartService())->get();
    }
}

if (!function_exists('cart_items_count')) {
    function cart_items_count()
    {
        return (new CartService())->getItemCount();
    }
}

if (!function_exists('locale')) {
    function locale()
    {
        return Locale::where('code', app()->getLocale())->first();
    }
}

if (!function_exists('active_locals')) {
    function active_locals()
    {
        return ['en' => 'en'];
        // return Locale::pluck('code')->toArray();
    }
}


if (!function_exists('page_content')) {
    function page_content(string $sectionType, string $key, $default = null)
    {
        $slug = request()->segment(1) ?: 'home';
        $locale = app()->getLocale();

        $page = once(function () use ($slug, $locale) {
            return Page::with([
                'metas',
                'translation',
                'sections.translations' => fn($q) => $q->where('locale', $locale),
            ])
                ->where('slug', $slug)
                ->where('is_active', true)
                ->first();
        });

        if (!$page) return $default;

        $section = $page->sections->firstWhere('type', $sectionType);
        if (!$section) return $default;

        if ($key == 'image') return  asset('storage/' . $section->image);


        $translation = $section->translations->firstWhere('locale', $locale);
        return $translation->{$key} ?? $default;
    }
}


if (!function_exists('render_meta_tags')) {
    function render_meta_tags($meta = null)
    {
        $defaultTitle = config('app.name', 'Optimised Medical Supplies');
        $defaultDescription = 'Welcome to ' . $defaultTitle . ' | Optimised Medical Supplies.';
        $defaultKeywords = 'Optimised Medical Supplies';

        $title = $meta->meta_title ?? $defaultTitle;
        $description = $meta->meta_description ?? $defaultDescription;
        $keywords = $meta->meta_keywords ?? $defaultKeywords;

        return <<<HTML
            <title>{$title}</title>
            <meta name="description" content="{$description}">
            <meta name="keywords" content="{$keywords}">
        HTML;
    }
}

if (!function_exists('active_currency')) {
    /**
     * When $obj = true, returns the Currency row (cached).
     * Otherwise returns the code string (e.g. 'AED').
     */
    function active_currency(bool $obj = false)
    {
        static $active = null;

        if ($active === null) {
            // TODO: swap this for your real “current site / session” currency
            $active = Currency::default()->first();

            if ($active->code === 'AED') {
                $active->symbol = '<span class="dirham-symbol">&#xea;</span>';
            }
        }

        return $obj ? $active : ($active?->code ?? 'GBP');
    }
}

if (!function_exists('price_format')) {
    /**
     * Format price according to currency settings.
     *
     * @param string $ccy  Currency code (e.g., AED, USD, EUR)
     * @param float  $amt  Amount to format
     * @return string
     */
    function price_format(string $ccy, float $amt, $decimal = null): string
    {
        $currency = Currency::where('code', $ccy)->first();

        if (!$currency) {
            return number_format($amt, 2); // fallback
        }

        if ($currency->code === 'AED') {
            $currency->symbol = '<span class="dirham-symbol">&#xea;</span>';
        }

        $formattedAmount = number_format(
            $amt,
            $decimal ?? $currency->decimal ?? 2,
            $currency->decimal_separator ?? '.',
            $currency->group_separator ?? ','
        );

        $formattedAmount = '<span class="variant-price">' . str_replace('&#xa0;', '&nbsp;', $formattedAmount) . '</span>';

        return $currency->currency_position === 'Left'
            ? $currency->symbol . ' ' .  $formattedAmount
            : $formattedAmount . $currency->symbol;
    }
}

if (!function_exists('active_country')) {
    function active_country()
    {
        return Country::first(); // for now send like this
    }
}

if (!function_exists('exchange_rate')) {
    /**
     * Get exchange rate from one currency to another.
     *
     * @param string $from
     * @param string $to
     * @return float
     */
    function exchange_rate($from, $to)
    {
        return 0.27; // AED to USD
    }
}

if (!function_exists('price_convert')) {
    /**
     * Convert price from one currency to another.
     *
     * @param mixed $amount
     * @param string $from
     * @param string $to
     * @return float
     */
    function price_convert($amount, $from, $to)
    {
        if ($from === $to) {
            return $amount;
        }

        $rate = exchange_rate($from, $to);
        return round($amount * $rate, 2);
    }
}
if (!function_exists('mask_sensitive')) {
    /**
     * Mask the sentive string
     *
     * @param mixed $value
     * @return string
     */
    function mask_sensitive(mixed $value): string
    {
        if (!$value) return '';
        $length = strlen($value);
        return substr($value, 0, 6) . str_repeat('*', max($length - 10, 4)) . substr($value, -4);
    }
}

if (!function_exists('header_offers')) {
    /**
     * Mask the sentive string
     *
     * @param int $limit
     * @return Collection
     */
    function header_offers(int $limit = 3): Collection
    {
        $offers = Offer::with('translation')->limit($limit)->get();
        return $offers;
    }
}

if (!function_exists('menu_categories')) {
    /**
     * Mask the sentive string
     *
     * @param int $limit
     * @return Collection
     */
    function menu_categories(int $limit = 6): Collection
    {
        return Category::visible()
            ->withJoins()
            ->withSelection()
            ->whereNull('parent_id')
            ->with(['children' => function ($q) {
                $q->visible()->applySorting('position')->with('translation');
            }])
            ->applySorting('position')
            ->limit($limit)
            ->get();
    }
}
