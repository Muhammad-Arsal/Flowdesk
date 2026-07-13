<?php

namespace App\Services\Tenancy;

use App\Models\Tenant;

/**
 * Remembers which tenant this request belongs to.
 */
class CurrentTenantService
{
    private ?Tenant $tenant = null;

    public function get(): ?Tenant
    {
        return $this->tenant;
    }

    public function set(Tenant $tenant): void
    {
        $this->tenant = $tenant;
    }

    public function check(): bool
    {
        return $this->tenant !== null;
    }

    /**
     * Forgets the current tenant.
     */
    public function forget(): void
    {
        $this->tenant = null;
    }
}
