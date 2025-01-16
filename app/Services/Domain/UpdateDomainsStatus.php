<?php


namespace App\Services\Domain;

use App\Models\Domain;
use App\Services\Cloudflare\Client;
use Exception;

class UpdateDomainsStatus
{
    public function update()
    {
        $CFClient = new Client();
        $domains = $CFClient->getDomains();
        foreach ($domains as $domainItem) {
            $domain = Domain::where('cloudflare_zone_id', $domainItem['id'])->first();
            if (!$domain) {
                continue;
            }
            $domain->status = $domainItem['status'];
            $domain->save();
        }
        return true;
    }
}
