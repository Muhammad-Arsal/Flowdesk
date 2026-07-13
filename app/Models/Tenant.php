<?php

namespace App\Models;

use App\Enums\TenantStatus;
use Database\Factories\TenantFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** @use HasFactory<TenantFactory> */
class Tenant extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'subdomain',
        'status',
    ];

    protected $casts = [
        'status' => TenantStatus::class,
    ];

    /**
     * Use the subdomain as the route/lookup key instead of the primary key.
     */
    public function getRouteKeyName(): string
    {
        return 'subdomain';
    }
}
