<?php
if (!defined("BASEPATH"))
    exit("No direct script access allowed");

class Fc_management extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
	}

  public function index() {
    echo modules::run('pages');
  }

  public function festival() {
    echo modules::run('pages');
  }

  public function cuisine() {
    echo modules::run('pages');
  }

  public function gallery() {
    echo modules::run('gallery');
  }

}
?>
