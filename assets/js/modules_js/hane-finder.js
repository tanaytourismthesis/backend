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
              'src': `${baseurl}${image_path}hane/${value['hotel_image']}`,
            }).css({
              'width': '100%'
            })
          )
        ).append(
          $('<td></td>')
            .append(value['hotel_name'])
            .append('<br />')
            .append(
              $('<button class="btn btn-info btn-xs">see map</button>')
                .on('click', function() {
                  var modal = $('#modalViewMap');
                  modal.find('.modal-title').html(value['hotel_name'])
                  var map = setMap(document.getElementById('viewMap'), parseFloat(value['longhitude']), parseFloat(value['latitude']));
                  google.maps.event.trigger(map, 'resize');
                  modal.modal({backdrop: 'static'});
                })
            )
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
              get_hane_rooms('', 0, page_limit, 0, value['hotel_id']);
              $('#modalHaneRooms').find('.hane-name').html(value['hotel_name']);
              $('#modalHaneRooms').find('.room-search-button').attr('data-hane', value['hotel_id']);
              $('#modalHaneRooms').find('.room-reload-list').attr('data-hane', value['hotel_id']);
              $('#frmHaneRoom #hotel_hotel_id').val(value['hotel_id']);
              $('#modalHaneRooms').modal({backdrop: 'static'});
            }).html('<i class="fas fa-eye"></i>')
          ).append(
            $('<span>&nbsp;</span>')
          ).append(
            $('<button class="btn btn-xs btn-default"></button>').on('click', function() {
              var modal = $('#modalHaneMetrics');
              modal.modal({backdrop: 'static'});
            }).html('<i class="fas fa-tachometer-alt"></i>')
          )
        );
        tbody.append(tr);
      });

      // Pagination
      var total_records = data.data.total_records;
      var total_pages = parseInt(total_records / items_per_page);
      total_pages = (total_records % items_per_page > 0) ? ++total_pages : total_pages;
      var page_num = parseInt($('.tab-content#hanes .page_num').text());

      setNavigation('.tab-content#hanes', total_records, total_pages, page_num, 'load_hane');

      $('.tab-content#hanes .navigator-fields').removeClass('hidden').show();
      tbody.fadeIn('slow');
    } else {
      tbody.show('slow');
      tbody.html('<tr><td colspan="100%" align="center">No results found...</td></tr>');
      $('.tab-content#hanes .navigator-fields').addClass('hidden').hide();
    }
  }).fail(function(){
    tbody.show('slow').html('');
    tbody.html('<tr><td colspan="100%" align="center">Oops! something went wrong. Please contact your administrator.</td></tr>');
    $('.navigator-fields').addClass('hidden').hide();
  });
};

