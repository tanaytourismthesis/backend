<?php
if (!defined("BASEPATH"))
    exit("No direct script access allowed");

class Page_model extends CI_Model {

	public function __construct()
	{
		$this->load->database();
		$this->load->library('query');
	}

  public function load_pagecontentlist($params = []) {
    $response['code'] = 0;
    $response['message'] = 'Success';

    try {
      if (empty($params)) {
        $response['code'] = -1;
        throw new Exception('LOAD_PAGE_CONTENTS: Invalid parameter(s).');
      }

      $searchkey = $params['searchkey'];
      $start = $params['start'];
      $limit = $params['limit'];
      $id = $params['id'];
      $id = ($id != 'all') ? decrypt(urldecode($id)) : $id;
      $slug = $params['slug'];
      $tag = $params['tag'];
      $isShown = $params['isShown'];
      $content_slug = $params['content_slug'];

      $default_fields = 'page_content.content_id, page_content.title, page_content.slug content_slug, page_content.date_posted,
                          page_content.tag, page_content.order_position, page.page_id, page.page_name,
                          page_content.isShown, IF (page_content.isShown=1, "Yes", "No") show_type';

      if (!empty($params['additional_fields'])) {
        $default_fields .= ',' . $params['additional_fields'];
      }

      $queryOptions = array(
        'table' => 'page_content',
        'fields' => $default_fields,
        'joins' => array(
          'page' => array(
            'type' => 'left',
            'page.page_id' => 'page_content.page_page_id'
          ),
          'users' => array(
            'type' => 'left',
            'page_content.users_user_id' => 'users.user_id'
          )
        ),
        'start' => $start,
        'limit' => $limit
      );

      $queryOptions['conditions'] = $params['conditions'] ?? [];

      if (!empty($searchkey)) {
        $like = (count($queryOptions['conditions']) > 0) ? 'or_like' : 'like';
        $queryOptions['conditions'][$like] = array_merge(
          $queryOptions['conditions'][$like] ?? [],
          ['page_content.title' => $searchkey]
        );
        $queryOptions['conditions']['or_like'] = [
          'page_content.content' => $searchkey,
          'page_content.slug' => $searchkey
        ];
      }

      if (!empty($slug) && $slug != 'gallery' && $slug != 'pages') {
        $queryOptions['conditions']['and'] = array_merge(
          $queryOptions['conditions']['and'] ?? [],
          ['page.slug' => $slug]
        );
      }

      if (!empty($tag)) {
        $queryOptions['conditions']['and'] = array_merge(
          $queryOptions['conditions']['and'] ?? [],
          ['page_content.tag' => $tag]
        );
      }

      if (!empty($isShown)) {
        $queryOptions['conditions']['and'] = array_merge(
          $queryOptions['conditions']['and'] ?? [],
          ['page_content.isShown' => $isShown]
        );
      }

      if (!empty($content_slug)) {
        $queryOptions['conditions']['and'] = array_merge(
          $queryOptions['conditions']['and'] ?? [],
          ['page_content.slug' => $content_slug]
        );
      }

      if (!empty($id) && $id != 'all') {
        $queryOptions['conditions']['and'] = array_merge(
          $queryOptions['conditions']['and'] ?? [],
          ['content_id' => $id]
        );
      }

      $result = $this->query->select($queryOptions);

      $queryOptions['fields'] = 'COUNT(page_content.content_id) total_records';
      unset($queryOptions['start']);
      unset($queryOptions['limit']);

      $result2 = $this->query->select($queryOptions);

      if (isset($result['code'])) {
        $response = array_merge($response, $result);
        throw new Exception($response['message']);
      } else if (!empty($result)) {
        $response['data']['records'] = (count($result) >= 1 && empty($id)) ? encrypt_id($result) : encrypt_id($result[0]);
        $response['data']['total_records'] = $result2[0]['total_records'];
      } else {
        $response['code'] = -1;
        throw new Exception('Failed to retrieve details.');
      }
    } catch (Exception $e) {
      $response['message'] = (ENVIRONMENT !== 'production') ? $e->getMessage() : 'Something went wrong. Please try again.';
    }
    return $response;
  }

