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
  			throw new Exception("LOAD_USERS: Invalid parameter(s)");
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
    $params = format_parameters(clean_parameters(json_decode($this->input->post('params'), true), []));
    if (isset($params['confirmpasswd'])) {
      unset($params['confirmpasswd']);
    }

		try {
			$result = $this->user_model->add_new_user($params);
      $data['message'] = $result['message'];

			if (!empty($result) && $result['code'] == 0){
				$data['response'] = TRUE;
				$data['message'] = 'Successfully added new user.';
        $res = $this->update_user_photo(['user_id' => $result['data']['user_id']]);
			}
		} catch (Exception $e) {
			$data['message'] = $e->getMessage();
		}

		header( 'Content-Type: application/x-json' );
		echo json_encode( $data );
  }

  public function update_user($params = [], $ajax = TRUE){
    $data['response'] = FALSE;
    $params = ($ajax) ? $this->input->post('params') : $params;
    $params = format_parameters(clean_parameters($params, []));
    $id = $params['user_id'] ?? 0;

    if (isset($params['confirmpasswd'])) {
      unset($params['confirmpasswd']);
    }
    if (isset($params['user_id'])) {
      unset($params['user_id']);
    }

		try {
      if (empty($id)) {
        throw new Exception('UPDATE_USER: Invalid parameter(s).');
      }

			$result = $this->user_model->update_user($id, $params);
      $data['message'] = $result['message'];

			if (!empty($result) && $result['code'] == 0){
				$data['response'] = TRUE;
				$data['message'] = 'Successfully updated user.';
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

  public function update_user_photo($params = []) {
    $data['response'] = FALSE;
    $data['message'] = 'Failed';

    try {
      $photo = $_FILES['file'] ?? [];
      $user_id = $this->input->post('user_id') ?? $params['user_id'] ?? 0;

      if (empty($photo) || empty($user_id)) {
        throw new Exception('UPDATE_USER_PHOTO: Invalid parameter(s).');
      }

      $name = $photo['name'];
      $ext = explode('.', $name);
      $ext = end($ext);
      $mime = $photo['type'];
      $size = $photo['size'] * 1e-6; // in MB
      $allowedExts = ['jpg','jpeg','png','gif','PNG','JPG','JPEG','GIF'];
      $allowedMimes = ['image/jpeg','image/jpg','image/png','image/gif'];

      if (!in_array($ext, $allowedExts) || !in_array($mime, $allowedMimes) || $size > 5) {
        throw new Exception('Invalid file type or size. Please use image files only with no more than 5MB.');
      }

      $newName = $user_id . '.' . $ext;
      $source = $photo['tmp_name'];
      $folder = ENV['image_upload_path'] . 'users/';
      $target = $folder . $newName;

      foreach ($allowedExts as $ex) {
        $filepath = $folder . $user_id . '.' . $ex;
        if (file_exists($filepath)) {
          unlink($filepath); // delete existing file
        }
      }

      if(move_uploaded_file($source, $target)) {
        $result = $this->update_user([
          [
            'name' => 'user_id',
            'value' => $user_id
          ],
          [
            'name' => 'user_photo',
            'value' => $newName
          ]
        ], FALSE);

        $data = $result;
      }

    } catch (Exception $e) {
			$data['message'] = $e->getMessage();
		}

		header( 'Content-Type: application/x-json' );
		echo json_encode( $data );
  }
}
?>
