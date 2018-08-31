<?php
if (!defined("BASEPATH"))
    exit("No direct script access allowed");

class Pages extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('page_model');
	}

  public function index($slug = NULL, $caption = NULL){
    $data = [
      'slug' => $slug,
      'caption' => $caption
    ];

    $this->template->build_template(
      $caption, //Page Title
      array( // Views
        array(
          'view' => 'pages/pages',
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
