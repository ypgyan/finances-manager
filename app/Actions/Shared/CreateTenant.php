<?php

namespace App\Actions\Shared;

use Spatie\Multitenancy\Models\Tenant;

class CreateTenant
{
    public function execute(string $name, int $domain): void
    {
        $tenant = new Tenant();
        $tenant->name = $name;
        $tenant->domain = $domain;
        $tenant->database = 'finances';
        $tenant->save();
    }
}
