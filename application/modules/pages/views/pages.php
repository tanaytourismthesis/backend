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
			<form id="AddPageContent">
				<div class="alert_group alert hidden"></div>
				<div class="form-group">
					<label for="item_name">Title:</label>
					<input type="hidden" class="form-control field" id="news_id" name="news_id" />
					<input type="text" class="form-control field" id="title" name="title" placeholder="Title" data-required="Please provide Title" />
					<span class="note"></span>
				</div>
				<div class="form-group">
					<label for="item_name">Content:</label>
					<textarea class="form-control field" id="content" name="content" placeholder="Content" data-required="Please provide the Content"></textarea>
					<span class="note"></span>
				</div>
				<div class="form-group">
					<label for="item_name">Tag:</label>
					<select class="form-control" id="tag" name="tag">
						<option value="history">History</option>
						<option value="culture">Culture</option>
						<option value="arts">Arts</option>
						<option value="people">People</option>
						<option value="places">Places</option>
						<option value="festival">Festival</option>
						<option value="cuisine">Cuisine</option>
					</select>
					<span class="note"></span>
				</div>
				<div class="form-group">
					<label for="item_name">Keywords:</label>
					<input type="text" class="form-control field" id="keywords" name="keywords" placeholder="Keywords" data-required="Please provide Keywords" />
					<span class="note"></span>
				</div>
				<div class="form-group">
					<label for="item_name">Order Position:</label>
					<input type="text" class="form-control field" id="order_position" name="order_position" placeholder="Order Position" data-required="Please provide Order Position" />
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
				<div class="row">
					<div class="col-xs-12 col-sm-6">
						<div class="form-group">
						<label for="isShown">Shown?:</label>
							<input type="hidden" class="form-control field" id="isShown" name="isShown" value="1" />
							<input type="checkbox" class="form-control" data-on-text="Yes" data-off-text="No" checked />
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
