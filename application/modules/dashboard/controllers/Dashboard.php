<?php
if (!defined("BASEPATH"))
    exit("No direct script access allowed");

class Dashboard extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('users/user_model');
		$this->load->model('pages/page_model');
		$this->load->model('news/news_model');
		$this->load->model('hf_management/hf_model');
	}

  public function index() {
    $data = [];

    $this->template->build_template(
      'Dashboard', //Page Title
      array( // Views
        array(
          'view' => 'dashboard',
          'data' => $data
        )
      ),
      array( // JavaScript Files
        'assets/js/modules_js/dashboard.js'
      ),
      array( // CSS Files
        'assets/css/dashboard.css'
      ),
      array( // Meta Tags

      ),
      'backend' // template page
    );
  }

}

?>
