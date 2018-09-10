<?php
if (!defined("BASEPATH"))
    exit("No direct script access allowed");

class Logout extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('users/user_model');
	}

  public function index() {
    if ($this->session->has_userdata('user_info')) {
      $id = urldecode($this->session->userdata('user_info')['user_id']) ?? 0;
      $result = $this->user_model->update_userlogstatus($id, TRUE);
    }
    $this->session->sess_destroy();
    
    redirect( base_url('login') );  
  }
}

?>
