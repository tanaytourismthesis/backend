<?php
if (!defined("BASEPATH"))
    exit("No direct script access allowed");

class User_model extends CI_Model {

	public function __construct()
	{
		$this->load->database();
		$this->load->library('query');
	}

  public function login_user($username,$password){
    return $this->query->select(
        array(
          'table' => 'users',
          'fields' => '*',
          'conditions' => array(
            'username' => $username,
            'passwd' => md5($password),
            'isLoggedin' => 0
          )
        )
      );
  }

  public function load_user(){
    return $this->query->select(
      array(
        'table' => 'users',
        'fields' => '*'
      )
    );
  }

  public function update_userlogstatus($id, $logout = FALSE){
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
}
?>
