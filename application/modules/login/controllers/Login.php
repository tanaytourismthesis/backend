<?php
if (!defined("BASEPATH"))
    exit("No direct script access allowed");

class Login extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('user_model');
	}

  public function test() {
    echo '<pre>';
    var_dump($_SESSION);
    echo '</pre>';
  }

  public function index(){
    $data = [];

    $this->template->build_template(
      'Administrative Login', //Page Title
      array( // Views
        array(
          'view' => 'login',
          'data' => $data
        )
      ),
      array( // JavaScript Files
        "assets/js/jquery.js",
        "assets/js/login.js"
      ),
      array( // CSS Files
        "assets/css/style.css"
      ),
      array( // Meta Tags

      ),
      'backend' // template page
    );
  }

  public function login_user() {
    $username = $this->input->post('username') ?? NULL;
    $password = $this->input->post('password') ?? NULL;

    $data['response'] = FALSE;

    if ($username === NULL || $password === NULL) {
      throw new Exception("Invalid parameter");
    }
    try {
      $result = $this->user_model->login_user($username,$password);

      if (!empty($result)) {
        $data['data'] = $result;
        $data['response'] = TRUE;
        $data['message'] = "Successful!";
        unset($result[0]['passwd']);
        $this->session->set_userdata('user_info', $result[0]);
        $updateres = $this->user_model->update_userlogstatus($result[0]['user_id']);
        if(empty($updateres)){
          throw new Exception("Something went wrong. Please try again!");
        }
      } else {
        $data['message'] = "Failed to retrieve data!";
      }
    } catch (Exception $e) {
      $data['message'] = $e->getMessage();
    }

    header( 'Content-Type: application/x-json' );
    echo json_encode( $data );
  }
}

?>
