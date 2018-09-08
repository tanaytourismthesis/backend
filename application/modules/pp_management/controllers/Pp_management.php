<?php
if (!defined("BASEPATH"))
    exit("No direct script access allowed");

class Pp_management extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
	}

  public function index() {
    echo modules::run('pages');
  }

  public function people() {
    echo modules::run('pages');
  }

  public function places() {
    echo modules::run('pages');
  }

  public function gallery() {
    echo modules::run('gallery');
  }

}
?>
