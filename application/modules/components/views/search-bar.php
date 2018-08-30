<div class="row search-bar">
	<div class="col-xs-12 col-sm-6 input-group">
    <?php if (isset($page_caption) && !empty($page_caption)): ?>
    <span class="input-group-addon hidden-xs"><?php echo $page_caption; ?></span>
    <?php endif; ?>
		<input type="text" class="form-control field" id="search-field" name="search-field" placeholder="Search for users here..."/>
		<span class="input-group-addon search-button ripple" data-toggle="popover" data-trigger="manual" data-placement="bottom" data-content="Please provide the search key.">
			<i class="glyphicon glyphicon-search"></i>
		</span>
		<span class="input-group-addon reload-list btn btn-success"><i class="fas fa-sync"></i></span>
	</div>
</div>
