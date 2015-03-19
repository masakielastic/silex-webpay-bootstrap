<?php
require_once 'config.php';
require_once 'functions.php';

if (!isset($_POST['amount']) || !isset($_POST['webpay-token'])) {
  header('Content-Type: application/json', true, 400);
  echo json_encode(array('status' => '400'));
  exit;
}

$data = array(
  'amount' => $_POST['amount'],
  'currency' => 'jpy',
  'card' => $_POST['webpay-token']
);

$ret = webpay_charges($private_key, $data);

if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
  $json = json_encode($ret, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
} else {
  $json = json_encode($ret);
}

header('Content-Type: application/json', true, $ret['status']);
echo $json;
