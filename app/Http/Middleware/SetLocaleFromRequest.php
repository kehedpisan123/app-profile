<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromRequest
{
    /**
     * Locales that have visitor-facing content translations.
     *
     * @var array<int, string>
     */
    private array $supported = ['id', 'en'];

    /**
     * Prefer an explicit `lang` query value, then the Accept-Language header.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->localeFrom($request);

        if ($locale !== null) {
            app()->setLocale($locale);
        }

        return $next($request);
    }

    private function localeFrom(Request $request): ?string
    {
        $requested = $request->query('lang');

        if (is_string($requested) && in_array($requested, $this->supported, true)) {
            return $requested;
        }

        $preferred = $request->getPreferredLanguage($this->supported);

        if (is_string($preferred) && in_array($preferred, $this->supported, true)) {
            return $preferred;
        }

        return null;
    }
}
