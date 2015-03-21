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

if (!file_exists(__DIR__.'/view/index.php')) {
    echo 'index.php を用意してください。';
    exit;
}

session_start();
if (!isset($_SESSION['csrf-token'])) {
    $_SESSION['csrf-token'] = generate_csrf_token();
}

include __DIR__.'/view/index.php';