<?php
if (!defined("BASEPATH"))
    exit("No direct script access allowed");

class Users extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('user_model');
	}

  public function index(){
    $data = [
      'user_info' => $this->session->userdata('user_info')
    ];

    $this->template->build_template(
      'User Management', //Page Title
      array( // Views
        array(
          'view' => 'users',
          'data' => $data
        )
      ),
      array( // JavaScript Files
        "assets/js/modules_js/user_management.js"
      ),
      array( // CSS Files
        "assets/css/user_management.css"
      ),
      array( // Meta Tags

      ),
      'backend' // template page
    );
  }

  public function load_users(){
    $searchkey = $this->input->post('searchkey') ?? NULL;
		$limit = $this->input->post('limit') ?? NULL;
		$start = $this->input->post('start') ?? NULL;
		$id = $this->input->post('id') ?? NULL;

    $data['response'] = FALSE;

    try {
      if ($searchkey === NULL || $start === NULL || $limit === NULL) {
  			throw new Exception("Invalid parameter");
  		}

      $params = [
        'searchkey' => $searchkey,
        'start' => $start,
        'limit' => $limit,
        'id' => $id
      ];

      if (!empty($id)) {
        $params['additional_fields'] = 'users.mid_name, users.email, users.date_created, users.user_type_type_id';
      }

      $result = $this->user_model->load_users($params);

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

  public function add_new_user(){
    $data['response'] = FALSE;

    $params = format_parameters(clean_parameters($this->input->post('params'), []));
    if (isset($params['confirmpasswd'])) {
      unset($params['confirmpasswd']);
    }

		try {
			$result = $this->user_model->add_new_user($params);
      $data['message'] = $result['message'];

			if (!empty($result) && $result['code'] == 0){
				$data['response'] = TRUE;
				$data['message'] = 'Successfully added new user.';
			}
		} catch (Exception $e) {
			$data['message'] = $e->getMessage();
		}

		header( 'Content-Type: application/x-json' );
		echo json_encode( $data );
  }

  public function update_user(){
    $data['response'] = FALSE;

    $params = format_parameters(clean_parameters($this->input->post('params'), []));
    $id = $params['user_id'] ?? 0;

    if (isset($params['confirmpasswd'])) {
      unset($params['confirmpasswd']);
    }
    if (isset($params['user_id'])) {
      unset($params['user_id']);
    }

		try {
      if (empty($id)) {
        throw new Exception('Invalid parameter(s).');
      }

			$res = $this->user_model->add_new_user($id, $params);
      $data['message'] = $result['message'];

			if (!empty($result) && $result['code'] == 0){
				$data['response'] = TRUE;
				$data['message'] = 'Successfully added new user.';
			}
		} catch (Exception $e) {
			$data['message'] = $e->getMessage();
		}

		header( 'Content-Type: application/x-json' );
		echo json_encode( $data );
  }

  public function update_user_photo() {
    
  }
}
?>
