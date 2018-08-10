<?php
if (!defined("BASEPATH"))
    exit("No direct script access allowed");

class Dashboard extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('user_model');
	}

  public function index(){
    $data = [];

    $this->template->build_template(
      'Dashboard | Site Administration', //Page Title
      array( // Views
        array(
          'view' => 'dashboard',
          'data' => $data
        )
      ),
      array( // JavaScript Files

      ),
      array( // CSS Files

      ),
      array( // Meta Tags

      ),
      'backend' // template page
    );
  }
  
}

?>
