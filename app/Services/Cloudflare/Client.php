<?php

namespace App\Services\Cloudflare;



class Client
{
    private function getAccountId()
    {
        $api_url = 'https://api.cloudflare.com/client/v4/accounts';
        $api_token = getenv('CLOUDFLARE_API_TOKEN');
        $api_email = getenv('CLOUDFLARE_API_EMAIL');
        $headers = [
            'X-Auth-Email: ' . $api_email,
            'X-Auth-Key:' . $api_token,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        $decodedResponse = json_decode($response, true);
        if ($decodedResponse['success']) {
            return $decodedResponse['result'][0]['id'];
        }
        return ['success' => false, 'error' => 'Ошибка при получении ID аккаунта'];
    }

    public function getDomains()
    {
        $api_url = 'https://api.cloudflare.com/client/v4/zones';
        $api_email = getenv('CLOUDFLARE_API_EMAIL');
        $api_token = getenv('CLOUDFLARE_API_TOKEN');
        $accountId = $this->getAccountId();
        $headers = [
            'X-Auth-Email: ' . $api_email,
            'X-Auth-Key:' . $api_token,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        $decodedResponse = json_decode($response, true);
        if ($decodedResponse['success']) {
            return $decodedResponse['result'];
        }
        return ['success' => false, 'error' => 'Ошибка при получении доменов'];
    }

    public function addDomain(string $domain)
    {
        $api_email = getenv('CLOUDFLARE_API_EMAIL');
        $api_token = getenv('CLOUDFLARE_API_TOKEN');
        $accountId = $this->getAccountId();
        if (!$accountId) {
            return ['success' => false, 'error' => 'Ошибка при получении ID аккаунта'];
        }
        $api_url = 'https://api.cloudflare.com/client/v4/zones';
        $headers = [
            'X-Auth-Email: ' . $api_email,
            'X-Auth-Key:' . $api_token,
            'Content-Type: application/json',
        ];

        $data = [
            'name' => $domain,
            'account' => [
                'id' => $accountId
            ],
            'type' => 'full'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $decodedResponse = json_decode($response, true);
        if ($httpCode !== 200 || !$decodedResponse['success']) {
            return ['success' => false, 'error' => 'Ошибка при добавлении домена: ' . $decodedResponse['errors'][0]['message']];
        }

        return ['success' => true, 'id' => $decodedResponse['result']['id'], 'name_servers' => $decodedResponse['result']['name_servers']];
    }

    public function changeARecord(string $zoneId, string $domain, string $ip)
    {
        $api_url = "https://api.cloudflare.com/client/v4/zones/{$zoneId}/dns_records";
        $api_email = getenv('CLOUDFLARE_API_EMAIL');
        $api_token = getenv('CLOUDFLARE_API_TOKEN');
        
        $headers = [
            'X-Auth-Email: ' . $api_email,
            'X-Auth-Key:' . $api_token,
            'Content-Type: application/json',
        ];

        $data = [
            'type' => 'A',
            'name' => $domain,
            'content' => $ip,
            'ttl' => 1, 
            'proxied' => true 
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $decodedResponse = json_decode($response, true);
        if ($httpCode !== 200 || !$decodedResponse['success']) {
            return [
                'success' => false, 
                'error' => 'Ошибка при добавлении DNS-записи: ' . 
                    ($decodedResponse['errors'][0]['message'] ?? 'Неизвестная ошибка')
            ];
        }

        return ['success' => true, 'record' => $decodedResponse['result']];
    }
}
