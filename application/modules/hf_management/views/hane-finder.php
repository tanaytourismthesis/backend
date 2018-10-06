<div class="container-fluid tab-container">
  <ul class="nav nav-tabs main-tab-items tab-items">
    <li class="active">
      <a href="#hanes"><i class="fas fa-hotel"></i>&nbsp;H.A.N.E.s</a>
    </li>
    <li>
      <a href="#metrics"><i class="fas fa-tachometer-alt"></i>&nbsp;H.A.N.E. Metric Settings</a>
    </li>
  </ul>
  <div id="hanes" class="main-tab-content tab-content">
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
    				<th>Status</th>
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
    <div id="modalViewMap" class="modal-dialog modal-lg modal fade">
      <div class="modal-content">
        <div class="modal-heading">
          <h2 class="text-center"><span class="modal-title"></span> on the Map</h2>
        </div>
        <hr />
        <div class="modal-body">
          <div id="viewMap"></div>
        </div>
        <div class="modal-footer">
          <button type="button" id="btnClose" class="btn btn-default ripple" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
    <div id="modalHANE" class="modal-dialog modal-lg modal fade">
      <div class="modal-content">
        <div class="modal-heading">
          <h2 class="text-center"><span class="modal-title"></span> H.A.N.E.</h2>
        </div>
        <hr />
        <div class="modal-body">
          <div class="alert_group alert hidden"></div>
          <div class="form-group text-center">
            <img id="haneImage" src="<?php echo base_url(ENV['image_upload_path']."hane/default-hane.jpg"); ?>" />
            <input type="hidden" class="form-control field" id="hotel_image" name="hotel_image" value="default-hane.jpg" />
            <input type="file" class="hidden" accept="image/*" id="imgHane" /><br/>
            <span class="note text-bold">Click on image to add/update image.</span>
          </div>
          <div class="form-group">
            <label for="hotel_name">Name:</label>
            <input type="hidden" class="form-control field" id="hotel_id" name="hotel_id" />
            <input type="text" class="form-control field" id="hotel_name" name="hotel_name" placeholder="Hotel Name" data-required="Please provide H.A.N.E. Name." />
            <span class="note"></span>
          </div>
          <div class="form-group">
            <label for="address">Address:</label>
            <input type="text" class="form-control field" id="address" name="address" placeholder="Address" data-required="Please provide Address." />
            <span class="note"></span>
          </div>
          <div class="form-group">
            <label for="amenities">Amenities:</label>
            <textarea class="form-control field" id="amenities" name="amenities" placeholder="Amenities" data-required="Please provide Amenities."></textarea>
            <span class="note"></span>
          </div>
          <div class="form-group">
            <label for="longhitude">Longhitude:</label>
            <input type="text" class="form-control field" id="longhitude" name="longhitude" placeholder="Longhitude" data-required="Please provide valid Longhitude." />
            <span class="note"></span>
          </div>
          <div class="form-group">
            <label for="latitude">Latitude:</label>
            <input type="text" class="form-control field" id="latitude" name="latitude" placeholder="Latitude" data-required="Please provide valid Latitude." />
            <span class="note"></span>
          </div>
          <div class="form-group">
            <label for="contact">Contact:</label>
            <input type="text" class="form-control field" id="contact" name="contact" placeholder="Contact" data-required="Please provide valid Contact Number." />
            <span class="note"></span>
          </div>
          <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control field" id="email" name="email" placeholder="Email Address" data-required="Please provide valid Email Address. (e.g. youremai@email.com)" />
            <span class="note"></span>
          </div>
          <div class="form-group">
            <label for="url">Website:</label>
            <input type="url" class="form-control field" id="url" name="url" placeholder="Website Address" />
            <span class="note"></span>
          </div>
          <div class="form-group">
            <label for="isActive">Status:</label>
            <input type="hidden" class="form-control field" id="isActive" name="isActive" value="1" />
            <input type="checkbox" class="form-control" data-on-text="active" data-off-text="inactive" />
            <span class="note"></span>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" id="btnUpdate" class="btn btn-primary ripple" data-caption="Update" data-processing="Processing">Update</button>
          <button type="button" id="btnSave" class="btn btn-primary ripple" data-caption="Save" data-processing="Processing">Save</button>
          <button type="button" id="btnCancel" class="btn btn-default ripple" data-dismiss="modal">Cancel</button>
          <button type="button" id="btnResetImage" class="btn btn-warning ripple hidden">Reset Image</button>
        </div>
      </div>
    </div>
    <div id="modalHaneRooms" class="modal-dialog modal-lg modal fade">
    	<div class="modal-content">
    		<div class="modal-heading">
    			<button type="button" class="close" data-dismiss="modal">&times;</button>
    			<h3 class="text-center hane-name"></h3>
    			<hr/>
    		</div>
    		<div class="modal-body">
    			<div class="row">
    				<div class="col-xs-12 col-md-12 main-window hane-rooms">
    					<div class="room-search-bar">
    						<div class="col-xs-12 input-group">
    					    <span class="input-group-addon hidden-xs">Search</span>
    							<input type="text" class="form-control" id="room-search-field" name="room-search-field" placeholder="Enter search key here..."/>
    							<span class="input-group-addon room-search-button ripple" data-hane="0" data-toggle="popover" data-trigger="manual" data-placement="bottom" data-content="Please provide the search key.">
    								<i class="glyphicon glyphicon-search"></i>
    							</span>
    							<span class="input-group-addon room-reload-list btn btn-success ripple" data-hane="0"><i class="fas fa-sync"></i></span>
    							<span class="input-group-addon room-add-item btn btn-warning ripple"><i class="fas fa-plus-circle"></i></span>
    						</div>
    					</div>
    					<hr/>
    					<div class="room-list">
    					</div>
    					<div class="room-navigator">
    						<div class="current-page hidden">1</div>
    						<div class="total-pages hidden">1</div>
    						<div class="total-records hidden">1</div>
    						<div class="room-navigator-buttons text-center">
    						</div>
    					</div>
    					<hr class="hidden-md hidden-lg" />
    				</div>
    				<div class="col-xs-12 hidden-xs hidden-sm col-md-5 main-window room-details">
    					<form id="frmHaneRoom">
    						<div class="text-center">
    							<h4><span class="room-form-title"></span> Room</h4>
    							<a id="closeRoomDetails"><i class="fas fa-times-circle"></i></a>
    						</div>
    						<div class="alert_group alert hidden">
    						</div>
    						<div class="form-group text-center">
    							<img id="roomImage" src="<?php echo base_url(ENV['image_upload_path']."hane/default-hane.jpg"); ?>" />
    							<input type="hidden" class="form-control field" id="room_image" name="room_image" value="default-hane.jpg" />
    							<input type="hidden" class="form-control field" id="room_id" name="room_id" value="0" />
    							<input type="hidden" class="form-control field" id="hotel_hotel_id" name="hotel_hotel_id" value="0" />
    							<input type="file" class="hidden" accept="image/*" id="imgRoom" /><br/>
    							<span class="note text-bold">Click on image to add/update image.</span>
    						</div>
    						<hr/>
    						<div class="form-group">
    							<label for="room_name">Name:</label>
    							<input type="text" class="form-control field" id="room_name" name="room_name" placeholder="Room Name" data-required="Please provide Room Name" />
    							<span class="note"></span>
    						</div>
    						<div class="form-group">
    							<label for="capacity">Capacity:</label>
    							<input type="number" class="form-control field" min="1" max="10" id="capacity" name="capacity" placeholder="Capacity" data-required="Please provide Capacity" />
    							<span class="note"></span>
    						</div>
    						<div class="form-group">
    							<label for="quantity">Number of Rooms:</label>
    							<input type="number" class="form-control field" min="1" max="99" id="quantity" name="quantity" placeholder="Quantity" data-required="Please provide Quantity" />
    							<span class="note"></span>
    						</div>
    						<div class="form-group">
    							<label for="room_rate_day">Room Rate (Day):</label>
    							<input type="text" class="form-control field" name="room_rate_day" placeholder="Room Rate (Day)" data-required="Please provide Room Rate (Day)" />
    							<span class="note"></span>
    						</div>
    						<div class="form-group">
    							<label for="room_rate_night">Room Rate (Night):</label>
    							<input type="text" class="form-control field" name="room_rate_night" placeholder="Room Rate (Night)" data-required="Please provide Room Rate (Night)" />
    							<span class="note"></span>
    						</div>
    						<div class="form-group">
    							<label for="inclusive_features">Inclusive Features:</label>
    							<textarea class="form-control field" id="inclusive_features" name="inclusive_features" placeholder="Inclusive Features" data-required="Please provide Inclusive Features"></textarea>
    							<span class="note"></span>
    						</div>
    						<div class="text-center">
    							<button type="button" id="btnSaveInfo" class="btn btn-primary ripple" data-caption="Save" data-processing="Processing">Save</button>
    							<button type="reset" id="btnResetInfo" class="btn btn-default ripple">Reset</button>
    							<button type="button" id="btnUpdateInfo" class="btn btn-primary ripple hidden" data-id="" data-caption="Update" data-processing="Processing">Update</button>
    							<button type="button" id="btnCancelInfo" class="btn btn-default ripple hidden">Cancel</button>
    							<button type="button" id="btnResetImageInfo" class="btn btn-warning ripple hidden">Reset Image</button>
    						</div>
    					</form>
    				</div>
    			</div>
    		</div>
    	</div>
    </div>
    <div id="modalHaneMetrics" class="modal-dialog modal-lg modal fade">
      <div class="modal-content">
    		<div class="modal-heading">
    			<button type="button" class="close" data-dismiss="modal">&times;</button>
    			<h2 class="text-center"><span class="modal-title"></span> H.A.N.E. Metrics</h2>
    			<hr/>
    		</div>
    		<div class="modal-body">
          <div class="container-fluid tab-container">
            <ul class="nav nav-tabs metric-tab-items tab-items">
              <li class="active">
                <a href="#add-hane-metrics"><i class="fas fa-tachometer-alt"></i>&nbsp;Add Metric</a>
              </li>
              <li>
                <a href="#hane-metrics-1">Record #1</a>
              </li>
              <li>
                <a href="#hane-metrics-2">Record #2</a>
              </li>
            </ul>
            <div id="add-hane-metrics" class="metric-tab-content tab-content">
              Add H.A.N.E. Metrics Form
            </div>
            <div id="hane-metrics-1" class="metric-tab-content tab-content">
              Metrics Record #1
            </div>
            <div id="hane-metrics-2" class="metric-tab-content tab-content">
              Metrics Record #2
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" id="btnCloseMetric" class="btn btn-default ripple hidden">Close</button>
        </div>
      </div>
    </div>
  </div>
  <div id="metrics" class="main-tab-content tab-content">
    <div class="row search-bar">
    	<div class="col-xs-12 col-sm-9 input-group">
    		<input type="text" class="form-control" id="metrics-search-field" name="metrics-search-field" placeholder="Search here..."/>
    		<span class="input-group-addon metrics-search-button ripple" data-toggle="popover" data-trigger="manual" data-placement="bottom" data-content="Please provide the search key.">
    			<i class="glyphicon glyphicon-search"></i>
    		</span>
    		<span class="input-group-addon metrics-reload-list btn btn-success ripple"><i class="fas fa-sync"></i></span>
    	</div>
    </div>
    <div class="table-container">
    	<table class="table table-data table-hover table-striped table-condensed table-responsive" id="tblMetrics">
    		<thead class="table-header">
    			<tr>
    				<th scope="row">#</th>
    				<th width="20%">Metric Name</th>
    				<th>Formula</th>
    				<th>Variable1</th>
    				<th>Variable2</th>
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
    						id="btnAddMetric"
    						class="btn btn-primary ripple"
    						data-toggle="modal"
    						data-target="#modalMetric">
    			Add <span class="hidden-xs">Metric</span>
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
    <div id="modalMetric" class="modal-dialog modal-lg modal fade">
      <div class="modal-content">
        <div class="modal-heading">
          <h2 class="text-center"><span class="modal-title"></span> H.A.N.E. Metric</h2>
        </div>
        <hr />
        <div class="modal-body">
          <div class="alert_group alert hidden"></div>
          <div class="form-group">
            <label for="metric_name">Metric Name:</label>
            <input type="hidden" class="form-control field" id="hotel_id" name="hotel_id" />
            <input type="text" class="form-control field" id="metric_name" name="metric_name" placeholder="Metric Name" data-required="Please provide Metric Name." />
            <span class="note"></span>
          </div>
          <div class="form-group">
            <label for="formula">Formula:</label>
            <input type="text" class="form-control field" id="formula" name="formula" placeholder="Formula" data-required="Please provide Formula." />
            <span class="note"></span>
          </div>
          <div class="form-group">
            <label for="variable1">Variable 1:</label>
            <input type="text" class="form-control field" id="variable1" name="variable1" placeholder="Variable 1" data-required="Please provide Variable 1" />
            <span class="note"></span>
          </div>
          <div class="form-group">
            <label for="variable2">Variable 2:</label>
            <input type="text" class="form-control field" id="variable2" name="variable2" placeholder="Variable 2" data-required="Please provide Variable 2" />
            <span class="note"></span>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" id="btnUpdate" class="btn btn-primary ripple" data-caption="Update" data-processing="Processing">Update</button>
          <button type="button" id="btnSave" class="btn btn-primary ripple" data-caption="Save" data-processing="Processing">Save</button>
          <button type="button" id="btnCancel" class="btn btn-default ripple" data-dismiss="modal">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</div>
