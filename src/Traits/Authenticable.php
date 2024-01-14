<?php

namespace Laiux\Auth\Traits;

use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use ipinfo\ipinfo\IPinfo;
use Jenssegers\Agent\Agent;
use Laiux\Auth\Models\Session;

trait Authenticable{

    /**
         * Generate a JWT with the parent object adding params for the generation
         *
         * @param string          $aud The Audience URL.
         *
         * @return string A signed JWT
    */
    public function generateJWTToken(Request $request, string $aud = null): array {

        $hidden = $this->hidden;

        $properties = $this->attributes;

        $data_object = [];

        $ip = $request->getClientIp();

        $ipInfo_access_token = config('app.ipinfo_access_token');

        $ipInfo = [];
        $ipInfo["ip"] = $ip;

        if($ip == "127.0.0.1"){
            $ipInfo["type"] = "local";
        } else {
            $client = new IPinfo($ipInfo_access_token);
            $details = $client->getDetails($ip);
            $ipInfo["type"] = "client";
            $ipInfo["details"] = [
                "continent" => $details->continent["name"],
                "country" => $details->country_name,
                "country_flag" => $details->country_flag_url,
                "region" => $details->region,
                "city" => $details->city,
                "location" => $details->loc,
                "timezone" => $details->timezone
            ];
        }

        foreach ($properties as $name => $value) {
            if(!in_array($name, $hidden)) $data_object[$name] = $value;
        }

        $secret = config('auth.secret');
        $iss = config('app.url');
        $exp = config('auth.expiration_time');
        $alg = config('auth.algorithm');

        /*  ENGLISH REFERENCE
            sub -> Subject: whom the token refers to.
            iss -> Issuer: who created and signed this token.
            aud -> Audience: who or what the token is intended for.
            exp -> Expiration Time: Identifies the expiration time on or after which the JWT MUST NOT be accepted for processing.
            iat -> Issued at: seconds since Unix epoch.
            nbf -> Not valid Before: seconds since Unix epoch. */

        /* SPANISH REFERENCE
            sub -> Asunto: a quién se refiere el token.
            iss -> Emisor: quién creó y firmó este token.
            aud -> Audiencia: a quién o para qué está destinado el token.
            exp -> Hora de vencimiento: identifica la hora de vencimiento a partir de la cual el JWT NO DEBE aceptarse para su procesamiento.
            iat -> Emitido a las: segundos desde la época de Unix.
            nbf -> No válido Antes: segundos desde la época de Unix. */

        $issued_date = time();

        $data_object['iat'] = $issued_date;
        $data_object['exp'] = $issued_date+$exp;
        if($iss) $data_object['iss'] = $iss;
        if($aud) $data_object['aud'] = $aud;

        $token = JWT::encode($data_object, $secret, $alg);
        $agent = new Agent();
        $agent->setUserAgent($request->userAgent());

        $newSession = Session::create([
            'user_id' => $this->id,
            'token' => $token,
            'expire_time' => $exp,
            'issued_date' => $issued_date,
            'device' => $agent->device() == false ? null : $agent->device(),
            'platform' => $agent->platform() == false ? null : $agent->platform(),
            'browser' => $agent->browser() == false ? null : $agent->browser(),
            'is_desktop' => $agent->isDesktop(),
            'is_phone' => $agent->isDesktop(),
            'is_robot' => $agent->isRobot(),
            'ipInfo' => json_encode($ipInfo)
        ]);

        return $newSession;
    }
}