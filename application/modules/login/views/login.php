<div id="loginForm" class="modal-dialog login-form">
		<div class="modal-content">
			<div class="modal-heading">
				<h2 class="text-center">
					<img width="10%" src="<?php echo base_url('assets/images/favicon.png'); ?>" />
					<br class="hidden-md hidden-lg" />
					<span class="hidden-xs">Administration Site</span>
				</h2>
			</div>
			<hr />
			<div class="modal-body">
				<div class="alert_group alert hidden">
				</div>
				<form action="" role="form">
					<div class="form-group">
						<div class="input-group">
							<span class="input-group-addon">
							<span class="glyphicon glyphicon-user"></span>
							</span>
							<input type="text" id="txtUser" class="form-control" placeholder="User Name" />
						</div>
					</div>
					<div class="form-group">
						<div class="input-group">
							<span class="input-group-addon">
							<span class="glyphicon glyphicon-lock"></span>
							</span>
							<input type="password" id="txtPass" class="form-control" placeholder="Password" />
						</div>
					</div>
					<div class="form-group text-center">
						<button type="button" id="btnLogin" class="btn btn-success btn-lg ripple"
							disabled="disabled" data-processing="Processing">Login</button>
					</div>
					<div class="text-center">Go to <a href="<?php echo ENV['client_url']; ?>" target="_blank">Tanay Tourism Site</a>.</div>
				</form>
			</div>
		</div>
	</div>
