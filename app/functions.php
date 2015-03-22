<?php
function generate_csrf_token()
{
  return base64url_encode(openssl_random_pseudo_bytes(48));
}

function base64url_encode($data)
{
  return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}