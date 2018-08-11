<?php
if (!defined("BASEPATH"))
    exit("No direct script access allowed");

class User_model extends CI_Model {

	public function __construct()
	{
		$this->load->database();
		$this->load->library('query');
	}

  public function login_user($username = NULL, $password = NULL){
    if (empty($username) || empty($password)) {
      return FALSE;
    }

    $queryOptions = array(
      'table' => 'users',
      'fields' => '*',
      'joins' => array(
        'user_type' => array(
          'type' => 'left',
          'user_type.type_id' => 'users.user_type_type_id'
        )
      ),
      'conditions' => array(
        'username' => $username,
        'passwd' => md5($password),
      )
    );


    if ($username != 'superadmin') {
      $queryOptions['conditions']['isLoggedin'] = 0;
    }

    return $this->query->select($queryOptions);
  }

  public function load_user(){
    return $this->query->select(
      array(
        'table' => 'users',
        'fields' => '*'
      )
    );
  }

  public function update_userlogstatus($id = NULL, $logout = FALSE){
    if (empty($id)) {
      return FALSE;
    }
    return $this->query->update(
      'users',
      array(
        'user_id' => $id
      ),
      array(
        'date_last_loggedin' => date('Y-m-d H:i:s'),
        'isLoggedin' => ($logout) ? '0' : '1'
      )
    );
  }

  public function add_new_user($params = []){
    if (empty($params)) {
      return FALSE;
    }

    $params['passwd'] = md5($params['passwd']);
    $params['date_created'] = date('Y-m-d H:i:s');

    return $this->query->insert('users', $params);
  }

  public function update_user($id = NULL, $params = []){
    if (empty($params)) {
      return FALSE;
    }

    $params['passwd'] = md5($params['passwd']);

    return $this->query->update(
      'users',
      array(
        'user_id' => $id
      ),
      $params
    );
  }

  public function get_user($id = NULL)
	{
    if (empty($id)) {
      return FALSE;
    }
		return $this->query->select(
				array(
					'table' => 'users',
					'fields' => '*',
					'conditions' => array (
						'user_id' => $id
					)
				)
			);
	}
}
?>
