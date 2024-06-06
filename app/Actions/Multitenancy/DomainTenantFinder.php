<?php

namespace App\Actions\Multitenancy;

use Illuminate\Http\Request;
use Spatie\Multitenancy\Models\Concerns\UsesTenantModel;
use Spatie\Multitenancy\Models\Tenant;
use Spatie\Multitenancy\TenantFinder\TenantFinder;

class DomainTenantFinder extends TenantFinder
{
    use UsesTenantModel;

    public function findForRequest(Request $request): ?Tenant
    {
        $host = $request->header('domain-id', null);
        return $this->getTenantModel()::whereDomain($host)->first();
    }
}
