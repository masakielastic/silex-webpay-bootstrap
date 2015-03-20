<?php
if (!file_exists('config.php')) {
    echo 'config.php.sample から config.php を用意してください。';
    exit;
}

require_once 'config.php';
require_once 'functions.php';

if (empty($_SERVER['HTTPS'])) {
    echo 'SSL/TLS でアクセスしてください。';
    exit;
}

if (empty($public_key) || empty($private_key)) {
    echo 'config.php に公開可能鍵と非公開鍵を記入してください。';
    exit;
}

if (!file_exists('template.php')) {
    echo 'template.php.example から template.php を用意してください。';
    exit;
}

session_start();
if (!isset($_SESSION['csrf-token'])) {
  $_SESSION['csrf-token'] = generate_csrf_token();
}

include 'template.php';
