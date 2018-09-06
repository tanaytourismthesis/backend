<?php
if (!defined("BASEPATH"))
    exit("No direct script access allowed");

class Gallery extends MX_Controller {

  private $page_alias;
  private $page_caption;
  private $page_icon;
  private $tag;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('gallery_model');
    $this->page_caption = $this->session->userdata('active_page_caption');
    $this->page_alias = $this->session->userdata('active_page_alias');
    $this->tag = $this->session->userdata('active_page_method');
    $this->page_icon = $this->session->userdata('active_page_icon');
	}

  public function index() {

    $slug = str_replace('manage-', '', $this->page_alias);
    $pagelist_result = modules::run('pages/load_pagelist', '', 0, 0, $slug, FALSE);

    $pagelist = ($pagelist_result['response']) ? $pagelist_result['data']['records'] : [];

    $data = [
      'slug' => $this->page_alias,
      'icon' => $this->page_icon,
      'pagelist' => $pagelist
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
  			throw new Exception("LOAD GALLERY: Invalid parameter(s)");
  		}

      $params = [
        'searchkey' => $searchkey,
        'start' => $start,
        'limit' => $limit,
        'id' => urldecode($id),
        'slug' => str_replace('manage-', '', $slug)
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

  public function update_gallery($params = [], $ajax = TRUE){
    $data['response'] = FALSE;
    $params = ($ajax) ? $this->input->post('params') : $params;
    $params = format_parameters(clean_parameters($params, []));
    $id = $params['gallery_id'] ?? 0;

    if (isset($params['gallery_id'])) {
      unset($params['gallery_id']);
    }

		try {
      if (empty($id)) {
        throw new Exception('UPDATE GALLERY: Invalid parameter(s)');
      }

			$result = $this->gallery_model->update_gallery($id, $params);
      $data['message'] = $result['message'];

			if (!empty($result) && $result['code'] == 0){
				$data['response'] = TRUE;
				$data['message'] = 'Successfully updated gallery.';
			}
		} catch (Exception $e) {
			$data['message'] = $e->getMessage();
		}

    if ($ajax) {
  		header( 'Content-Type: application/x-json' );
  		echo json_encode( $data );
    }
    return $data;
  }

  public function add_new_gallery(){
    $data['response'] = FALSE;
    $params = format_parameters(clean_parameters($this->input->post('params'), []));
    $params['slug'] = str_replace('manage-', '', $this->input->post('slug'));

		try {
			$result = $this->gallery_model->add_new_gallery($params);
      $data['message'] = $result['message'];

			if (!empty($result) && $result['code'] == 0){
				$data['response'] = TRUE;
				$data['message'] = 'Successfully added new gallery.';
			}
		} catch (Exception $e) {
			$data['message'] = $e->getMessage();
		}

		header( 'Content-Type: application/x-json' );
		echo json_encode( $data );
  }
}
?>
