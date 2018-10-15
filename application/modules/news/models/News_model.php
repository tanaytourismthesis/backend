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
        throw new Exception('LOAD_NEWS: Invalid parameter(s).');
      }

      // parse params
      $searchkey = $params['searchkey'];
      $start = $params['start'];
      $limit = $params['limit'];
      $slug = $params['slug'];
      $id = $params['id'];
      $id = ($id != 'all') ? decrypt(urldecode($id)) : $id;
      $status = $params['status'];
      $newsslug = $params['newsslug'];
      $newsslug = ($newsslug == 'all') ? '' : $newsslug;

      // set default fields
      $default_fields = 'news.news_id news_id, news.title title, news.tags tags,
                          news.status status, news.slug slug ,news.date_posted date_posted,
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

      // for news slug
      if (!empty($newsslug) && $newsslug != 'all') {
        $queryOptions['conditions']['and'] = array_merge(
          $queryOptions['conditions']['and'] ?? [],
          ['news.slug' => $newsslug]
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
        $response['data']['records'] =  (count($result) >= 1 && (empty($id) || $id == 'all') && (empty($newsslug) || $newsslug === 'all')) ? encrypt_id($result) : encrypt_id($result[0]);
        $response['data']['total_records'] = $result2[0]['total_records'];
      } else { // else, throw Exception
        $response['code'] = -1;
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
        throw new Exception('ADD_NEWS: Invalid parameter(s).');
      }

      // check if News already exists
      $doesNewsExist = $this->load_news([
        'searchkey' => '',
        'start' => 0,
        'limit'=> 1,
        'id' => 0,
        'slug' => '',
        'status' => 'all',
        'newsslug' => url_title($params['title'],'-',true),
        'conditions' => [
          'and' => [
            'title' => $params['title'],
            'content' => $params['content'],
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
        throw new Exception('UPDATE_NEWS: Invalid parameter(s).');
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
        $response['code'] = -1;
        throw new Exception('Failed to retrieve details.');
      }

    } catch (Exception $e) {
        $response['message'] =  (ENVIRONMENT !== 'production') ? $e->getMessage() : 'Something went wrong. Please try again.';
    }
    return $response;
  }

  public function load_newsclick($id = NULL){
    $response['code'] = 0;
    $response['message'] = 'Success';
    $datetoday = date('Y-m-d 00:00:00');

    try{
      if (empty($id)) {
        // set error code and throw an Exception
        $response['code'] = -1;
        throw new Exception('Invalid parameter(s).');
      }

      $querynew = array(
        'table' => 'news_clicks',
        'fields' => '*',
        'conditions' => array (
          'news_news_id' => $id,
          'click_date'=> $datetoday
        ),
        'start' => 0,
        'limit' => 1
      );

      $result = $this->query->select($querynew);

      if (isset($result['code'])) { // if 'code' index exists (means SQL error),...
        // ...merge SQL error object to default response
        $response = array_merge($response, $result);
        // ...and throw Exception
        throw new Exception($response['message']);
      } else if (!empty($result)) { // if $result has data,...
        // ...and get queried data
        $response['data'] = (count($result) >= 1 && empty($id)) ? encrypt_id($result) : encrypt_id($result[0]);
      } else { // else, throw Exception
        $response['code'] = -1;
        throw new Exception('Failed to retrieve details.');
      }


    } catch (Exception $e){
      $response['message'] =  (ENVIRONMENT !== 'production') ? $e->getMessage() : 'Something went wrong. Please try again.';
    }

    return $response;
  }

  public function updatenewsclick($id = NULL, $numclicks = NULL){
    $response['code'] = 0;
    $response['message'] = 'Success';

    $datetoday = date('Y-m-d 00:00:00');

    try{
      if (empty($id) || empty($numclicks)) {
        // set error code and throw an Exception
        $response['code'] = -1;
        throw new Exception('Invalid parameter(s).');
      }

      // TODO: Add checking of Site Cookie to ensure unique news click count

      $result = $this->query->update(
        'news_clicks',
    		array(
    			'click_id' => $id
    		),
    		array(
    			'num_clicks' => intval($numclicks) + 1,
    		)
      );

      if (isset($result['code'])) { // if 'code' index exists (means SQL error),...
        // ...merge SQL error object to default response
        $response = array_merge($response, $result);
        // ...and throw Exception
        throw new Exception($response['message']);
      } else if (!empty($result)) { // if $result has data,...
        // ...and get queried data
        $response['data'] = (count($result) >= 1 && empty($id)) ? encrypt_id($result) : encrypt_id($result[0]);
      } else { // else, throw Exception
        $response['code'] = -1;
        throw new Exception('Failed to update news click.');
      }


    } catch (Exception $e){
      $response['message'] =  (ENVIRONMENT !== 'production') ? $e->getMessage() : 'Something went wrong. Please try again.';
    }

    return $response;
  }

  public function addnewsclick($id = NULL){
    $response['code'] = 0;
    $response['message'] = 'Success';

    $datetoday = date('Y-m-d');
    try{
      if (empty($id)) {
        // set error code and throw an Exception
        $response['code'] = -1;
        throw new Exception('Invalid parameter(s).');
      }

      // TODO: Add checking of Site Cookie to ensure unique news click count

      $result = $this->query->insert(
        'news_clicks',
        array(
          'num_clicks' => '1',
          'click_date' => $datetoday,
          'news_news_id' => $id
        ),
        TRUE
      );

      if (isset($result['code'])) { // if 'code' index exists (means SQL error),...
        // ...merge SQL error object to default response
        $response = array_merge($response, $result);
        // ...and throw Exception
        throw new Exception($response['message']);
      } else if ($result) {
        $response['data']['click_id'] = encrypt($result['id']);
      } else { // else, throw Exception
        $response['code'] = -1;
        throw new Exception('Failed to add news click.');
      }
    } catch (Exception $e){
      $response['message'] = (ENVIRONMENT !== 'production') ? $e->getMessage() : 'Something went wrong. Please try again.';
    }

    return $response;
  }

  public function popular_news($top = 5) {
    $top = empty($top) ? 5 : $top;

    $response['code'] = 0;
    $response['message'] = 'Success';

    try {
      $queryOptions = [
        'table' => 'news',
        'fields' => 'title, news_type.slug news_type_slug, news.slug news_slug, SUM(num_clicks) click_count',
        'conditions' => [
          'news_type.slug' => 'news-and-update'
        ],
        'joins' => array(
          'news_type' => array(
            'type' => 'left',
            'type_id' => 'news_type_type_id'
          ),
          'news_clicks' => array(
            'type' => 'inner',
            'news_id' => 'news_news_id'
          )
        ),
        'order' => 'click_count DESC',
        'group' => 'title',
        'start' => 0,
        'limit' => $top
      ];

      $result = $this->query->select($queryOptions);

      if (isset($result['code'])) { // if 'code' index exists (means SQL error),...
        // ...merge SQL error object to default response
        $response = array_merge($response, $result);
        // ...and throw Exception
        throw new Exception($response['message']);
      } else if ($result) {
        $response['data'] = $result;
      } else { // else, throw Exception
        $response['code'] = -1;
        throw new Exception('Failed to retrieve details.');
      }
    } catch (Exception $e) {
      $response['code'] = -1;
      $response['message'] = $e->getMessage();
    }

    return $response;
  }

  public function top_contributors($top = 5) {
    $top = empty($top) ? 5 : $top;

    $response['code'] = 0;
    $response['message'] = 'Success';

    try {
      $queryOptions = [
        'table' => 'users',
        'fields' => 'first_name, last_name, COUNT(news_id) contrib_count',
        'joins' => array(
          'news' => array(
            'type' => 'inner',
            'user_id' => 'users_user_id'
          ),
        ),
        'order' => 'contrib_count DESC',
        'group' => 'user_id',
        'start' => 0,
        'limit' => $top
      ];

      $result = $this->query->select($queryOptions);

      if (isset($result['code'])) { // if 'code' index exists (means SQL error),...
        // ...merge SQL error object to default response
        $response = array_merge($response, $result);
        // ...and throw Exception
        throw new Exception($response['message']);
      } else if ($result) {
        $response['data'] = $result;
      } else { // else, throw Exception
        $response['code'] = -1;
        throw new Exception('Failed to retrieve details.');
      }
    } catch (Exception $e) {
      $response['code'] = -1;
      $response['message'] = $e->getMessage();
    }

    return $response;
  }
}
?>
