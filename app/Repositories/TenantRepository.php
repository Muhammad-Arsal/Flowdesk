<?php

namespace App\Repositories;

use App\Models\Tenant;

/**
 * Handles all database lookups for tenants.
 */
class TenantRepository
{
    public function findBySubdomain(string $subdomain): ?Tenant
    {
        return Tenant::query()->where('subdomain', $subdomain)->first();
    }
}
