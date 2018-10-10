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
        'assets/js/modules_js/dashboard.js'
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

      $post = $params;

      if (empty($post)) {
        throw new Exception('Invalid parameter(s)');
      }

      $id = $post['news_id'];

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
      $id = $this->input->post('news_id');
      $id = decrypt(urldecode($id));

      $result = $this->load_newsclick([
        'news_id' => $id
      ], FALSE);

      if(!$result['response']){
        $newres = $this->news_model->addnewsclick($id);
      } else {
        $newres = $this->news_model->updatenewsclick($id, $result['data']['num_clicks']);
      }
    }

    public function load_pageclick($params = [], $ajax = TRUE) {
        $data['response'] = FALSE;
        $data['message'] = 'Failed';

        $post = $params;

        if (empty($post)) {
          throw new Exception('Invalid parameter(s)');
        }

        $id = $post['content_id'];

        try {
          $result = $this->page_model->load_pageclick($id);
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

    public function add_pageclick(){
        $id = $this->input->post('content_id');
        $id = decrypt(urldecode($id));

        $result = $this->load_pageclick([
          'content_id' => $id
        ], FALSE);

        if(!$result['response']){
          $newres = $this->page_model->addpageclick($id);
        } else {
          $newres = $this->page_model->updatepageclick($id, $result['data']['num_clicks']);
        }
      }

}

?>
