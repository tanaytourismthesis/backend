var load_hane = (searchkey, start, limit, id) => {
  var tbody = $('#tblHANE tbody');

  setSearchTablePlaceholder(tbody, items_per_page);

  $.post(
    `${baseurl}hf_management/load_hane`,
    {
      searchkey: searchkey,
      start: start,
      limit: limit,
      id: id
    }
  ).done(function(data) {
    tbody.html('');
    if(data.response) {
      var ctr = start;
      $.each(data.data.records, function(index, value) {
        var tr = $('<tr></tr>');
        tr.append(
          $('<td></td>').html(++ctr)
        ).append(
          $('<td class="hidden-xs"></td>').append(
            $('<img />',{
              'src': `${baseurl}assets/images/hane/${value['hotel_image']}`,
            }).css({
              'width': '100%'
            })
          )
        ).append(
          $('<td></td>').html(value['hotel_name'])
        ).append(
          $('<td></td>').html(value['hotel_status'])
        ).append(
          $('<td></td>').append(
            $('<button class="btn btn-xs btn-default"></button>').on('click', function() {
              var thisButton = $(this);
              thisButton.prop('disabled', true).attr('disabled', 'disabled')
                .html(`<i class="fa fa-spinner fa-spin"></i>`);

              $.post(
                `${baseurl}hf_management/load_hane`,
                {
                  searchkey: '',
                  start: 0,
                  limit: 1,
                  id: value['hotel_id']
                }
              ).done(function(data) {
                var imagepath = baseurl + image_path;
                if (data.response) {
                  var modal = $('#modalHANE');
                  $.each(data.data.records, function(index, value) {
                    var thisField = modal.find(`:input.field[name="${index}"]`);
                    if (index === 'hotel_id') {
                      modal.find('#btnUpdate').attr('data-id', value);
                    } else if (index === 'hotel_image') {
                      modal.find('#haneImage').attr('src', `${imagepath}hane/${value}`);
                    }
                    thisField.val(value);
                    if (thisField.attr('type') === 'hidden') {
                      thisField.parents('.form-group').find('[type="checkbox"]')
                        .bootstrapSwitch('state', parseInt(value));
                    }
                  });

                  modal.find('.modal-title').html('Edit');
                  modal.find('#btnUpdate').removeClass('hidden').show();
                  modal.find('#btnSave').addClass('hidden').hide();
                  modal.addClass('edit-form').removeClass('add-form');
                  modal.modal({backdrop: 'static'});
                }
                thisButton.prop('disabled', false).removeAttr('disabled').html('<i class="fas fa-edit"></i>');
              });
            }).html('<i class="fas fa-edit"></i>')
          ).append(
            $('<span>&nbsp;</span>')
          ).append(
            $('<button class="btn btn-xs btn-default"></button>').on('click', function() {

            }).html('<i class="fas fa-eye"></i>')
          )
        );
        tbody.append(tr);
      });

      // Pagination
      var total_records = data.data.total_records;
      var total_pages = parseInt(total_records / items_per_page);
      total_pages = (total_records % items_per_page > 0) ? ++total_pages : total_pages;
      var page_num = parseInt($('.page_num').text());

      setNavigation(total_records, total_pages, page_num, 'load_hane');

      $('.navigator-fields').removeClass('hidden').show();
      tbody.fadeIn('slow');
    } else {
      tbody.show('slow');
      tbody.html('<tr><td colspan="100%" align="center">No results found...</td></tr>');
      $('.navigator-fields').addClass('hidden').hide();
    }
  });
};

