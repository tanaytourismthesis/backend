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
					);
					tbody.append(tr);
				});
			} else {
				tbody.html('<tr><td colspan="100%" align="center">Failed to load news list...</td></tr>');
			}
		});
  }
  load_userlist();
});
