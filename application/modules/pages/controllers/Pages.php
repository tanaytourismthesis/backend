<?php
if (!defined("BASEPATH"))
    exit("No direct script access allowed");

class Pages extends MX_Controller {

  private $page_alias;
  private $page_caption;
  private $page_icon;
  private $tag;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('page_model');
    $this->page_caption = $this->session->userdata('active_page_caption');
    $this->page_alias = $this->session->userdata('active_page_alias');
    $this->tag = $this->session->userdata('active_page_method');
    $this->page_icon = $this->session->userdata('active_page_icon');    
	}

  public function index(){
    $slug = str_replace('manage-', '', $this->page_alias);
    $pagelist_result = $this->load_pagelist('',0,0,'',FALSE);
    $pagelist = ($pagelist_result['response']) ? $pagelist_result['data']['records'] : [];

    $data = [
      'slug' => $slug,
      'caption' => $this->page_caption,
      'icon' => $this->page_icon,
      'tag' => $this->tag,
      'pagelist' => $pagelist,
      'tags' => (empty($this->tag)) ? $this->page_model->getPageTags($slug) : [$this->tag]
    ];

    $this->template->build_template(
      $this->page_caption, //Page Title
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

  public function load_pagecontentlist(){
    $searchkey = $this->input->post('searchkey') ?? NULL;
		$limit = $this->input->post('limit') ?? NULL;
		$start = $this->input->post('start') ?? NULL;
		$id = $this->input->post('id') ?? NULL;
    $slug = $this->input->post('slug') ?? NULL;
    $tag = $this->input->post('tag') ?? NULL;

    $data['response'] = FALSE;

    try {
      if ($searchkey === NULL || $start === NULL || $limit === NULL) {
  			throw new Exception("LOAD PAGE CONTENTS: Invalid parameter(s)");
  		}

      $params = [
        'searchkey' => $searchkey,
        'start' => $start,
        'limit' => $limit,
        'id' => urldecode($id),
        'slug' => $slug,
        'tag' => $tag
      ];

      $result = $this->page_model->load_pagecontentlist($params);

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

  public function load_pagelist($searchkey = '', $start = 0, $limit = 5, $slug = '', $ajax = TRUE) {
    $searchkey = $searchkey ?? $this->input->post('searchkey') ?? NULL;
		$limit = $limit ?? $this->input->post('limit') ?? NULL;
		$start = $start ?? $this->input->post('start') ?? NULL;
		$id = $this->input->post('id') ?? NULL;
		$slug = $slug ?? $this->input->post('slug') ?? NULL;
		$tag = $this->input->post('tag') ?? NULL;

    $data['response'] = FALSE;

    try {
      if ($searchkey === NULL || $start === NULL || $limit === NULL) {
  			throw new Exception("LOAD PAGE CONTENTS: Invalid parameter(s)");
  		}

      $params = [
        'searchkey' => $searchkey,
        'start' => $start,
        'limit' => $limit,
        'id' => urldecode($id),
        'slug' => $slug
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

    if ($ajax) {
      header( 'Content-Type: application/x-json' );
      echo json_encode( $data );
    }
    return $data;
  }

  public function add_page_content(){
    $data['response'] = FALSE;

    $exception = ['content'];
    $params = format_parameters(clean_parameters($this->input->post('params'), $exception));
    $newId = $this->session->userdata('user_info')['user_id'];
    $params['users_user_id'] = decrypt(urldecode($newId));
    $newpageid = $params['page_page_id'];
    $params['page_page_id'] = decrypt(urldecode($newpageid));
    $params['slug'] = url_title($params['title'],'-',true);
    $params['page_slug'] = $this->input->post('slug');
    $params['page_tag'] = $this->input->post('tag');
        
		try {
			$result = $this->page_model->add_page_content($params);

      $data['message'] = $result['message'];

			if (!empty($result) && $result['code'] == 0){
				$data['response'] = TRUE;
				$data['message'] = 'Successfully added the Page Content.';
			}
		}
		catch (Exception $e) {
			$data['message'] = $e->getMessage();
		}

		header( 'Content-Type: application/x-json' );
		echo json_encode( $data );

  }

}
?>
