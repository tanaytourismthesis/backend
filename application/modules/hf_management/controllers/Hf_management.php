<?php
if (!defined("BASEPATH"))
    exit("No direct script access allowed");

class Hf_management extends MX_Controller {

  public function __construct()
  {
    parent::__construct();
		$this->load->model('hf_management/hf_model');
  }

  public function index() {
    $data = [];

    $this->template->build_template(
      'H.A.N.E. Finder',
      array(
        array(
          'view' => 'hane-finder',
          'data' => $data
        )
      ),
      array(
        'assets/js/modules_js/hane-finder.js',
        'assets/js/bootstrap-datetimepicker.min.js'
      ),
      array( // CSS Files
        'assets/css/bootstrap-datetimepicker.min.css',
        'assets/css/hane-finder.css'
      ),
      array( // Meta Tags

      ),
      'backend' // template page
    );
  }

  public function load_hane() {
    $data['response'] = FALSE;

    try {
      $post = (isJsonPostContentType()) ? decodeJsonPost($this->security->xss_clean($this->input->raw_input_stream)) : $this->input->post();

      $searchkey = $post['searchkey'] ?? NULL;
  		$limit = $post['limit'] ?? NULL;
  		$start = $post['start'] ?? NULL;
  		$id = $post['id'] ?? NULL;

      if ($searchkey === NULL || $start === NULL || $limit === NULL) {
  			throw new Exception("LOAD HANE: Invalid parameter(s)");
  		}

      $params = [
        'searchkey' => $searchkey,
        'start' => $start,
        'limit' => $limit,
        'id' => urldecode($id),
      ];

      $result = $this->hf_model->load_hane($params);

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
