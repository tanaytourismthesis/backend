function alert_msg(obj, type, title, content) {
  if (!obj) {
    obj = $('.alert_group');
  }
  obj.html('');
  obj
    .addClass(`alert-${type}`)
    .removeClass('hidden')
    .append(
      $('<i class="close fa fa-times"></i>')
        .on('click', function(){
          clear_alert();
        })
    )
    .append(
      $('<div></div>').html(
        `<strong>${title}</strong>
        <br/>
        ${content}`
      )
    )
}

function clear_alert(obj) {
  if (!obj) {
    obj = $('.alert_group');
  }
  obj
    .addClass('hidden')
    .removeClass('alert-danger alert-warning')
    .html('');
}

function validateEmail(email) {
  var re = /[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+(?:[A-Z]{2}|com|org|net|gov|mil|biz|info|mobi|name|aero|jobs|museum)\b/;
  return re.test(email);
}

function setSearchTablePlaceholder(tbody, num) {
  var placeholder = '';
  for (i = 0; i < num; i++) {
    placeholder += '<tr class="placeholder"><td colspan="100%">&nbsp;</td></tr>';
  }
  tbody.html(placeholder);
}

function setAlbumPlacehoder(container, imagepath) {
  var placeholder = '';
  for (i = 0; i < 4; i++) {
    placeholder += `<div class="row">
      <div class="col-xs-12 col-md-4 album-item placeholder placeholder-image text-center">
        <img class="item-image" src="${imagepath}gallery/default-image.png" />
      </div>
      <div class="col-xs-12 col-md-4 album-item placeholder placeholder-image text-center">
        <img class="item-image" src="${imagepath}gallery/default-image.png" />
      </div>
      <div class="col-xs-12 col-md-4 album-item placeholder placeholder-image text-center">
        <img class="item-image" src="${imagepath}gallery/default-image.png" />
      </div>
    </div><hr>`;
  }
  container.html(placeholder);
}

function setNavigation(total_records, total_pages, page_num, func_name, func_option = '') {
  var buttonHidden = (total_records <= items_per_page) ? 'hidden' : '';
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
  var searchKey = $('#search-field').val();

  if (prevButtonDisabled) {
    prevButtonOptions['disabled'] = 'disabled';
  }

  if (nextButtonDisabled) {
    nextButtonOptions['disabled'] = 'disabled';
  }

  $('.total_pages').html(total_pages);
  $('.total_records').html(total_records);

  $('.navigator-buttons').html('');
  $('.navigator-buttons')
    .append(
      $(
        '<button></button',
        prevButtonOptions
      ).on('click', function(){
        page_num--;
        $('.page_num').html(page_num);
        if (page_num === 1) {
          $(this).prop('disabled', true).attr('disabled', 'disabled');
        }
        if (func_option.length) {
          window[func_name](searchKey, ((page_num-1) * items_per_page), items_per_page, 0, func_option);
        } else {
          window[func_name](searchKey, ((page_num-1) * items_per_page), items_per_page, 0);
        }
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
        $('.page_num').html(page_num);
        if (page_num === total_pages) {
          $(this).prop('disabled', true).attr('disabled', 'disabled');
        }
        if (func_option.length) {
          window[func_name](searchKey, ((page_num-1) * items_per_page), items_per_page, 0, func_option);
        } else {
          window[func_name](searchKey, ((page_num-1) * items_per_page), items_per_page, 0);
        }
      }).append(
        $(
          '<i></i>', {
            'class': 'fas fa-angle-right'
        })
      )
  );
}

$('#search-field').on('change paste keyup', function(e){
  var searchKey = $(this).val();
  if (searchKey.length) {
    $(this).parent('.input-group').removeClass('error');
    $(this).siblings('.search-button').popover('hide');
  }

  if (e.type == 'keyup') {
    e = e || window.event;
    if (e.keyCode == 13) { // Return key
        $(this).siblings('.search-button').trigger('click');
        return false;
    }
  }
});

$('.search-button').on('mouseout', function(){
  $(this).popover('hide');
});