var get_hane_rooms = (searchkey, start, limit, id, hane) => {
  var roomList = $('#modalHaneRooms .room-list');
  setImageListPlacehoder(roomList, baseurl + image_path, 'room', 'hane/default-hane.jpg', 1);
  $.post(
    `${baseurl}hf_management/get_hane_rooms`,
    {
      searchkey: searchkey,
      start: start,
      limit: limit,
      id: id,
      hane: hane
    }
  ).done(function(data) {
    var imagepath = baseurl + image_path;
    if (data.response) {
      roomList.html('');
      var row = '';
      $.each(data.data.records, function(index, value) {
        var details = JSON.stringify(value);
        var idx = index + 1;
        if (idx % 3 === 1) {
          row = $('<div class="row room-row"></div>');
        }
        row.append(
          $(`<div class="col-xs-4 room-item ripple text-center">
            <img class="item-image" src="${imagepath}hane/${value['room_image']}" />
            <div class="item-name">${value['room_name']}</div>
          </div>`).on('click', function() {
            var haneRoomForm = $('#frmHaneRoom');
            haneRoomForm.addClass('edit-form').removeClass('add-form');
            haneRoomForm.find('.room-form-title').html('Edit');
            $('#btnSaveInfo').addClass('hidden').hide();
            $('#btnResetInfo').addClass('hidden').hide();
            $('#btnUpdateInfo').removeClass('hidden').show();
            $('#btnCancelInfo').removeClass('hidden').show();
            $.each(value, function(i, v) {
              haneRoomForm.find(`.field[name="${i}"]`).val(v);
              if (i === 'room_image') {
                $('#roomImage').attr('src', `${imagepath}hane/${v}`)
                haneRoomForm.find('#url').val(`${imagepath}hane/${v}`);
              }
              if (i === 'inclusive_features') {
                tinymce.activeEditor.setContent(v,{format: 'raw'});
              }
            });

            $('.hane-rooms').removeClass('col-md-12').addClass('col-md-7');
            $('.room-details').removeClass('hidden-xs hidden-sm').fadeIn('slow');
            $('#btnResetImageInfo').addClass('hidden').hide();

            // scroll to form
            var formOffset = $('.room-details').offset();
            $('#modalHaneRooms').scrollTop(0);
            $('#modalHaneRooms').animate({
              scrollTop: formOffset.top * 0.9
            });
          })
        );
        if (idx % 3 === 0) {
          roomList.append(row);
          row = '';
        }
        if (data.data.records.length < 3 && data.data.records.length === idx) {
          roomList.append(row);
        }
      });

      // Pagination
      var total_records = data.data.total_records;
      var total_pages = parseInt(total_records / limit);
      total_pages = (total_records % limit > 0) ? ++total_pages : total_pages;
      var page_num = parseInt($('.current-page').text());

      var pages = $('<span class="pages"></span>');
      var i = 1;
      for (i; i <= total_pages; i++) {
        pageButtonDisabled = (i === page_num);
        var pageButtonOptions = {
          'type': 'button',
          'class': `btn btn-default btn-info${pageButtonDisabled ? ' disabled' : ''}`,
          'data-page': i
        };
        if (pageButtonDisabled) {
          pageButtonOptions['disabled'] = 'disabled';
        }
        pages
          .append(
            $(
              '<button></button>',
              pageButtonOptions
            ).on('click', function() {
              var currPage = parseInt($('.current-page').text());
              var pageNum = parseInt($(this).attr('data-page'));
              $('.current-page').html(pageNum);
              if (pageNum === currPage) {
                $(this).prop('disabled', true).attr('disabled', 'disabled');
              }
              get_hane_rooms(searchKey, ((pageNum-1) * limit), limit, 0, hane);
            }).html(i)
          )
          .append('<span>&nbsp;</span>')
      }

      var buttonHidden = (total_records <= limit) ? 'hidden' : '';
      var prevButtonOptions = {
        'type': 'button',
        'class': `btn btn-default ${buttonHidden}`
      };
      var nextButtonOptions = {
        'type': 'button',
        'class': `btn btn-default ${buttonHidden}`
      };
      var prevButtonDisabled = (page_num === 1) ? true : false;
      var nextButtonDisabled = (page_num === total_pages) ? true : false;
      var searchKey = $('#room-search-field').val();

      if (prevButtonDisabled) {
        prevButtonOptions['disabled'] = 'disabled';
      }

      if (nextButtonDisabled) {
        nextButtonOptions['disabled'] = 'disabled';
      }

      $('.total-pages').html(total_pages);
      $('.total-records').html(total_records);

      $('.room-navigator-buttons').html('');
      $('.room-navigator-buttons')
        .append(
          $(
            '<button></button>',
            prevButtonOptions
          ).on('click', function(){
            page_num--;
            $('.current-page').html(page_num);
            if (page_num === 1) {
              $(this).prop('disabled', true).attr('disabled', 'disabled');
            }
            get_hane_rooms(searchKey, ((page_num-1) * limit), limit, 0, hane);
          }).append(
            $(
              '<i></i>', {
                'class': 'fas fa-angle-left'
            })
          )
        )
        .append('<span>&nbsp;</span>')
        .append(pages)
        .append(
          $(
            '<button></button>',
             nextButtonOptions
          ).on('click', function(){
            page_num++;
            $('.current-page').html(page_num);
            if (page_num === total_pages) {
              $(this).prop('disabled', true).attr('disabled', 'disabled');
            }
            get_hane_rooms(searchKey, ((page_num-1) * limit), limit, 0, hane);
          }).append(
            $(
              '<i></i>', {
                'class': 'fas fa-angle-right'
            })
          )
      );

    } else {
      roomList.html(`
        <div class="note text-center">
          <img style="width: 30%;" src="${imagepath}/error404page-icon.png" /><br>
          No items found.
        </div>
      `);
      $('.room-navigator').addClass('hidden').hide().find('.room-navigator-buttons').html('');
    }
  });
};

