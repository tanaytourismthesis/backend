var load_news = (searchkey, start, limit, id, slug, status) => {
  var tbody = $('#tbtlNewsList tbody');

  setSearchTablePlaceholder(tbody, items_per_page);

  $.post(
    `${baseurl}news/load_news`,
    {
      searchkey: searchkey,
      start: start,
      limit: limit,
      id: id,
      slug: slug,
      status: status
    }
  ).done(function(data){
    tbody.html('');
    if(data.response){
      var ctr = start;
      $.each(data.data.records, function(index, value){
        var tr = $('<tr></tr>');
        tr.append(
          $('<td></td>').html(++ctr)
        ).append(
          $('<td></td>').html(value['title'])
        ).append(
          $('<td></td>').html(value['status'])
        ).append(
          $('<td></td>', {
             'class' : 'hidden-xs hidden-sm'
          }).html(value['date_posted'])
        ).append(
          $('<td></td>', {
             'class' : 'hidden-xs hidden-sm'
          }).html(value['date_updated'])
        ).append(
          $('<td></td>').html(value['type_name'])
        ).append(
          $('<td></td>').html(value['numHits'] || 0)
        ).append(
          $('<td></td>',{
            'class' : 'hidden-xs'
          }).html(value['first_name'] + ' ' + value['last_name'])
        ).append(
          $('<td></td>').append(
            $(
              '<button><i class="fas fa-edit"></i></button>', {
                'id' : 'btnEditNews'
              }
            ).on('click', function() {
              var thisButton = $(this);
              thisButton.prop('disabled', true).attr('disabled', 'disabled')
                .html(`<i class="fa fa-spinner fa-spin"></i>`);
              var searchkey = '';
              var start = 0;
              var limit = 1;
              var id = value['news_id'];

              $.post(
                `${baseurl}news/load_news`,
                {
                  searchkey: searchkey,
                  start: start,
                  limit: limit,
                  id: id,
                  status: status
                }
              ).done(function(data){
                if(data.response){
                  $.each(data.data.records, function(index, value){
                    //if form field exists
                    if ($('#UpdateForm #'+index) !== 'undefined') {

                      // set value to form field
                      $('#UpdateForm #'+index).val(value);

                      // if form field is dropdown
                      if ($('#UpdateForm #'+index).is('select')) {
                        // select the option denoted by the value from request
                        $('#UpdateForm #'+index+' option[value="'+value+'"]').prop('selected',true);
                      }

                      // if form field is textarea
                      if ($('#UpdateForm #'+index).is('textarea')) {
                        $('#UpdateForm #'+index).html(value);
                        tinymce.init({
                          selector: '#content',
                          hidden_input: false,
                          height: 200,
                          plugins: [
                              "advlist autolink lists link image charmap print preview anchor",
                              "searchreplace visualblocks code fullscreen",
                              "insertdatetime media table contextmenu paste imagetools wordcount"
                          ],
                          toolbar: `insertfile undo redo | styleselect | bold italic | alignleft
                                    aligncenter alignright alignjustify | bullist numlist outdent
                                    indent | link image`,
                          content_css: [
                            `${baseurl}assets/css/editor.css?tm=${today}`
                          ],
                          init_instance_callback: function (editor) {
                            editor.on('keyup change paste', function (e) {
                              CheckTinymce();
                            });
                          }
                        });
                        tinymce.activeEditor.setContent(value,{format: 'raw'});
                      }
                    }
                  });
                  $('#headerUpdate').show();
                  $('#btnUpdate').show();
                  $('#headerAdd').hide();
                  $('#btnSave').hide();
                  $('#modalNews').modal({backdrop: 'static'});
                  thisButton.prop('disabled', false).removeAttr('disabled').html('<i class="fas fa-edit"></i>');
                }
              });
            })
          )
        );
        tbody.append(tr);
      });
      var total_records = data.data.total_records;
      var total_pages = parseInt(total_records / items_per_page);
      total_pages = (total_records % items_per_page > 0) ? ++total_pages : total_pages;
      var page_num = parseInt($('.page_num').text());

      setNavigation(total_records, total_pages, page_num, 'load_news','');

      $('.navigator-fields').removeClass('hidden').show();
      tbody.fadeIn('slow');
    } else {
      tbody.show('slow');
      tbody.html('<tr><td colspan="100%" align="center">No results found...</td></tr>');
      $('.navigator-fields').addClass('hidden').hide();
    }
  });
}

function CheckTinymce(){
  var content = $.trim(tinyMCE.activeEditor.getContent({format: 'text'}));
  $('#content').click();
  if(!content.length){
    $('#content').parent('.form-group').addClass('error')
      .find('.note').html($('#content').data('required'));
    return false;
  }
  $('#content').parent('.form-group').removeClass('error')
    .find('.note').html('');
  return true;
}

