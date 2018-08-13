<?php
if (!defined("BASEPATH"))
    exit("No direct script access allowed");

class User_model extends CI_Model {

	public function __construct()
	{
		$this->load->database();
		$this->load->library('query');
	}

  public function login_user($username = NULL, $password = NULL){
    $response['code'] = 0;
    $response['message'] = 'Success';

    try {
      if (empty($username) || empty($password)) {
        $response['code'] = -1;
        throw new Exception('Invalid parameter(s).');
      }

      $queryOptions = array(
        'table' => 'users',
        'fields' => '*',
        'joins' => array(
          'user_type' => array(
            'type' => 'left',
            'user_type.type_id' => 'users.user_type_type_id'
          )
        ),
        'conditions' => array(
          'username' => $username,
          'passwd' => md5($password),
        )
      );

      if ($username != 'superadmin') {
        $queryOptions['conditions']['isLoggedin'] = 0;
      }

      $result = $this->query->select($queryOptions);

      if (isset($result['code'])) {
        $response = array_merge($response, $result);
        throw new Exception($response['message']);
      } else if (!empty($result)) {
        $response['data'] = $result[0];
      } else {
        throw new Exception('Failed to retrieve details.');
      }
    } catch (Exception $e) {
      $response['message'] = (ENVIRONMENT !== 'production') ? $e->getMessage() : 'Something went wrong. Please try again.';
    }

    return $response;
  }

  public function load_users($params = []){
    $response['code'] = 0;
    $response['message'] = 'Success';

    try {
      if (empty($params)) {
        $response['code'] = -1;
        throw new Exception('Invalid parameter(s).');
      }

      $searchkey = $params['searchkey'];
      $start = $params['start'];
      $limit = $params['limit'];
      $id = $params['id'];

      $default_fields = 'users.user_id, users.username, users.last_name, users.first_name, users.position,
                  users.isLoggedin, users.date_last_loggedin, users.isActive, user_type.type_name';

      if (!empty($params['additional_fields'])) {
        $default_fields .= ',' . $params['additional_fields'];
      }

      $queryOptions = array(
        'table' => 'users',
        'fields' => $default_fields,
        'joins' => array(
          'user_type' => array(
            'type' => 'left',
            'user_type.type_id' => 'users.user_type_type_id'
          )
        ),
        'start' => $start,
        'limit' => $limit
      );

      if (!empty($params['conditions'])) {
        $queryOptions['conditions'] = $params['conditions'];
      }

      if (!empty($searchkey)) {
        $queryOptions['conditions']['like'] = ['users.username' => '%'.$searchkey.'%'];
        $queryOptions['conditions']['or_like'] = ['users.last_name' => '%'.$searchkey.'%'];
        $queryOptions['conditions']['or_like'] = ['users.first_name' => '%'.$searchkey.'%'];
      }

      if (!empty($id)) {
        $queryOptions['conditions'] = ['user_id' => $id];
      }

      $result = $this->query->select($queryOptions);

      if (isset($result['code'])) {
        $response = array_merge($response, $result);
        throw new Exception($response['message']);
      } else if (!empty($result)) {
        $response['data'] = (count($result) >= 1 && empty($id)) ? $result : $result[0];
      } else {
        throw new Exception('Failed to retrieve details.');
      }
    } catch (Exception $e) {
      $response['message'] = (ENVIRONMENT !== 'production') ? $e->getMessage() : 'Something went wrong. Please try again.';
    }
    return $response;
  }

  public function update_userlogstatus($id = NULL, $logout = FALSE){
    $response['code'] = 0;
    $response['message'] = 'Success';

    try {
      if (empty($id)) {
        $response['code'] = -1;
        throw new Exception('Invalid parameter(s).');
      }
      $result = $this->query->update(
        'users',
        array(
          'user_id' => $id
        ),
        array(
          'date_last_loggedin' => date('Y-m-d H:i:s'),
          'isLoggedin' => ($logout) ? '0' : '1'
        )
      );

      if (isset($result['code'])) {
        $response = array_merge($response, $result);
        throw new Exception($response['message']);
      }
    } catch (Exception $e) {
      $response['message'] =  (ENVIRONMENT !== 'production') ? $e->getMessage() : 'Something went wrong. Please try again.';
    }
    return $response;
  }

  public function add_new_user($params = []){
    $response['code'] = 0;
    $response['message'] = 'Success';

    try {
      if (empty($params)) {
        $response['code'] = -1;
        throw new Exception('Invalid parameter(s).');
      }

      $params['passwd'] = md5($params['passwd']);
      $params['date_created'] = date('Y-m-d H:i:s');

      $result = $this->query->insert('users', $params);

      if (isset($result['code'])) {
        $response = array_merge($response, $result);
        throw new Exception($response['message']);
      }
    } catch (Exception $e) {
      $response['message'] =  (ENVIRONMENT !== 'production') ? $e->getMessage() : 'Something went wrong. Please try again.';
    }
    return $response;
  }

  public function update_user($id = NULL, $params = []){
    $response['code'] = 0;
    $response['message'] = 'Success';

    try {
      if (empty($params)) {
        $response['code'] = -1;
        throw new Exception('Invalid parameter(s).');
      }

      $params['passwd'] = md5($params['passwd']);

      $result = $this->query->update(
        'users',
        array(
          'user_id' => $id
        ),
        $params
      );

      if (isset($result['code'])) {
        $response = array_merge($response, $result);
        throw new Exception($response['message']);
      }
    } catch (Exception $e) {
      $response['message'] =  (ENVIRONMENT !== 'production') ? $e->getMessage() : 'Something went wrong. Please try again.';
    }
    return $response;
  }
}
?>