var load_metrics = (searchkey, start, limit, id) => {
  var tbody = $('#tblMetrics tbody');
  setSearchTablePlaceholder(tbody, items_per_page);

  $.post(
    `${baseurl}hf_management/load_metrics`,
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
          $('<td></td>').html(value['metric_name'])
        ).append(
          $('<td></td>').html(value['variable1'])
        ).append(
          $('<td></td>').html(value['variable2'])
        ).append(
          $('<td></td>').html(value['formula'])
        ).append(
          $('<td></td>').append(
            $('<button class="btn btn-xs btn-default"></button>').on('click', function() {
              var thisButton = $(this);
              thisButton.prop('disabled', true).attr('disabled', 'disabled')
                .html(`<i class="fa fa-spinner fa-spin"></i>`);

              $.post(
                `${baseurl}hf_management/load_metrics`,
                {
                  searchkey: '',
                  start: 0,
                  limit: 1,
                  id: value['metric_id']
                }
              ).done(function(data) {
                if (data.response) {
                  var modal = $('#modalMetric');
                  $.each(data.data.records, function(index, value) {
                    if (index === 'metric_id') {
                      modal.find('#btnUpdate').attr('data-id', value);
                    }
                    var thisField = modal.find(`:input.field[name="${index}"]`);
                    thisField.val(value);
                  });

                  modal.find('.modal-title').html('Add');
                  modal.find('#btnUpdate').removeClass('hidden').show();
                  modal.find('#btnSave').addClass('hidden').hide();
                  modal.modal({backdrop: 'static'});
                }
                thisButton.prop('disabled', false).removeAttr('disabled').html('<i class="fas fa-edit"></i>');
              });
            }).html('<i class="fas fa-edit"></i>')
          )
        );
        tbody.append(tr);
      });

      // Pagination
      var total_records = data.data.total_records;
      var total_pages = parseInt(total_records / items_per_page);
      total_pages = (total_records % items_per_page > 0) ? ++total_pages : total_pages;
      var page_num = parseInt($('.tab-content#metrics .page_num').text());

      setNavigation('.tab-content#metrics', total_records, total_pages, page_num, 'load_metrics');

      $('.tab-content#metrics .navigator-fields').removeClass('hidden').show();
      tbody.fadeIn('slow');
    } else {
      tbody.show('slow');
      tbody.html('<tr><td colspan="100%" align="center">No results found...</td></tr>');
      $('.tab-content#metrics .navigator-fields').addClass('hidden').hide();
    }
  }).fail(function(){
    tbody.show('slow').html('');
    tbody.html('<tr><td colspan="100%" align="center">Oops! something went wrong. Please contact your administrator.</td></tr>');
    $('.tab-content#metrics .navigator-fields').addClass('hidden').hide();
  });
};

function clearAllContentEditor(){
  for(i=0; i<tinymce.editors.length; i++){
     tinymce.editors[i].setContent("");
     $("[name='" + tinymce.editors[i].targetElm.name + "']").val("");
  }
}

function CheckTinymce(el){
  var elEditor = $.trim(tinyMCE.get(el).getContent({format: 'text'}));
  var element = $(`#${el}`);
  element.click();
  if(!elEditor.length){
    element.parent('.form-group').addClass('error')
      .find('.note').html(element.data('required'));
    return false;
  }
  element.parent('.form-group').removeClass('error')
    .find('.note').html('');
  return true;
}

