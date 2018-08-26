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
