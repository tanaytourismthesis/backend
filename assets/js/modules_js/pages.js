var load_pagecontentlist = (searchkey, start, limit, id, slug, tag) => {
  var tbody = $('#tblPages tbody');

  setSearchTablePlaceholder(tbody, items_per_page);

  $.post(
    `${baseurl}pages/load_pagecontentlist`,
    {
      searchkey: searchkey,
      start: start,
      limit: limit,
      id: id,
      slug: slug,
      tag: tag
    }
  ).done(function(data){
    tbody.html('');
    if(data.response) {
      var ctr = start;
      $.each(data.data.records,function(index,value){
        value['isLoggedin'] = (value['isLoggedin'] > 0) ? 'Active' : 'Inactive';
        var tr = $('<tr></tr>');
        tr.append(
          $('<td></td>').html(++ctr)
        ).append(
          $('<td></td>').html(value['title'])
        ).append(
          $('<td></td>').html(value['content_slug'])
        ).append(
          $('<td></td>').html(value['show_type'])
        ).append(
          $('<td></td>').html(value['tag'])
        ).append(
          $('<td></td>').html(value['page_name'])
        ).append(
          $('<td></td>').append(
            $('<button class="btn btn-xs btn-default"></button>').on('click', function() {
              var thisButton = $(this);
              thisButton.prop('disabled', true).attr('disabled', 'disabled')
                .html(`<i class="fa fa-spinner fa-spin"></i>`);
              $('#modalPages .modal-heading > h2').html('Edit Content');
              $('#btnUpdate').removeClass('hidden').show();
              $('#btnSave').addClass('hidden').hide();

              $.post(
                `${baseurl}pages/load_pagecontentlist`,
                {
                  searchkey: '',
                  start: 0,
                  limit: 1,
                  id: value['content_id'],
                  slug: slug,
                  tag: tag
                }
              ).done(function(data){
                if(data.response){
                  // $.each(data.data.records, function(index, value){
                  // }
                  $('#modalPages').modal({backdrop: 'static'});
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

      setNavigation(total_records, total_pages, page_num, 'load_pagecontentlist', slug);

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
  var tag = $('.page_tag').attr('alt');
  $('.page_num').html('1');
	load_pagecontentlist('', 0, items_per_page, 0, slug, tag);

  $('.search-button').on('click', function(e){
    var searchKey = $.trim($('#search-field').val());

    if (!searchKey.length) {
      $('#search-field').parent('.input-group').addClass('error');
      $(this).popover('toggle');
    } else {
      $(this).popover('hide');
      $('.page_num').html('1');
      load_pagecontentlist(searchKey, 0, items_per_page, 0, slug, tag);
    }
  });

  $('.reload-list').on('click', function(){
    $('#search-field').val('');
    $('.page_num').html('1');
    load_pagecontentlist('', 0, items_per_page, 0, slug, tag);
  });

  $('#btnAdd').on('click', function(){
    $('#modalPage .modal-heading > h2').html('Add New Content');
    $('#btnUpdate').addClass('hidden').hide();
    $('#btnSave').removeClass('hidden').show();
  });
});
