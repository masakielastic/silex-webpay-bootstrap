<?php

$app_config = [

    // 公開可能鍵と秘密鍵の文字列を記入します。
    // https://webpay.jp/settings

    // test_public_XXXXXXXXXX
    'public_key' => '',
    // test_secret_XXXXXXXXX
    'private_key' => '',

    // 公開する URL に合わせて修正してください。
    // 例1: https://example.org/silex-bootstrap/web
    // 'base_uri' => '/silex-webpay-bootstrap/web'
    //
    // 例2; https://example.org/
    // 'base_uri' => ''

    'base_uri' => '/silex-webpay-bootstrap/web',

    // トップページで読み込まれるビューファイルの名前
    'views' => ['index.example.twig', 'index.example.php'],

    'msg' => [
        'success' => 'ありがとうございます。',
        'failure' => '決済を完了できませんでした。'
    ],

    // SessionServiceProvider に渡す設定値
   'session.storage.options' => [
       'name' => 'payment_app',
       'cookie_secure' => true,
       'cookie_httponly' => true
    ],
    // デバッグモードの切り替え
    'debug' => false
];
