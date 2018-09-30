var load_pagecontentlist = (searchkey, start, limit, id, slug, tag) => {
  var tbody = $('#tblPages tbody');

  setSearchTablePlaceholder(tbody, items_per_page);

  $.post(
    `${baseurl}pages/load_pagecontentlist`,
    {
      searchkey: searchkey,
      start: start,
      limit: limit,
      id: id,
      slug: slug,
      tag: tag
    }
  ).done(function(data){
    tbody.html('');
    if(data.response) {
      var ctr = start;
      $.each(data.data.records,function(index, value){
        value['isLoggedin'] = (value['isLoggedin'] > 0) ? 'Active' : 'Inactive';
        var tr = $('<tr></tr>');
        tr.append(
          $('<td></td>').html(++ctr)
        ).append(
          $('<td></td>').html(value['title'])
        ).append(
          $('<td></td>').html(value['content_slug'])
        ).append(
          $('<td></td>').html(value['show_type'])
        ).append(
          $('<td></td>').html(value['tag'])
        ).append(
          $('<td></td>').html(value['page_name'])
        ).append(
          $('<td></td>').append(
            $('<button class="btn btn-xs btn-default"></button>').on('click', function() {
              var thisButton = $(this);
              thisButton.prop('disabled', true).attr('disabled', 'disabled')
                .html(`<i class="fa fa-spinner fa-spin"></i>`);
              $('#modalPages .modal-heading > h2').html('Edit Content');

              $.post(
                `${baseurl}pages/load_pagecontentlist`,
                {
                  searchkey: '',
                  start: 0,
                  limit: 1,
                  id: value['content_id'],
                  slug: slug,
                  tag: tag
                }
              ).done(function(data){
                if(data.response){
                  $.each(data.data.records, function(index, value){
                    //if form field exists
                    if ($('#AddPageContent #'+index) !== 'undefined') {
                      var thisField = $(`#AddPageContent :input.field[name="${index}"]`);
                      thisField.val(value);
                      // set value to form field
                      $('#AddPageContent #'+index).val(value);

                      // if form field is dropdown
                      if ($('#AddPageContent #'+index).is('select')) {
                        // select the option denoted by the value from request
                        $('#AddPageContent #'+index+' option[value="'+value+'"]').prop('selected',true);
                      }

                      if (thisField.attr('type') === 'hidden') {
                        thisField.parents('.form-group').find('[type="checkbox"]')
                          .bootstrapSwitch('state', parseInt(value));
                      }

                      // if form field is textarea
                      if ($('#AddPageContent #'+index).is('textarea')) {
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
                  $('#btnUpdate').removeClass('hidden').show();
                  $('#headerAdd').hide();
                  $('#btnSave').hide();
                  $('#modalPages').modal({backdrop: 'static'});
                }
                thisButton.prop('disabled', false).removeAttr('disabled').html('<i class="fas fa-edit"></i>');
              });
            }).html('<i class="fas fa-edit"></i>')
          )
        );
        tbody.append(tr);
      });

      // Pagination
      var total_records = data.data.total_records;
      var total_pages = parseInt(total_records / items_per_page);
      total_pages = (total_records % items_per_page > 0) ? ++total_pages : total_pages;
      var page_num = parseInt($('.page_num').text());

      setNavigation(total_records, total_pages, page_num, 'load_pagecontentlist', slug);

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
  if(!content.length){
    $('#content').parent('.form-group').addClass('error')
      .find('.note').html($('#content').data('required'));
    return false;
  }
  $('#content').parent('.form-group').removeClass('error')
    .find('.note').html('');
  return true;
}

function update_page_content(id){
  var params = 	$('#AddPageContent :input').not(':hidden').serializeArray();
  params.push({name: 'content', value: tinymce.activeEditor.getContent({format: 'raw'})});

  $.post(
    baseurl + 'pages/update_page_content',
    {
      params: params,
      id: id
    }
  ).done(function(data){
    $('#modalPages').animate({
      scrollTop: 0
    }, 300);
    alert_msg(
      $('#AddPageContent .alert_group'),
      (data.response) ? 'success' : 'danger',
      (data.response) ? 'Success!' : 'Failed!',
      (data.response) ? 'Successfully added Updated News!' : data.message
    );

    if (data.response) {
      var slug = $('.page_slug').attr('alt');
      var tag = $('.page_tag').attr('alt');
      load_pagecontentlist('', 0, items_per_page, 0, slug, tag);
      setTimeout(function(){
        $('#btnCancel').trigger('click');
      }, 3000);
    }
  });
}

function add_page_content(){
  var slug = $('.page_slug').attr('alt');
  var tag = $('.page_tag').attr('alt');

  var params = 	$('#AddPageContent :input').not(':hidden').serializeArray();
  params.push({name: 'content', value: tinymce.activeEditor.getContent({format: 'raw'})});
  var shown = $('#isShown').val();
  params.push({name: 'isShown',value: shown});

  $.post(
    baseurl + 'pages/add_page_content',
    {
      params: params,
      slug: slug,
      tag: tag
    }
  ).done(function(data){
    $('#modalPages').animate({
      scrollTop: 0
    }, 300);
    alert_msg(
      $('#AddPageContent .alert_group'),
      (data.response) ? 'success' : 'danger',
      (data.response) ? 'Success!' : 'Failed!',
      (data.response) ? 'Successfully added new Page Content!' : data.message
    );

    if (data.response) {
      load_pagecontentlist('', 0, items_per_page, 0, slug, tag);
      setTimeout(function() {
        $('#btnCancel').trigger('click');
      }, 3000);
    }
  });
}

function clearAllContentEditor(){
  for(i=0; i<tinymce.editors.length; i++){
     tinymce.editors[i].setContent("");
     $("[name='" + tinymce.editors[i].targetElm.name + "']").val("");
  }
}

$(function() {

  $('#btnCancel').on('click',function() {
    $('#AddPageContent :input').prop('disabled',false)
    .removeAttr('disabled').val('');
    $('#AddPageContent :input').parent('.form-group').removeClass('error')
      .find('.note').html('');
    $('#AddPageContent alert_group').addClass('hidden').html('');
    clear_alert();
    clearAllContentEditor();
  });

  $('[type="checkbox"]').bootstrapSwitch({
    'onColor': 'success'
  }).on('switchChange.bootstrapSwitch', function(event, state) {
    $(this).parents('.form-group').find('[type=hidden]').val((state) ? 1 : 0);
  });

  var slug = $('.page_slug').attr('alt');
  var tag = $('.page_tag').attr('alt');
  $('.page_num').html('1');
	load_pagecontentlist('', 0, items_per_page, 0, slug, tag);

  $('.search-button').on('click', function(e){
    var searchKey = $.trim($('#search-field').val());

    if (!searchKey.length) {
      $('#search-field').parent('.input-group').addClass('error');
      $(this).popover('toggle');
    } else {
      $(this).popover('hide');
      $('.page_num').html('1');
      load_pagecontentlist(searchKey, 0, items_per_page, 0, slug, tag);
    }
  });

  $('.reload-list').on('click', function() {
    $('#search-field').val('');
    $('.page_num').html('1');
    load_pagecontentlist('', 0, items_per_page, 0, slug, tag);
  });

  $('#btnUpdate').on('click',function(){
    update_page_content($('#AddPageContent #content_id').val());
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
    $('#headerUpdate').addClass('hidden').hide();
    $('#btnUpdate').hide();
    $('#headerAdd').removeClass('hidden').show();
    $('#btnSave').removeClass('hidden').show();
  });

  $('#btnSave').on('click', function() {
    var error = 0;
    var content = tinyMCE.activeEditor.getContent({format: 'text'});

    $('#AddPageContent :input.field').not('textarea').each(function() {
      var thisField = $(this);
      if (thisField.attr('data-required') && !thisField.val().length) {
        thisField.parent('.form-group').addClass('error')
          .find('.note').html(thisField.data('required'));
        error++;
      }
    });

    error = (!CheckTinymce()) ? error++ : error;

    if(!error){
      add_page_content();
    }
  });

  $('#AddPageContent :input.field').on('keyup change paste', function() {
    $(this).parent('.form-group').removeClass('error')
      .find('.note').html('');
  });

});
