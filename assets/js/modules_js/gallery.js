var load_gallerylist = (searchkey, start, limit, id, slug) => {
  var tbody = $('#tblGallery tbody');

  setSearchTablePlaceholder(tbody, items_per_page);

  $.post(
    `${baseurl}gallery/load_gallery`,
    {
      searchkey: searchkey,
      start: start,
      limit: limit,
      id: id,
      slug: slug
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
          $('<td></td>').html(value['gallery_name'])
        ).append(
          $('<td></td>').html(value['gallery_type'])
        ).append(
          $('<td class="hidden-xs"></td>').html(value['gallery_status'])
        ).append(
          $('<td></td>').html(value['page_name'])
        );

        var td = $('<td></td>').append(
            $('<button class="btn btn-xs btn-default"></button>').on('click', function() {
              var page_limit = (items_per_page % 3 !== 0) ? (items_per_page + (3 - (items_per_page % 3))) : items_per_page;
              get_gallery_items('', 0, page_limit, 0, value['gallery_id']);
              $('#modalAlbum').find('.gallery-name').html(value['gallery_name']);
              $('#modalAlbum').find('.album-search-button').attr('data-gallery', value['gallery_id']);
              $('#modalAlbum').modal('show');
            }).html('<i class="fas fa-eye"></i>')
          );

        if (parseInt(value['hasGallery']) && !parseInt(value['carouselOnly'])) {
          td.prepend(
              $('<button class="btn btn-xs btn-default"></button>').on('click', function() {
                var thisButton = $(this);
                thisButton.prop('disabled', true).attr('disabled', 'disabled')
                  .html(`<i class="fa fa-spinner fa-spin"></i>`);
                $('#modalGallery .modal-heading > h2').html('Edit Gallery');
                $('#btnUpdate').removeClass('hidden').show();
                $('#btnSave').addClass('hidden').hide();

                $.post(
                  `${baseurl}gallery/load_gallery`,
                  {
                    searchkey: '',
                    start: 0,
                    limit: 1,
                    id: value['gallery_id']
                  }
                ).done(function(data) {
                  if(data.response) {
                    $.each(data.data.records, function(index, value) {
                      if (index === 'gallery_id') {
                        $('#btnUpdate').attr('data-id', value);
                      }

                      var thisField = $(`#frmGallery :input.field[name="${index}"]`);
                      thisField.val(value);

                      if (thisField.attr('type') === 'hidden') {
                        thisField.parents('.form-group').find('[type="checkbox"]')
                          .bootstrapSwitch('state', parseInt(value));
                      }

                      if (thisField.is('select')) {
                        thisField.find('option[value="'+value+'"]').prop('selected',true);
                      }
                    });

                    $('#modalGallery').modal({backdrop: 'static'});
                  }
                  thisButton.prop('disabled', false).removeAttr('disabled').html('<i class="fas fa-edit"></i>');
                });
              }).html('<i class="fas fa-edit"></i>')
            ).append(
              $('<span>&nbsp;</span>')
            );
        }
        tr.append(td);
        tbody.append(tr);
      });

      // Pagination
      var total_records = data.data.total_records;
      var total_pages = parseInt(total_records / items_per_page);
      total_pages = (total_records % items_per_page > 0) ? ++total_pages : total_pages;
      var page_num = parseInt($('.page_num').text());

      setNavigation(total_records, total_pages, page_num, 'load_gallerylist', slug);

      $('.navigator-fields').removeClass('hidden').show();
      tbody.fadeIn('slow');
    } else {
      tbody.show('slow');
      tbody.html('<tr><td colspan="100%" align="center">No results found...</td></tr>');
      $('.navigator-fields').addClass('hidden').hide();
    }
  });
};