  public function update_page_content($id = NULL, $params = []){
    $response['code'] = 0;
    $response['message'] = 'Success';

    $id = decrypt(urldecode($id)) ?? 0;
    unset($params['content_id']);
    try {
      if (empty($params)) {
        $response['code'] = -1;
        throw new Exception('UPDATE_PAGE_CONTENT: Invalid parameter(s).');
      }

      $params['page_page_id'] = decrypt(urldecode($params['page_page_id']));
      $result = $this->query->update(
        'page_content',
        array(
          'content_id' => $id
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

  public function load_pagelist($params = []) {
    $response['code'] = 0;
    $response['message'] = 'Success';

    try {
      if (empty($params)) {
        $response['code'] = -1;
        throw new Exception('LOAD_PAGE_LIST: Invalid parameter(s).');
      }

      $searchkey = $params['searchkey'];
      $start = $params['start'];
      $limit = $params['limit'];
      $id = decrypt(urldecode($params['id'])) ?? 0;
      $slug = $params['slug'];

      $default_fields = '*';

      $queryOptions = array(
        'table' => 'page',
        'fields' => $default_fields,
        'conditions' => [
          'and' => [
            'hasGallery' => 1,
            'carouselOnly' => 0
          ]
        ]
      );

      if (!empty($limit)) {
        $queryOptions['start'] = $start;
        $queryOptions['limit'] = $limit;
      }

      if (!empty($params['conditions'])) {
        $queryOptions['conditions'] = $params['conditions'];
      }

      if (!empty($searchkey)) {
        $like = isset($queryOptions['conditions']) ? 'or_like' : 'like';
        $queryOptions['conditions'][$like] = ['page_name' => $searchkey];
        $queryOptions['conditions']['or_like'] = ['slug' => $searchkey];
      }

      if (!empty($slug) && !($slug == 'gallery' || $slug == 'pages')) {
        $queryOptions['conditions']['and'] = ['slug' => $slug];
      }

      if (!empty($id)) {
        $queryOptions['conditions'] = ['page_id' => $id];
      }

      $result = $this->query->select($queryOptions);

      $queryOptions['fields'] = 'COUNT(page.page_id) total_records';
      unset($queryOptions['start']);
      unset($queryOptions['limit']);

      $result2 = $this->query->select($queryOptions);

      if (isset($result['code'])) {
        $response = array_merge($response, $result);
        throw new Exception($response['message']);
      } else if (!empty($result)) {
        $response['data']['records'] = (count($result) >= 1 && empty($id)) ? encrypt_id($result) : encrypt_id($result[0]);
        $response['data']['total_records'] = $result2[0]['total_records'];
      } else {
        $response['code'] = -1;
        throw new Exception('Failed to retrieve details.');
      }
    } catch (Exception $e) {
      $response['message'] = (ENVIRONMENT !== 'production') ? $e->getMessage() : 'Something went wrong. Please try again.';
    }
    return $response;
  }

  public function add_page_content($params = []) {
    $response['code'] = 0;
    $response['message'] = 'Success';

    try {
      if (empty($params)) {
        $response['code'] = -1;
        throw new Exception('ADD_PAGE_CONTENT: Invalid parameter(s).');
      }

      unset($params['page_slug']);
      unset($params['page_tag']);
      $result = $this->query->insert('page_content', $params);

      if (isset($result['code'])) {
        $response = array_merge($response, $result);
        throw new Exception($response['message']);
      }
    } catch (Exception $e) {
      $response['message'] =  (ENVIRONMENT !== 'production') ? $e->getMessage() : 'Something went wrong. Please try again.';
    }
    return $response;
  }

  public function getPageTags($slug = '') {
    $tags = [
      'hca' => ['history', 'culture', 'arts'],
      'fc' => ['festival', 'cuisine'],
      'pp' => ['people', 'places']
    ];

    $all = [];
    foreach ($tags as $index => $val) {
      $all = array_merge($all, $val);
    }

    return (!empty($slug) && array_key_exists($slug, $tags)) ? $tags[$slug] : $all;
  }

  public function load_pageclick($id = NULL){
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
        'table' => 'page_click',
        'fields' => '*',
        'conditions' => array (
          'page_content_content_id' => $id,
          'date'=> $datetoday
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

  public function updatepageclick($id = NULL, $numclicks = NULL){
    $response['code'] = 0;
    $response['message'] = 'Success';

    $datetoday = date('Y-m-d 00:00:00');

    try{
      if (empty($id) || empty($numclicks)) {
        // set error code and throw an Exception
        $response['code'] = -1;
        throw new Exception('Invalid parameter(s).');
      }

      // TODO: Add checking of Site Cookie to ensure unique page click count

      $result = $this->query->update(
        'page_click',
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
      } else { // else, throw Exception
        $response['code'] = -1;
        throw new Exception('Failed to Update details.');
      }
    } catch (Exception $e){
      $response['message'] =  (ENVIRONMENT !== 'production') ? $e->getMessage() : 'Something went wrong. Please try again.';
    }

    return $response;
  }

  public function addpageclick($id = NULL){
    $response['code'] = 0;
    $response['message'] = 'Success';

    $datetoday = date('Y-m-d');
    try{
      if (empty($id)) {
        // set error code and throw an Exception
        $response['code'] = -1;
        throw new Exception('Invalid parameter(s).');
      }

      // TODO: Add checking of Site Cookie to ensure unique page click count

      $result = $this->query->insert(
        'page_click',
        array(
          'num_clicks' => '1',
          'click_date' => $datetoday,
          'page_content_content_id' => $id
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
        throw new Exception('Failed to Update details.');
      }
    } catch (Exception $e){
      $response['message'] =  (ENVIRONMENT !== 'production') ? $e->getMessage() : 'Something went wrong. Please try again.';
    }
    return $response;
  }

  public function record_site_visit() {
    $response['code'] = 0;
    $response['message'] = 'Success';

    $datetoday = date('Y-m-d 00:00:00');
    try{
      // TODO: Add checking of Site Cookie to ensure unique visit count only

      $check = $this->query->select([
        'table' => 'site_visit',
        'conditions' => [
          'visit_date' => $datetoday
        ]
      ]);

      if (!$check) {
        $result = $this->query->insert(
          'site_visit',
          array(
            'visit_count' => '1',
            'visit_date' => $datetoday
          ),
          TRUE
        );
      } else {
        $result = $this->query->update(
          'site_visit',
          [
            'visit_id' => $check[0]['visit_id']
          ],
          [
            'visit_count' => intval($check[0]['visit_count']) + 1,
            'visit_date' => $datetoday
          ]
        );
      }

      if (isset($result['code'])) { // if 'code' index exists (means SQL error),...
        // ...merge SQL error object to default response
        $response = array_merge($response, $result);
        // ...and throw Exception
        throw new Exception($response['message']);
      } else if ($result) {
        $response['data']['visit_id'] = encrypt($result['id']);
      } else { // else, throw Exception
        $response['code'] = -1;
        throw new Exception('Failed to record site visit.');
      }
    } catch (Exception $e){
      $response['message'] =  (ENVIRONMENT !== 'production') ? $e->getMessage() : 'Something went wrong. Please try again.';
    }
    return $response;
  }

  public function popular_page_contents($top = 5) {
    $top = empty($top) ? 5 : $top;

    $response['code'] = 0;
    $response['message'] = 'Success';

    try {
      $queryOptions = [
        'table' => 'page_content',
        'fields' => 'title, tag, slug, SUM(num_clicks) click_count',
        'joins' => array(
          'page_click' => array(
            'type' => 'inner',
            'content_id' => 'page_content_content_id'
          ),
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
        'fields' => 'first_name, last_name, COUNT(content_id) contrib_count',
        'joins' => array(
          'page_content' => array(
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
