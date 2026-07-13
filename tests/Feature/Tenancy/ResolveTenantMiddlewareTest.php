<?php

use App\Models\Tenant;
use App\Services\Tenancy\CurrentTenantService;
use Illuminate\Support\Facades\Route;

beforeEach(function (): void {
    config(['tenancy.base_domain' => 'flowdesk.test']);
    config(['tenancy.central_domains' => ['flowdesk.test', 'localhost']]);

    Route::middleware('tenant')->get('/__test_tenant_probe', function () {
        return response()->json([
            'tenant_id' => app(CurrentTenantService::class)->get()?->id,
        ]);
    });
});

it('resolves the correct tenant from the subdomain and binds it into CurrentTenantService', function (): void {
    $tenant = Tenant::factory()->create(['subdomain' => 'acme', 'status' => 'active']);
    Tenant::factory()->create(['subdomain' => 'globex', 'status' => 'active']);

    $response = $this->get('http://acme.flowdesk.test/__test_tenant_probe');

    $response->assertOk()->assertJson(['tenant_id' => $tenant->id]);
});

it('rejects an unknown subdomain with a 404', function (): void {
    $response = $this->get('http://ghost.flowdesk.test/__test_tenant_probe');

    $response->assertNotFound();
});

it('rejects a suspended tenant subdomain with a 404', function (): void {
    Tenant::factory()->create(['subdomain' => 'suspended-co', 'status' => 'suspended']);

    $response = $this->get('http://suspended-co.flowdesk.test/__test_tenant_probe');

    $response->assertNotFound();
});

it('skips tenant resolution entirely for central domains', function (): void {
    $response = $this->get('http://flowdesk.test/__test_tenant_probe');

    $response->assertOk()->assertJson(['tenant_id' => null]);
});
