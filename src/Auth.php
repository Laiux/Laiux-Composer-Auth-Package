<?php

namespace Laiux\Auth;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Client;

class Auth {
    public static function signIn(string $username, string $password, string $userAgent): array | null{

        $url = config('laiux_auth.laiux_auth_server_url');

        if($url == null) abort(401);

        $url = $url.'/api/auth';

        $headers = [
            'User-Agent' => $userAgent
        ];

        try {
            $client = new Client();

            $response = $client->post($url, [
                'headers' => $headers,
                'json' => [
                    'email' => $username,
                    'password' => $password
                ]
            ]);

            if(!($response->getStatusCode() == 200)) abort(401);

            $responseBody = $response->getBody()->getContents();

            $data = json_decode($responseBody, true);

            return $data;

        } catch (ClientException $e) {
            if ($e->hasResponse() && $e->getResponse()->getStatusCode() != 200) {
                abort(401);
            } else {
                $responseBody = $e->getResponse()->getBody()->getContents();

                $data = json_decode($responseBody, true);

                if(!(isset($data['success']) && $data['success'] == true)) abort(401);

                return $data;
            }
        }

    }

    public static function signOut(string $token): bool {
        $url = config('laiux_auth.laiux_auth_server_url');

        if($url == null) abort(401);

        $url = $url.'/api/unauth';

        $headers = [
            'Authorization' => 'Bearer '.$token
        ];

        try {
            $client = new Client();

            $response = $client->post($url, [
                'headers' => $headers
            ]);

            if(!($response->getStatusCode() == 200)) return false;

            $responseBody = $response->getBody()->getContents();

            $data = json_decode($responseBody, true);

            if(!(isset($data['success']) && $data['success'] == true)) return false;

            return true;

        } catch (ClientException $e) {
            if ($e->hasResponse() && $e->getResponse()->getStatusCode() != 200) {
                return false;
            } else {
                $responseBody = $e->getResponse()->getBody()->getContents();

                $data = json_decode($responseBody, true);

                if(!(isset($data['success']) && $data['success'] == true)) return false;

                return true;
            }
        }
    }
}