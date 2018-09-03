<div class="table-container">
	<table class="table table-data table-hover table-striped table-condensed table-responsive" id="tblGallery">
		<thead class="table-header">
			<tr>
				<th scope="row">#</th>
				<th>Gallery</th>
				<th>Type</th>
				<th>Status</th>
				<th>Page</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>
<div class="page_slug hidden" alt="<?php echo $slug; ?>"></div>

<div id="modalGallery" class="modal-dialog modal-sm modal fade">
	<div class="modal-content">
		<div class="modal-heading">
			<h2 class="text-center">Add New Gallery</h2>
		</div>
		<hr />
		<div class="modal-body">
			<form id="frmGallery">
					<div class="alert_group alert hidden">
					</div>
					<div class="row">
						<div class="col-xs-12">
							<div class="form-group">
								<label for="gallery_name">Gallery Name:</label>
								<input type="text" class="form-control field" id="gallery_name" name="gallery_name" placeholder="Gallery Name" data-required="Please provide Gallery Name" />
								<span class="note"></span>
							</div>
							<div class="form-group">
								<label for="item_status">On Page:</label>
								<select class="form-control field" id="page_page_id" name="page_page_id" data-required="Please select Page">
									<?php foreach ($pagelist as $value): ?>
										<option value="<?php echo $value['page_id']; ?>"><?php echo $value['page_name']; ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12 col-sm-6">
							<div class="form-group">
								<label for="isCarousel">Type:</label>
								<input type="hidden" class="form-control field" id="isCarousel" name="isCarousel" value="0" />
								<input type="checkbox" class="form-control" data-on-text="carousel" data-off-text="gallery" />
								<span class="note"></span>
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							<div class="form-group">
								<label for="isActive">Status:</label>
								<input type="hidden" class="form-control field" id="isActive" name="isActive" value="1" />
								<input type="checkbox" class="form-control" data-on-text="active" data-off-text="inactive" checked />
								<span class="note"></span>
							</div>
						</div>
					</div>
			</form>
		</div>
		<div class="modal-footer">
				<button type="button" id="btnSave" class="btn btn-primary ripple hidden">Save</button>
				<button type="button" id="btnUpdate" class="btn btn-primary ripple hidden" data-id="">Update</button>
				<button type="button" id="btnCancel" class="btn btn-secondary ripple" data-dismiss="modal">Cancel</button>
			</div>
	</div>
</div>
