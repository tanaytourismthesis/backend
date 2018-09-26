<div class="container-fluid tab-container">
  <ul class="nav nav-tabs tab-items">
    <li class="active">
      <a href="#hanes"><i class="fas fa-hotel"></i>&nbsp;H.A.N.E.s</a>
    </li>
    <li>
      <a href="#metrics"><i class="fas fa-tachometer-alt"></i>&nbsp;H.A.N.E. Metrics</a>
    </li>
  </ul>
  <div id="hanes" class="tab-content">
    <div class="row search-bar">
    	<div class="col-xs-12 col-sm-9 input-group">
    		<input type="text" class="form-control" id="search-field" name="search-field" placeholder="Search here..."/>
    		<span class="input-group-addon search-button ripple" data-toggle="popover" data-trigger="manual" data-placement="bottom" data-content="Please provide the search key.">
    			<i class="glyphicon glyphicon-search"></i>
    		</span>
    		<span class="input-group-addon reload-list btn btn-success ripple"><i class="fas fa-sync"></i></span>
    	</div>
    </div>
    <div class="table-container">
    	<table class="table table-data table-hover table-striped table-condensed table-responsive" id="tblHANE">
    		<thead class="table-header">
    			<tr>
    				<th scope="row">#</th>
    				<th width="20%" class="hidden-xs">&nbsp</th>
    				<th>Name</th>
    				<th>&nbsp;</th>
    			</tr>
    		</thead>
    		<tbody>
    		</tbody>
    	</table>
    </div>
    <div class="row navigator navigator-green">
    	<div class="col-xs-4 col-sm-6 text-left">
    		<button type="button"
    						id="btnAdd"
    						class="btn btn-primary ripple"
    						data-toggle="modal"
    						data-target="#modalHANE">
    			Add <span class="hidden-xs">H.A.N.E.</span>
    		</button>
    	</div>
    	<div class="col-xs-8 col-sm-6 text-right navigator-fields hidden">
    		<span class="hidden-xs">Page</span>
    		<span class="page_num badge">1</span> of <span class="total_pages badge">1</span>
    		<span class="hidden-xs">
          (Total Records: <span class="total_records badge">1</span>)
        </span>
    		<span class="navigator-buttons">&nbsp;</span>
    	</div>
    </div>
    <div id="modalHANE" class="modal-dialog modal-lg modal fade">
      <div class="modal-content">
        <div class="modal-heading">
          <h2 id="headerAdd" class="text-center">Add H.A.N.E.</h2>
          <h2 id="headerUpdate" class="text-center">Update H.A.N.E.</h2>
        </div>
        <hr />
        <div class="modal-body">
          <div class="alert_group alert hidden"></div>
          <div class="form-group">
            <label for="hotel_name">Name:</label>
            <input type="hidden" class="form-control field" id="hotel_id" name="news_id" />
            <input type="text" class="form-control field" id="hotel_name" name="hotel_name" placeholder="Hotel Name" data-required="Please provide Hotel Name." />
            <span class="note"></span>
          </div>
          <div class="form-group">
            <label for="address">Address:</label>
            <input type="text" class="form-control field" id="address" name="address" placeholder="Address" data-required="Please provide Address." />
            <span class="note"></span>
          </div>
          <div class="form-group">
            <label for="contact">Contact:</label>
            <input type="text" class="form-control field" id="contact" name="contact" placeholder="Contact" data-required="Please provide Contact." />
            <span class="note"></span>
          </div>
          <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control field" id="email" name="email" placeholder="Email Address" data-required="Please provide valid Email Address." />
            <span class="note"></span>
          </div>
          <div class="form-group">
            <label for="url">Website:</label>
            <input type="text" class="form-control field" id="url" name="url" placeholder="Website Address" data-required="Please provide valid Website Address." />
            <span class="note"></span>
          </div>
          <div class="form-group">
            <label for="isActive">Status:</label>
            <input type="hidden" class="form-control field" id="isActive" name="isActive" value="1" />
            <input type="checkbox" class="form-control" data-on-text="active" data-off-text="inactive" />
            <span class="note"></span>
          </div>
          <div>
            <button type="button" id="btnUpdate" class="btn btn-primary">Update</button>
            <button type="button" id="btnSave" class="btn btn-primary">Save</button>
            <button type="button" id="btnCancel" class="btn btn-default" data-dismiss="modal">Cancel</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div id="metrics" class="tab-content">
    HANEs metrics here...
  </div>
</div>
