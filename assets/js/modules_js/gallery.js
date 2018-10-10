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
              get_gallery_items('', 0, page_limit, 0, value['gallery_id']);
              $('#modalAlbum').find('.gallery-name').html(value['gallery_name']);
              $('#modalAlbum').find('.album-search-button').attr('data-gallery', value['gallery_id']);
              $('#modalAlbum').find('.album-reload-list').attr('data-gallery', value['gallery_id']);
              $('#frmAlbumImage #gallery_gallery_id').val(value['gallery_id']);
              $('#modalAlbum').modal('show');
            }).html('<i class="fas fa-eye"></i>')
          );

        if (parseInt(value['hasGallery']) && !parseInt(value['carouselOnly'])) {
          td.prepend(
              $('<button class="btn btn-xs btn-default"></button>').on('click', function() {
                var thisButton = $(this);
                thisButton.prop('disabled', true).attr('disabled', 'disabled')
                  .html(`<i class="fa fa-spinner fa-spin"></i>`);

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

                    $('#modalGallery .modal-heading > h2').html('Edit Gallery');
                    $('#btnUpdate').removeClass('hidden').show();
                    $('#btnSave').addClass('hidden').hide();
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

      setNavigation('', total_records, total_pages, page_num, 'load_gallerylist', slug);

      $('.navigator-fields').removeClass('hidden').show();
      tbody.fadeIn('slow');
    } else {
      tbody.show('slow');
      tbody.html('<tr><td colspan="100%" align="center">No results found...</td></tr>');
      $('.navigator-fields').addClass('hidden').hide();
    }
  }).fail(function(){
    tbody.show('slow').html('');
    tbody.html('<tr><td colspan="100%" align="center">Oops something went wrong. Please contact your administrator.</td></tr>');
    $('.navigator-fields').addClass('hidden').hide();
  });
};

