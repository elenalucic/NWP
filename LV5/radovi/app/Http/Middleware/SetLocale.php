<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->segment(1); // Uzima prvi segment URL-a (npr. hr ili en)
        if (in_array($locale, config('app.available_locales', ['hr', 'en']))) {
            App::setLocale($locale);
        } else {
            $locale = config('app.locale');
            App::setLocale($locale);
        }
        return $next($request);
    }
}