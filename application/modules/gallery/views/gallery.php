<div class="row search-bar">
	<div class="col-xs-12 col-sm-6 input-group">
		<span class="input-group-addon hidden-xs"><?php echo $page_caption; ?></span>
		<input type="text" class="form-control field" id="search-field" name="search-field" placeholder="Search here..."/>
		<span class="input-group-addon search-button" data-toggle="popover" data-trigger="manual" data-placement="bottom" data-content="Please provide the search key.">
			<i class="glyphicon glyphicon-search"></i>
		</span>
		<span class="input-group-addon reload-list btn btn-success"><i class="fas fa-sync"></i></span>
	</div>
</div>
<div class="table-container">
	<table class="table table-data table-hover table-striped table-condensed table-responsive" id="tblGallery">
		<thead class="table-header">
			<tr>
				<th scope="row">#</th>
				<th>Gallery</th>
				<th class="hidden-xs">Status</th>
				<th class="hidden-xs">Type</th>
				<th>Page</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>

<div id="modalGallery" class="modal-dialog modal-md modal fade">
	<div class="modal-content">
		<div class="modal-heading">
			<h2 class="text-center">Add New Gallery</h2>
		</div>
		<hr />
		<div class="modal-body">
		</div>
		<div class="modal-footer">
				<button type="button" id="btnSave" class="btn btn-primary ripple hidden">Save</button>
				<button type="button" id="btnUpdate" class="btn btn-primary ripple hidden" data-id="">Update</button>
				<button type="button" id="btnCancel" class="btn btn-secondary ripple" data-dismiss="modal">Cancel</button>
			</div>
	</div>
</div>
