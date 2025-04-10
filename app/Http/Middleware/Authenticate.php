<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        // Pre API - keď nie je autentifikovaný, nech to len vráti 401, žiadne presmerovanie
        return $request->expectsJson() ? null : null;
    }
}