<?php


namespace App\Services\Domain;

use App\Models\Domain;
use App\Services\Cloudflare\Client;
use Exception;

class CreateDomain
{
    public function create(array $data): Domain
    {
        $user = auth()->user();
        $countDomain = Domain::where('user_id', $user->id)->count();
        if ($countDomain >= 3) {
            throw new Exception('Вы достигли лимита доменов.');
        }


        $CFClient = new Client();
        $domain = $data['domain'];
        $response = $CFClient->addDomain($domain);
        $ip = getenv('IP_SERVER');
        $CFClient->changeARecord($response['id'], $domain, $ip);
        
        if (!$response['success']) {
            throw new Exception($response['error']);
        }


    

        $domain = Domain::create([
            'domain' => $domain,
            'user_id' => $user->id,
            'status' => 'pending',
            'title' => $data['title'],
            'cloudflare_zone_id' => $response['id'],
            'ns_records' => $response['name_servers'],
        ]);

        return $domain;
    }
}