function update_news(id){
  var params = 	$('#UpdateForm :input').not(':hidden').serializeArray();
  params.push({name: 'content', value: tinymce.activeEditor.getContent({format: 'raw'})});

  $.post(
    `${baseurl}news/update_news`,
    {
      params: params,
      id: id
    }
  ).done(function(data){
    $('#modalNews .modal-body').animate({
      scrollTop: 0
    }, 300);
    alert_msg(
      $('#UpdateForm .alert_group'),
      (data.response) ? 'success' : 'danger',
      (data.response) ? 'Success!' : 'Failed!',
      (data.response) ? 'Successfully added Updated News!' : data.message
    );

    if (data.response) {
      setTimeout(function() {
        $('#btnCancel').trigger('click');
      }, 3000);
    }

    load_news('', 0, items_per_page, 0, '','');
  });
}

function add_news(){
  var params = 	$('#UpdateForm :input').not(':hidden').serializeArray();
  params.push({name: 'content', value: tinymce.activeEditor.getContent({format: 'raw'})});
  $.post(
    `${baseurl}news/add_news`,
    {
      params: params
    }
  ).done(function(data){
    $('#modalNews .modal-body').animate({
      scrollTop: 0
    }, 300);
    alert_msg(
      $('#UpdateForm .alert_group'),
      (data.response) ? 'success' : 'danger',
      (data.response) ? 'Success!' : 'Failed!',
      (data.response) ? 'Successfully added new News!' : data.message
    );
    load_news('', 0, items_per_page, 0, '','');
    setTimeout(function() {
      $('#btnCancel').trigger('click');
    }, 3000);

  })
}

function clearAllContentEditor(){
 for(i=0; i<tinymce.editors.length; i++){
    tinymce.editors[i].setContent("");
    $("[name='" + tinymce.editors[i].targetElm.name + "']").val("");
 }
}

$(function() {
  load_news('', 0, items_per_page, 0, '','');

  $('.search-button').on('click', function(e){
    var searchKey = $.trim($('#search-field').val());
    if (!searchKey.length) {
      $('#search-field').parent('.input-group').addClass('error');
      $(this).popover('toggle');
    } else {
      $(this).popover('hide');
      $('.page_num').html('1');
      load_news(searchKey, 0, items_per_page, 0, '','');
    }
  });

  $('.reload-list').on('click', function() {
    $('#search-field').val('');
    $('.page_num').html('1');
    load_news ('', 0, items_per_page, 0,'','');
  });

  $('#btnUpdate').on('click',function() {
    update_news($('#UpdateForm #news_id').val());
  });

  $('#btnCancel').on('click',function() {
    $('#UpdateForm :input').prop('disabled',false)
    .removeAttr('disabled').val('');
    $('#UpdateForm :input').parent('.form-group').removeClass('error')
      .find('.note').html('');
    $('#UpdateForm alert_group').addClass('hidden').html('');
    clear_alert();
    clearAllContentEditor();
  });

  $('#btnAdd').on('click', function() {
    tinymce.init({
      selector: '#content',
      hidden_input: false,
      height: 200,
      plugins: [
          "advlist autolink lists link image charmap print preview anchor",
          "searchreplace visualblocks code fullscreen",
          "insertdatetime media table contextmenu paste imagetools wordcount"
      ],
      toolbar: `insertfile undo redo | styleselect | bold italic | alignleft
                aligncenter alignright alignjustify | bullist numlist outdent
                indent | link image`,
      content_css: [
        `${baseurl}assets/css/editor.css?tm=${today}`
      ],
      init_instance_callback: function (editor) {
        editor.on('keyup change paste', function (e) {
          CheckTinymce();
        });
      }
    });
    $('#headerUpdate').hide();
    $('#btnUpdate').hide();
    $('#headerAdd').show();
    $('#btnSave').show();
  });

  $('#btnSave').on('click', function() {
    var error = 0;
    var content = tinyMCE.activeEditor.getContent({format: 'text'});

    $('#UpdateForm :input.field').not('textarea').each(function() {
      var thisField = $(this);
      if (thisField.attr('data-required') && !thisField.val().length) {
        thisField.parent('.form-group').addClass('error')
          .find('.note').html(thisField.data('required'));
        error++;
      }
    });

    error = (!CheckTinymce()) ? error++ : error;

    if(!error){
      add_news();
    }
  });

  $('#UpdateForm :input.field').on('keyup change paste', function() {
    $(this).parent('.form-group').removeClass('error')
      .find('.note').html('');
  });

  $('#DateForm').datetimepicker();

});
