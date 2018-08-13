<?php
if (!defined("BASEPATH"))
    exit("No direct script access allowed");

class News_model extends CI_Model {

	public function __construct()
	{
		$this->load->database();
		$this->load->library('query');
	}

  public function load_news($params = []){
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

      $default_fields = 'news.news_id news_id, news.title title, news.status status, '
                        .'news.date_posted date_posted, news.date_updated date_updated, '
                        .'news_type.type_name type_name, users.first_name first_name, '
                        .'users.last_name last_name';

      if (!empty($params['additional_fields'])) {
        $default_fields .= ',' . $params['additional_fields'];
      }

      $queryOptions = array(
        'table' => 'news',
        'fields' => $default_fields,
        'joins' => array(
          'news_type' => array(
            'type' => 'left',
            'news.news_type_type_id' => 'news_type.type_id'
          ),
          'users' => array(
            'type' => 'left',
            'news.users_user_id' => 'users.user_id'
          )
        ),
        'start' => $start,
        'limit' => $limit
      );

      if (!empty($params['conditions'])) {
        $queryOptions['conditions'] = $params['conditions'];
      }

      if (!empty($searchkey)) {
        $queryOptions['conditions']['like'] = ['news.title' => '%'.$searchkey.'%'];
        $queryOptions['conditions']['or_like'] = ['news.content' => '%'.$searchkey.'%'];
      }

      if (!empty($id)) {
        $queryOptions['conditions'] = ['news.news_id' => $id];
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

  public function add_news($params = []){
    $response['code'] = 0;
    $response['message'] = 'Success';

    try {
      if (empty($params)) {
        $response['code'] = -1;
        throw new Exception('Invalid parameter(s).');
      }

      $params['date_created'] = date('Y-m-d H:i:s');

      $result = $this->query->insert('news', $params);

      if (isset($result['code'])) {
        $response = array_merge($response, $result);
        throw new Exception($response['message']);
      }
    } catch (Exception $e) {
      $response['message'] =  (ENVIRONMENT !== 'production') ? $e->getMessage() : 'Something went wrong. Please try again.';
    }
    return $response;
  }

  public function update_news($id = NULL, $params = []){
    $response['code'] = 0;
    $response['message'] = 'Success';

    try {
      if (empty($params)) {
        $response['code'] = -1;
        throw new Exception('Invalid parameter(s).');
      }

      $result = $this->query->update(
        'news',
        array(
          'news_id' => $id
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
