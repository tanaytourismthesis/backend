<h1>Welcome, <?php echo $user_info['username']; ?>!</h1>
[<a href="<?php echo base_url('logout'); ?>">LOGOUT</a>]
<center>
<table class="table table-hover" border="1" width="50%" align="center" id="tblUserList">
	<thead class="thead-dark">
		<tr>
			<th scope="row">ID</th>
			<th>Username</th>
			<th>Name</th>
			<th>Position</th>
			<th>Login status</th>
			<th>Last Login Date</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
	<tfoot>
	</tfoot>
</table>
</center>
	<button type="button" id="btnAdd" class="btn btn-primary" data-toggle="modal" data-target="#modalAddUser">Add User</button>
  <div id="modalAddUser" class="modal-dialog modal-lg modal fade">
		<div class="modal-content">
			<div class="modal-heading">
				<h2 class="text-center">Add User</h2>
			</div>
			<hr />
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-4" id="image_container">
						<div>
							<div class="form-group" id="frmItemImage">
								<img src="" height="250px" width="250px" />
							</div>
							<div class="form-group caption">
								Change image?
								<input type="checkbox" id="changeImage" />
								<div class='input-group'>
									<input type="file" accept=".jpg" id="imgItem" disabled />
									<span class="note"></span><br />
									<button type="button" class="btn btn-default" id="btnRESETPIC" disabled>Reset</button>
									<button type="button" class="btn btn-primary" id="btnUPDATEPIC" disabled>Update Image</button>
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-8">
						<form class="" id="frmAddUser">
							<div class="alert_group alert hidden">
							</div>
							<div class="row">
								<div class="col-xs-6">
										<div class="form-group">
											<label for="item_name">First Name:</label>
											<input type="text" class="form-control field" id="first_name" name="first_name" placeholder="First Name" data-required="Please provide First Name" />
											<span class="note"></span>
										</div>
										<div class="form-group">
												<label for="item_name">Middle Name:</label>
												<input type="text" class="form-control field" id="mid_name" name="mid_name" placeholder="Middle Name" />
												<span class="note"></span>
										</div>
										<div class="form-group">
												<label for="item_name">Last Name:</label>
												<input type="text" class="form-control field" id="last_name" name="last_name" placeholder="Last Name" data-required="Please provide Last Name" />
												<span class="note"></span>
											</div>
											<div class="form-group">
													<label for="item_category">Email:</label>
													<input type="email" class="form-control field" id="email" name="email" placeholder="Position" data-required="Please provide Position"  />
													<span class="note"></span>
												</div>
											<div class="form-group">
												<label for="item_category">Position:</label>
												<input type="text" class="form-control field" id="position" name="position" placeholder="Position" data-required="Please provide Position"  />
												<span class="note"></span>
											</div>
								</div>
								<div class="col-xs-6">
										<div class="form-group">
												<label for="Username">Username:</label>
												<input type="text" class="form-control field" id="username" name="username"  placeholder="Username" data-required="Please provide Username"  />
												<span class="note"></span>
											</div>
											<div class="form-group">
												<label for="Password">Password:</label>
												<input type="password" class="form-control field" id="passwd" name="passwd" placeholder="Password" data-required="Please provide Password"  />
												<span class="note"></span>
											</div>
											<div class="form-group">
												<label for="ConfirmPassword">Confirm Password:</label>
												<input type="password" class="form-control field" id="confirmpasswd" name="confirmpasswd" placeholder="Confirm Password" data-required="Please Confirm your password" />
												<span class="note"></span>
											</div>
											<div class="form-group">
												<label for="item_status">User Type:</label>
												<select class="form-control" id="user_type_type_id" name="user_type_type_id">
													<option value="2" selected>Content Writer</option>
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
					<button type="button" id="btnSave" class="btn btn-primary">Save</button>
					<button type="button" id="btnCancel" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
				</div>
		</div>
	</div>