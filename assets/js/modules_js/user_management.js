$(function(){
  function load_userlist(){
    var tbody = $('#tblUserList tbody');
		tbody.html('<tr><td colspan="100%" align="center">Searching news list...</td></tr>');
		//submit data then retrieve from news_model
		$.get(
			'users/load_users' //controllers/slug
		).done(function(data){
			tbody.html(''); // clear table body
			if(data.response) {
				//get each value and create table row/data
				$.each(data.data,function(index,value){
          value['isLoggedin'] = (value['isLoggedin'] > 0) ? 'Active' : 'Inactive';
					var tr = $('<tr></tr>');
					tr.append(
						$('<td></td>').html(value['user_id'])
					).append(
						$('<td></td>').html(value['username'])
					).append(
						$('<td></td>').html(value['first_name'] + ' ' + value['last_name'])
					).append(
						$('<td></td>').html(value['position'])
					).append(
						$('<td></td>').html(value['isLoggedin'])
					).append(
						$('<td></td>').html(value['date_last_loggedin'])
					).append(
						$('<td></td>').append(
							$(
								'<button class="btn btn-danger"></button>', {
									'id' : 'btnUpdate',
									'data-id': value['user_id']
								}
							).on('click', function() {
								$.get(
									'users/get_user/'+value['user_id']
								).done(function(data){
									if(data.response)
									{
										$('#user_id').html(value['user_id']);
										$('#txtUsernameUpdate').val(data.data[0].username);
										$('#txtPasswordUpdate').val(data.data[0].passwd);
										$('#txtEmailUpdate').val(data.data[0].email);
										$('#txtFnameUpdate').val(data.data[0].first_name);
										$('#txtMnameUpdate').val(data.data[0].mid_name);
										$('#txtLnameUpdate').val(data.data[0].last_name);
										$('#txtPositionUpdate').val(data.data[0].position);
										$('.formUpdate').show();
									}
									else
									{
										console.log(data.message);
									}
								});
							}).html('Edit')
						)
					);
					tbody.append(tr);
				});
			} else {
				tbody.html('<tr><td colspan="100%" align="center">Failed to load news list...</td></tr>');
			}
		});
  }
	load_userlist();

	$('#btnSave').on('click', function() {
		var error = 0;

		$('#frmAddUser :input').each(function() {
			var thisField = $(this);
			if (thisField.attr('data-required') && !thisField.val().length) {
				thisField.parent('.form-group').addClass('error')
					.find('.note').html(thisField.data('required'));
				error++;
			}

			if (thisField.attr('name') == 'passwd' || thisField.attr('name') == 'confirmpasswd') {
				if ($('#passwd').val() != $('#confirmpasswd').val()) {
					$('#passwd').parent('.form-group').addClass('error')
					.find('.note').html('Password does not match.');
					$('#confirmpasswd').parent('.form-group').addClass('error')
					.find('.note').html('');
					error++;
				}
			}
		});

		if (!error) {
			var params = 	$('#frmAddUser :input').serializeArray();
			$.post(
				baseurl + 'users/add_new_user',
				{
					params: params
				}
			).done(function(data){
				if (data.response) {
					$('#frmAddUser .alert_group').removeClass('hidden')
						.addClass('alert-success')
						.html(
							`<strong>Success!</strong> ${data.message}`
						).show();
					load_userlist();

					setTimeout(function(){
						$('#btnCancel').trigger('click');
					}, 3000);
				} else {
					$('#frmAddUser .alert_group').removeClass('hidden')
						.addClass('alert-danger')
						.html(
							`<strong>Failed!</strong> ${data.message}`
						).show();
				}
			});
		}
	});

	$('#frmAddUser :input').on('keyup change paste', function(){
		$(this).parent('.form-group').removeClass('error')
			.find('.note').html('');

		if ($(this).attr('name') == 'passwd' || $(this).attr('name') == 'confirmpasswd') {
			$('#passwd, #confirmpasswd').parent('.form-group').removeClass('error')
				.find('.note').html('');
		}
	});

	$('#btnCancel').on('click',function(){
		$('#frmAddUser [type="text"], #frmAddUser [type="password"]').val('');
		$('#frmAddUser :input').parent('.form-group').removeClass('error')
			.find('.note').html('');
			$('#frmAddUser alert_group').addClass('hidden').html('');
	});



});
