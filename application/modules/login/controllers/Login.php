<?php
if (!defined("BASEPATH"))
    exit("No direct script access allowed");

class Login extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('users/user_model');
	}

  public function index() {
    $data = [];

    $this->template->build_template(
      'Login', //Page Title
      array( // Views
        array(
          'view' => 'login',
          'data' => $data
        )
      ),
      array( // JavaScript Files
        "assets/js/modules_js/login.js"
      ),
      array( // CSS Files
        "assets/css/login.css"
      ),
      array( // Meta Tags

      ),
      'backend' // template page
    );
  }

  public function login_user() {
    $data['response'] = FALSE;

    try {
      $post = (isJsonPostContentType()) ? decodeJsonPost($this->security->xss_clean($this->input->raw_input_stream)) : $this->input->post();

      $username = $post['username'] ?? NULL;
      $password = $post['password'] ?? NULL;

      if (empty($username) || empty($password)) {
        throw new Exception("Invalid parameters");
      }

      $result = $this->user_model->login_user($username,$password);
      $data['message'] = $result['message'];

      if (!empty($result) && $result['code'] == 0 && !empty($result['data'])) {
        $data['response'] = TRUE;
        $data['data'] = $result['data'];

        unset($data['data']['passwd']);

        $user_info = $data['data'];

        // Update user log status
        $updateres = $this->user_model->update_userlogstatus($user_info['user_id']);

        if(!empty($updateres) && isset($updateres['code']) && $updateres['code'] != 0) {
          $data['response'] = FALSE;
          $data['data'] = NULL;
          throw new Exception($updateres['message']);
        }

        // Determine allowed user menu items
        $menu_items = ENV['menu_items'];
        $user_menu = [];

        foreach ($menu_items as $key => $value) {
          if (
            $value['allowed_users'][0] == 'all'
            ||
            in_array($user_info['type_alias'], $value['allowed_users'])
          ) {
            $user_menu[] = $value;
          }
        }

        $user_info['menu_items'] = $user_menu;

        $this->session->set_userdata('user_info', $user_info);
      }
    } catch (Exception $e) {
      $data['message'] = $e->getMessage();
    }

    header( 'Content-Type: application/x-json' );
    echo json_encode( $data );
  }
}

?>
