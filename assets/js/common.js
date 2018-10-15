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

var windowWidth = $(window).innerWidth();
var substrLen = (windowWidth < 480) ? 10 : 25;

function alert_msg(obj, type, title, content) {
  if (!obj) {
    obj = $('.alert_group');
  }

  clear_alert(obj);

  obj
    .removeClass('alert-success alert-danger alert-warning')
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
    .removeClass('alert-danger alert-success alert-warning')
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

function validateLonghitude(long) {
  var re = /^[+-]?((([1-9]?[0-9]|1[0-7][0-9])(\.[0-9]{1,6})?)|180(\.0{1,6})?)$/;
  return re.test(long);
}

function validateLatitude(lat) {
  var re = /^[+-]?(([1-8]?[0-9])(\.[0-9]{1,6})?|90(\.0{1,6})?)$/;
  return re.test(lat);
}

function validateAmount(amount) {
  var re = /^[0-9]+(\.[0-9]{1,6})?$/;
  return re.test(amount);
}

function setSearchTablePlaceholder(tbody, num) {
  var placeholder = '';
  for (i = 0; i < num; i++) {
    placeholder += '<tr class="placeholder"><td colspan="100%">&nbsp;</td></tr>';
  }
  tbody.html(placeholder);
}

function setImageListPlacehoder(container, imagepath, prefix = 'album', image = 'gallery/default-image.png', numrows = 3) {
  var placeholder = '';
  for (i = 0; i < numrows; i++) {
    placeholder += `<div class="row">
      <div class="col-xs-12 col-sm-4 col-md-4 ${prefix}-item placeholder placeholder-image text-center">
        <img class="item-image" src="${imagepath}${image}" />
      </div>
      <div class="col-xs-12 col-sm-4 col-md-4 ${prefix}-item placeholder placeholder-image text-center">
        <img class="item-image" src="${imagepath}${image}" />
      </div>
      <div class="col-xs-12 col-sm-4 col-md-4 ${prefix}-item placeholder placeholder-image text-center">
        <img class="item-image" src="${imagepath}${image}" />
      </div>
    </div>`;
  }
  container.html(placeholder);
}

function setNavigation(nav_container = '', total_records, total_pages, page_num, func_name, func_option = '') {
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
  var searchKey = $(`${nav_container}#search-field`).val();

  if (prevButtonDisabled) {
    prevButtonOptions['disabled'] = 'disabled';
  }

  if (nextButtonDisabled) {
    nextButtonOptions['disabled'] = 'disabled';
  }

  $(`${nav_container}.total_pages`).html(total_pages);
  $(`${nav_container}.total_records`).html(total_records);

  $(`${nav_container}.navigator-buttons`).html('');
  $(`${nav_container}.navigator-buttons`)
    .append(
      $(
        '<button></button>',
        prevButtonOptions
      ).on('click', function(){
        page_num--;
        $(`${nav_container}.page_num`).html(page_num);
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
        $(`${nav_container}.page_num`).html(page_num);
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

function setMap(mapContainer, long = 121.2849, lat = 14.5005) {
  // The location in long-lat
  var longLat = {lat: lat, lng: long};
  // The map, centered at location
  var map = new google.maps.Map(
    mapContainer,
    {
      zoom: 18,
      center: longLat
    }
  );
  // The marker, positioned at location
  var marker = new google.maps.Marker({
    position: longLat,
    map: map
  });

  return map;
}

function resetSelectMenuToIndex(obj, type = 'index', value = 0) {
  obj.find('option[selected="selected"]').each(
    function() {
      $(this).removeAttr('selected');
    }
  );

  switch(type) {
    case 'index':
      obj.find('option').eq(value).attr('selected','selected');
      break;
    case 'value':
      obj.find(`option[value="${value}"]`).attr('selected','selected');
      break;
  }
}

var searchField = [
  '#album-', '#room-', '#metrics-', ''
].join('search-field,');

$(`${searchField}#search-field`).on('change paste keyup', function(e){
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

$(`${searchField}#search-field`).on('blur mouseout', function() {
  var searchButton = `.${$(this).attr('id').replace('field', 'button')}`;
  $(this).parent('.input-group').removeClass('error');
  $(this).siblings(searchButton).popover('hide');
});

$('.search-button, .album-search-button').on('mouseout', function(){
  $(this).popover('hide');
});
