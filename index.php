<?php
if (empty($_SERVER['HTTPS'])) {
    echo 'SSL/TLS でアクセスしてください。';
    exit;
}

if (!file_exists('config.php')) {
    echo 'config.php.sample から config.php を用意してください。';
    exit;
}

require_once 'config.php';

if (empty($public_key) || empty($private_key)) {
    echo 'config.php に公開可能鍵と非公開鍵を記入してください。';
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>決済のページ</title>
</head>
<body>
<form id="paymentForm" action="#" method="post">
  <input placeholder="金額を入力してください。" name="amount" type="number" />
  <script 
    src="https://checkout.webpay.jp/v2/"
    class="webpay-button"
    data-key="<?php echo $public_key; ?>"
    data-lang="ja"
    data-partial="true"
  ></script>
  <input type="submit" value="投稿する" />
</form>
<div id="msg"></div>
<link rel="stylesheet" type="text/css" href="css/main.css">
<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>