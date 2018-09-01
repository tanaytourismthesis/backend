<div class="table-container">
	<table class="table table-data table-hover table-striped table-condensed table-responsive" id="tblGallery">
		<thead class="table-header">
			<tr>
				<th scope="row">#</th>
				<th>Gallery</th>
				<th>Status</th>
				<th>Type</th>
				<th>Page</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>
<div class="page_slug hidden" alt="<?php echo $slug; ?>"></div>

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