var get_gallery_items = (searchkey, start, limit, id, gallery) => {
  var album = $('#modalAlbum .album-list');
  setAlbumPlacehoder(album, baseurl+image_path);
  $.post(
    `${baseurl}gallery/get_gallery_items`,
    {
      searchkey: searchkey,
      start: start,
      limit: limit,
      id: id,
      gallery: gallery
    }
  ).done(function(data) {
    var imagepath = baseurl + image_path;
    if (data.response) {
      album.html('');
      var row = '';
      $.each(data.data.records, function(index, value) {
        var details = JSON.stringify(value);
        var idx = index + 1;
        if (idx % 3 === 1) {
          row = $('<div class="row album-row"></div>');
        }
        row.append(
          $(`<div class="col-xs-4 album-item ripple text-center">
            <img class="item-image" src="${imagepath}gallery/${value['image_filename']}" />
          </div>`).on('click', function() {
            var albumImageForm = $('#frmAlbumImage');
            albumImageForm.find('.album-form-title').html('Edit Image');
            $('#btnSaveInfo').addClass('hidden').hide();
            $('#btnResetInfo').addClass('hidden').hide();
            $('#btnUpdateInfo').removeClass('hidden').show();
            $('#btnCancelInfo').removeClass('hidden').show();
            $('#btnCancelInfo').attr('data-form-type', 'edit');
            $.each(value, function(i, v) {
              albumImageForm.find(`.field[name="${i}"]`).val(v);
              if (i === 'image_filename') {
                $('#albumImage').attr('src', `${imagepath}gallery/${v}`)
              }
              if (i === 'caption') {
                tinymce.activeEditor.setContent(v,{format: 'raw'});
              }
            });
            $('.album-add-item').trigger('click');
          })
        );
        if (idx % 3 === 0) {
          album.append(row);
          row = '';
        }
        if (data.data.records.length < 3 && data.data.records.length === idx) {
          album.append(row);
        }
      });

      // Pagination
      var total_records = data.data.total_records;
      var total_pages = parseInt(total_records / limit);
      total_pages = (total_records % limit > 0) ? ++total_pages : total_pages;
      var page_num = parseInt($('.current-page').text());

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
      var searchKey = $('#album-search-field').val();

      if (prevButtonDisabled) {
        prevButtonOptions['disabled'] = 'disabled';
      }

      if (nextButtonDisabled) {
        nextButtonOptions['disabled'] = 'disabled';
      }

      $('.total-pages').html(total_pages);
      $('.total-records').html(total_records);

      $('.album-navigator-buttons').html('');
      $('.album-navigator-buttons')
        .append(
          $(
            '<button></button',
            prevButtonOptions
          ).on('click', function(){
            page_num--;
            $('.current-page').html(page_num);
            if (page_num === 1) {
              $(this).prop('disabled', true).attr('disabled', 'disabled');
            }
            get_gallery_items(searchKey, ((page_num-1) * limit), limit, 0, gallery);
          }).append(
            $(
              '<i></i>', {
                'class': 'fas fa-angle-left'
            })
          )
        )
        .append('<span>&nbsp;</span>')
        .append(
          $(
            '<button></button',
             nextButtonOptions
          ).on('click', function(){
            page_num++;
            $('.current-page').html(page_num);
            if (page_num === total_pages) {
              $(this).prop('disabled', true).attr('disabled', 'disabled');
            }
            get_gallery_items(searchKey, ((page_num-1) * limit), limit, 0, gallery);
          }).append(
            $(
              '<i></i>', {
                'class': 'fas fa-angle-right'
            })
          )
      );

    } else {
      album.html(`
        <div class="note text-center">
          <img style="width: 30%;" src="${imagepath}/error404page-icon.png" /><br>
          No items found.
        </div>
      `);
      $('.album-navigator').addClass('hidden').hide().find('.album-navigator-buttons').html('');
    }
  });
};

