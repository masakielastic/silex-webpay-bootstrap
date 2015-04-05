<?php

$app_config = [

    // 公開可能鍵と秘密鍵の文字列を記入します。
    // https://webpay.jp/settings

    // test_public_XXXXXXXXXX
    'public_key' => '',
    // test_secret_XXXXXXXXX
    'private_key' => '',

    // トップページで読み込まれるビューファイルの名前
    'views' => ['index.php', 'index.twig', 'index.example.php', 'index.example.twig'],

    'msg' => [
        'success' => 'ありがとうございます。',
        'failure' => '決済を完了できませんでした。'
    ]
];
