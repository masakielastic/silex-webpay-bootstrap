<?php
function generate_csrf_token()
{
  return base64url_encode(openssl_random_pseudo_bytes(48));
}

function base64url_encode($data)
{
  return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function webpay_charges($key, $data)
{
    $url = 'https://api.webpay.jp/v1/charges';
    return webpay_post($url, $key, $data);
}

function webpay_tokens($key, $data)
{
    $url = 'https://api.webpay.jp/v1/tokens';
    return webpay_post($url, $key, $data);
}

function webpay_post($url, $key, $data)
{
    $body = http_build_query($data, PHP_QUERY_RFC3986);

    $opts['http'] = array(
        'method' => 'POST',
        'header' => 'Authorization: Basic '.base64_encode($key.':')."\r\n".
                    'Content-Type: application/x-www-form-urlencoded',
        'content' => $body
    );

    $context  = stream_context_create($opts);
    $ret = file_get_contents($url, false, $context);
    $ret = json_decode($ret, true);
    preg_match('/\d{3}/', $http_response_header[0], $match);
    $ret['status'] = (int) $match[0];

    return $ret;
}