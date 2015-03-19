<?php
require_once 'config.php';
require_once 'functions.php';

session_start();

$msg = '';
$valid = true;

if (!isset($_SERVER['HTTP_X_CSRF_TOKEN'])) {
  $msg .= 'CSRF 対策のヘッダートークンが送信されていません。';
  $valid = false;
} else if (!isset($_SESSION['csrf-token'])) {
  $msg .= 'CSRF 対策のセッショントークンが送信されていません。';
  $valid = false;
} else {
  if (function_exists('hash_equals') &&
    !hash_equals($_SERVER['HTTP_X_CSRF_TOKEN'], $_SESSION['csrf-token'])
  ) {
    $msg .= 'CSRF 対策のトークンが一致しません。';
    $valid = false;
  } else if ($_SERVER['HTTP_X_CSRF_TOKEN'] !== $_SESSION['csrf-token']) {
    $msg .= 'CSRF 対策のトークンが一致しません。';
    $valid = false;
  }
}

if (!isset($_POST['webpay-token'])) {
  $msg .= 'クレジットカードのトークンが送信されていません。';
  $valid = false;
}

if (!isset($_POST['amount'])) {
  $msg .= '金額が送信されていません。';
  $valid = false;
}

if (!$valid) {
  header('Content-Type: application/json', true, 400);
  echo json_encode(array('msg' => $msg));
  exit;
}

$data = array(
  'amount' => $_POST['amount'],
  'currency' => 'jpy',
  'card' => $_POST['webpay-token']
);

$ret = webpay_charges($private_key, $data);
$json = json_encode($ret, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);

header('Content-Type: application/json', true, $ret['status']);
echo $json;