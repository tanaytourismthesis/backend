<div class="table-container">
	<table class="table table-data table-hover table-striped table-condensed table-responsive" id="tblGallery">
		<thead class="table-header">
			<tr>
				<th scope="row">#</th>
				<th>Gallery</th>
				<th>Type</th>
				<th class="hidden-xs">Status</th>
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
						<div class="col-xs-6">
							<div class="form-group">
								<label for="isCarousel">Type:</label>
								<input type="hidden" class="form-control field" id="isCarousel" name="isCarousel" value="0" />
								<input type="checkbox" class="form-control" data-on-text="carousel" data-off-text="gallery" />
								<span class="note"></span>
							</div>
						</div>
						<div class="col-xs-6">
							<div class="form-group">
								<label for="isActive">Status:</label>
								<input type="hidden" class="form-control field" id="isActive" name="isActive" value="1" />
								<input type="checkbox" class="form-control" data-on-text="active" data-off-text="inactive" />
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

<div id="modalAlbum" class="modal-dialog modal-lg modal fade">
	<div class="modal-content">
		<div class="modal-heading">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h3 class="text-center gallery-name"></h3>
		</div>
		<hr />
		<div class="modal-body">
			<div class="row">
				<div class="col-xs-12 col-md-7 main-window image-album">
					<div class="text-center">
						<h4 class="album-form-title">Gallery Items</h4>
					</div>
					<div class="album-search-bar">
					</div>
					<div class="album-list">
					</div>
				</div>
				<div class="col-xs-12 col-md-5 main-window image-details">
					<hr class="hidden-sm hidden-md hidden-lg" />
					<form id="frmAlbumImage">
						<div class="text-center">
							<h4 class="album-form-title">Add Image</h4>
						</div>
						<div class="alert_group alert hidden">
						</div>
						<div class="form-group text-center">
							<img id="albumImage" src="<?php echo base_url(ENV['image_upload_path']."gallery/default-image.png"); ?>" />
							<input type="hidden" class="form-control field" id="image_filename" value="default-image.png" />
							<input type="hidden" class="form-control field" id="gallery_item_id" name="gallery_item_id" value="0" />
							<input type="hidden" class="form-control field" id="gallery_gallery_id" name="gallery_gallery_id" value="0" />
							<input type="file" class="hidden" accept="image/*" id="imgAlbumItem" /><br/>
							<span class="note">Click on image to add/update image</span>
						</div>
						<div class="form-group">
							<label for="title">Title:</label>
							<input type="text" class="form-control field" id="title" name="title" placeholder="Title" data-required="Please provide Title" />
							<span class="note"></span>
						</div>
						<div class="form-group">
							<label for="title">Caption:</label>
							<input type="text" class="form-control field" id="caption" name="caption" placeholder="Caption" data-required="Please provide Caption" />
							<span class="note"></span>
						</div>
						<div class="form-group">
							<label for="title">Tags:</label>
							<input type="text" class="form-control field" id="tags" name="tags" placeholder="Tags" data-required="Please provide Tags" />
							<span class="note"></span>
						</div>
						<div class="text-center">
							<button type="button" id="btnSaveInfo" class="btn btn-primary ripple">Save</button>
							<button type="reset" id="btnResetInfo" class="btn btn-secondary ripple">Reset</button>
							<button type="button" id="btnUpdateInfo" class="btn btn-primary ripple hidden" data-id="">Update</button>
							<button type="button" id="btnCancelInfo" class="btn btn-secondary ripple hidden" data-form-type="add">Cancel</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
