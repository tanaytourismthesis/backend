<div class="table-container">
<table class="table table-data table-hover table-striped table-condensed table-responsive" id="tblPages">
	<thead class="table-header">
		<tr>
			<th scope="row">#</th>
			<th>Title</th>
			<th>Slug</th>
			<th>Shown?</th>
			<th>Page Tag</th>
			<th>Page</th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
</div>
<div class="page_slug hidden" alt="<?php echo $slug; ?>"></div>
<div class="page_tag hidden" alt="<?php echo $tag; ?>"></div>

<div id="modalPages" class="modal-dialog modal-md modal fade">
	<div class="modal-content">
		<div class="modal-heading">
			<h2 class="text-center">Add New Content</h2>
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
