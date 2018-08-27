<?php
if (!defined("BASEPATH"))
    exit("No direct script access allowed");

class Hca_management extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
	}

  public function index(){
    $data = [];

    $this->template->build_template(
      'History, Culture, Arts', //Page Title
      array( // Views
        array(
          'view' => 'hca_management',
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

  public function gallery() {
    $module = str_replace( "/", "", $this->router->fetch_module() );
    $url = get_route_alias($module, $this->router->routes);
    echo modules::run('gallery', $url);
  }

}
?>
