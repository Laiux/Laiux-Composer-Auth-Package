<?php

namespace Laiux\Auth;

use Firebase\JWT\JWT;

trait Authenticable{

    /**
         * Generate a JWT with the parent object adding params for the generation
         *
         * @param string          $aud The Audience URL.
         *
         * @return string A signed JWT
    */
    public function generateJWTToken(string $aud = null): string {
        $data_object = (array) $this;
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

        return $token;
    }
}