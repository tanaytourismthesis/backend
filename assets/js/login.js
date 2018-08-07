$(function(){

  $('#txtPass').on('keyup', function(e) {
    e = e || window.event;
    if (e.keyCode == 13) { // Return key
        $('#btnLogin').trigger('click');
        return false;
    }
  });

  $('#btnLogin').on('click', function(){
    var username = $('#txtUser').val();
    var password = $('#txtPass').val();

    $.post(
  		'login/login_user',
  			{
  				username: username,
          password: password
  			}
  	).done(function(data){
  			var msg = data.message;

        if (data.response) {
          window.location = baseurl + 'users';
        }
		});
  });

});
