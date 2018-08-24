<?php
if (!defined("BASEPATH"))
    exit("No direct script access allowed");

class Gallery extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
	}

  public function index($page = NULL) {
    $data = [
      'page' => $page,
      'page_caption' => $this->get_page_caption($page);
    ];

    $this->template->build_template(
      'News', //Page Title
      array( // Views
        array(
          'view' => 'gallery',
          'data' => $data
        )
      ),
      array( // JavaScript Files
        'assets/js/modules_js/gallery.js'
      ),
      array( // CSS Files

      ),
      array( // Meta Tags

      ),
      'backend' // template page
    );
  }

  private function get_page_caption() {
		$menu_items = $this->session->userdata('user_info')['menu_items'] ?? [];
		foreach ($menu_items as $menu) {
			if ($menu['controller'] == $this->url) {
				return $menu['caption'];
			}
		}
		return '';
	}

}
?>
