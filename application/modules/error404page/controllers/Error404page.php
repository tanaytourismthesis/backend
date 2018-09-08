<?php
if (!defined("BASEPATH"))
    exit("No direct script access allowed");

class Error404page extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
	}

  public function index() {
    $data = [];

    $this->template->build_template(
      'Page Not Found', //Page Title
      array( // Views
        array(
          'view' => 'error404page',
          'data' => $data
        )
      ),
      array( // JavaScript Files

      ),
      array( // CSS Files

      ),
      array( // Meta Tags

      ),
      'error404_page' // template page
    );
  }

}

?>
