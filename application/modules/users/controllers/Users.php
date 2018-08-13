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

      ),
      array( // Meta Tags

      ),
      'backend' // template page
    );
  }

  public function load_users($id = NULL){
    $data['response'] = FALSE;

    try {
      $result = (empty($id)) ? $this->user_model->load_users() : $this->user_model->get_user($id);
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

    $params = format_parameters(clean_parameters($this->input->post('params')));
    unset($params['confirmpasswd']);

		try {
			$result = $this->user_model->add_new_user($params);
      $data['message'] = $result['message'];

			if (!empty($result) && $result['code'] == 0){
				$data['response'] = TRUE;
				$data['message'] = 'Successfully added new user.';
			}
		}
		catch (Exception $e) {
			$data['message'] = $e->getMessage();
		}

		header( 'Content-Type: application/x-json' );
		echo json_encode( $data );
  }

  public function update_user(){
    $data['response'] = FALSE;
    $data['message'] = 'Please check required fields or check your network connection.';

    $params = format_parameters(clean_parameters($this->input->post('params')));
    $id = $params['user_id'];
    unset($params['confirmpasswd']);
    unset($params['user_id']);

		try {
			$res = $this->user_model->add_new_user($id, $params);

			if ($res === TRUE)
			{
				$data['response'] = TRUE;
				$data['message'] = 'Successfully updated user.';
			}
		}
		catch (Exception $e) {
			$data['message'] = $e->getMessage();
		}

		header( 'Content-Type: application/x-json' );
		echo json_encode( $data );
  }

	public function get_user($id = NULL){
    if (empty($id)) {
      $data = [
        'response' => FALSE,
        'message' => 'Invalid parameter.'
      ];
      header( 'Content-Type: application/x-json' );
      echo json_encode( $data );
      return;
    }
    return $this->load_users($id);
  }
}
?>
