<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /** @param Closure(Request): Response $next */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && in_array(Auth::user()->locale, ['it', 'en'])) {
            App::setLocale(Auth::user()->locale);
        } elseif ($request->session()->has('locale') && in_array($request->session()->get('locale'), ['it', 'en'])) {
            App::setLocale($request->session()->get('locale'));
        }

        return $next($request);
    }
}
