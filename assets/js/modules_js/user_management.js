$(function(){
  function load_userlist(searchkey, start, limit, id){
    var tbody = $('#tblUserList tbody');
		tbody.html('<tr><td colspan="100%" align="center">Searching users...</td></tr>');
		//submit data then retrieve from news_model
		$.post(
			`${baseurl}users/load_users`, //controllers/slug
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
        var ctr = 0
				$.each(data.data.records,function(index,value){
          value['isLoggedin'] = (value['isLoggedin'] > 0) ? 'Active' : 'Inactive';
					var tr = $('<tr></tr>');
					tr.append(
						$('<td></td>').html(++ctr)
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
                $('#modalUser :input').removeClass('disabled').prop('disabled', false)
                  .removeAttr('disabled');
                $('#changeImage').parent().show().siblings(':input').prop('disabled', true)
                  .attr('disabled', 'disabled');
                $('#changePassword').prop('checked', false).trigger('change')
                  .parent('label').show();

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
                    $.each(data.data.records, function(index, value){
                      // set update button's data-id to user_id of the user to be edited
                      if (index === 'user_id') {
                        $('#btnUPDATEPIC').attr('data-id', value);
                        $('#btnUpdate').attr('data-id', value);
                      }
                      // disable username field for superadmin
                      if (index === 'username' && data.data.records['user_type_type_id'] === 'aQ%3D%3D') {
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
                          if (value === 'aQ%3D%3D') { // disable changing user type for superadmin
                            $('#modalUser #'+index).prop('disabled', true)
                              .attr('disabled', 'disabled');
                          }
                        }

                        if (index === 'user_photo') {
                          var img = (value.length) ? value : 'default.jpg';
                          $('#userImage').attr('src', `${baseurl}${image_path}users/${img}`);
                          $('#userImageFile').val(img);
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

        // Pagination
        var total_records = data.data.total_records;
        var total_pages = parseInt(total_records / items_per_page);
        total_pages = (total_records % items_per_page > 0) ? ++total_pages : total_pages;
        var page_num = parseInt($('.page_num').text());

        $('.total_pages').html(total_pages);
        $('.total_records').html(total_records);

        $('#btnPREV, #btnNEXT').show();
        if (total_records < items_per_page) {
          $('#btnPREV, #btnNEXT').hide();
        }

        $('#btnPREV').prop('disabled', false).removeAttr('disabled');
        if (page_num === 1) {
          $('#btnPREV').prop('disabled', true).attr('disabled', 'disabled');
        }

        $('#btnNEXT').prop('disabled', false).removeAttr('disabled');
        if (page_num === total_pages) {
          $('#btnNEXT').prop('disabled', true).attr('disabled', 'disabled');
        }

        $('#btnPREV').on('click', function(){
          page_num--;
          $('.page_num').html(page_num);
          if (page_num === 1) {
            $(this).prop('disabled', true).attr('disabled', 'disabled');
          }
          load_userlist('', ((page_num-1) * items_per_page), items_per_page, 0);
        });

        $('#btnNEXT').on('click', function(){
          page_num++;
          $('.page_num').html(page_num);
          if (page_num === total_pages) {
            $(this).prop('disabled', true).attr('disabled', 'disabled');
          }
          load_userlist('', ((page_num-1) * items_per_page), items_per_page, 0);
        });
        $('.navigator-right').removeClass('hidden').show();
        tbody.fadeIn('slow');
			} else {
				tbody.html('<tr><td colspan="100%" align="center">Failed to load user list...</td></tr>');
        $('.navigator-right').addClass('hidden').hide();
			}
		});
  }
	load_userlist('', 0, items_per_page, 0);

  $('#btnAdd').on('click', function(){
    $('#modalUser .modal-heading > h2').html('Add New User');
    $('#btnUpdate, #btnUPDATEPIC, #btnRESETPIC').addClass('hidden').hide();
    $('#btnSave').removeClass('hidden').show();
    $('#changeImage').parent().hide();
    $('#modalUser :input').removeClass('disabled').prop('disabled', false)
      .removeAttr('disabled');
    $('#changePassword').prop('checked', true).trigger('change')
      .parent('label').hide();
  });

	$('#btnSave').on('click', function() {
		var error = 0;

		$('#frmUser :input.field').each(function() {
			var thisField = $(this);
			if (thisField.attr('data-required') && !thisField.val().length) {
				thisField.parent('.form-group').addClass('error')
					.find('.note').html(thisField.data('required'));
				error++;
			}

      if (thisField.attr('name') == 'email') {
        if (!validateEmail(thisField.val())) {
          thisField.parent('.form-group').addClass('error')
  					.find('.note').html('Please provide a valid email.');
          error++;
        }
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

    if (!$('#imgUser')[0].files.length) {
      $('#imgUser').parent('.form-group').addClass('error')
      .find('.note').html('Please upload image.');
      error++;
    } else {
      var imgname = $('#imgUser').val();
      var size = $('#imgUser')[0].files[0].size;
      var ext = imgname.substr( (imgname.lastIndexOf('.') +1) );
      var allowedExts = ['jpg','jpeg','png','gif','PNG','JPG','JPEG','GIF']
      var user_id = $(this).data('id');

      if(allowedExts.indexOf(ext) === -1) {
        $('#imgUser').parent('.form-group').addClass('error')
        .find('.note').html(`Please use image files only. (Allowed file type: ${allowedExts.join(', ')})`);
        error++;
      } else if (size * 1e-6 > 5) {
        $('#imgUser').parent('.form-group').addClass('error')
        .find('.note').html('File size must not exceed 5MB.');
        error++;
      }
    }


		if (!error) {
      var data = new FormData();
      data.append('file', $('#imgUser')[0].files[0]);

			var params = 	JSON.stringify($('#frmUser :input.field').serializeArray());
      data.append('params', params);

      $.ajax({
        url: `${baseurl}users/add_new_user`,
        type: 'post',
        data: data,
        enctype: 'multipart/form-data',
        processData: false,  // tell jQuery not to process the data
        contentType: false,   // tell jQuery not to set contentType
        cache: false,
        success: function(data){
          alert_msg(
            $('#frmUser .alert_group'),
            (data.response) ? 'success' : 'danger',
            (data.response) ? 'Success!' : 'Failed!',
            (data.response) ? 'Successfully added new user!' : data.message
          );
          if (data.response) {
    				load_userlist('', 0, items_per_page, 0);
            $('#btnSave').attr('disabled','disabled').prop('disabled', true);
            setTimeout(function(){
              $('#btnCancel').trigger('click');
            }, 3000);
          }
        },
        error: function(data) {
          alert_msg(
            $('#frmUser .alert_group'),
            'danger',
            'Failed!',
            'Oops! Something went wrong. Please contact your administrator.'
          );
        }
      });
		}
	});

	$('#frmUser :input').on('keyup change paste', function(){
		$(this).parent('.form-group').removeClass('error')
			.find('.note').html('');

		if ($(this).attr('name') == 'passwd' || $(this).attr('name') == 'confirmpasswd') {
			$('#passwd, #confirmpasswd').parent('.form-group').removeClass('error')
				.find('.note').html('');
		}
	});

	$('#btnCancel').on('click',function(){
		$('#frmUser :input').prop('disabled',false)
      .removeAttr('disabled').val('');
    $('#frmUser :input').parent('.form-group').removeClass('error')
      .find('.note').html('');
		$('#frmUser alert_group').addClass('hidden').html('');
    $('#changeImage').prop('checked', false).trigger('change');
    $('#userImageFile').val('default.jpg');
    $('#btnRESETPIC').trigger('click');
    clear_alert();
	});

  $('#changeImage').on('change', function(){
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
      preview.attr('src', reader.result);
    }, false);

    if (file) {
      var ext = file.name.substr( (file.name.lastIndexOf('.') +1) );
      var allowedExts = ['jpg','jpeg','png','gif','PNG','JPG','JPEG','GIF'];
      var size  =  $('#imgUser')[0].files[0].size;

      if(allowedExts.indexOf(ext) === -1) {
        alert_msg(
          $('#frmUser .alert_group'),
          'danger',
          'Invalid File!',
          `Please use image files only. (Allowed file type: ${allowedExts.join(', ')})`
        );
        return;
      } else if (size * 1e-6 > 5) { // 5MB
        alert_msg(
          $('#frmUser .alert_group'),
          'danger',
          'Invalid File Size!',
          'Files must not exceed 5MB.'
        );
        return;
      }
      clear_alert();
      $('#btnUPDATEPIC').prop('disabled', false).removeAttr('disabled');
      reader.readAsDataURL(file);
    }
  });

  $('#btnRESETPIC').on('click', function(){
    $('#userImage').attr('src', `${baseurl}${image_path}users/${$('#userImageFile').val()}`);
    $('#imgUser').val('');
    $('#btnUPDATEPIC').prop('disabled', true).attr('disabled', 'disabled');
  });

  $('#btnUPDATEPIC').on('click', function(){
    var conf = confirm('Continue updating image?');
    if (conf) {
      var data = new FormData();
      var imgname  =  $('#imgUser').val();
      var size  =  $('#imgUser')[0].files[0].size;
      var ext = imgname.substr( (imgname.lastIndexOf('.') +1) );
      var allowedExts = ['jpg','jpeg','png','gif','PNG','JPG','JPEG','GIF']
      var user_id = $(this).data('id');

      if(allowedExts.indexOf(ext) === -1) {
        alert_msg(
          $('#frmUser .alert_group'),
          'danger',
          'Invalid File!',
          `Please use image files only. (Allowed file type: ${allowedExts.join(', ')})`
        );
        return;
      } else if (size * 1e-6 > 5) {
        alert_msg(
          $('#frmUser .alert_group'),
          'danger',
          'Invalid File Size!',
          'Files must not exceed 5MB.'
        );
        return;
      }
      clear_alert();
      $('#btnUPDATEPIC').prop('disabled', false).removeAttr('disabled');

      data.append('file', $('#imgUser')[0].files[0]);
      data.append('user_id', user_id);
      $.ajax({
        url: `${baseurl}users/update_user_photo`,
        type: 'post',
        data: data,
        enctype: 'multipart/form-data',
        processData: false,  // tell jQuery not to process the data
        contentType: false,   // tell jQuery not to set contentType
        cache: false,
        success: function(data){
          alert_msg(
            $('#frmUser .alert_group'),
            (data.response) ? 'success' : 'danger',
            (data.response) ? 'Success!' : 'Failed!',
            data.message
          );
          if (data.response) {
            $('#imgUser').val('');
            $('#changeImage').prop('checked', false).trigger('change');
          }
        },
        error: function(data) {
          alert_msg(
            $('#frmUser .alert_group'),
            'danger',
            'Failed!',
            'Oops! Something went wrong. Please contact your administrator.'
          );
        }
      });
    }
  });

  $('#changePassword').on('change', function(){
    if ($(this).prop('checked')) {
      $('#passwd').removeClass('hidden').show();
      $('#confirmpasswd').parent('.form-group').removeClass('hidden').show();
    } else {
      $('#passwd').addClass('hidden').hide();
      $('#confirmpasswd').parent('.form-group').addClass('hidden').hide();
    }
  });

  $('#btnUpdate').on('click', function(){
    var error = 0;
    $('#frmUser :input').not(':disabled').not('#passwd, #confirmpasswd').each(function(){
      var thisField = $(this);

      if (thisField.attr('data-required') && !thisField.val().length) {
				thisField.parent('.form-group').addClass('error')
					.find('.note').html(thisField.data('required'));
				error++;
			}

      if (thisField.attr('name') == 'email') {
        if (!validateEmail(thisField.val())) {
          thisField.parent('.form-group').addClass('error')
  					.find('.note').html('Please provide a valid email.');
          error++;
        }
      }
    });

    if ($('#changePassword').prop('checked')) {
      var passVal = $('#passwd').val();
      var confVal = $('#confirmpasswd').val();

      if (!(passVal.length || confVal.length) || passVal !== confVal) {
        $('#passwd').parent('.form-group').addClass('error')
        .find('.note').html('Password does not match.');
        $('#confirmpasswd').parent('.form-group').addClass('error')
        .find('.note').html('');
        error++;
      }
    }

    if (!error) {
      var params = 	$('#frmUser :input').not(':disabled')
                      .not('#passwd, #confirmpasswd').serializeArray();
      var user_id = $(this).data('id');

      params.push({'name':'user_id', 'value':user_id});

      if ($('#changePassword').prop('checked')) {
        params.push({'name':'passwd', 'value':$('#passwd').val()});
      }

			$.post(
        `${baseurl}users/update_user`,
				{
					params: params
				}
			).done(function(data){
        alert_msg(
          $('#frmUser .alert_group'),
          (data.response) ? 'success' : 'danger',
          (data.response) ? 'Success!' : 'Failed!',
          data.message
        );
				if (data.response) {
					load_userlist('', 0, items_per_page, 0);
				}
			}).fail(function(){
        alert_msg(
          $('#frmUser .alert_group'),
          'danger',
          'Failed!',
          'Oops! Something went wrong. Please contact your administrator.'
        );
      });
    }
  });

  $('.search-button').on('click', function(e){
    var searchKey = $.trim($('#search-field').val());
    if (!searchKey.length) {
      $('#search-field').parent('.input-group').addClass('error');
      $(this).popover('toggle');
    } else {
      $(this).popover('hide');

      load_userlist(searchKey, 0, items_per_page, 0);
    }
  });

});
