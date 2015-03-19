$(function() {
  var el = $('#paymentForm input[type=submit]');

  el.click(function(event) {
    event.preventDefault();
    el.prop('disabled', true);

    var formData = $('#paymentForm').serializeArray();

    if (formData[0]['value'] === '') {
      $('#msg').html('金額を入力してください。');
      el.prop('disabled', false);
      return false;
    }

    if (formData[1]['value'] === '') {
      $('#msg').html('カード番号を入力してください。');
      el.prop('disabled', false);
      return false;
    }

    $.post('purchase.php', formData).done(function(res) {
      $('#msg').html('ありがとうございました。');
    }).fail(function() {
      $('#msg').html('投稿が失敗しました。');
      el.prop('disabled', false);
    });

    return false;
  });

});