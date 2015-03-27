(function($) {

  $.ajaxSetup({
    beforeSend: function(xhr) {
      xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
    }
  });

  var msg = $('#alertMsg');
  var el = $('#chargeSubmit');

  el.click(function(event) {
    event.preventDefault();
    el.prop('disabled', true);

    var formData = {
      'amount': amount = $('#chargeAmount').val(),
      'webpay-token': $('#chargeForm').serializeArray()[0]['value']
    };

    if (formData['amount'] === '') {
      msg.children('p').html('金額を入力してください。');
      msg.show();
      el.prop('disabled', false);
      return false;
    }

    if (formData['webpay-token'] === '') {
      msg.children('p').html('カード情報を入力してください。');
      msg.show();
      el.prop('disabled', false);
      return false;
    }

    $.post(charge_uri, formData).done(function(data, textStatus, jqXHR) {
      msg.removeClass('alert-danger');
      msg.addClass('alert-success');

      msg.children('p').html(data['msg']);
      msg.show();

      el.removeClass('btn-primary');
      el.addClass('btn-success');
    }).fail(function(jqXHR, textStatus, errorThrown) {
      msg.children('p').html('投稿が失敗しました。' + jqXHR.responseJSON['msg']);
      msg.show();
      el.prop('disabled', false);
    });

    return false;
  });

})(jQuery);