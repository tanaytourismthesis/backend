<div class="row search-bar">
	<div class="col-xs-6 input-group">
		<span class="input-group-addon"><?php echo $page_caption; ?></span>
		<input type="text" class="form-control field" id="search-field" name="search-field" placeholder="Search here..."/>
		<span class="input-group-addon search-button" data-toggle="popover" data-trigger="manual" data-placement="bottom" data-content="Please provide the search key.">
			<i class="glyphicon glyphicon-search"></i>
		</span>
	</div>
</div>
<div class="table-container">
	<table class="table table-data table-hover table-striped table-condensed table-responsive" id="tblGallery">
		<thead class="table-header">
			<tr>
				<th scope="row">#</th>
				<th>Username</th>
				<th>Name</th>
				<th>Position</th>
				<th class="hidden-xs">Login Status</th>
				<th class="hidden-xs">Last Login Date</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>
<div class="row navigator">
	<div class="col-xs-12 navigator-left">&nbsp;</div>
</div>