$(function() {
  var slug = $('.page_slug').attr('alt');
  $('.page_num').html('1');
	load_gallerylist('', 0, items_per_page, 0, slug);

  $('.search-button').on('click', function(e) {
    var searchKey = $.trim($('#search-field').val());

    if (!searchKey.length) {
      $('#search-field').parent('.input-group').addClass('error');
      $(this).popover('toggle');
    } else {
      $(this).popover('hide');
      $('.page_num').html('1');
      load_gallerylist(searchKey, 0, items_per_page, 0, slug);
    }
  });

  $('.reload-list').on('click', function() {
    $('#search-field').val('');
    $('.page_num').html('1');
    load_gallerylist('', 0, items_per_page, 0, slug);
  });

  $('#btnAdd').on('click', function() {
    $('#modalGallery .modal-heading > h2').html('Add New Gallery');
    $('#btnUpdate').addClass('hidden').hide();
    $('#btnSave').removeClass('hidden').show();
  });

  $('[type="checkbox"]').bootstrapSwitch({
    'onColor': 'success'
  }).on('switchChange.bootstrapSwitch', function(event, state) {
    $(this).parents('.form-group').find('[type=hidden]').val((state) ? 1 : 0);
  });

  $('#frmGallery :input').on('keyup change paste', function() {
		$(this).parent('.form-group').removeClass('error')
			.find('.note').html('');
	});

  $('#btnCancel').on('click', function() {
    $('#frmGallery :input').each(function() {
      var thisField = $(this);
      thisField.val('')
  		thisField.parent('.form-group').removeClass('error')
  			.find('.note').html('');

      if (thisField.attr('type') === 'hidden') {
        thisField.val(0);
        thisField.parents('.form-group').find('[type="checkbox"]')
          .bootstrapSwitch('state', false);
      }

      if (thisField.is('select')) {
        thisField.find('option').eq(0).prop('selected', true);
      }
  	});

    $('#frmGallery .alert_group').addClass('hidden').html('');
  });

  $('#btnSave, #btnUpdate').on('click', function() {
    var thisButton = $(this);
    var error = 0;
    var method = 'add_new_gallery';

    $('#frmGallery :input.field').each(function() {
      var thisField = $(this);
      if (thisField.attr('data-required') && !thisField.val().length) {
        thisField.parent('.form-group').addClass('error')
          .find('.note').html(thisField.data('required'));
        error++;
      }
    });

    if (!error) {
      var params = $('#frmGallery :input.field').serializeArray();
      if (thisButton.attr('id') === 'btnUpdate') {
        method = 'update_gallery';
        params.push({'name': 'gallery_id', 'value': $(this).data('id')});
      }

      $.post(
        `${baseurl}gallery/${method}`,
				{
					params: params,
          slug: slug
				}
			).done(function(data) {
        alert_msg(
          $('#frmGallery .alert_group'),
          (data.response) ? 'success' : 'danger',
          (data.response) ? 'Success!' : 'Failed!',
          data.message
        );
				if (data.response) {
          var page_num = parseInt($('.page_num').text());
          var searchKey = $.trim($('#search-field').val());
          if (thisButton.attr('id') === 'btnUpdate') {
            load_gallerylist(searchKey, ((page_num-1) * items_per_page), items_per_page, 0, slug);
          } else {
            load_gallerylist('', 0, items_per_page, 0, slug);
          }
				}
			}).fail(function() {
        alert_msg(
          $('#frmGallery .alert_group'),
          'danger',
          'Failed!',
          'Oops! Something went wrong. Please contact your administrator.'
        );
      });
    }
  });

  $('.album-search-button').on('click', function(e){
    var searchKey = $.trim($('#album-search-field').val());
    if (!searchKey.length) {
      $('#album-search-field').parent('.input-group').addClass('error');
      $(this).popover('toggle');
    } else {
      $(this).popover('hide');
      $('.current-page').html('1');
      var page_limit = (items_per_page % 3 !== 0) ? (items_per_page + (3 - (items_per_page % 3))) : items_per_page;
      get_gallery_items(searchKey, 0, page_limit, 0, $(this).data('gallery'));
    }
  });

  $('.album-add-item').on('click', function() {
    $('.image-album').removeClass('col-md-12').addClass('col-md-7');
    $('.image-details').removeClass('hidden-xs hidden-sm').fadeIn('slow');
  });

  $('#closeImageDetails').on('click', function() {
    $('.image-album').removeClass('col-md-7').addClass('col-md-12');
    $('.image-details').addClass('hidden-xs hidden-sm').fadeOut();
    $('#btnCancelInfo, #btnResetInfo').trigger('click');
  });

  $('#albumImage').on('click', function() {
    $('#imgAlbumItem').trigger('click');
  });

  $('#imgAlbumItem').on('change', function() {
    var preview = $('#albumImage');
    var file    = $(this)[0].files[0];
    var reader  = new FileReader();

    reader.addEventListener("load", function () {
      preview.attr('src', reader.result);
    }, false);

    if (file) {
      var ext = file.name.substr( (file.name.lastIndexOf('.') +1) );
      var allowedExts = ['jpg','jpeg','png','gif','PNG','JPG','JPEG','GIF'];
      var size  =  $('#imgAlbumItem')[0].files[0].size;

      if(allowedExts.indexOf(ext) === -1) {
        alert_msg(
          $('#frmAlbumImage .alert_group'),
          'danger',
          'Invalid File!',
          `Please use image files only. (Allowed file type: ${allowedExts.join(', ')})`
        );
        return;
      } else if (size * 1e-6 > 5) { // 5MB
        alert_msg(
          $('#frmAlbumImage .alert_group'),
          'danger',
          'Invalid File Size!',
          'Files must not exceed 5MB.'
        );
        return;
      }
      clear_alert();
      reader.readAsDataURL(file);
    }
  });

  $('#btnResetInfo').on('click', function() {
    var imagepath = baseurl + image_path;
    $('#albumImage').attr('src', `${imagepath}gallery/default-image.png`);
  });

  $('#btnCancelInfo').on('click', function() {
    var thisButton = $(this);
    if (thisButton.data('form-type') === 'edit') {
      $('#btnSaveInfo').removeClass('hidden').show();
      $('#btnResetInfo').removeClass('hidden').show();
      $('#btnUpdateInfo').addClass('hidden').hide();
      thisButton.addClass('hidden').hide();
      thisButton.attr('data-form-type', 'add');
      $('#btnResetInfo').trigger('click');
      $('#frmAlbumImage').find('.album-form-title').html('Add Image');
    }
  });

  $('#modalAlbum .close').on('click', function() {
    $('#btnResetInfo').trigger('click');
  });

  tinymce.init({
    selector: '#caption',
    hidden_input: false,
    menubar: false,
    toolbar: false,
    content_css: [
      baseurl + "assets/css/editor.css?tm=" + today
    ],
    init_instance_callback: function (editor) {
      editor.on('keyup change paste', function (e) {
        //
      });
    }
  });
});
