$(function(){
  function load_userlist(searchkey, start, limit, id){
    var tbody = $('#tblUserList tbody');
		tbody.html('<tr><td colspan="100%" align="center">Searching news list...</td></tr>');
		//submit data then retrieve from news_model
		$.post(
			'users/load_users', //controllers/slug
      {
        searchkey: searchkey,
        start: start,
        limit: limit,
        id: id
      }
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
						$('<td class="hidden-xs"></td>').html(value['position'])
					).append(
						$('<td class="hidden-xs"></td>').html(value['isLoggedin'])
					).append(
						$('<td></td>').html(value['date_last_loggedin'])
					).append(
						$('<td></td>').append(
							$(
								'<button class="btn btn-danger"></button>', {
									'id' : 'btnEdit',
									'data-id': value['news_id']
								}
							).on('click', function() {
                $('#modalUser .modal-heading > h2').html('Edit User');
                $('#btnUpdate').removeClass('hidden').show();
                $('#btnSave').addClass('hidden').hide();
                $('#passwd, #confirmpasswd').parent('.form-group').addClass('hidden').hide();
								$.post(
									'users/load_users',
                  {
                    searchkey: '',
                    start: 0,
                    limit: 1,
                    id: value['user_id']
                  }
								).done(function(data){
                  if(data.response){
                    $.each(data.data, function(index, value){
                      //if form field exists
                      if ($('#modalUser #'+index) !== 'undefined') {
                        // set value to form field
                        $('#modalUser #'+index).val(value);

                        if ($('#modalUser #'+index).is('select')) {
                          // select the option denoted by the value from request
                          $('#modalUser #'+index+' option[value="'+value+'"]').prop('selected',true);

                          $('#modalUser #'+index).prop('disabled', false).removeAttr('disabled');
                          if (value === '1') {
                            $('#modalUser #'+index).prop('disabled', true).attr('disabled', 'disabled');
                          }
                        }

                        if (index === 'user_photo') {
                          var img = (value.length) ? value : 'default.jpg';
                          $('#userImage').attr('src', `${image_path}users/${img}`);
                        }
                      }
                    });
                    $('#modalUser').modal({backdrop: 'static'});
                  }
								});
							}).html('Edit')
						)
					);
					tbody.append(tr);
				});
			} else {
				tbody.html('<tr><td colspan="100%" align="center">Failed to load user list...</td></tr>');
			}
		});
  }
	load_userlist('', 0, 5, 0);

  $('#btnAdd').on('click', function(){
    $('#modalUser .modal-heading > h2').html('Add New User');
    $('#btnUpdate').addClass('hidden').hide();
    $('#btnSave').removeClass('hidden').show();
    $('#passwd, #confirmpasswd').parent('.form-group').removeClass('hidden').show();
  });

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
			var params = 	$('#frmAddUser :input').serializeArray();console.log(params); return;
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
		$('#frmAddUser :input').prop('disabled',false).removeAttr('disabled').val('');
    $('#frmAddUser :input').parent('.form-group').removeClass('error').find('.note').html('');
		$('#frmAddUser alert_group').addClass('hidden').html('');
	});

  $('#changeImage').on('click', function(){
    if($(this).prop('checked')) {
      
    }
  });

});