$(function(){
  load_hane('', 0, items_per_page, 0);
  load_metrics('', 0, items_per_page, 0);

  $('.main-tab-items.tab-items a').on('click', function(e) {
    e.preventDefault();
    var thisTab = $(this);
    var tabContent = thisTab.attr('href');

    thisTab.closest('li').addClass('active').siblings('li').removeClass('active');
    $(`.main-tab-content.tab-content${tabContent}`).fadeIn('slow')
      .siblings('.main-tab-content.tab-content').slideUp(1);
  });

  $('.metric-tab-items.tab-items a').on('click', function(e) {
    e.preventDefault();
    var thisTab = $(this);
    var tabContent = thisTab.attr('href');

    thisTab.closest('li').addClass('active').siblings('li').removeClass('active');
    $(`.metric-tab-content.tab-content${tabContent}`).fadeIn('slow')
      .siblings('.metric-tab-content.tab-content').slideUp(1);
  });

  $('.search-button').on('click', function(e) {
    var searchKey = $.trim($('#search-field').val());

    if (!searchKey.length) {
      $('#search-field').parent('.input-group').addClass('error');
      $(this).popover('toggle');
    } else {
      $(this).popover('hide');
      $('.tab-content#hanes .page_num').html('1');
      load_hane(searchKey, 0, items_per_page, 0);
    }
  });

  $('.reload-list').on('click', function() {
    $('#search-field').val('');
    $('.tab-content#hanes .page_num').html('1');
    load_hane('', 0, items_per_page, 0);
  });

  $('[type="checkbox"]').bootstrapSwitch({
    'onColor': 'success'
  }).on('switchChange.bootstrapSwitch', function(event, state) {
    $(this).parents('.form-group').find('[type=hidden]').val((state) ? 1 : 0);
  });

  $('#hanes #btnAdd').on('click', function(){
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

  $('#modalHANE #btnResetImage').on('click', function() {
    var imagepath = baseurl + image_path;
    var imagefile = $('#hotel_image').val();
    $('#haneImage').attr('src', `${imagepath}hane/${imagefile}`);
    $(this).addClass('hidden').hide();
    clear_alert();
  })

  $('#modalHANE #btnCancel').on('click', function() {
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

  $('#modalHANE #btnSave, #modalHANE #btnUpdate').on('click', function() {
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

      if (thisField.attr('name') === 'contact' && thisField.val().length) {
        if (!validateContactNumber(thisField.val())) {
          thisField.parent('.form-group').addClass('error')
          .find('.note').html(thisField.data('required'));
          error++;
        }
      }

      if (thisField.attr('name') === 'email') {
        if (!validateEmail(thisField.val())) {
          thisField.parent('.form-group').addClass('error')
  					.find('.note').html(thisField.data('required'));
          error++;
        }
      }

      if (thisField.attr('name') === 'longhitude') {
        if (!validateLonghitude(thisField.val())) {
          thisField.parent('.form-group').addClass('error')
  					.find('.note').html(thisField.data('required'));
          error++;
        }
      }

      if (thisField.attr('name') === 'latitude') {
        if (!validateLatitude(thisField.val())) {
          thisField.parent('.form-group').addClass('error')
  					.find('.note').html(thisField.data('required'));
          error++;
        }
      }

      if (thisField.attr('name') === 'url' && thisField.val().length) {
        if (!validateURL(thisField.val())) {
          thisField.parent('.form-group').addClass('error')
  					.find('.note').html('Please provide valid website address. (e.g. http://www.hotel.com.ph)');
          error++;
        }
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
      if (method === 'add_hane') {
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
            var page_num = parseInt($('.tab-content#hanes .page_num').text());
            var searchKey = $.trim($('#search-field').val());
            var imagepath = baseurl + image_path;

            if (typeof(data.data) != 'undefined') {
              $('#modalHANE #hotel_image').val(data.data.hotel_image);
            }

            $('#modalHANE').animate({
              scrollTop: 0
            });

            if (thisButton.attr('id') === 'btnUpdate') {
              load_hane(searchKey, ((page_num-1) * items_per_page), items_per_page, 0);
            } else {
              load_hane('', 0, items_per_page, 0);
              setTimeout(function() {
                $('#modalHANE #btnCancel').trigger('click');
              }, 3000);
            }
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

  $('.room-search-button').on('click', function(e){
    var searchKey = $.trim($('#room-search-field').val());
    if (!searchKey.length) {
      $('#room-search-field').parent('.input-group').addClass('error');
      $(this).popover('toggle');
    } else {
      $(this).popover('hide');
      $('.current-page').html('1');
      get_hane_rooms(searchKey, 0, page_limit, 0, $(this).data('hane'));
    }
  });

  $('.room-reload-list').on('click', function() {
    $('#room-search-field').val('');
    $('.current-page').html('1');
    get_hane_rooms('', 0, page_limit, 0, $(this).data('hane'));
  });

  $('.room-add-item').on('click', function() {
    $('.hane-rooms').removeClass('col-md-12').addClass('col-md-7');
    $('.room-details').removeClass('hidden-xs hidden-sm').fadeIn('slow');
    $('#frmHaneRoom').addClass('add-form').removeClass('edit-form');
    $('#btnResetImageInfo').addClass('hidden').hide();

    // scroll to form
    var modalOffset = $('#modalHaneRooms').offset();
    $('#modalHaneRooms').animate({
      scrollTop: modalOffset.top
    });
  });

  $('#closeRoomDetails').on('click', function() {
    $('.room-details').addClass('hidden-xs hidden-sm').fadeOut(function(){
      $('.hane-rooms').removeClass('col-md-7').addClass('col-md-12');
    });
    $('#btnCancelInfo, #btnResetInfo').trigger('click');
  });

  $('#roomImage').on('click', function() {
    $('#imgRoom').trigger('click');
  });

  $('#imgRoom').on('change', function() {
    var preview = $('#roomImage');
    var file    = $(this)[0].files[0];
    var reader  = new FileReader();

    reader.addEventListener("load", function () {
      preview.attr('src', reader.result);
    }, false);

    if (file) {
      var ext = file.name.substr( (file.name.lastIndexOf('.') +1) );
      var allowedExts = ['jpg','jpeg','png','gif','PNG','JPG','JPEG','GIF'];
      var size  =  $('#imgRoom')[0].files[0].size;

      if(allowedExts.indexOf(ext) === -1) {
        alert_msg(
          $('#frmHaneRoom .alert_group'),
          'danger',
          'Invalid File!',
          `Please use image files only. (Allowed file type: ${allowedExts.join(', ')})`
        );
        return;
      } else if (size * 1e-6 > max_filesize) { // 5MB
        alert_msg(
          $('#frmHaneRoom .alert_group'),
          'danger',
          'Invalid File Size!',
          `Files must not exceed ${max_filesize}MB.`
        );
        return;
      }

      if ($('#frmHaneRoom').hasClass('edit-form')) {
        $('#btnResetImageInfo').removeClass('hidden').show();
      }

      clear_alert();
      reader.readAsDataURL(file);
    }
  });

  $('#frmHaneRoom :input').on('keyup change paste', function() {
		$(this).parent('.form-group').removeClass('error')
			.find('.note').html('');
	});

  $('#btnResetInfo').on('click', function() {
    var imagepath = baseurl + image_path;
    $('#roomImage').attr('src', `${imagepath}hane/default-hane.jpg`);
    $('#room_image').val('default-hane.jpg');
    $('#frmHaneRoom input.field').parents('.form-group').removeClass('error')
      .find('.note').html('')
    clear_alert();

    // scroll to form
    var modalOffset = $('#modalHaneRooms').offset();
    $('#modalHaneRooms').animate({
      scrollTop: modalOffset.top
    });
  });

  $('#btnCancelInfo').on('click', function() {
    var thisButton = $(this);
    $('#btnSaveInfo').removeClass('hidden').show();
    $('#btnResetInfo').removeClass('hidden').show();
    $('#btnUpdateInfo').addClass('hidden').hide();
    thisButton.addClass('hidden').hide();
    $('#btnResetInfo').trigger('click');
    $('#frmHaneRoom #room_id').val('0');
    $('#frmHaneRoom #room_image').val('default-hane.jpg');
    $('#frmHaneRoom').find('.room-form-title').html('Add');
    clearAllContentEditor();
    clear_alert();
  });

  $('#btnResetImageInfo').on('click', function() {
    var imagepath = baseurl + image_path;
    var imagefile = $('#room_image').val();
    $('#roomImage').attr('src', `${imagepath}hane/${imagefile}`);
    $(this).addClass('hidden').hide();
    clear_alert();
  });

  $('#modalHaneRooms .close').on('click', function() {
    $('#btnResetInfo').trigger('click');
  });

  $('#btnUpdateInfo, #btnSaveInfo').on('click', function() {
    var room = $('#frmHaneRoom');
    var fields = room.find('input.field');
    var file = $('#imgRoom');
    var error = 0;
    var method = ($('#frmHaneRoom').hasClass('edit-form')) ? 'update_hane_room' : 'add_hane_room';
    var thisButton = $(this);

    thisButton.prop('disabled', true).attr('disabled', 'disabled')
      .html(`<i class="fa fa-spinner fa-spin"></i>&nbsp;${$(this).data('processing')}`);

    fields.each(function() {
      var thisField = $(this);
      if (thisField.attr('data-required') && !thisField.val().length) {
        thisField.parent('.form-group').addClass('error')
					.find('.note').html(thisField.data('required'));
				error++;
      }
    });
    error = (!CheckTinymce('inclusive_features')) ? error++ : error;

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
      if (method === 'add_hane_room') {
        file.parent('.form-group').addClass('error')
          .find('.note').html('Please select a photo.');
        error++;
      }
    }

    if (!error) {
      var data = new FormData();
      var params = fields.serializeArray();
      var thisButton = $(this);
      var inclusive_features = $.trim(tinyMCE.activeEditor.getContent({format: 'raw'}));

      params.push({'name': 'inclusive_features', 'value': inclusive_features});
      params = JSON.stringify(params);

      thisButton.prop('disabled', true).attr('disabled', 'disabled')
        .html(`<i class="fa fa-spinner fa-spin"></i>&nbsp;${$(this).data('processing')}`);

      if (file[0].files.length) {
        data.append('file', file[0].files[0]);
      }
      data.append('params', params);

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
            $('#frmHaneRoom .alert_group'),
            (data.response) ? 'success' : 'danger',
            (data.response) ? 'Success!' : 'Failed!',
            (data.response) ? 'Successfully updated H.A.N.E room!' : data.message
          );
          if (data.response) {
            var currPage = parseInt($('.current-page').text());
            var searchKey = $('#room-search-field').val();
            var hane = $('#hotel_hotel_id').val();
            var imagepath = baseurl + image_path;
            get_hane_rooms(searchKey, ((currPage-1) * page_limit), page_limit, 0, hane);
            if (typeof(data.data) != 'undefined') {
              $('#frmHaneRoom').find('#room_image').val(data.data.room_image);
            }
          }
          // scroll to form
          var formOffset = $('.room-details').offset();
          var modalOffset = $('#modalHaneRooms').offset();
          $('#modalHaneRooms').animate({
            scrollTop: modalOffset.top - formOffset.top
          });

          $('#btnResetImageInfo').addClass('hidden').hide();

          if (method === 'add_hane_room' && data.response) {
            setTimeout(function() {
              $('#btnResetInfo').trigger('click');
            }, 3000);
          }

          thisButton.prop('disabled', false).removeAttr('disabled')
            .html(thisButton.data('caption'));
        },
        error: function (data) {
          alert_msg(
            $('#frmHaneRoom .alert_group'),
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

  $('.metrics-search-button').on('click', function(e) {
    var searchKey = $.trim($('#metrics-search-field').val());

    if (!searchKey.length) {
      $('#metrics-search-field').parent('.input-group').addClass('error');
      $(this).popover('toggle');
    } else {
      $(this).popover('hide');
      $('.tab-content#metrics .page_num').html('1');
      load_metrics(searchKey, 0, items_per_page, 0);
    }
  });

  $('.metrics-reload-list').on('click', function() {
    $('#metrics-search-field').val('');
    $('.tab-content#metrics .page_num').html('1');
    load_metrics('', 0, items_per_page, 0);
  });

  $('#metrics #btnAddMetric').on('click', function(){
    $('#modalMetric .modal-title').html('Add');
    $('#modalMetric #btnSave').removeClass('hidden').show();
    $('#modalMetric #btnUpdate').addClass('hidden').hide();
    $('#modalMetric').addClass('add-form').removeClass('edit-form');
  });

  $('#modalMetric #btnCancel').on('click', function() {
    $('#modalMetric :input.field').each(function() {
      var thisField = $(this);
      thisField.val('');
  		thisField.parent('.form-group').removeClass('error')
  			.find('.note').html('');
  	});

    $('#modalMetric .alert_group').addClass('hidden').html('');
  });

  tinymce.init({
    selector: '#inclusive_features',
    hidden_input: false,
    height: 200,
    menubar: false,
    plugins: [
        "advlist autolink lists link image charmap print preview anchor",
        "searchreplace visualblocks code fullscreen",
        "insertdatetime media table contextmenu paste imagetools wordcount"
    ],
    toolbar: `insertfile undo redo | bold italic | bullist numlist`,
    content_css: [
      `${baseurl}assets/css/editor.css?tm=${today}`
    ],
    init_instance_callback: function (editor) {
      editor.on('keyup change paste', function (e) {
        $('#frmHaneRoom #inclusive_features').parent('.form-group')
          .removeClass('error').find('.note').html('');
      });
    }
  });

  tinymce.init({
    selector: '#amenities',
    hidden_input: false,
    height: 200,
    menubar: false,
    plugins: [
        "advlist autolink lists link image charmap print preview anchor",
        "searchreplace visualblocks code fullscreen",
        "insertdatetime media table contextmenu paste imagetools wordcount"
    ],
    toolbar: `insertfile undo redo | bold italic | bullist numlist`,
    content_css: [
      `${baseurl}assets/css/editor.css?tm=${today}`
    ],
    init_instance_callback: function (editor) {
      editor.on('keyup change paste', function (e) {
        $('#modalHANE #amenities').parent('.form-group')
          .removeClass('error').find('.note').html('');
      });
    }
  });
});
