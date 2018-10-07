<?php
if (!defined("BASEPATH"))
    exit("No direct script access allowed");

class Dashboard extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('users/user_model');
		$this->load->model('pages/page_model');
		$this->load->model('news/news_model');
		$this->load->model('hf_management/hf_model');
	}

  public function index() {
    $data = [];

    $this->template->build_template(
      'Dashboard', //Page Title
      array( // Views
        array(
          'view' => 'dashboard',
          'data' => $data
        )
      ),
      array( // JavaScript Files
        'assets/js/dashboard.js'
      ),
      array( // CSS Files
        'assets/css/dashboard.css'
      ),
      array( // Meta Tags

      ),
      'backend' // template page
    );
  }

  public function load_newsclick($params = [], $ajax = TRUE) {
    $data['response'] = FALSE;
    $data['message'] = 'Failed';

    if (!empty($params)) {
      $post = $params;
    } else {
      $post = (isJsonPostContentType()) ? decodeJsonPost($this->security->xss_clean($this->input->raw_input_stream)) : $this->input->post();
    }

    if (empty($post)) {
      throw new Exception('Invalid parameter(s)');
    }

    $id = $post['id'] ?? '';
    $id = decrypt(urldecode($id));

    try {
      $result = $this->news_model->load_newsclick($id);
      // parse response message
      $data['message'] = $result['message'];

      // if result is not error and code is 0 and data is not empty...
      if (!empty($result) && $result['code'] == 0 && !empty($result['data'])) {
        // ...set response to true
        $data['response'] = TRUE;
        //...and, parse data
        $data['data'] = $result['data'];
      }
    } catch (Exception $e) {
      $data['message'] = $e->getMessage();
    }

    if ($ajax) {
      header( 'Content-Type: application/x-json' );
  		echo json_encode($data);
    }
    return $data;
  }

  public function add_newsclick(){
    $post = (isJsonPostContentType()) ? decodeJsonPost($this->security->xss_clean($this->input->raw_input_stream)) : $this->input->post();

    $params = [
      'searchkey' => '',
      'start' => 0,
      'limit' => 1,
      'id' => '',
      'slug' => '',
      'status' => 'published',
      'newsslug' => $post['newsslug'] ?? ''
    ];

    $res = modules::run('news/load_news', $params, FALSE);
    
    if ($res['response'] && $res['data']) {
      $result = $this->load_newsclick([
        'id' => $res['data']['records']['news_id']
      ]);
      debug($result);
    }
  }

}

?>
