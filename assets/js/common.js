/* ===================================================== */
// Source: https://stackoverflow.com/questions/9847580/how-to-detect-safari-chrome-ie-firefox-and-opera-browser
var BROWSER = '';
isIE = /*@cc_on!@*/false || !!document.documentMode;
isEdge = !isIE && !!window.StyleMedia;
if(navigator.userAgent.indexOf("Chrome") != -1 && !isEdge)
{
    BROWSER = 'chrome';
}
else if(navigator.userAgent.indexOf("Safari") != -1 && !isEdge)
{
    BROWSER = 'safari';
}
else if(navigator.userAgent.indexOf("Firefox") != -1 )
{
    BROWSER = 'firefox';
}
else if((navigator.userAgent.indexOf("MSIE") != -1 ) || (!!document.documentMode == true )) //IF IE > 10
{
    BROWSER = 'ie';
}
else if(isEdge)
{
    BROWSER = 'edge';
}
else
{
   BROWSER = 'other-browser';
}
$('html').addClass(BROWSER);
/* ===================================================== */

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

function validateURL(url) {
    var re = "^(https?://)?(www\\.)?([-a-z0-9]{1,63}\\.)*?[a-z0-9][-a-z0-9]{0,61}[a-z0-9]\\.[a-z]{2,6}(/[-\\w@\\+\\.~#\\?&/=%]*)?$";
    var website = new RegExp(re,"i");
    return website.test(url);
}

function validateContactNumber(contact) {
  var re = /^[+]?[\d]+([\-][\d]+)*\d$/;
  return re.test(contact);
}

function setSearchTablePlaceholder(tbody, num) {
  var placeholder = '';
  for (i = 0; i < num; i++) {
    placeholder += '<tr class="placeholder"><td colspan="100%">&nbsp;</td></tr>';
  }
  tbody.html(placeholder);
}

function setAlbumPlacehoder(container, imagepath, numrows = 3) {
  var placeholder = '';
  for (i = 0; i < numrows; i++) {
    placeholder += `<div class="row">
      <div class="col-xs-12 col-sm-4 col-md-4 album-item placeholder placeholder-image text-center">
        <img class="item-image" src="${imagepath}gallery/default-image.png" />
      </div>
      <div class="col-xs-12 col-sm-4 col-md-4 album-item placeholder placeholder-image text-center">
        <img class="item-image" src="${imagepath}gallery/default-image.png" />
      </div>
      <div class="col-xs-12 col-sm-4 col-md-4 album-item placeholder placeholder-image text-center">
        <img class="item-image" src="${imagepath}gallery/default-image.png" />
      </div>
    </div>`;
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
        '<button></button>',
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
        '<button></button>',
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

$('#search-field, #album-search-field').on('change paste keyup', function(e){
  var searchKey = $(this).val();
  var searchButton = `.${$(this).attr('id').replace('field', 'button')}`;
  if (searchKey.length) {
    $(this).parent('.input-group').removeClass('error');
    $(this).siblings(searchButton).popover('hide');
  }

  if (e.type == 'keyup') {
    e = e || window.event;
    if (e.keyCode == 13) { // Return key
        $(this).siblings(searchButton).trigger('click');
        return false;
    }
  }
});

$('.search-button, .album-search-button').on('mouseout', function(){
  $(this).popover('hide');
});
