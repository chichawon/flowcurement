<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(AuthenticatedSessionController $sessions): RedirectResponse|View
    {
        return auth()->check()
            ? redirect()->route('dashboard')
            : $sessions->create();
    }
}
