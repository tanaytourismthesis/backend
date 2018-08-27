<div class="row search-bar">
	<div class="col-xs-6 input-group">
		<input type="text" class="form-control field" id="search-field" name="search-field" placeholder="Search here..."/>
		<span class="input-group-addon search-button" data-toggle="popover" data-trigger="manual" data-placement="bottom" data-content="Please provide the search key.">
			<i class="glyphicon glyphicon-search"></i>
		</span>
	</div>
</div>
<div class="table-container">
<table class="table table-data table-hover table-striped table-condensed table-responsive" id="tblHCA">
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
	<div class="col-xs-6 navigator-left text-left">
		<button type="button" id="btnAdd" class="btn btn-primary" data-toggle="modal" data-target="#modalUser">Add User</button>
	</div>
	<div class="col-xs-6 navigator-right text-right hidden">
		Page <span class="page_num badge">1</span> of <span class="total_pages badge">1</span>
		(Total Records: <span class="total_records badge">1</span>)
		<button type="button" id="btnPREV" class="btn btn-default"><i class="fas fa-angle-left"></i></button>
		<button type="button" id="btnNEXT" class="btn btn-default"><i class="fas fa-angle-right"></i></button>
	</div>
<div>
