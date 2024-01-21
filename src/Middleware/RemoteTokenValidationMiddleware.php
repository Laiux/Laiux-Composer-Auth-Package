<?php

namespace Laiux\Auth\Middleware;

use Closure;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class RemoteTokenValidationMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        //Validate if token is send
        $token = $request->bearerToken();

        if($token == null) abort(401);
        
        $url = config('laiux_auth.laiux_auth_server_url');

        if($url == null) abort(401);

        $url = $url.'/api/validate/token';

        $headers = [
            'Authorization' => 'Bearer '.$token
        ];
        try {
            $client = new Client();

            $response = $client->post($url, [
                'headers' => $headers
            ]);

            if(!($response->getStatusCode() == 200)) abort(401);

            $responseBody = $response->getBody()->getContents();

            $data = json_decode($responseBody, true);

            if(!(isset($data['success']) && $data['success'] == true)) abort(401);

            return $next($request);

        } catch (ClientException $e) {
            if ($e->hasResponse() && $e->getResponse()->getStatusCode() != 200) {
                abort(401);
            } else {
                $responseBody = $e->getResponse()->getBody()->getContents();

                $data = json_decode($responseBody, true);

                if(!(isset($data['success']) && $data['success'] == true)) abort(401);

                return $next($request);
            }
        }
    }

}
