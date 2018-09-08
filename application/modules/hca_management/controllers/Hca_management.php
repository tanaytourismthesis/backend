<?php
if (!defined("BASEPATH"))
    exit("No direct script access allowed");

class Hca_management extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
	}

  public function index() {
    echo modules::run('pages');
  }

  public function history() {
    echo modules::run('pages');
  }

  public function culture() {
    echo modules::run('pages');
  }

  public function arts() {
    echo modules::run('pages');
  }

  public function gallery() {
    echo modules::run('gallery');
  }

}
?>
