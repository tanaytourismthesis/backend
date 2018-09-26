<?php
if (!defined("BASEPATH"))
    exit("No direct script access allowed");

class Hf_model extends CI_Model {

	public function __construct()
	{
		$this->load->database();
		$this->load->library('query');
	}

  public function load_hane($params = []) {
    $response['code'] = 0;
    $response['message'] = 'Success';

    try {
      if (empty($params)) {
        $response['code'] = -1;
        throw new Exception('LOAD_HANEs: Invalid parameter(s).');
      }

      $searchkey = $params['searchkey'];
      $start = $params['start'];
      $limit = $params['limit'];
      $id = decrypt(urldecode($params['id'])) ?? 0;

      $default_fields = 'hotel_id, hotel_name, hotel_image';

      if (!empty($params['additional_fields'])) {
        $default_fields .= ',' . $params['additional_fields'];
      }

      $queryOptions = array(
        'table' => 'hotel',
        'fields' => $default_fields,
        'order' => 'hotel_name ASC',
        'start' => $start,
        'limit' => $limit
      );

      $queryOptions['conditions'] = $params['conditions'] ?? [];

      if (!empty($searchkey)) {
        $like = (count($queryOptions['conditions']) > 0) ? 'or_like' : 'like';
        $queryOptions['conditions'][$like] = array_merge(
          $queryOptions['conditions'][$like] ?? [],
          [
            'hotel_name' => $searchkey,
            'address' => $searchkey
          ]
        );
      }

      if (!empty($id)) {
        $queryOptions['conditions']['and'] = array_merge(
          $queryOptions['conditions']['and'] ?? [],
          ['hotel_id' => $id]
        );
      }

      $result = $this->query->select($queryOptions);

      $queryOptions['fields'] = 'COUNT(hotel_id) total_records';
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

  public function update_hane($id = NULL, $params = []) {
    $response['code'] = 0;
    $response['message'] = 'Success';

    $id = decrypt(urldecode($id)) ?? 0;

    try {
      if (empty($id) || empty($params)) {
        $response['code'] = -1;
        throw new Exception('UPDATE_HANE: Invalid parameter(s).');
      }

      $result = $this->query->update(
        'hotel',
        array(
          'hotel_id' => $id
        ),
        $params
      );

      if (isset($result['code'])) {
        $response = array_merge($response, $result);
        throw new Exception($response['message']);
      }
    } catch (Exception $e) { // catch Exception
      $response['message'] =  (ENVIRONMENT !== 'production') ? $e->getMessage() : 'Something went wrong. Please try again.';
    }
    return $response;
  }

  public function add_new_hane($params = []) {
    $response['code'] = 0;
    $response['message'] = 'Success';

    try {
      if (empty($params)) {
        $response['code'] = -1;
        throw new Exception('ADD_NEW_HANE: Invalid parameter(s).');
      }

      $doesHANEExists = $this->load_hane([
        'searchkey' => '',
        'start' => 0,
        'limit'=> 1,
        'id' => 0,
        'conditions' => [
          'like' => [
            'hotel_name' => $params['hotel_name']
          ]
        ]
      ]);

      if ($doesGalleryExists['code'] == 0 && !empty($doesHANEExists['data'])) {
        $response['code'] = -1;
        throw new Exception('H.A.N.E. already exists!');
      }

      $result = $this->query->insert('hotel', $params, TRUE);

      if (isset($result['response']['code'])) {
        $response = array_merge($response, $result['response']);
        throw new Exception($response['message']);
      } else {
        $response['data'] = [ 'hotel_id' => encrypt_id($result['id']) ];
      }
    } catch (Exception $e) {
      $response['message'] =  (ENVIRONMENT !== 'production') ? $e->getMessage() : 'Something went wrong. Please try again.';
    }
    return $response;
  }

  public function get_gallery_items($params = []) {
    $response['code'] = 0;
    $response['message'] = 'Success';

    try {
      if (empty($params)) {
        $response['code'] = -1;
        throw new Exception('LOAD_GALLERY_ITEMS: Invalid parameter(s).');
      }

      $searchkey = $params['searchkey'];
      $start = $params['start'];
      $limit = $params['limit'];
      $id = decrypt(urldecode($params['id'])) ?? 0;
      $gallery = decrypt(urldecode($params['gallery'])) ?? 0;

      $default_fields = '*';

      $queryOptions = array(
        'table' => 'gallery_items',
        'fields' => $default_fields,
        'order' => 'date_uploaded ASC',
        'start' => $start,
        'limit' => $limit
      );

      $queryOptions['conditions'] = $params['conditions'] ?? [];

      if (!empty($searchkey)) {
        $like = (count($queryOptions['conditions']) > 0) ? 'or_like' : 'like';
        $queryOptions['conditions'][$like] = array_merge(
          $queryOptions['conditions'][$like] ?? [],
          ['gallery_items.title' => $searchkey]
        );
        $queryOptions['conditions']['or_like'] = [
          'gallery_items.caption' => $searchkey
        ];
      }

      if (!empty($gallery)) {
        $queryOptions['conditions']['and'] = array_merge(
          $queryOptions['conditions']['and'] ?? [],
          ['gallery_items.gallery_gallery_id' => $gallery]
        );
      }

      if (!empty($id)) {
        $queryOptions['conditions']['and'] = array_merge(
          $queryOptions['conditions']['and'] ?? [],
          ['gallery_items.gallery_item_id' => $id]
        );
      }

      $result = $this->query->select($queryOptions);

      $queryOptions['fields'] = 'COUNT(gallery_items.gallery_item_id) total_records';
      unset($queryOptions['start']);
      unset($queryOptions['limit']);

      $result2 = $this->query->select($queryOptions);

      if (isset($result['code'])) {
        $response = array_merge($response, $result);
        throw new Exception($response['message']);
      } else if (!empty($result)) {
        $response['data']['records'] = encrypt_id($result);
        $response['data']['total_records'] = $result2[0]['total_records'];
      } else {
        throw new Exception('Failed to retrieve details.');
      }
    } catch (Exception $e) {
      $response['message'] = (ENVIRONMENT !== 'production') ? $e->getMessage() : 'Something went wrong. Please try again.';
    }
    return $response;
  }

  public function update_gallery_item($id = NULL, $params = []) {
    $response['code'] = 0;
    $response['message'] = 'Success';

    $id = decrypt(urldecode($id)) ?? 0;

    try {
      if (empty($id) || empty($params)) {
        $response['code'] = -1;
        throw new Exception('UPDATE_GALLERY_ITEM: Invalid parameter(s).');
      }

      if (isset($params['gallery_gallery_id'])) {
        $params['gallery_gallery_id'] = decrypt(urldecode($params['gallery_gallery_id']));
      }

      $result = $this->query->update(
        'gallery_items',
        array(
          'gallery_item_id' => $id
        ),
        $params
      );

      if (isset($result['code'])) {
        $response = array_merge($response, $result);
        throw new Exception($response['message']);
      }
    } catch (Exception $e) { // catch Exception
      $response['message'] =  (ENVIRONMENT !== 'production') ? $e->getMessage() : 'Something went wrong. Please try again.';
    }
    return $response;
  }

  public function add_gallery_item($params = []) {
    $response['code'] = 0;
    $response['message'] = 'Success';

    try {
      if (empty($params)) {
        $response['code'] = -1;
        throw new Exception('ADD_GALLERY_ITEM: Invalid parameter(s).');
      }

      if (isset($params['gallery_gallery_id'])) {
        $params['gallery_gallery_id'] = decrypt(urldecode($params['gallery_gallery_id']));
      }

      $result = $this->query->insert('gallery_items', $params, TRUE);

      if (isset($result['response']['code'])) {
        $response = array_merge($response, $result['response']);
        throw new Exception($response['message']);
      } else {
        $response['data'] = [ 'gallery_item_id' => encrypt_id($result['id']) ];
      }
    } catch (Exception $e) {
      $response['message'] =  (ENVIRONMENT !== 'production') ? $e->getMessage() : 'Something went wrong. Please try again.';
    }
    return $response;
  }

}
?>
