<?php

namespace App\Models\Concerns;

use App\Models\Scopes\TenantScope;
use App\Models\Tenant;
use App\Services\Tenancy\CurrentTenantService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Add this to any model that belongs to a tenant.
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait BelongsToTenant
{
    /**
     * Runs automatically when a model using this trait boots.
     */
    public static function bootBelongsToTenant(): void
    {
        static::creating(function (Model $model): void {
            if (empty($model->tenant_id)) {
                $currentTenant = app(CurrentTenantService::class);

                if ($currentTenant->check()) {
                    $model->tenant_id = $currentTenant->get()->id;
                }
            }
        });

        static::addGlobalScope(new TenantScope());
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Lets a super-admin query all tenants' data, skipping the filter.
     */
    public static function withoutTenantScope(): Builder
    {
        return static::query()->withoutGlobalScope(TenantScope::class);
    }
}
