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

  public function index() {
    $slug = str_replace('manage-', '', $this->page_alias);
    $pagelist_result = $this->load_pagelist('', 0, 0, $slug, FALSE);
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

  public function load_pages(){
    $data = [
      'response' => FALSE,
      'data' => [
        'records' => [],
        'total_records' => 0
      ]
    ];

    try {
      $post = (isJsonPostContentType()) ? decodeJsonPost($this->security->xss_clean($this->input->raw_input_stream)) : $this->input->post();

      if (empty($post)) {
        throw new Exception('LOAD PAGES: Invalid parameter(s)');
      }

      $success = 0;
      foreach($post as $slug => $tags) {
        foreach($tags as $tag => $settings) {
          $settings['slug'] = $slug;
          $settings['tag'] = $tag;
          $settings['additional_fields'] = 'page_content.page_page_id, page.page_name, page.slug page_slug, users.first_name first_name,
          users.last_name last_name';
          $result = $this->load_pagecontentlist($settings, FALSE);
          if ($result['response']) {
            $data['data']['records'] = array_merge($data['data']['records'], $result['data']['records']);
            $data['data']['total_records'] += $result['data']['total_records'];
            $success++;
          }
        }
      }

      if ($success > 0) {
        $data['response'] = TRUE;
        $data['message'] = 'Success';
      }
    } catch (Exception $e) {
      $data['message'] = $e->getMessage();
    }

    header( 'Content-Type: application/x-json' );
    echo json_encode( $data );
  }

  public function load_pagecontentlist($params = [], $ajax = TRUE) {
    $data['response'] = FALSE;

    try {
      if (!empty($params)) {
        $post = $params;
      } else {
        $post = (isJsonPostContentType()) ? decodeJsonPost($this->security->xss_clean($this->input->raw_input_stream)) : $this->input->post();
      }

      $searchkey = $post['searchkey'] ?? NULL;
  		$limit = $post['limit'] ?? NULL;
  		$start = $post['start'] ?? NULL;
  		$id = $post['id'] ?? NULL;
      $slug = $post['slug'] ?? NULL;
      $tag = $post['tag'] ?? NULL;
      $isShown = $post['isShown'] ?? NULL;
      $additional = $post['additional_fields'] ?? '';
      $content_slug = $post['content_slug'] ?? '';

      if ($searchkey === NULL || $start === NULL || $limit === NULL) {
  			throw new Exception("LOAD PAGE CONTENT LIST: Invalid parameter(s)");
  		}

      $params = [
        'searchkey' => $searchkey,
        'start' => $start,
        'limit' => $limit,
        'id' => urldecode($id),
        'slug' => $slug,
        'tag' => $tag,
        'isShown' => $isShown,
        'content_slug' => $content_slug
      ];

      if (!empty($id) || $id == 'all' || !empty($tag)) {
        $params['additional_fields'] = 'page_content.content, page_content.keywords, page_content.order_position';
        if (!empty($additional)) {
          $params['additional_fields'] .= ', ' . $additional;
        }
      }

      $result = $this->page_model->load_pagecontentlist($params);

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

  public function update_page_content(){
    $data['response'] = FALSE;

    $exceptions = ['content'];
    $params = format_parameters(clean_parameters($this->input->post('params'), $exceptions));

    $id = $this->input->post('id');
    $data['response'] = FALSE;
    $data['message'] = 'Failed to update data.';

    try {
      if(empty($params) || empty($id)){
        throw new Exception("Invalid parameters");
      }

      $params['slug'] = url_title($params['title'],'-',true);

      $result = $this->page_model->update_page_content($id,$params);

      $data['message'] = $result['message'];

			if (!empty($result) && $result['code'] == 0){
				$data['response'] = TRUE;
				$data['message'] = 'Successfully updated Page Content.';
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

  public function add_page_content() {
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

			if (!empty($result) && $result['code'] == 0) {
				$data['response'] = TRUE;
				$data['message'] = 'Successfully added Page Content.';
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
