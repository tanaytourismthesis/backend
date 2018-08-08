<div class="container">
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
	<center>
		<button type="button" id="btnAdd" class="btn btn-primary">Add User</button>
		<div class="formInsert" style="display:none">
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label for="InputUsername">Username</label>
						<input type="email" class="form-control" id="txtUsername" placeholder="Username">
					</div>
					<div class="form-group">
						<label for="InputPassword">Password</label>
						<input type="password" class="form-control" id="txtPassword" placeholder="Password">
					</div>
					<div class="form-group">
						<label for="InputEmail">Enter Email here</label>
						<input type="text" class="form-control" id="txtEmail" placehold="Enter Email Here /">
					</div>
					<div class="form-group">
							<label for="colFormLabelSm" class="col-sm-1 col-form-label col-form-label-sm">First Name</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" id="txtFname" placeholder="First Name" />
							</div>
							<label for="colFormLabelSm" class="col-sm-1 col-form-label col-form-label-sm">Middle Name</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" id="txtMname" placeholder="Middle Name" />
							</div>
							<label for="colFormLabelSm" class="col-sm-1 col-form-label col-form-label-sm">Last Name</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" id="txtLname" placeholder="Last Name" />
							</div>
					</div>
					<div class="form-group">
						<input type="text" class="form-control" id="txtPosition" placeholder="Enter Position" />
					</div>
					<button type="button" id="btnSubmit" class="btn btn-primary">Submit</button>
					<button type="button" id="btnCancel" class="btn btn-primary">Cancel</button>
				</div>
			</div>
		</div>

				<div class="formUpdate" style="display:none">
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label for="InputUsername">Username</label>
						<input type="email" class="form-control" id="txtUsernameUpdate" placeholder="Username">
					</div>
					<div class="form-group">
						<label for="InputPassword">Password</label>
						<input type="password" class="form-control" id="txtPasswordUpdate" placeholder="Password">
					</div>
					<div class="form-group">
						<label for="InputEmail">Enter Email here</label>
						<input type="text" class="form-control" id="txtEmailUpdate" placehold="Enter Email Here" />
					</div>
					<div class="form-group">
							<label for="colFormLabelSm" class="col-sm-1 col-form-label col-form-label-sm">First Name</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" id="txtFnameUpdate" placeholder="First Name" />
							</div>
							<label for="colFormLabelSm" class="col-sm-1 col-form-label col-form-label-sm">Middle Name</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" id="txtMnameUpdate" placeholder="Middle Name" />
							</div>
							<label for="colFormLabelSm" class="col-sm-1 col-form-label col-form-label-sm">Last Name</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" id="txtLnameUpdate" placeholder="Last Name" />
							</div>
							<span id="user_id" style="visibility:hidden">&nbsp;</span>
					</div>
					<div class="form-group">
						<input type="text" class="form-control" id="txtPositionUpdate" placeholder="Enter Position" />
					</div>
					<button type="button" id="btnSubmitUpdate" class="btn btn-primary">Submit</button>
					<button type="button" id="btnCancelUpdate" class="btn btn-primary">Cancel</button>
				</div>
			</div>
		</div>
	</center>
</div>


<!-- <span id="news_id" style="visibility:hidden">&nbsp;</span> -->
