<?php

namespace App\Http\Middleware;

use App\Services\Tenancy\TenantResolverService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Runs before every tenant request. Figures out the tenant from the URL.
 */
class ResolveTenantFromSubdomain
{
    public function __construct(
        private readonly TenantResolverService $resolver,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $this->resolver->resolveFromHost($request->getHost());

        return $next($request);
    }
}
