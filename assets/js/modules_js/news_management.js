$(function(){

  function load_news(searchkey, start, limit, id){
    var tbody = $('#tbtlNewsList tbody');
		tbody.html('<tr><td colspan="100%" align="center">Searching news list...</td></tr>');
    $.post(
      baseurl + 'news_management/load_news',
      {
        searchkey: searchkey,
        start: start,
        limit: limit,
        id: id
      }
    ).done(function(data){
      tbody.html('');
      if(data.response){
        $.each(data.data, function(index,value){
          var tr = $('<tr></tr>');
          tr.append(
            $('<td></td>').html(value['news_id'])
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
            $('<td></td>').html(value['first_name'] + ' ' + value['last_name'])
          ).append(
            $('<td></td>').append(
              $(
                '<button class="btn btn-danger"></button>', {
                  'id' : 'btnEditNews'
                }
              ).on('click', function(){
                var searchkey = '';
                var start = 0;
                var limit = 1;
                var id = value['news_id'];

                $.post(
                  baseurl + 'news_management/load_news',
                  {
                    searchkey: searchkey,
                    start: start,
                    limit: limit,
                    id: id
                  }
                ).done(function(data){
                  if(data.response){
                    $.each(data.data, function(index, value){
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
                              baseurl + "assets/css/editor.css?tm=" + today
                            ]
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
                  }
                });
              }).html('Edit')
            )
          );
          tbody.append(tr);
        });
        // Pagination
        var total_records = data.data.total_records;
        var total_pages = parseInt(total_records / items_per_page);
        total_pages = (total_records % items_per_page > 0) ? ++total_pages : total_pages;
        var page_num = parseInt($('.page_num').text());

        $('.total_pages').html(total_pages);
        $('.total_records').html(total_records);

        $('#btnPREV, #btnNEXT').show();
        if (total_records < items_per_page) {
          $('#btnPREV, #btnNEXT').hide();
        }

        $('#btnPREV').prop('disabled', false).removeAttr('disabled');
        if (page_num === 1) {
          $('#btnPREV').prop('disabled', true).attr('disabled', 'disabled');
        }

        $('#btnNEXT').prop('disabled', false).removeAttr('disabled');
        if (page_num === total_pages) {
          $('#btnNEXT').prop('disabled', true).attr('disabled', 'disabled');
        }

        $('#btnPREV').on('click', function(){
          page_num--;
          $('.page_num').html(page_num);
          if (page_num === 1) {
            $(this).prop('disabled', true).attr('disabled', 'disabled');
          }
          load_news('', ((page_num-1) * items_per_page), items_per_page, 0);
        });

        $('#btnNEXT').on('click', function(){
          page_num++;
          $('.page_num').html(page_num);
          if (page_num === total_pages) {
            $(this).prop('disabled', true).attr('disabled', 'disabled');
          }
          load_news('', ((page_num-1) * items_per_page), items_per_page, 0);
        });
        $('.navigator-right').removeClass('hidden').show();
        tbody.fadeIn('slow');
      } else {
        tbody.html('<tr><td colspan="100%" align="center">Failed to load user list...</td></tr>');
        $('.navigator-right').addClass('hidden').hide();
      }
    });
  }
  load_news('', 0, items_per_page, 0);

  function update_news(id){
    var params = 	$('#UpdateForm :input').not(':hidden').serializeArray();
    params.push({name: 'content', value: tinymce.activeEditor.getContent({format: 'raw'})});
    $.post(
      baseurl + 'news_management/update_news',
      {
        params: params,
        id: id
      }
    ).done(function(data){
      console.log(data);
    })
  }

  function add_news(){
    var params = 	$('#UpdateForm :input').not(':hidden').serializeArray();
    params.push({name: 'content', value: tinymce.activeEditor.getContent({format: 'raw'})});
    $.post(
      baseurl + 'news_management/add_news',
      {
        params: params
      }
    ).done(function(data){
      alert_msg(
        $('#UpdateForm .alert_group'),
        (data.response) ? 'success' : 'danger',
        (data.response) ? 'Success!' : 'Failed!',
        (data.response) ? 'Successfully added new News!' : data.message
      );
      load_news('', 0, items_per_page, 0);
      setTimeout(function(){
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

  $('#btnUpdate').on('click',function(){
    update_news($('#UpdateForm #news_id').val());
  });

  $('#btnCancel').on('click',function(){
    $('#UpdateForm :input').each(function(){
      $(this).val('');
    });
    clearAllContentEditor();
  });

  $('#btnAddNewNews').on('click', function(){
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
        baseurl + "assets/css/editor.css?tm=" + today
      ]
    });
    $('#headerUpdate').hide();
    $('#btnUpdate').hide();
    $('#headerAdd').show();
    $('#btnSave').show();
  });

  $('#btnSave').on('click', function(){
    var error = 0;
    var content = tinyMCE.activeEditor.getContent({format: 'text'});

    if(!content.length) {
      $('#content').parent('.form-group').addClass('error')
      .find('.note').html($('#content').data('required'));
      error++;
    }

    $('#UpdateForm :input.field').not('textarea').each(function() {
      var thisField = $(this);
      if (thisField.attr('data-required') && !thisField.val().length) {
        thisField.parent('.form-group').addClass('error')
          .find('.note').html(thisField.data('required'));
        error++;
      }  
    });

    $('#UpdateForm :input.field').on('keyup change paste', function(){
      $(this).parent('.form-group').removeClass('error')
        .find('.note').html('');
    });

    $('#content').parent('.form-group').on('keyup change paste', function(){
      $('#content').parent('.form-group cont').removeClass('error')
      .find('.note').html('');
    });

  
    if(!error){
      add_news();
    }

  });


  $('#DateForm').datetimepicker();


});
