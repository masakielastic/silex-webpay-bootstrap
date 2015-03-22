<?php
require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/headers.php';

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

if (!isset($base_uri)) {
    $base_uri = '';
}

$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

session_start();

if ($base_uri.'/' === $uri) {

    if ($method !== 'GET') {
        echo "許可されない HTTP メソッドです。\n";
        exit;
    }

    if (!file_exists(__DIR__.'/views/index.twig')) {
        echo "index.twig を用意してください。\n";
        exit;
    }

    if (!isset($_SESSION['csrf-token'])) {
        $_SESSION['csrf-token'] = generate_csrf_token();
    }

    $loader = new Twig_Loader_Filesystem(__DIR__.'/views');
    $twig = new Twig_Environment($loader);
    echo $twig->render('index.twig', [
        'csrf_token' => $_SESSION['csrf-token'],
        'public_key'=> $public_key,
        'uri' => $base_uri.'/payment'
    ]);

} else if ($base_uri.'/payment' === $uri) {

    if ($method !== 'POST') {
        header('Content-Type: application/json', true, 400);
        echo json_encode(['msg' => '許可されない HTTP メソッドです。']);
        exit;
    }

    require_once __DIR__.'/vendor/autoload.php';
    include __DIR__.'/views/payment.php';
} else {
    http_response_code(404);
    echo "ページは見つかりませんでした。\n";
}