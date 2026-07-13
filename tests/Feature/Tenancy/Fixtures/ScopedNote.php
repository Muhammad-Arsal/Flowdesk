<?php

namespace Tests\Feature\Tenancy\Fixtures;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

/**
 * Fake model, only used to test tenant scoping. Not a real feature.
 */
class ScopedNote extends Model
{
    use BelongsToTenant;

    protected $table = 'scoped_notes';

    protected $fillable = ['title', 'tenant_id'];
}
