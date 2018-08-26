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
        throw new Exception('LOGIN_USER: Invalid parameter(s).');
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
        throw new Exception('LOAD_USERS: Invalid parameter(s).');
      }

      $searchkey = $params['searchkey'];
      $start = $params['start'];
      $limit = $params['limit'];
      $id = $params['id'];

      $default_fields = 'users.user_id, users.username, users.last_name,
                          users.first_name, users.position, users.user_photo,
                          users.isLoggedin, users.date_last_loggedin,
                          users.isActive, users.user_type_type_id, user_type.type_name';

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
        $like = !empty($params['conditions']) ? 'or_like' : 'like';
        $queryOptions['conditions'][$like] = ['users.username' => $searchkey];
        $queryOptions['conditions']['or_like'] = ['users.last_name' => $searchkey];
        $queryOptions['conditions']['or_like'] = ['users.first_name' => $searchkey];
      }

      if (!empty($id)) {
        $queryOptions['conditions'] = ['user_id' => $id];
      }

      $result = $this->query->select($queryOptions);
      $queryOptions['fields'] = 'COUNT(users.user_id) total_records';

      if (empty($searchkey)) {
        unset($queryOptions['start']);
        unset($queryOptions['limit']);
      }
      $result2 = $this->query->select($queryOptions);

      if (isset($result['code'])) {
        $response = array_merge($response, $result);
        throw new Exception($response['message']);
      } else if (!empty($result)) {
        $response['data']['records'] = (count($result) >= 1 && empty($id)) ? $result : $result[0];
        $response['data']['total_records'] = $result2[0]['total_records'];
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
        throw new Exception('UPDATE_USER_LOGSTATUS: Invalid parameter(s).');
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
        throw new Exception('ADD_NEW_USER: Invalid parameter(s).');
      }

      // check if user already exists
      $doesUserExists = $this->load_users([
        'searchkey' => '',
        'start' => 0,
        'limit'=> 1,
        'id' => 0,
        'conditions' => [
          'or_like' => [
            'username' => $params['username'],
            'email' => $params['email'],
          ],
          'like' => [
            'last_name' => $params['last_name'],
            'first_name' => $params['first_name']
          ]
        ]
      ]);

      // if user is already existing, set response code and throw an Exception
      if ($doesUserExists['code'] == 0 && !empty($doesUserExists['data'])) {
        $response['code'] = -1;
        throw new Exception('User already exists! Please contact your administrator.');
      }

      // hash password using MD5
      $params['passwd'] = md5($params['passwd']);

      // set create date
      $params['date_created'] = date('Y-m-d H:i:s');

      // execute query
      $result = $this->query->insert('users', $params, TRUE);

      if (isset($result['response']['code'])) { // if 'code' index exists (means SQL error),...
        // ...merge SQL error object to default response
        $response = array_merge($response, $result['response']);
        // ...and throw Exception
        throw new Exception($response['message']);
      } else {
        $response['data'] = ['user_id' => $result['id']];
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
        throw new Exception('UPDATE_USER: Invalid parameter(s).');
      }

      // hash password using MD5
      if (isset($params['passwd'])) {
        $params['passwd'] = md5($params['passwd']);
      }

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

  public function get_usertypes() {
    $response['code'] = 0;
    $response['message'] = 'Success';
     try {
      $result = $this->query->select(
        array(
        'table' => 'user_type',
        'fields' => '*'
      ));
      if (isset($result['code'])) {
        $response = array_merge($response, $result);
        throw new Exception($response['message']);
      } else if (!empty($result)) {
        $response['data'] = (count($result) >= 1 && empty($id)) ? $result : $result[0];
      } else {
        throw new Exception('Failed to retrieve details.');
      }
     } catch (Exception $e) {
        $response['message'] =  (ENVIRONMENT !== 'production') ? $e->getMessage() : 'Something went wrong. Please try again.';
    }
    return $response;
  }
}
?>
