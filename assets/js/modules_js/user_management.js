$(function(){
  function load_userlist(searchkey, start, limit, id){
    var tbody = $('#tblUserList tbody');
		tbody.html('<tr><td colspan="100%" align="center">Searching users...</td></tr>');
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
			tbody.hide().html(''); // clear table body
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
							$('<button class="btn btn-danger"></button>').on('click', function() {
                var thisButton = $(this);
                thisButton.prop('disabled', true).attr('disabled', 'disabled')
                  .html(`<i class="fa fa-spinner fa-spin"></i>`);
                $('#modalUser .modal-heading > h2').html('Edit User');
                $('#btnUpdate, #btnUPDATEPIC, #btnRESETPIC').removeClass('hidden').show();
                $('#btnSave').addClass('hidden').hide();
                $('#passwd, #confirmpasswd').parent('.form-group').addClass('hidden').hide();
                $('#modalUser :input').removeClass('disabled').prop('disabled', false)
                  .removeAttr('disabled');
                $('#changeImage').parent().show().siblings(':input').prop('disabled', true)
                  .attr('disabled', 'disabled');
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
                      // set update button's data-id to user_id of the user to be edited
                      if (index === 'user_id') {
                        $('#btnUPDATEPIC').attr('data-id', value);
                      }
                      // disable username field for superadmin
                      if (index === 'username' && data.data['user_id'] === '1') {
                        $('#modalUser #'+index).prop('disabled', true)
                          .attr('disabled', 'disabled');
                      }

                      //if form field exists
                      if ($('#modalUser #'+index) !== 'undefined') {
                        // set value to form field
                        $('#modalUser #'+index).val(value);

                        if ($('#modalUser #'+index).is('select')) {
                          // select the option denoted by the value from request
                          $('#modalUser #'+index+' option[value="'+value+'"]').prop('selected',true);

                          $('#modalUser #'+index).prop('disabled', false)
                            .removeAttr('disabled');
                          if (value === '1') { // disable changing user type for superadmin
                            $('#modalUser #'+index).prop('disabled', true)
                              .attr('disabled', 'disabled');
                          }
                        }

                        if (index === 'user_photo') {
                          var img = (value.length) ? value : 'default.jpg';
                          $('#userImage').attr('src', `${image_path}users/${img}`);
                          $('#userImageFile').val(`${image_path}users/${img}`);
                        }
                      }
                    });
                    $('#modalUser').modal({backdrop: 'static'});
                  }
                  thisButton.prop('disabled', false).removeAttr('disabled').html('Edit');
								});
							}).html('Edit')
						)
					);
					tbody.append(tr);
				});
        tbody.fadeIn('slow');
			} else {
				tbody.html('<tr><td colspan="100%" align="center">Failed to load user list...</td></tr>');
			}
		});
  }
	load_userlist('', 0, 5, 0);

  $('#btnAdd').on('click', function(){
    $('#modalUser .modal-heading > h2').html('Add New User');
    $('#btnUpdate, #btnUPDATEPIC, #btnRESETPIC').addClass('hidden').hide();
    $('#btnSave').removeClass('hidden').show();
    $('#changeImage').parent().hide();
    $('#modalUser :input').removeClass('disabled').prop('disabled', false)
      .removeAttr('disabled');
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
			var params = 	$('#frmAddUser :input').serializeArray();
			$.post(
				baseurl + 'users/add_new_user',
				{
					params: params
				}
			).done(function(data){
				if (data.response) {
          alert_msg(
            $('#frmAddUser .alert_group'),
            'success',
            'Success!',
            data.message
          );
					load_userlist();

					setTimeout(function(){
						$('#btnCancel').trigger('click');
					}, 3000);
				} else {
          alert_msg(
            $('#frmAddUser .alert_group'),
            'danger',
            'Failed!',
            data.message
          );
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
		$('#frmAddUser :input').prop('disabled',false)
      .removeAttr('disabled').val('');
    $('#frmAddUser :input').parent('.form-group').removeClass('error')
      .find('.note').html('');
		$('#frmAddUser alert_group').addClass('hidden').html('');
	});

  $('#changeImage').on('click', function(){
    if($(this).prop('checked')) {
      $(this).parent().siblings(':input').not('#btnUPDATEPIC').prop('disabled', false)
        .removeAttr('disabled');
    } else {
      $(this).parent().siblings(':input').prop('disabled', true)
        .attr('disabled', 'disabled');
    }
  });

  $('#imgUser').on('change', function(){
    var preview = $('#userImage');
    var file    = $(this)[0].files[0];
    var reader  = new FileReader();

    reader.addEventListener("load", function () {
      var ext = file.substr( (file.lastIndexOf('.') +1) ); console.log(ext);
      var allowedExts = ['jpg','jpeg','png','gif','PNG','JPG','JPEG','GIF']

      if(allowedExts.indexOf(ext) === -1) {
        alert_msg(
          $('#frmAddUser .alert_group'),
          'danger',
          'Invalid File!',
          `Please use image files only. (Allowed file type: ${allowedExts.join(', ')})`
        );
        $('#btnUPDATEPIC').prop('disabled')
        return;
      }
      preview.attr('src', reader.result);
    }, false);

    if (file) {
      reader.readAsDataURL(file);
    }
  });

  $('#btnRESETPIC').on('click', function(){
    $('#userImage').attr('src', `${image_path}users/${$('#userImageFile').val()}`);
    $('#imgUser').reset();
  });

  $('#btnUPDATEPIC').on('click', function(){
    var conf = confirm('Continue updating image?');
    if (conf) {
      var data = new FormData();
      var imgname  =  $('input[type=file]').val();
      var size  =  $('#file')[0].files[0].size;
      var ext = file.substr( (file.lastIndexOf('.') +1) );
      var allowedExts = ['jpg','jpeg','png','gif','PNG','JPG','JPEG','GIF']

      if(allowedExts.indexOf(ext) === -1) {
        alert_msg(
          $('#frmAddUser .alert_group'),
          'danger',
          'Invalid File!',
          `Please use image files only. (Allowed file type: ${allowedExts.join(', ')})`
        );
        return;
      }

      data.append('file', $('#imgUser')[0].files[0]);
      $.post(
        baseurl + 'users/update_user_photo',
        {
          data: data,
          enctype: 'multipart/form-data',
          processData: false,  // tell jQuery not to process the data
          contentType: false   // tell jQuery not to set contentType
        }
      ).done(function(data){

      });
    }
  });

});
