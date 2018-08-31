<?php
if (!defined("BASEPATH"))
    exit("No direct script access allowed");

class Hca_management extends MX_Controller {

  private $page_alias;
  private $page_caption;
  private $tag;

	public function __construct()
	{
		parent::__construct();
    $this->page_caption = $this->session->userdata('active_page_caption');
    $this->page_alias = $this->session->userdata('active_page_alias');
    $this->tag = $this->session->userdata('active_page_method');
    $this->page_caption = $this->session->userdata('active_page_caption');
	}

  public function index(){
    echo modules::run('pages', $this->page_alias, $this->page_caption, $this->tag);
  }

  public function history(){
    echo modules::run('pages', $this->page_alias, $this->page_caption, $this->tag);
  }

  public function culture(){
    echo modules::run('pages', $this->page_alias, $this->page_caption, $this->tag);
  }

  public function arts(){
    echo modules::run('pages', $this->page_alias, $this->page_caption, $this->tag);
  }

  public function gallery() {
    echo modules::run('gallery', $this->page_alias);
  }

}
?>
