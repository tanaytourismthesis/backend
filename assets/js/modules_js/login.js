$(function() {

  $('#txtPass, #txtUser').on('keyup change paste', function() {
    var usernameLen = $('#txtUser').val().length;
    var passwordLen = $('#txtPass').val().length;

    $('#btnLogin').prop('disabled', true).attr('disabled', 'disabled');
    if (usernameLen && passwordLen) {
      $('#btnLogin').prop('disabled', false).removeAttr('disabled');
    }
  });

  $('#txtPass').on('keyup change paste', function(e) {
    e = e || window.event;
    if (e.keyCode === 13) { // Return key
        $('#btnLogin').trigger('click');
        return false;
    }
  });

  $('#btnLogin').on('click', function() {
    var username = $('#txtUser').val();
    var password = $('#txtPass').val();
    var thisButton = $(this);

    thisButton.prop('disabled', true).attr('disabled', 'disabled')
      .html(`<i class="fa fa-spinner fa-spin"></i>&nbsp;${$(this).data('processing')}`);

    clear_alert($('.alert_group'));

    $.post(
  		'login/login_user',
  			{
  				username: username,
          password: password
  			}
  	).done(function(data){
  			var msg = data.message;

        if (data.response) {
          window.location = baseurl + defctrl;
        } else {
          alert_msg(
            $('.alert_group'),
            'danger',
            'Failed to login!',
            'Invalid username and password'
          );
          thisButton.prop('disabled', false).removeAttr('disabled')
            .html('Login');
        }
		}).fail(function(){
      alert_msg(
        $('.alert_group'),
        'danger',
        'Oops! Something went wrong.',
        'Please contact your administrator'
      );
      thisButton.prop('disabled', false).removeAttr('disabled')
        .html('Login');
    });
  });

  $('#txtUser').focus();

});
