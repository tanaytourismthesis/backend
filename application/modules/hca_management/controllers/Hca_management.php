<?php
if (!defined("BASEPATH"))
    exit("No direct script access allowed");

class Hca_management extends MX_Controller {

  private $page_module;
  private $page;
  private $page_caption;
  private $slug;

	public function __construct()
	{
		parent::__construct();
    $this->page_module = $this->router->fetch_module();
    $this->page = str_replace( "/", "", $this->page_module );
    $this->page_caption = get_page_caption($this->slug, $this->session->userdata('user_info')['menu_items']);
	}

  public function index(){
    $this->slug = get_route_alias($this->page, $this->router->routes);
    echo modules::run('page', $this->slug, $this->page_caption);
  }

  public function gallery() {
    $this->slug = get_route_alias($this->page, $this->router->routes);
    echo modules::run('gallery', $this->slug);
  }

}
?>
