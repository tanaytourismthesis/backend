$(function() {
  $('#mnuConfigFiles').on('change', function() {
    var thisMenu = $(this);
    var thisMenuVal = thisMenu.val();
    var editor = $('.editor-area');
    var configFile = editor.find('#configFile');

    editor.find('.action-buttons').addClass('hidden');
    configFile.val('Loading configuration file...');
    configFile.attr('data-old', '');
    thisMenu.prop('disabled', true).attr('disabled', 'disabled');

    clear_alert(editor.find('.alert_group'));

    if (thisMenuVal != '0') {
      $.get(
        `${baseurl}settings/get_config_file/${thisMenuVal}`
      ).done(function(data) {
        var content = atob(data.data.filecontent) || 'Invalid content!';
        configFile.val(content);

        alert_msg(
          editor.find('.alert_group'),
          data.response ? 'success' : 'danger',
          data.response ? 'Success!' : 'Failed!',
          data.message
        );

        if (data.response) {
          editor.find('.action-buttons').removeClass('hidden');
          configFile.attr('data-old', data.data.filecontent);
          thisMenu.prop('disabled', false).removeAttr('disabled');
        }
      }).fail(function() {
        configFile.val('Oops! Something went wrong. Please contact your administrator');
        thisMenu.prop('disabled', false).removeAttr('disabled');
      });
    } else {
      configFile.val('Please select a configuration file to edit.');
      thisMenu.prop('disabled', false).removeAttr('disabled');
    }
  });

  $('#btnResetConfigFile').on('click', function() {
    var thisButton = $(this);
    var editor = $('.editor-area');
    var configFile = editor.find('#configFile');

    thisButton.prop('disabled', true).attr('disabled', 'disabled')
      .html(`<i class="fa fa-spinner fa-spin"></i> ${thisButton.data('processing')}`);

    clear_alert(editor.find('.alert_group'));

    configFile.val('Restoring to last content update...');
    setTimeout(function() {
      configFile.val(atob(configFile.data('old')));
      thisButton.prop('disabled', false).removeAttr('disabled')
        .html(thisButton.data('caption'));
      alert_msg(
        editor.find('.alert_group'),
        'success',
        'Success!',
        'Restoration complete.'
      );
    }, 2000);
  });

  $('#btnSaveConfigFile').on('click', function() {
    var thisButton = $(this);
    var editor = $('.editor-area');
    var configFile = editor.find('#configFile');
    var content = configFile.val();
    var config_type = $('#mnuConfigFiles').val();

    thisButton.prop('disabled', true).attr('disabled', 'disabled')
      .html(`<i class="fa fa-spinner fa-spin"></i> ${thisButton.data('processing')}`);
  
    clear_alert(editor.find('.alert_group'));

    try {
      JSON.parse(content);
      content = btoa(content);
      $.post(
        `${baseurl}/settings/save_config_file`,
        {
          filecontent: content,
          type: config_type
        }
      ).done(function(data) {
        alert_msg(
          editor.find('.alert_group'),
          data.response ? 'success' : 'danger',
          data.response ? 'Success!' : 'Failed!',
          data.message
        );
        thisButton.prop('disabled', false).removeAttr('disabled')
          .html(thisButton.data('caption'));
      }).fail(function() {
        alert_msg(
          editor.find('.alert_group'),
          'danger',
          'Oops! Something went wrong.',
          'Please contact your administrator and try again.'
        );
        thisButton.prop('disabled', false).removeAttr('disabled')
          .html(thisButton.data('caption'));
      });
    } catch (err) {
      alert_msg(
        editor.find('.alert_group'),
        'danger',
        'Parse Error',
        err.message
      );
      thisButton.prop('disabled', false).removeAttr('disabled')
        .html(thisButton.data('caption'));
    }
  });
});
