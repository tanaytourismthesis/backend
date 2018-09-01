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
  ).done(function(data){
    tbody.html('');
    if(data.response) {
      var ctr = start;
      $.each(data.data.records,function(index,value){
        var tr = $('<tr></tr>');
        tr.append(
          $('<td></td>').html(++ctr)
        ).append(
          $('<td></td>').html(value['gallery_name'])
        ).append(
          $('<td></td>').html(value['gallery_status'])
        ).append(
          $('<td></td>').html(value['gallery_type'])
        ).append(
          $('<td></td>').html(value['page_name'])
        ).append(
          $('<td></td>').append(
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
              ).done(function(data){
                if(data.response){
                  // $.each(data.data.records, function(index, value){
                  // }
                  $('#modalGallery').modal({backdrop: 'static'});
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
}

$(function(){
  var slug = $('.page_slug').attr('alt');
  $('.page_num').html('1');
	load_gallerylist('', 0, items_per_page, 0, slug);

  $('.search-button').on('click', function(e){
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

  $('.reload-list').on('click', function(){
    $('#search-field').val('');
    $('.page_num').html('1');
    load_gallerylist('', 0, items_per_page, 0, slug);
  });

  $('#btnAdd').on('click', function(){
    $('#modalGallery .modal-heading > h2').html('Add New Gallery');
    $('#btnUpdate').addClass('hidden').hide();
    $('#btnSave').removeClass('hidden').show();
  });
});
