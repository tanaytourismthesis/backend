<div class="container container-fluid settings-container">
  <div class="row caution alert alert-warning">
    <h3>Warning!</h3>
    <h4>You are editing the configuration file the client and administrator side of the Tanay Tourism website.
        Please proceed with caution as it may have direct, adverse cause to the website.</h4>
  </div>
  <div class="row alert selector alert-info">
    <h4 class="alert alert-success">
      Please select a configuration file to edit.
      <select id="mnuConfigFiles" class="btn btn-primary">
        <option value="0">--select-one--</option>
        <?php foreach ($config_files as $cf): ?>
        <option value="<?php echo $cf['type']; ?>"><?php echo $cf['caption']; ?></option>
        <?php endforeach; ?>
      </select>
    </h4>
  </div>
  <div class="row editor-area alert alert-info">
    <div class="alert alert_group hidden"></div>
    <textarea id="configFile" data-old="">Please select a configuration file to edit.</textarea>
    <div class="action-buttons text-right">
      <button id="btnSaveConfigFile" class="btn btn-primary" data-caption="Save Configuration" data-processing="Saving changes">Save Configuration</button>
      <button id="btnResetConfigFile" class="btn btn-default" data-caption="Reset Configuration" data-processing="Restoring configuration">Reset Configuration</button>
    </div>
  </div>
</div>
