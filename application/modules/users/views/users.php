<div class="table-container">
<table class="table table-data table-hover table-striped table-condensed table-responsive" border="1" width="50%" align="center" id="tblUserList">
	<thead class="table-header">
		<tr>
			<th scope="row">ID</th>
			<th>Username</th>
			<th>Name</th>
			<th>Position</th>
			<th class="hidden-xs">Login status</th>
			<th class="hidden-xs">Last Login Date</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
</div>
<button type="button" id="btnAdd" class="btn btn-primary" data-toggle="modal" data-target="#modalUser">Add User</button>

<div id="modalUser" class="modal-dialog modal-lg modal fade">
	<div class="modal-content">
		<div class="modal-heading">
			<h2 class="text-center">Add User</h2>
		</div>
		<hr />
		<div class="modal-body">
			<div class="row">
				<div class="col-sm-12 col-md-6" id="image_container">
					<div>
						<div class="form-group">
							<img height="250px" width="250px" id="userImage" src="<?php echo base_url(ENV['image_upload_path']."users/default.jpg"); ?>" />
							<input type="hidden" id="userImageFile" value="default.jpg" />
						</div>
						<div class="form-group caption">
							<label>Change image? <input type="checkbox" id="changeImage" /></label><br/>
							<input type="file" accept="image/*" id="imgUser" disabled /><br/>
							<span class="note"></span><br/>
							<button type="button" class="btn btn-primary" id="btnUPDATEPIC" data-id="" disabled>Update Image</button>
							<button type="button" class="btn btn-default" id="btnRESETPIC" disabled>Reset</button>
						</div>
					</div>
				</div>
				<div class="col-sm-12 col-md-6">
					<form class="" id="frmUser">
						<div class="alert_group alert hidden">
						</div>
						<div class="row">
							<div class="col-xs-12 col-sm-6">
									<div class="form-group">
										<label for="first_name">First Name:</label>
										<input type="text" class="form-control field" id="first_name" name="first_name" placeholder="First Name" data-required="Please provide First Name" />
										<span class="note"></span>
									</div>
									<div class="form-group">
											<label for="mid_name">Middle Name:</label>
											<input type="text" class="form-control field" id="mid_name" name="mid_name" placeholder="Middle Name" />
											<span class="note"></span>
									</div>
									<div class="form-group">
											<label for="last_name">Last Name:</label>
											<input type="text" class="form-control field" id="last_name" name="last_name" placeholder="Last Name" data-required="Please provide Last Name" />
											<span class="note"></span>
										</div>
										<div class="form-group">
											<label for="email">Email:</label>
											<input type="email" class="form-control field" id="email" name="email" placeholder="Email Address" data-required="Please provide Email Address" />
											<span class="note"></span>
										</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<div class="form-group">
									<label for="position">Position:</label>
									<input type="text" class="form-control field" id="position" name="position" placeholder="Position" data-required="Please provide Position"  />
									<span class="note"></span>
								</div>
								<div class="form-group">
									<label for="username">Username:</label>
									<input type="text" class="form-control field" id="username" name="username" placeholder="Username" data-required="Please provide Username" disabled />
									<span class="note"></span>
								</div>
								<div class="form-group">
									<label for="password">Password:</label>
									<label><input type="checkbox" id="changePassword"/> Edit password?</label>
									<input type="password" class="form-control field hidden" id="passwd" name="passwd" placeholder="Password" data-required="Please provide Password" />
									<span class="note"></span>
								</div>
								<div class="form-group hidden">
									<label for="confirmpasswd">Confirm:</label>
									<input type="password" class="form-control" id="confirmpasswd" name="confirmpasswd" placeholder="Confirm Password" data-required="Please confirm Password" />
									<span class="note"></span>
								</div>
								<div class="form-group">
									<label for="item_status">User Type:</label>
									<select class="form-control field" id="user_type_type_id" name="user_type_type_id" placeholder="User Type" data-required="Please provide User Type">
										<option value="1">Super Administrator</option>
										<option value="2">Content Writer</option>
										<option value="3">Content Editor</option>
										<option value="4">Page Editor</option>
										<option value="5">Administrator</option>
									</select>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="modal-footer">
				<button type="button" id="btnSave" class="btn btn-primary hidden">Save</button>
				<button type="button" id="btnUpdate" class="btn btn-primary hidden" data-id="">Update</button>
				<button type="button" id="btnCancel" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
			</div>
	</div>
</div>
