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
      'User Management | Site Administration', //Page Title
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

  public function load_user(){

    $data['response'] = FALSE;

    try {
      $result = $this->user_model->load_user();

      if (!empty($result)) {
        $data['data'] = $result;
        $data['response'] = TRUE;
      }

    } catch (Exception $e) {
      $data['message'] = $e->getMessage();
    }

    header( 'Content-Type: application/x-json' );
    echo json_encode( $data );
  }

  public function add_new_user(){
    $data['response'] = FALSE;
		$data['message'] = 'Please check required fields or check your network connection.';
    
    $params = format_parameters(clean_parameters($this->input->post('params')));
    unset($params['confirmpasswd']);

		try {
			$res = $this->user_model->add_new_user($params);

			if ($res === TRUE)
			{
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

	public function get_user($id = NULL)
	{
		$data['response'] = FALSE;
		$data['message'] = 'Something Went Wrong!.';

		if ($id === NULL) {
			throw new Exception("Invalid parameter");
		}

		try {
			$result = $this->user_model->get_user($id);

			if (!empty($result)) {
				$data['data'] = $result;
				$data['response'] = TRUE;
				$data['message'] = 'Success!';
			}

		} catch (Exception $e) {
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
}
?>
