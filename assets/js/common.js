function alert_msg(obj, type, title, content) {
    obj
      .addClass(`alert-${type}`)
      .removeClass('hidden')
      .append(
        $('<i class="close fa fa-times"></i>')
          .on('click', function(){
            obj.addClass('hidden')
            .removeClass('alert-danger alert-warning')
            .html('');
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
