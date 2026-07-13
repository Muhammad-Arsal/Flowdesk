<?php

namespace App\Models\Scopes;

use App\Services\Tenancy\CurrentTenantService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    /**
     * Adds "where tenant_id = current tenant" to every query for this model.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $currentTenant = app(CurrentTenantService::class);

        if ($currentTenant->check()) {
            $builder->where($model->qualifyColumn('tenant_id'), '=', $currentTenant->get()->id);
        }
    }
}
