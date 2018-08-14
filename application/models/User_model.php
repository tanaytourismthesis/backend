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
    // set default response
    $response['code'] = 0;
    $response['message'] = 'Success';

    try {
      // check params validity
      if (empty($username) || empty($password)) {
        //set error code and throw an Exception
        $response['code'] = -1;
        throw new Exception('Invalid parameter(s).');
      }

      // set query options
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

      // add login exception checking for superadmin
      if ($username != 'superadmin') {
        $queryOptions['conditions']['isLoggedin'] = 0;
      }

      // execute query
      $result = $this->query->select($queryOptions);

      if (isset($result['code'])) { // if 'code' index exists (means SQL error),...
        // ...merge SQL error object to default response
        $response = array_merge($response, $result);
        // ...and throw Exception
        throw new Exception($response['message']);
      } else if (!empty($result)) { // if $result has data,...
        // ...and get queried data
        $response['data'] = $result[0];
      } else { // else, throw Exception
        throw new Exception('Failed to retrieve details.');
      }
    } catch (Exception $e) { // catch Exception
      $response['message'] = (ENVIRONMENT !== 'production') ? $e->getMessage() : 'Something went wrong. Please try again.';
    }
    // return response data
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
        $like = !empty($params['conditions']) ? 'or_ike' : 'like';
        $queryOptions['conditions'][$like] = ['users.username' => '%'.$searchkey.'%'];
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
    // set default response
    $response['code'] = 0;
    $response['message'] = 'Success';

    try {
      // check params validity
      if (empty($params)) {
        // set error code and throw an Exception
        $response['code'] = -1;
        throw new Exception('Invalid parameter(s).');
      }

      // check if user already exists
      $doesUserExists = $this->load_users([
        'searchkey' => '',
        'start' => 0,
        'limit'=> 1,
        'conditions' => $params
      ]);

      debug($doesUserExists, TRUE);

      // hash password using MD5
      $params['passwd'] = md5($params['passwd']);

      // set create date
      $params['date_created'] = date('Y-m-d H:i:s');

      // execute query
      $result = $this->query->insert('users', $params);

      if (isset($result['code'])) { // if 'code' index exists (means SQL error),...
        // ...merge SQL error object to default response
        $response = array_merge($response, $result);
        // ...and throw Exception
        throw new Exception($response['message']);
      }
    } catch (Exception $e) { // catch Exception
      $response['message'] =  (ENVIRONMENT !== 'production') ? $e->getMessage() : 'Something went wrong. Please try again.';
    }
    // return response data
    return $response;
  }

  public function update_user($id = NULL, $params = []){
    // set default response
    $response['code'] = 0;
    $response['message'] = 'Success';

    try {
      // check params validity
      if (empty($params)) {
        // set error code and throw an Exception
        $response['code'] = -1;
        throw new Exception('Invalid parameter(s).');
      }

      // has password using MD5
      $params['passwd'] = md5($params['passwd']);

      // execute query
      $result = $this->query->update(
        'users', // table
        array(
          'user_id' => $id // condition
        ),
        $params // updated fields
      );

      if (isset($result['code'])) { // if 'code' index exists (means SQL error),...
        // ...merge SQL error object to default response
        $response = array_merge($response, $result);
        // ...and throw Exception
        throw new Exception($response['message']);
      }
    } catch (Exception $e) { // catch Exception
      $response['message'] =  (ENVIRONMENT !== 'production') ? $e->getMessage() : 'Something went wrong. Please try again.';
    }
    // return response data
    return $response;
  }
}
?>
