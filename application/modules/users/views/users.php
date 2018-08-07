<h1>Welcome, <?php echo $user_info['username']; ?>!</h1>
[<a href="<?php echo base_url('logout'); ?>">LOGOUT</a>]
<center>
</center>
<table border="1" width="80%" align="center" id="tblUserList">
	<thead>
		<tr>
			<th>ID</th>
			<th>Username</th>
			<th>Name</th>
      <th>Position</th>
      <th>Login status</th>
      <th>Last Login Date</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
	<tfoot>
		<tr>
		</tr>
	</tfoot>
</table>
</center>
