<?php
if (!defined("BASEPATH"))
    exit("No direct script access allowed");

class News_model extends CI_Model {

	public function __construct()
	{
		$this->load->database();
		$this->load->library('query');
	}

  public function load_news($params = []) {
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

      // parse params
      $searchkey = $params['searchkey'];
      $start = $params['start'];
      $limit = $params['limit'];
      $slug = $params['slug'];
      $id = $params['id'];
      $status = $params['status'];
      $id = ($id != 'all') ? decrypt(urldecode($id)) : $id;

      // set default fields
      $default_fields = 'news.news_id news_id, news.title title, news.tags tags,
                          news.status status, news.slug,news.content content ,news.date_posted date_posted,
                          news.date_updated date_updated, news_type.type_name type_name,
                          news_type.slug type_slug, users.first_name first_name,
                          users.last_name last_name,
                          (SELECT SUM(`news_clicks`.`num_clicks`)
                            FROM `news_clicks`
                            WHERE `news_clicks`.`news_news_id` = `news_id`
                          ) `numHits`';

      // get additional fields
      if (!empty($params['additional_fields'])) {
        $default_fields .= ',' . $params['additional_fields'];
      }

      // set query options
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
        "order" => 'news.date_posted DESC',
        'start' => $start,
        'limit' => $limit
      );

      // set additional conditions
      $queryOptions['conditions'] = $params['conditions'] ?? [];

      // set search key
      if (!empty($searchkey)) {
        $like = (count($queryOptions['conditions']) > 0) ? 'or_like' : 'like';
        $queryOptions['conditions'][$like] = array_merge(
          $queryOptions['conditions'][$like] ?? [],
          ['news.title' => $searchkey]
        );
        $queryOptions['conditions']['or_like'] = [
          'news.content' => $searchkey
        ];
      }

      // set id (for specific search)
      if (!empty($id) && $id != 'all') {
        $queryOptions['conditions']['and'] = array_merge(
          $queryOptions['conditions']['and'] ?? [],
          ['news.news_id' => $id]
        );
      }

      // set slug for news type
      if (!empty($slug)) {
        $queryOptions['conditions']['and'] = array_merge(
          $queryOptions['conditions']['and'] ?? [],
          ['news_type.slug' => $slug]
        );
      }

      if (!empty($status) && $status != 'all') {
        $queryOptions['conditions']['and'] = array_merge(
          $queryOptions['conditions']['and'] ?? [],
          ['news.status' => $status]
        );
      }

      // execute query
      $result = $this->query->select($queryOptions);
      $queryOptions['fields'] = 'COUNT(news.news_id) total_records';

      if (empty($searchkey)) {
        unset($queryOptions['start']);
        unset($queryOptions['limit']);
      }
      $result2 = $this->query->select($queryOptions);

      if (isset($result['code'])) { // if 'code' index exists (means SQL error),...
        // ...merge SQL error object to default response
        $response = array_merge($response, $result);
        // ...and throw Exception
        throw new Exception($response['message']);
      } else if (!empty($result)) { // if $result has data,...
        // ...and get queried data
        $response['data']['records'] =  (count($result) >= 1 && (empty($id) || $id == 'all')) ? encrypt_id($result) : encrypt_id($result[0]);
        $response['data']['total_records'] = $result2[0]['total_records'];
      } else { // else, throw Exception
        throw new Exception('Failed to retrieve details.');
      }
    } catch (Exception $e) { // catch Exception
      $response['message'] = (ENVIRONMENT !== 'production') ? $e->getMessage() : 'Something went wrong. Please try again.';
    }
    // return response data
    return $response;
  }

  public function add_news($params = []) {
    $response['code'] = 0;
    $response['message'] = 'Success';

    try {
      if (empty($params)) {
        $response['code'] = -1;
        throw new Exception('Invalid parameter(s).');
      }

      // check if News already exists
      $doesNewsExist = $this->load_news([
        'searchkey' => '',
        'start' => 0,
        'limit'=> 1,
        'id' => 0,
        'slug' => '',
        'conditions' => [
          'or_like' => [
            'title' => $params['title'],
            'content' => $params['content'],
            'news.slug' => url_title($params['title'],'-',true)
          ]
        ]
      ]);

      // if News is already existing, set response code and throw an Exception
      if ($doesNewsExist['code'] == 0 && !empty($doesNewsExist['data'])) {
        $response['code'] = -1;
        throw new Exception('News already exists!');
      }

      $params['news_type_type_id'] = decrypt(urldecode($params['news_type_type_id']));

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

  public function update_news($id = NULL, $params = []) {
    $response['code'] = 0;
    $response['message'] = 'Success';

    $id = decrypt(urldecode($id)) ?? 0;

    try {
      if (empty($params)) {
        $response['code'] = -1;
        throw new Exception('Invalid parameter(s).');
      }

      $params['news_type_type_id'] = decrypt(urldecode($params['news_type_type_id']));

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

  public function get_newstype() {
    $response['code'] = 0;
    $response['message'] = 'Success';

    try {
      $result = $this->query->select(
        array(
        'table' => 'news_type',
        'fields' => '*'
      ));
      if (isset($result['code'])) { // if 'code' index exists (means SQL error),...
        // ...merge SQL error object to default response
        $response = array_merge($response, $result);
        // ...and throw Exception
        throw new Exception($response['message']);
      } else if (!empty($result)) { // if $result has data,...
        // ...and get queried data
        $response['data'] = (count($result) >= 1 && empty($id)) ? encrypt_id($result) : encrypt_id($result[0]);
      } else { // else, throw Exception
        throw new Exception('Failed to retrieve details.');
      }

    } catch (Exception $e) {
        $response['message'] =  (ENVIRONMENT !== 'production') ? $e->getMessage() : 'Something went wrong. Please try again.';
    }
    return $response;
  }
}
?>
