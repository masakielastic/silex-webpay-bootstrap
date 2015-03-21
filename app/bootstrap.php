<?php

require_once __DIR__.'/headers.php';

if (empty($_SERVER['HTTPS'])) {
    echo 'SSL/TLS でアクセスしてください。';
    exit;
}

if (!file_exists(__DIR__.'/config.php')) {
    echo 'config.php を用意してください。';
    exit;
}

require_once __DIR__.'/config.php';
require_once __DIR__.'/functions.php';

if (!isset($public_key) || !isset($private_key)) {
    echo 'config.php に公開可能鍵と非公開鍵を記入してください。';
    exit;
}

session_start();

if (!isset($base_uri)) {
    $base_uri = '';
}

$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

if ($base_uri.'/' === $uri) {

    if ($method !== 'GET') {
        echo "許可される HTTP メソッドではありません。\n";
        exit;
    } else {

    }

    if (!file_exists(__DIR__.'/view/index.php')) {
        echo 'index.php を用意してください。';
        exit;
    }

    if (!isset($_SESSION['csrf-token'])) {
        $_SESSION['csrf-token'] = generate_csrf_token();
    }

    include __DIR__.'/view/index.php';

} else {
    http_response_code(404);
    echo "ページは見つかりませんでした。\n";
}