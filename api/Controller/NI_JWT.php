<?php

class NI_JWT
{
    public static function CheckToken($jwt)
    {
        try {
            $decoded = JWT::decode($jwt, APISK, array('HS256'));
            $data =json_decode(json_encode($decoded->data), true);
            /*  $user = model\users::check($data);
             if (empty($user)) {
                 return (array(
                     false,
                      "error" => 'error on token',
                      "data" => $data
                  ));
             }else{
                 return (array(
                     true,
                     $user->id,
                     'user'
                 ));
             } */
        } catch (Exception $e) {
            return (array(
               false,
                "error" => $e->getMessage()
            ));
        }
    }

    public static function CreateToken(array $data)
    {
        $issuedat_claim = time(); // issued at
        $token = array(
            "iat" => $issuedat_claim,
            "data" => array(
                "mail" => $data['mail'],
                "password" => $data['password']
        ));
        return JWT::encode($token, APISK);
    }

    public static function getJWT()
    {
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }
}
