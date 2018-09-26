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
              'src': `${baseurl}assets/images/gallery/${value['hotel_image']}`,
            }).css({
              'width': '100%'
            })
          )
        ).append(
          $('<td></td>').html(value['hotel_name'])
        ).append(
          $('<td></td>').append(
            $('<button class="btn btn-xs btn-default"></button>').on('click', function() {
              $('#modalHANE').modal('show');
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

  $('[type="checkbox"]').bootstrapSwitch({
    'onColor': 'success'
  }).on('switchChange.bootstrapSwitch', function(event, state) {
    $(this).parents('.form-group').find('[type=hidden]').val((state) ? 1 : 0);
  });
});