$(function(){
  load_hane('', 0, items_per_page, 0);

  $('.tab-items a').on('click', function(e) {
    e.preventDefault();
    var thisTab = $(this);
    var tabContent = thisTab.attr('href');

    thisTab.closest('li').addClass('active').siblings('li').removeClass('active');
    $(`.tab-content${tabContent}`).fadeIn('slow').siblings('.tab-content').slideUp(1);
  });

  $('.search-button').on('click', function(e) {
    var searchKey = $.trim($('#search-field').val());

    if (!searchKey.length) {
      $('#search-field').parent('.input-group').addClass('error');
      $(this).popover('toggle');
    } else {
      $(this).popover('hide');
      $('.page_num').html('1');
      load_hane(searchKey, 0, items_per_page, 0);
    }
  });

  $('.reload-list').on('click', function() {
    $('#search-field').val('');
    $('.page_num').html('1');
    load_hane('', 0, items_per_page, 0);
  });

  $('[type="checkbox"]').bootstrapSwitch({
    'onColor': 'success'
  }).on('switchChange.bootstrapSwitch', function(event, state) {
    $(this).parents('.form-group').find('[type=hidden]').val((state) ? 1 : 0);
  });

  $('#btnAdd').on('click', function(){
    $('#modalHANE .modal-title').html('Add');
    $('#modalHANE #btnSave').removeClass('hidden').show();
    $('#modalHANE #btnUpdate').addClass('hidden').hide();
    $('#modalHANE').addClass('add-form').removeClass('edit-form');
  });

  $('#haneImage').on('click', function(){
    $('#imgHane').trigger('click');
  });

  $('#imgHane').on('change', function() {
    var preview = $('#haneImage');
    var file    = $(this)[0].files[0];
    var reader  = new FileReader();

    reader.addEventListener("load", function () {
      preview.attr('src', reader.result);
    }, false);

    if (file) {
      var ext = file.name.substr( (file.name.lastIndexOf('.') +1) );
      var allowedExts = ['jpg','jpeg','png','gif','PNG','JPG','JPEG','GIF'];
      var size  =  $('#imgHane')[0].files[0].size;

      if(allowedExts.indexOf(ext) === -1) {
        alert_msg(
          $('#modalHANE .alert_group'),
          'danger',
          'Invalid File!',
          `Please use image files only. (Allowed file type: ${allowedExts.join(', ')})`
        );
        return;
      } else if (size * 1e-6 > max_filesize) { // 5MB
        alert_msg(
          $('#modalHANE .alert_group'),
          'danger',
          'Invalid File Size!',
          `Files must not exceed ${max_filesize}MB.`
        );
        return;
      }

      if ($('#modalHANE').hasClass('edit-form')) {
        $('#btnResetImage').removeClass('hidden').show();
      }

      clear_alert();
      reader.readAsDataURL(file);
    }
  });

  $('#modalHANE :input').on('keyup change paste', function() {
		$(this).parent('.form-group').removeClass('error')
			.find('.note').html('');
	});

  $('#btnResetImage').on('click', function() {
    var imagepath = baseurl + image_path;
    var imagefile = $('#hotel_image').val();
    $('#haneImage').attr('src', `${imagepath}hane/${imagefile}`);
    $(this).addClass('hidden').hide();
    clear_alert();
  })

  $('#btnCancel').on('click', function() {
    var imagepath = baseurl + image_path;
    $('#modalHANE :input.field').each(function() {
      var thisField = $(this);
      thisField.val('');
  		thisField.parent('.form-group').removeClass('error')
  			.find('.note').html('');

      if (thisField.attr('id') === 'hotel_image') {
        thisField.val('default-hane.jpg');
        $('#haneImage').attr('src', `${imagepath}hane/default-hane.jpg`);
    		thisField.parent('.form-group').removeClass('error')
    			.find('.note').html('Click on image to add/update image.');
      } else if (thisField.attr('type') === 'hidden') {
        thisField.val(0);
        thisField.parents('.form-group').find('[type="checkbox"]')
          .bootstrapSwitch('state', false);
      }
  	});

    $('#modalHANE .alert_group').addClass('hidden').html('');
  });

  $('#btnSave, #btnUpdate').on('click', function() {
    var thisButton = $(this);
    var file = $('#imgHane');
    var error = 0;
    var method = ($('#modalHANE').hasClass('edit-form')) ? 'update_hane' : 'add_hane';
    var thisButton = $(this);

    thisButton.prop('disabled', true).attr('disabled', 'disabled')
      .html(`<i class="fa fa-spinner fa-spin"></i>&nbsp;${$(this).data('processing')}`);

    $('#modalHANE :input.field').each(function() {
      var thisField = $(this);
      if (thisField.attr('data-required') && !thisField.val().length) {
        thisField.parent('.form-group').addClass('error')
          .find('.note').html(thisField.data('required'));
        error++;
      }
    });

    if (file[0].files.length) {
      var imgname = file.val();
      var size = file[0].files[0].size;
      var ext = imgname.substr( (imgname.lastIndexOf('.') +1) );
      var allowedExts = ['jpg','jpeg','png','gif','PNG','JPG','JPEG','GIF']

      if(allowedExts.indexOf(ext) === -1) {
        file.parent('.form-group').addClass('error')
        .find('.note').html(`Please use image files only. (Allowed file type: ${allowedExts.join(', ')})`);
        error++;
      } else if (size * 1e-6 > max_filesize) {
        file.parent('.form-group').addClass('error')
        .find('.note').html('File size must not exceed 5MB.');
        error++;
      } else {
        file.parent('.form-group').removeClass('error')
        .find('.note').html('Click on image to add/update image.');
      }
    } else {
      if (method === 'add_gallery_item') {
        file.parent('.form-group').addClass('error')
        .find('.note').html('Please select a photo.');
        error++;
      }
    }

    if (!error) {
      var data = new FormData();
      var params = $('#modalHANE :input.field').serializeArray();

      if (file[0].files.length) {
        data.append('file', file[0].files[0]);
      }
      data.append('params', JSON.stringify(params));

      $.ajax({
        url: `${baseurl}hf_management/${method}`,
        type: 'post',
        data: data,
        enctype: 'multipart/form-data',
        processData: false,  // tell jQuery not to process the data
        contentType: false,   // tell jQuery not to set contentType
        cache: false,
        success: function (data) {
          alert_msg(
            $('#modalHANE .alert_group'),
            (data.response) ? 'success' : 'danger',
            (data.response) ? 'Success!' : 'Failed!',
            data.message
          );
          if (data.response) {
            var page_num = parseInt($('.page_num').text());
            var searchKey = $.trim($('#search-field').val());
            var imagepath = baseurl + image_path;

            if (typeof(data.data) != 'undefined') {
              $('#modalHANE #hotel_image').val(data.data.hotel_image);
              $('#modalHANE #url').val(`${imagepath}hane/${data.data.hotel_image}`);
            }

            $('#modalHANE').animate({
              scrollTop: 0
            });

            if (thisButton.attr('id') === 'btnUpdate') {
              load_hane(searchKey, ((page_num-1) * items_per_page), items_per_page, 0);
            } else {
              load_hane('', 0, items_per_page, 0);
            }

            setTimeout(function() {
              $('#btnCancel').trigger('click');
            }, 3000);
          }
          thisButton.prop('disabled', false).removeAttr('disabled')
            .html(thisButton.data('caption'));
        },
        error: function (data) {
          alert_msg(
            $('#modalHANE .alert_group'),
            'danger',
            'Failed!',
            'Oops! Something went wrong. Please contact your administrator.'
          );
          thisButton.prop('disabled', false).removeAttr('disabled')
            .html(thisButton.data('caption'));
        }
      });
    } else {
      thisButton.prop('disabled', false).removeAttr('disabled')
        .html(thisButton.data('caption'));
    }
  });
});
