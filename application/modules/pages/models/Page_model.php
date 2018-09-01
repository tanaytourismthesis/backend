<?php
if (!defined("BASEPATH"))
    exit("No direct script access allowed");

class Page_model extends CI_Model {

	public function __construct()
	{
		$this->load->database();
		$this->load->library('query');
	}

  public function load_pagelist($params = []){
    $response['code'] = 0;
    $response['message'] = 'Success';

    try {
      if (empty($params)) {
        $response['code'] = -1;
        throw new Exception('LOAD_PAGES: Invalid parameter(s).');
      }

      $searchkey = $params['searchkey'];
      $start = $params['start'];
      $limit = $params['limit'];
      $id = decrypt(urldecode($params['id'])) ?? 0;
      $slug = $params['slug'];
      $tag = $params['tag'];

      $default_fields = 'page_content.content_id, page_content.title,
                          page_content.slug content_slug, page_content.tag,
                          page_content.isShown, IF (page_content.isShown=1, "Yes", "No")
                          show_type, page_content.page_page_id, page.page_name,
                          page.slug page_slug';

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
          )
        ),
        'start' => $start,
        'limit' => $limit
      );

      if (!empty($params['conditions'])) {
        $queryOptions['conditions'] = $params['conditions'];
      }

      if (!empty($searchkey)) {
        $like = isset($queryOptions['conditions']) ? 'or_like' : 'like';
        $queryOptions['conditions'][$like] = ['page_content.title' => $searchkey];
        $queryOptions['conditions']['or_like'] = ['page_content.content' => $searchkey];
        $queryOptions['conditions']['or_like'] = ['page_content.slug' => $searchkey];
      }

      if (!empty($slug)) {
        $queryOptions['conditions']['and'] = ['page.slug' => $slug];
      }

      if (!empty($tag)) {
        $queryOptions['conditions']['and'] = ['page_content.tag' => $tag];
      }

      if (!empty($id)) {
        $queryOptions['conditions'] = ['content_id' => $id];
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
        throw new Exception('Failed to retrieve details.');
      }
    } catch (Exception $e) {
      $response['message'] = (ENVIRONMENT !== 'production') ? $e->getMessage() : 'Something went wrong. Please try again.';
    }
    return $response;
  }

}
?>
