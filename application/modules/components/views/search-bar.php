<div class="row search-bar">
	<div class="col-xs-12 col-sm-9 input-group">
    <?php if (isset($icon) && !empty($icon)): ?>
    <span class="input-group-addon hidden-xs"><i class="<?php echo $icon; ?>"></i></span>
    <?php endif; ?>
		<input type="text" class="form-control" id="search-field" name="search-field" placeholder="Search here..."/>
		<span class="input-group-addon search-button ripple" data-toggle="popover" data-trigger="manual" data-placement="bottom" data-content="Please provide the search key.">
			<i class="glyphicon glyphicon-search"></i>
		</span>
		<span class="input-group-addon reload-list btn btn-success ripple"><i class="fas fa-sync"></i></span>
	</div>
</div>
