<?php
if (!file_exists('config.php')) {
  echo 'config.php.sample から config.php を用意してください。';
  exit();
}

require_once 'config.php';

if (!isset($public_key) || !isset($private_key) || empty($public_key) || empty($private_key)) {
    echo 'config.php に公開可能鍵と非公開鍵を記入してください。';
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>決済のページ</title>
</head>
<body>
<form id="myForm" action="#" method="post">
  <input id="webPayInput" placeholder="金額を入力してください。" type="number" />
  <script>
    var token = '';

    function onCreate(data) {
      token = data.id;
  
      return false;
   }
  </script>
  <script 
    src="https://checkout.webpay.jp/v2/"
    class="webpay-button"
    data-key="<?php echo htmlspecialchars($public_key, ENT_QUOTES, 'UTF-8'); ?>"
    data-lang="ja"
    data-partial="true"
    data-on-created="onCreate"
  ></script>
  <input id="webPaySubmit" type="submit" value="投稿する" />
</form>
<div id="result"></div>
<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
<script>
$(function() {

  var el = $('#webPaySubmit');

  el.click(function(event) {
    event.preventDefault();

    data = {
      'amount' : $('#webPayInput').val(),
      'token': token
    };

    if (data['amount'] === '') {
        $('#result').html('金額を入力してください。');
        return false;
    }

    if (data['token'] === '') {
      $('#result').html('カード番号を入力してください。');
      return false;
    }

    $.post('purchase.php', data, function(data) {

      if (data['status'] > 199 && 300 > data['status'])  {  	
        $('#result').html('ありがとうございました。');
      } else {
        $('#result').html('投稿が失敗しました。');
      }

    }, 'json').fail(function() {
      $('#result').html('投稿が失敗しました。');
    });

    return false;
  });

});
</script>
</body>
</html>