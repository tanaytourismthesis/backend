$(function(){

  function load_news(searchkey,start,limit,id){
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
            $('<td></td>').html(value['date_posted'])
          ).append(
            $('<td></td>').html(value['date_updated'])
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
                    $.each(data.data[0], function(index, value){
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
                            height: 500,
                            plugins: [
                                "advlist autolink lists link image charmap print preview anchor",
                                "searchreplace visualblocks code fullscreen",
                                "insertdatetime media table contextmenu paste imagetools wordcount"
                            ],
                            toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
                          });
                          tinymce.activeEditor.setContent(value,{format: 'raw'});
                        }
                      }
                    });
                  }
                  $('#headerUpdate').show();
                  $('#btnUpdate').show();
                  $('#headerAdd').hide();
                  $('#btnSave').hide();
                  $('#modalNews').modal({backdrop: 'static'});
                });
              }).html('Edit')
            )
          );
          tbody.append(tr);
        })
      }
    });
  }
  load_news('',0,5,0);

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

  $('#btnUpdate').on('click',function(){
    update_news($('#UpdateForm #news_id').val());
  });

  $('#btnCancel').on('click',function(){
    $('#UpdateForm :input').each(function(){
      $(this).val('');
    });
    clearAllContentEditor();
  });

  function clearAllContentEditor(){
   for(i=0; i<tinymce.editors.length; i++){
      tinymce.editors[i].setContent("");
      $("[name='" + tinymce.editors[i].targetElm.name + "']").val("");
   }
  }

  $('#btnAddNewNews').on('click', function(){
    tinymce.init({
      selector: '#content',
      hidden_input: false,
      height: 500,
      plugins: [
          "advlist autolink lists link image charmap print preview anchor",
          "searchreplace visualblocks code fullscreen",
          "insertdatetime media table contextmenu paste imagetools wordcount"
      ],
      toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
    });
    $('#headerUpdate').hide();
    $('#btnUpdate').hide();
    $('#headerAdd').show();
    $('#btnSave').show();
  });

  function add_news(){
    var params = 	$('#UpdateForm :input').not(':hidden').serializeArray();
    params.push({name: 'content', value: tinymce.activeEditor.getContent({format: 'raw'})});
    $.post(
      baseurl + 'news_management/add_news',
      {
        params: params
      }
    ).done(function(data){
      console.log(data);
    })
  }

  $('#btnSave').on('click', function(){
    add_news();
  });





});
