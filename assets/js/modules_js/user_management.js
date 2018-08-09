$(function(){
  function load_userlist(){
    var tbody = $('#tblUserList tbody');
		tbody.html('<tr><td colspan="100%" align="center">Searching news list...</td></tr>');
		//submit data then retrieve from news_model
		$.get(
			'users/load_user' //controllers/slug
		).done(function(data){
			tbody.html(''); // clear table body
			if(data.response) {
				//get each value and create table row/data
				$.each(data.data,function(index,value){
          value['isLoggedin'] = (value['isLoggedin'] > 0) ? 'Active' : 'Inactive';
					var tr = $('<tr></tr>');
					tr.append(
						$('<td></td>').html(value['user_id'])
					).append(
						$('<td></td>').html(value['username'])
					).append(
						$('<td></td>').html(value['first_name'] + ' ' + value['last_name'])
					).append(
						$('<td></td>').html(value['position'])
					).append(
						$('<td></td>').html(value['isLoggedin'])
					).append(
						$('<td></td>').html(value['date_last_loggedin'])
					).append(
						$($('<td></td>').append(
							$(
								'<button class="btn btn-outline-warning"></button>', {
									'id' : 'btnUpdate',
									'data-id': value['user_id']
								}
							).on('click', function() {
								$.get(
									'users/get_user/'+value['user_id']
								).done(function(data){
									if(data.response)
									{
										$('#user_id').html(value['user_id']);
										$('#txtUsernameUpdate').val(data.data[0].username);
										$('#txtPasswordUpdate').val(data.data[0].passwd);
										$('#txtEmailUpdate').val(data.data[0].email);
										$('#txtFnameUpdate').val(data.data[0].first_name);
										$('#txtMnameUpdate').val(data.data[0].mid_name);
										$('#txtLnameUpdate').val(data.data[0].last_name);
										$('#txtPositionUpdate').val(data.data[0].position);
										$('.formUpdate').show();
									}
									else
									{
										console.log(data.message);
									}
								});
							}).html('Edit')
						))
					);
					tbody.append(tr);
				});
			} else {
				tbody.html('<tr><td colspan="100%" align="center">Failed to load news list...</td></tr>');
			}
		});
  }
	load_userlist();

	function add_new_user(username,password,email,fname,mname,lname,position){
		$.post(
			'users/add_new_user',
				{
					username: username,
					password: password,
					email: email,
					fname: fname,
					mname: mname,
					lname: lname,
					position: position
				}
		).done(function(data){
			clearInsert();
			load_userlist();
		})
	}

	function clearUpdate(){
		$('#txtUsernameUpdate').val('');
		$('#txtPasswordUpdate').val('');
		$('#txtEmailUpdate').val('');
		$('#txtFnameUpdate').val('');
		$('#txtMnameUpdate').val('');
		$('#txtLnameUpdate').val('');	
		$('#txtPositionUpdate').val('');
		$('.formUpdate').hide();
	}

	function clearInsert(){
		$('#txtUsername').val('');
		$('#txtPassword').val('');
		$('#txtEmail').val('');
		$('#txtFname').val('');
		$('#txtMname').val('');
		$('#txtLname').val('');	
		$('#txtPosition').val('');
		$('.formInsert').hide();
	}
	
	$('#btnAdd').on('click',function(){
		// console.log("Success click");
		$('.formInsert').show();
	});

	$('#btnCancel').on('click',function(){
		// console.log("Success click");
		$('.formInsert').hide();
	});

	$('#btnSubmit').on('click',function(){
		var username = $('#txtUsername').val();
		var password = $('#txtPassword').val();
		var email = $('#txtEmail').val();
		var fname = $('#txtFname').val();
		var mname = $('#txtMname').val();
		var lname = $('#txtLname').val();
		var position = $('#txtPosition').val();

		add_new_user(username,password,email,fname,mname,lname,position);
	});

	$('#btnCancelUpdate').on('click',function(){
		clearUpdate();
		$('.formUpdate').hide();
	})

	$('#btnSubmitUpdate').on('click',function(){
		var username = $('#txtUsernameUpdate').val();
		var password = $('#txtPasswordUpdate').val();
		var email = $('#txtEmailUpdate').val();
		var fname = $('#txtFnameUpdate').val();
		var mname = $('#txtMnameUpdate').val();
		var lname = $('#txtLnameUpdate').val();
		var position = $('#txtPositionUpdate').val();
		var id = $('#user_id').text();
	
		$.post(
			'users/update_user',
				{
					id: id,
					username: username,
					password: password,
					email: email,
					fname: fname,
					mname: mname,
					lname: lname,
					position: position
				}
		).done(function(data){
			clearUpdate();
			load_userlist();
		})
	});




});