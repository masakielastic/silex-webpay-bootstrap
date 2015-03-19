$(function() {
  $.ajaxSetup({
    beforeSend: function(xhr) {
      xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
    }
  });

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

    $.post('purchase.php', formData).done(function(data, textStatus, jqXHR) {
      $('#msg').html('ありがとうございました。');
    }).fail(function(jqXHR, textStatus, errorThrown) {
      $('#msg').html('投稿が失敗しました。' + jqXHR.responseJSON['msg']);
      el.prop('disabled', false);
    });

    return false;
  });

});