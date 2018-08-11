
<table class="table table-hover table-data" border="1" width="50%" id="tbtlNewsList">
	<thead class="table-header">
		<tr>
			<th scope="row">ID</th>
			<th>Title</th>
			<th>Status</th>
			<th>Date Posted</th>
			<th>Date Updated</th>
			<th>News Type</th>
			<th>Author</th>
      <th></th>
		</tr>
	</thead>
	<tbody>
	</tbody>
	<tfoot>
    <tr>
      <td colspan="12">
        <button type="button" class="btn btn-primary" id="btnAddNewNews" data-target="#modalNews" data-toggle="modal">Add News</button>
      </td>
    </tr>
	</tfoot>
</table>

<div id="modalNews" class="modal-dialog modal-md modal fade">
  <div class="modal-content">
    <div class="modal-heading">
      <h2 id="headerAdd" class="text-center">Add News</h2>
      <h2 id="headerUpdate" class="text-center">Update News</h2>
    </div>
    <hr />
    <div class="modal-body">
      <div class="alert_group alert hidden"></div>
      <form id="UpdateForm">
        <div class="form-group">
            <label for="item_name">Title:</label>
            <input type="hidden" class="form-control field" id="news_id" name="news_id" />
            <input type="text" class="form-control field" id="title" name="title" placeholder="Title" />
            <span class="note"></span>
        </div>
        <div class="form-group">
            <label for="item_name">Content:</label>
            <textarea class="form-control field" id="content" name="content" placeholder="Content"></textarea>
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
            <input type="text" class="form-control field" id="date_posted" name="date_posted" placeholder="Date Posted" />
            <span class="note"></span>
        </div>
        <div class="form-group">
            <label for="item_name">News Type:</label>
            <input type="text" class="form-control field" id="news_type_type_id" name="news_type_type_id" placeholder="News Type" />
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
