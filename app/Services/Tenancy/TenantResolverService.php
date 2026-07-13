<?php

namespace App\Services\Tenancy;

use App\Enums\TenantStatus;
use App\Exceptions\TenantNotFoundException;
use App\Repositories\TenantRepository;

/**
 * Works out which tenant a domain belongs to, and checks it's allowed in.
 */
class TenantResolverService
{
    public function __construct(
        private readonly TenantRepository $tenants,
        private readonly CurrentTenantService $currentTenant,
    ) {}

    /**
     * @throws TenantNotFoundException
     */
    public function resolveFromHost(string $host): void
    {
        $centralDomains = (array) config('tenancy.central_domains', []);

        if (in_array($host, $centralDomains, true)) {
            return;
        }

        $baseDomain = (string) config('tenancy.base_domain');
        $subdomain = $this->extractSubdomain($host, $baseDomain);

        if ($subdomain === null) {
            throw new TenantNotFoundException("Unable to resolve a tenant subdomain from host [{$host}].");
        }

        $tenant = $this->tenants->findBySubdomain($subdomain);

        if ($tenant === null) {
            throw new TenantNotFoundException("No tenant found for subdomain [{$subdomain}].");
        }

        if ($tenant->status === TenantStatus::Suspended) {
            throw new TenantNotFoundException("Tenant [{$subdomain}] is suspended.");
        }

        $this->currentTenant->set($tenant);
    }

    private function extractSubdomain(string $host, string $baseDomain): ?string
    {
        if ($baseDomain === '' || ! str_ends_with($host, '.'.$baseDomain)) {
            return null;
        }

        $subdomain = substr($host, 0, -(strlen($baseDomain) + 1));

        return $subdomain !== '' ? $subdomain : null;
    }
}
