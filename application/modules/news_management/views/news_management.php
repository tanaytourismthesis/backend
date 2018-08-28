<div class="row search-bar">
	<div class="col-xs-6 input-group">
		<input type="text" class="form-control field" id="search-field" name="search-field" placeholder="Search for users here..."/>
		<span class="input-group-addon search-button" data-toggle="popover" data-trigger="manual" data-placement="bottom" data-content="Please provide the search key.">
			<i class="glyphicon glyphicon-search"></i>
		</span>
	</div>
</div>
<div class="table-container">
	<table class="table table-data table-hover table-condensed table-responsive" id="tbtlNewsList">
		<thead class="table-header">
			<tr>
				<th scope="row">ID</th>
				<th>Title</th>
				<th>Status</th>
				<th class="hidden-xs hidden-sm">Date Posted</th>
				<th class="hidden-xs hidden-sm">Date Updated</th>
				<th>News Type</th>
				<th>Author</th>
	      <th></th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>
<div class="row navigator">
	<div class="col-xs-6 navigator-left text-left">
		<button type="button" id="btnAddNewNews" class="btn btn-primary" data-toggle="modal" data-target="#modalNews">Add User</button>
	</div>
	<div class="col-xs-6 navigator-right text-right hidden">
		Page <span class="page_num badge">1</span> of <span class="total_pages badge">1</span>
		(Total Records: <span class="total_records badge">1</span>)
		<button type="button" id="btnPREV" class="btn btn-default"><i class="fas fa-angle-left"></i></button>
		<button type="button" id="btnNEXT" class="btn btn-default"><i class="fas fa-angle-right"></i></button>
	</div>
</div>

<div id="modalNews" class="modal-dialog modal-md modal fade">
  <div class="modal-content">
    <div class="modal-heading">
      <h2 id="headerAdd" class="text-center">Add News</h2>
      <h2 id="headerUpdate" class="text-center">Update News</h2>
    </div>
    <hr />
    <div class="modal-body">
      <form id="UpdateForm">
				<div class="alert_group alert hidden"></div>
        <div class="form-group">
            <label for="item_name">Title:</label>
            <input type="hidden" class="form-control field" id="news_id" name="news_id" />
            <input type="text" class="form-control field" id="title" name="title" placeholder="Title" data-required="Please provide Title" />
            <span class="note"></span>
        </div>
        <div class="form-group">
            <label for="item_name">Content:</label>
            <textarea class="form-control field" id="content" name="content" placeholder="Content" data-required="Please provide the News Content"></textarea>
            <span class="note"></span>
        </div>
        <div class="form-group">
            <label for="item_name">Status:</label>
            <select class="form-control" id="status" name="status">
              <option value="draft">Draft</option>
              <option value="published">Published</option>
              <option value="archived">Archived</option>
              <option value="deleted">Deleted</option>
            </select>
            <span class="note"></span>
        </div>
        <div class="form-group">
					  <label for="item_name">Date Posted:</label>
						<div class='input-group date' id='DateForm'>
								 <input type="text" class="form-control field" id="date_posted" name="date_posted" placeholder="Date Posted" data-required="Please provide the date to be posted"/>
								 <span class="input-group-addon">
										 <span class="glyphicon glyphicon-calendar"></span>
								 </span>
								 <span class="note"></span>
						 </div>
        </div>
        <div class="form-group">
            <label for="item_name">News Type:</label>
						<select class="form-control" id="news_type_type_id" name="news_type_type_id">
						<?php foreach ($news_types as $key => $value) { ?>
							<option value="<?php echo $value['type_id']; ?>"><?php echo $value['type_name']; ?></option>
						<?php } ?>
					</select>
            <span class="note"></span>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button type="button" id="btnUpdate" class="btn btn-primary">Update</button>
      <button type="button" id="btnSave" class="btn btn-primary">Save</button>
      <button type="button" id="btnCancel" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
    </div>
  </div>
</div>
