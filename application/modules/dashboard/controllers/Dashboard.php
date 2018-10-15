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
		$this->load->model('dashboard/dashboard_model');
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

  public function add_newsclick(){
    $id = $this->input->post('news_id');
    $id = decrypt(urldecode($id));

    $result = $this->news_model->load_newsclick($id);

    if($result['code'] != 0){
      $newres = $this->news_model->addnewsclick($id);
    } else {
      $newres = $this->news_model->updatenewsclick($result['data']['click_id'], $result['data']['num_clicks']);
    }
  }

  public function add_pageclick(){
    $id = $this->input->post('content_id');
    $id = decrypt(urldecode($id));

    $result = $this->page_model->load_pageclick($id);

    if($result['code'] != 0){
      $newres = $this->page_model->addpageclick($id);
    } else {
      $newres = $this->page_model->updatepageclick($result['data']['click_id'], $result['data']['num_clicks']);
    }
  }

  public function record_site_visit() {
    $data['response'] = FALSE;
    $data['message'] = 'Failed';

    try {
      $result = $this->page_model->record_site_visit();
      // parse response message
      $data['message'] = $result['message'];

      // if result is not error and code is 0 and data is not empty...
      if (!empty($result) && $result['code'] == 0) {
        // ...set response to true
        $data['response'] = TRUE;
      }
    } catch (Exception $e) {
      $data['message'] = $e->getMessage();
    }

    header( 'Content-Type: application/x-json' );
  	echo json_encode($data);
  }

  public function getVisitsAndClicks() {
    $data['response'] = FALSE;
    $data['message'] = 'Failed';
    $data['data'] = [];

    $result = $this->dashboard_model->getVisitsAndClicks();
    if ($result['code'] == 0) {
      $data['data'] = $result['data'];
      $data['response'] = TRUE;
      $data['message'] = 'Success';
    }

    header( 'Content-Type: application/x-json' );
    echo json_encode($data);
  }

  public function popular_page_contents($top = 5) {
    $data['response'] = FALSE;
    $data['message'] = 'Failed';
    $data['data'] = [];

    $result = $this->page_model->popular_page_contents($top);
    if ($result['code'] == 0) {
      $data['data'] = $result['data'];
      $data['response'] = TRUE;
      $data['message'] = 'Success';
    }

    header( 'Content-Type: application/x-json' );
    echo json_encode($data);
  }

  public function popular_news($top = 5) {
    $data['response'] = FALSE;
    $data['message'] = 'Failed';
    $data['data'] = [];

    $result = $this->news_model->popular_news($top);
    if ($result['code'] == 0) {
      $data['data'] = $result['data'];
      $data['response'] = TRUE;
      $data['message'] = 'Success';
    }

    header( 'Content-Type: application/x-json' );
    echo json_encode($data);
  }

  public function top_contributors_pagecontent($top = 5) {
    $data['response'] = FALSE;
    $data['message'] = 'Failed';
    $data['data'] = [];

    $result = $this->page_model->top_contributors($top);
    if ($result['code'] == 0) {
      $data['data'] = $result['data'];
      $data['response'] = TRUE;
      $data['message'] = 'Success';
    }

    header( 'Content-Type: application/x-json' );
    echo json_encode($data);
  }

  public function top_contributors_news($top = 5) {
    $data['response'] = FALSE;
    $data['message'] = 'Failed';
    $data['data'] = [];

    $result = $this->news_model->top_contributors($top);
    if ($result['code'] == 0) {
      $data['data'] = $result['data'];
      $data['response'] = TRUE;
      $data['message'] = 'Success';
    }

    header( 'Content-Type: application/x-json' );
    echo json_encode($data);
  }

  public function getUserStats() {
    $data['response'] = FALSE;
    $data['message'] = 'Failed';
    $data['data'] = [];

    $result = $this->dashboard_model->getUserStatistics();
    if ($result['code'] == 0) {
      $data['data'] = $result['data'];
      $data['response'] = TRUE;
      $data['message'] = 'Success';
    }

    header( 'Content-Type: application/x-json' );
    echo json_encode($data);
  }
}
?>
