<?php

namespace App\Exceptions;

use Illuminate\Http\Request;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Thrown when a subdomain has no matching tenant, or the tenant is suspended.
 */
class TenantNotFoundException extends RuntimeException
{
    public function render(Request $request): Response
    {
        $message = $this->getMessage() !== '' ? $this->getMessage() : 'Tenant not found.';

        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 404);
        }

        return response($message, 404);
    }
}
