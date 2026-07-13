<?php

use App\Models\Tenant;
use App\Services\Tenancy\CurrentTenantService;
use Illuminate\Support\Facades\Schema;
use Tests\Feature\Tenancy\Fixtures\ScopedNote;

beforeEach(function (): void {
    if (! Schema::hasTable('scoped_notes')) {
        Schema::create('scoped_notes', function ($table): void {
            $table->id();
            $table->uuid('tenant_id');
            $table->string('title');
            $table->timestamps();
        });
    }
});

it('bypasses tenant scoping via withoutTenantScope for super-admin-style cross-tenant queries', function (): void {
    $tenantA = Tenant::factory()->create();
    $tenantB = Tenant::factory()->create();

    $current = app(CurrentTenantService::class);

    $current->set($tenantA);
    ScopedNote::create(['title' => 'Tenant A note']);

    $current->set($tenantB);
    ScopedNote::create(['title' => 'Tenant B note 1']);
    ScopedNote::create(['title' => 'Tenant B note 2']);

    // Normal query: only sees tenant B's notes.
    expect(ScopedNote::all())->toHaveCount(2);

    // withoutTenantScope(): sees every tenant's notes.
    $all = ScopedNote::withoutTenantScope()->get();

    expect($all)->toHaveCount(3);
    expect($all->pluck('tenant_id')->unique()->sort()->values()->all())
        ->toBe(collect([$tenantA->id, $tenantB->id])->sort()->values()->all());
});
