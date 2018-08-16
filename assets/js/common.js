function alert_msg(obj, type, title, content) {
    obj.html('');
    obj
      .addClass(`alert-${type}`)
      .removeClass('hidden')
      .append(
        $('<i class="close fa fa-times"></i>')
          .on('click', function(){
            cleart_alert();
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
  obj
    .addClass('hidden')
    .removeClass('alert-danger alert-warning')
    .html('');
}
