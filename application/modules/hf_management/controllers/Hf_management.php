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

      if (!empty($id) || $id == 'all') {
        $params['additional_fields'] = 'address, contact, email, url';
      }

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

  public function update_hane($params = [], $ajax = TRUE) {
    $data['response'] = FALSE;
    $params = ($ajax) ? $this->input->post('params') : $params;
    $params = format_parameters(clean_parameters($params, []));
    $id = $params['hotel_id'] ?? 0;

    if (isset($params['hotel_id'])) {
      unset($params['hotel_id']);
    }

		try {
      if (empty($id)) {
        throw new Exception('UPDATE HANE: Invalid parameter(s)');
      }

			$result = $this->hf_model->update_hane($id, $params);
      $data['message'] = $result['message'];

			if (!empty($result) && $result['code'] == 0) {
				$data['response'] = TRUE;
				$data['message'] = 'Successfully updated H.A.NE.';
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

  public function add_hane() {
    $data['response'] = FALSE;
    $params = format_parameters(clean_parameters($this->input->post('params'), []));

		try {
      if (empty($params)) {
        throw new Exception('ADD NEW HANE: Invalid parameter(s)');
      }

			$result = $this->hf_model->add_new_hane($params);

      $data['message'] = $result['message'];
			if (!empty($result) && $result['code'] == 0) {
				$data['response'] = TRUE;
				$data['message'] = 'Successfully added new H.A.N.E.';
			}
		} catch (Exception $e) {
			$data['message'] = $e->getMessage();
		}

		header( 'Content-Type: application/x-json' );
		echo json_encode( $data );
  }
}
