<?php
if (!defined("BASEPATH"))
    exit("No direct script access allowed");

class Pages extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('page_model');
	}

  public function index($slug = NULL, $caption = NULL, $tag = NULL){
    $data = [
      'slug' => $slug,
      'caption' => $caption,
      'tag' => $tag
    ];

    $this->template->build_template(
      $caption, //Page Title
      array( // Views
        array(
          'view' => 'components/search-bar',
          'data' => $data
        ),
        array(
          'view' => 'pages/pages',
          'data' => $data
        ),
        array(
          'view' => 'components/navigator',
          'data' => [
            'modal_name' => '#modalPages',
            'btn_add_label' => 'Add <span class="hidden-xs">Content</span>'
          ]
        )
      ),
      array( // JavaScript Files
        'assets/js/modules_js/pages.js'
      ),
      array( // CSS Files

      ),
      array( // Meta Tags

      ),
      'backend' // template page
    );
  }

  public function load_pages(){
    $searchkey = $this->input->post('searchkey') ?? NULL;
		$limit = $this->input->post('limit') ?? NULL;
		$start = $this->input->post('start') ?? NULL;
		$id = $this->input->post('id') ?? NULL;
		$slug = $this->input->post('slug') ?? NULL;
		$tag = $this->input->post('tag') ?? NULL;

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
        'slug' => $slug,
        'tag' => $tag
      ];

      $result = $this->page_model->load_pagelist($params);

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
