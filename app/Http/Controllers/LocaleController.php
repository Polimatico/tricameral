<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocaleController extends Controller
{
    public function switch(Request $request): RedirectResponse
    {
        $locale = $request->input('locale');

        if (! in_array($locale, ['it', 'en'])) {
            return back();
        }

        if (Auth::check()) {
            Auth::user()->update(['locale' => $locale]);
        } else {
            $request->session()->put('locale', $locale);
        }

        return back();
    }
}
