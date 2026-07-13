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

it('never returns another tenant\'s rows when querying as the current tenant', function (): void {
    $tenantA = Tenant::factory()->create();
    $tenantB = Tenant::factory()->create();

    $current = app(CurrentTenantService::class);

    $current->set($tenantA);
    ScopedNote::create(['title' => 'Tenant A note']);

    $current->set($tenantB);
    ScopedNote::create(['title' => 'Tenant B note']);
    ScopedNote::create(['title' => 'Tenant B second note']);

    $current->set($tenantA);
    $visibleToA = ScopedNote::all();

    expect($visibleToA)->toHaveCount(1);
    expect($visibleToA->first()->title)->toBe('Tenant A note');
    expect($visibleToA->first()->tenant_id)->toBe($tenantA->id);

    $current->set($tenantB);
    $visibleToB = ScopedNote::all();

    expect($visibleToB)->toHaveCount(2);
    expect($visibleToB->pluck('tenant_id')->unique()->all())->toBe([$tenantB->id]);
});

it('stamps tenant_id automatically from the current tenant context on create', function (): void {
    $tenant = Tenant::factory()->create();

    app(CurrentTenantService::class)->set($tenant);

    $note = ScopedNote::create(['title' => 'Auto stamped']);

    expect($note->tenant_id)->toBe($tenant->id);
});

it('leaves queries unscoped when no tenant is set in context (e.g. console/queue)', function (): void {
    $tenantA = Tenant::factory()->create();
    $tenantB = Tenant::factory()->create();

    $current = app(CurrentTenantService::class);

    $current->set($tenantA);
    ScopedNote::create(['title' => 'Tenant A note']);

    $current->set($tenantB);
    ScopedNote::create(['title' => 'Tenant B note']);

    $current->forget();

    expect($current->check())->toBeFalse();
    expect(ScopedNote::all())->toHaveCount(2);
});
