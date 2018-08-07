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
        "assets/js/jquery.js",
        "assets/js/user_management.js"
      ),
      array( // CSS Files
        "assets/css/bootstrap.min.css"
      ),
      array( // Meta Tags

      ),
      'backend' // template page
    );
  }

  public function logout() {
    if ($this->session->has_userdata('user_info')) {
      $id = $this->session->userdata('user_info')['user_id'];
      $result = $this->user_model->update_userlogstatus($id, TRUE);

      if ($result) {
        $this->session->unset_userdata('user_info');
      }
    }
    redirect(base_url() . 'admin');
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
		
    $username = $this->input->post('username');
    $password = $this->input->post('password');
    $email = $this->input->post('email');
    $fname = $this->input->post('fname');
    $mname = $this->input->post('mname');
    $lname = $this->input->post('lname');
    $position = $this->input->post('position');

		if($username === NULL || $password === NULL || $email === NULL || $fname === NULL || $mname === NULL || $lname === NULL || $position === NULL){
			throw new Exception("Invalid parameter");
		}
		
		try {
			$res = $this->user_model->add_new_user($username,$password,$email,$fname,$mname,$lname,$position);
				
			if ($res === TRUE)
			{
				$data['response'] = TRUE;
				$data['message'] = 'Successfully added Updated User.';
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
		
    $username = $this->input->post('username');
    $password = $this->input->post('password');
    $email = $this->input->post('email');
    $fname = $this->input->post('fname');
    $mname = $this->input->post('mname');
    $lname = $this->input->post('lname');
    $position = $this->input->post('position');
    $id = $this->input->post('id');

		if(empty($id) || empty($username) || empty($password) || empty($email) || empty($fname) || empty($mname) || empty($lname) || empty($position)){
			throw new Exception("Invalid parameter");
		}
		
		try {
			$res = $this->user_model->update_user($id,$username,$password,$email,$fname,$mname,$lname,$position);
				
			if ($res === TRUE)
			{
				$data['response'] = TRUE;
				$data['message'] = 'Successfully added Updated User.';
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
