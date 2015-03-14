$(document).ready(function() {
  $('body').on('submit', '#xfw-login', function(e) {
    e.preventDefault();
    var form = $(this);
    $('.xfw_login_msg').remove();
    form.ajaxSubmit({
      dataType : 'json',
      success : function(resp) {
        if (resp.process) {
          location.href = resp.redirect;
        } else {
          form.replaceWith(resp.form);
        }
      }
    });
  });
});
