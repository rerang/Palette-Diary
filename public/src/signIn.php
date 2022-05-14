<?php

$HEADER = '{"alg":"HS256","typ":"JWT"}';
$PAYLOAD = '{"name":"John Doe","password":1516239022}';
$secret_key = 'your-256-bit-secret';

class jwt{
    protected $alg;
    protected $secret_key;
    function __construct() {
        $this->alg='HS256';
        $this->secret_key = "secretTest";
    }
    function hashing(array $data) {
        $header = json_encode(array(
            'alg'=>$this->alg,
            'typ'=>'JWT'
        ));
        $payload = json_encode($data);

        $base64URLencodeHEADER = strreplace(array('+', '/', '='), array('-', '', ''), base64_encode($header));
        $base64URLencodePAYLOAD = strreplace(array('+', '/', '='), array('-', '', ''), base64_encode($payload));

        $data = $base64URLencodeHEADER.".".$base64URLencodePAYLOAD;
        $hashHmacData = hash_hmac('sha256', $data, $secret_key, true);

        $signature =  strreplace(array('+', '/', '='), array('-', '', ''), base64_encode($hashHmacData));

        $token = $base64URLencodeHEADER.".".$base64URLencodePAYLOAD.".".$signature;
        var_dump($token);
}}
?>