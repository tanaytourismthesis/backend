<?php
if (!defined("BASEPATH"))
    exit("No direct script access allowed");

class Gallery extends MX_Controller {

  private $page_caption;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('gallery_model');
	}

  public function index($slug = NULL) {
    $this->page_caption = $this->session->userdata('active_page_caption');

    $data = [
      'slug' => $slug,
      'page_caption' => $this->page_caption
    ];

    $this->template->build_template(
      $this->page_caption, //Page Title
      array( // Views
        array(
          'view' => 'components/search-bar',
          'data' => $data
        ),
        array(
          'view' => 'gallery/gallery',
          'data' => $data
        ),
        array(
          'view' => 'components/navigator',
          'data' => [
            'modal_name' => '#modalGallery',
            'btn_add_label' => 'Add <span class="hidden-xs">Gallery</span>'
          ]
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

  public function load_gallery(){
    $searchkey = $this->input->post('searchkey') ?? NULL;
		$limit = $this->input->post('limit') ?? NULL;
		$start = $this->input->post('start') ?? NULL;
		$id = $this->input->post('id') ?? NULL;
		$slug = $this->input->post('slug') ?? NULL;

    $data['response'] = FALSE;

    try {
      if ($searchkey === NULL || $start === NULL || $limit === NULL) {
  			throw new Exception("LOAD_GALLERY: Invalid parameter(s)");
  		}

      $params = [
        'searchkey' => $searchkey,
        'start' => $start,
        'limit' => $limit,
        'id' => urldecode($id),
        'slug' => str_replace('manage-', '', $slug),
      ];

      $result = $this->gallery_model->load_gallery($params);

      $data['message'] = $result['message'];

      if (!empty($result) && $result['code'] == 0 && !empty($result['data'])) {
        $data['response'] = TRUE;
        $data['data'] = $result['data'];
      }
    } catch (Exception $e) {
      $data['message'] = $e->getMessage();
    }

    header( 'Content-Type: application/x-json' );
    echo json_encode( $data );
  }

}
?>
