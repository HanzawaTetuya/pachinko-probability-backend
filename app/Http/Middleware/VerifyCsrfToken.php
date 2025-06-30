<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyCsrfToken extends Middleware
{
    protected function tokensMatch($request)
    {
        $matches = parent::tokensMatch($request);

        if (!$matches) {
            Log::warning("CSRF token mismatch detected!", [
                'url' => $request->fullUrl(),
                'expected_token' => $request->session()->token(),
                'provided_token' => $request->header('X-CSRF-TOKEN') ?? $request->_token,
            ]);
        }

        return $matches;
    }
}
