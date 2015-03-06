<?php
require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>決済のページ</title>
</head>
<body>
<form id="myForm" action="#" method="post">
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
    data-key="<?php echo $public_key; ?>"
    data-lang="ja"
    data-partial="true"
    data-on-created="onCreate"
  ></script>
  <input type="submit" value="投稿する" />
</form>
<div id="result"></div>
<script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
<script>
$(function() {

  var el = $('#myForm');

  el.click(function(event) {
    event.preventDefault();

    if (token === '') {
      $('#result').html('カード番号を入力してください。');
      return false;
    }

    $.post('purchase.php', { 'token': token }, function(data) {
      console.log(data);
      console.log(data['status']);   
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