var get_gallery_items = (searchkey, start, limit, id, gallery) => {
  var album = $('#modalAlbum .album-list');
  setImageListPlacehoder(album, baseurl + image_path,'album', 'gallery/default-image.png', 1);
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
            <div class="item-name">${value['title']}</div>
          </div>`).on('click', function() {
            var albumImageForm = $('#frmAlbumImage');
            albumImageForm.addClass('edit-form').removeClass('add-form');
            albumImageForm.find('.album-form-title').html('Edit Image');
            $('#btnSaveInfo').addClass('hidden').hide();
            $('#btnResetInfo').addClass('hidden').hide();
            $('#btnUpdateInfo').removeClass('hidden').show();
            $('#btnCancelInfo').removeClass('hidden').show();
            $('.copy-url').show();
            $.each(value, function(i, v) {
              albumImageForm.find(`.field[name="${i}"]`).val(v);
              if (i === 'image_filename') {
                $('#albumImage').attr('src', `${imagepath}gallery/${v}`)
                albumImageForm.find('#url').val(`${imagepath}gallery/${v}`);
              }
              if (i === 'caption') {
                tinymce.activeEditor.setContent(v,{format: 'raw'});
              }
            });

            $('.image-album').removeClass('col-md-12').addClass('col-md-7');
            $('.image-details').removeClass('hidden-xs hidden-sm').fadeIn('slow');
            $('#btnResetImage').addClass('hidden').hide();

            // scroll to form
            var formOffset = $('.image-details').offset();
            $('#modalAlbum').scrollTop(0);
            $('#modalAlbum').animate({
              scrollTop: formOffset.top * 0.9
            });
          })
        );
        if (idx % 3 === 0 || (idx % 3 < 3 && data.data.records.length === idx)) {
          album.append(row);
          row = '';
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
              get_gallery_items(searchKey, ((pageNum-1) * limit), limit, 0, gallery);
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
            '<button></button>',
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

function clearAllContentEditor(){
  for(i=0; i<tinymce.editors.length; i++){
     tinymce.editors[i].setContent("");
     $("[name='" + tinymce.editors[i].targetElm.name + "']").val("");
  }
}

function CheckTinymce(){
  var caption = $.trim(tinyMCE.activeEditor.getContent({format: 'text'}));
  $('#caption').click();
  if(!caption.length){
    $('#caption').parent('.form-group').addClass('error')
      .find('.note').html($('#content').data('required'));
    return false;
  }
  $('#caption').parent('.form-group').removeClass('error')
    .find('.note').html('');
  return true;
}

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
    $('.copy-url').hide();
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
    $('#btnSave').prop('disabled', false).removeAttr('disabled');
  });

  $('#btnSave, #btnUpdate').on('click', function() {
    var thisButton = $(this);
    var error = 0;
    var method = 'add_new_gallery';
    var thisButton = $(this);

    thisButton.prop('disabled', true).attr('disabled', 'disabled')
      .html(`<i class="fa fa-spinner fa-spin"></i>&nbsp;${$(this).data('processing')}`);

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
        params.push({name: 'gallery_id', value: $(this).data('id')});
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
            $('#btnSave').prop('disabled', true).attr('disabled', '');
            setTimeout(function() {
              $('#btnCancel').trigger('click');
            }, 1000);
          }

				}
        thisButton.prop('disabled', false).removeAttr('disabled')
          .html(thisButton.data('caption'));
			}).fail(function() {
        alert_msg(
          $('#frmGallery .alert_group'),
          'danger',
          'Oops! Something went wrong.',
          'Please contact your administrator.'
        );
        thisButton.prop('disabled', false).removeAttr('disabled')
          .html(thisButton.data('caption'));
      });
    } else {
      thisButton.prop('disabled', false).removeAttr('disabled')
        .html(thisButton.data('caption'));
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
      get_gallery_items(searchKey, 0, page_limit, 0, $(this).data('gallery'));
    }
  });

  $('.album-reload-list').on('click', function() {
    $('#album-search-field').val('');
    $('.current-page').html('1');
    get_gallery_items('', 0, page_limit, 0, $(this).data('gallery'));
  });

  $('.album-add-item').on('click', function() {
    $('.image-album').removeClass('col-md-12').addClass('col-md-7');
    $('.image-details').removeClass('hidden-xs hidden-sm').fadeIn('slow');
    $('#frmAlbumImage').addClass('add-form').removeClass('edit-form');
    $('#btnResetImage').addClass('hidden').hide();

    // scroll to form
    var formOffset = $('.image-details').offset();
    $('#modalAlbum').scrollTop(0);
    $('#modalAlbum').animate({
      scrollTop: formOffset.top * 0.9
    });
  });

  $('#closeImageDetails').on('click', function() {
    $('.image-details').addClass('hidden-xs hidden-sm').fadeOut(function(){
      $('.image-album').removeClass('col-md-7').addClass('col-md-12');
    });
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
      } else if (size * 1e-6 > max_filesize) { // 5MB
        alert_msg(
          $('#frmAlbumImage .alert_group'),
          'danger',
          'Invalid File Size!',
          `Files must not exceed ${max_filesize}MB.`
        );
        return;
      }

      if ($('#frmAlbumImage').hasClass('edit-form')) {
        $('#btnResetImage').removeClass('hidden').show();
      }

      clear_alert();
      reader.readAsDataURL(file);
    }
  });

  $('#frmAlbumImage :input').on('keyup change paste', function() {
		$(this).parent('.form-group').removeClass('error')
			.find('.note').html('');
	});

  $('#btnResetInfo').on('click', function() {
    var imagepath = baseurl + image_path;
    $('#albumImage').attr('src', `${imagepath}gallery/default-image.png`);
    $('#image_filename').val('default-image.png');
    $('#frmAlbumImage input.field').parents('.form-group').removeClass('error')
      .find('.note').html('')
    clear_alert();

    // scroll to form
    var formOffset = $('.image-details').offset();
    var modalOffset = $('#modalAlbum').offset();
    $('#modalAlbum').animate({
      scrollTop: modalOffset.top - formOffset.top
    });
  });

  $('#btnCancelInfo').on('click', function() {
    var thisButton = $(this);
    $('#btnSaveInfo').prop('disabled', false).removeAttr('disabled');
    $('#btnSaveInfo').removeClass('hidden').show();
    $('#btnResetInfo').removeClass('hidden').show();
    $('#btnUpdateInfo').addClass('hidden').hide();
    thisButton.addClass('hidden').hide();
    $('#btnResetInfo').trigger('click');
    $('#frmAlbumImage #gallery_item_id').val('0');
    $('#frmAlbumImage #image_filename').val('default-image.png');
    $('#frmAlbumImage').find('.album-form-title').html('Add Image');
    $('.copy-url').hide();
    clearAllContentEditor();
    clear_alert();
  });

  $('#btnResetImage').on('click', function() {
    var imagepath = baseurl + image_path;
    var imagefile = $('#image_filename').val();
    $('#albumImage').attr('src', `${imagepath}gallery/${imagefile}`);
    $(this).addClass('hidden').hide();
    clear_alert();
  });

  $('#modalAlbum .close').on('click', function() {
    $('#btnResetInfo').trigger('click');
  });

  $('#btnUpdateInfo, #btnSaveInfo').on('click', function() {
    var album = $('#frmAlbumImage');
    var fields = album.find('input.field');
    var file = $('#imgAlbumItem');
    var error = 0;
    var method = ($('#frmAlbumImage').hasClass('edit-form')) ? 'update_gallery_item' : 'add_gallery_item';
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
    error = (!CheckTinymce()) ? error++ : error;

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
      var params = fields.serializeArray();
      var thisButton = $(this);
      var caption = $.trim(tinyMCE.activeEditor.getContent({format: 'raw'}));

      params.push({name: 'caption', value: caption});
      params = JSON.stringify(params);

      thisButton.prop('disabled', true).attr('disabled', 'disabled')
        .html(`<i class="fa fa-spinner fa-spin"></i>&nbsp;${$(this).data('processing')}`);

      if (file[0].files.length) {
        data.append('file', file[0].files[0]);
      }
      data.append('params', params);

      $.ajax({
        url: `${baseurl}gallery/${method}`,
        type: 'post',
        data: data,
        enctype: 'multipart/form-data',
        processData: false,  // tell jQuery not to process the data
        contentType: false,   // tell jQuery not to set contentType
        cache: false,
        success: function (data) {
          alert_msg(
            $('#frmAlbumImage .alert_group'),
            (data.response) ? 'success' : 'danger',
            (data.response) ? 'Success!' : 'Failed!',
            data.message
          );
          if (data.response) {
            var currPage = parseInt($('.current-page').text());
            var searchKey = $('#album-search-field').val();
            var gallery = $('#gallery_gallery_id').val();
            var imagepath = baseurl + image_path;
            get_gallery_items(searchKey, ((currPage-1) * page_limit), page_limit, 0, gallery);
            if (typeof(data.data) != 'undefined') {
              $('#frmAlbumImage').find('#image_filename').val(data.data.image_filename);
              $('#frmAlbumImage').find('#url').val(`${imagepath}gallery/${data.data.image_filename}`);
            }
          }
          // scroll to form
          var formOffset = $('.image-details').offset();
          var modalOffset = $('#modalAlbum').offset();
          $('#modalAlbum').animate({
            scrollTop: modalOffset.top - formOffset.top
          });

          $('#btnResetImage').addClass('hidden').hide();

          if (method === 'add_gallery_item' && data.response) {
            $('#btnSaveInfo').prop('disabled', true).attr('disabled', '');
            setTimeout(function() {
              $('#btnResetInfo').trigger('click');
            }, 1000);
          }

          thisButton.prop('disabled', false).removeAttr('disabled')
            .html(thisButton.data('caption'));
        },
        error: function (data) {
          alert_msg(
            $('#frmAlbumImage .alert_group'),
            'danger',
            'Oops! Something went wrong.',
            'Please contact your administrator.'
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

  $('#frmAlbumImage').find('#url').on('click', function() {
    $(this).select();
    if (BROWSER === 'safari') {
      this.setSelectionRange(0, this.value.length);
    }
  });

  $('#btnCopyURL').on('click', function() {
    $('#frmAlbumImage').find('#url').trigger('click');
    document.execCommand("copy");
  });

  tinymce.init({
    selector: '#caption',
    hidden_input: false,
    menubar: false,
    toolbar: false,
    content_css: [
      `${baseurl}assets/css/editor.css?tm=${today}`
    ],
    init_instance_callback: function (editor) {
      editor.on('keyup change paste', function (e) {
        $('#frmAlbumImage #caption').parent('.form-group')
          .removeClass('error').find('.note').html('');
      });
    }
  });
});
