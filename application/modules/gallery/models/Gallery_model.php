<?php
if (!defined("BASEPATH"))
    exit("No direct script access allowed");

class Gallery_model extends CI_Model {

	public function __construct()
	{
		$this->load->database();
		$this->load->library('query');
	}

  public function load_gallery($params = []){
    $response['code'] = 0;
    $response['message'] = 'Success';

    try {
      if (empty($params)) {
        $response['code'] = -1;
        throw new Exception('LOAD_GALLERIES: Invalid parameter(s).');
      }

      $searchkey = $params['searchkey'];
      $start = $params['start'];
      $limit = $params['limit'];
      $id = decrypt(urldecode($params['id'])) ?? 0;
      $slug = $params['slug'];

      $default_fields = 'gallery.gallery_id, gallery.gallery_name, gallery.isActive,
                          IF (gallery.isActive=1, "Active", "Inactive") gallery_status,
                          IF (gallery.isCarousel=1, "Carousel", "Gallery") gallery_type,
                          gallery.isCarousel, gallery.page_page_id, page.page_name,
                          page.slug, page.hasGallery, page.carouselOnly';

      if (!empty($params['additional_fields'])) {
        $default_fields .= ',' . $params['additional_fields'];
      }

      $queryOptions = array(
        'table' => 'gallery',
        'fields' => $default_fields,
        'joins' => array(
          'page' => array(
            'type' => 'left',
            'page.page_id' => 'gallery.page_page_id'
          )
        ),
        'order' => 'page_name ASC, gallery_name ASC',
        'start' => $start,
        'limit' => $limit
      );

      if (!empty($params['conditions'])) {
        $queryOptions['conditions'] = $params['conditions'];
      }

      if (!empty($searchkey)) {
        $like = isset($queryOptions['conditions']) ? 'or_like' : 'like';
        $queryOptions['conditions'][$like] = ['gallery.gallery_name' => $searchkey];
      }

      if (!empty($slug) && $slug != 'gallery') {
        $queryOptions['conditions']['and'] = ['page.slug' => $slug];
      }

      if (!empty($id)) {
        $queryOptions['conditions'] = ['gallery_id' => $id];
      }

      $result = $this->query->select($queryOptions);

      $queryOptions['fields'] = 'COUNT(gallery.gallery_id) total_records';
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

  public function update_gallery($id = NULL, $params = []){
    $response['code'] = 0;
    $response['message'] = 'Success';

    $id = decrypt(urldecode($id)) ?? 0;

    try {
      if (empty($id) || empty($params)) {
        $response['code'] = -1;
        throw new Exception('UPDATE_GALLERY: Invalid parameter(s).');
      }

      $params['page_page_id'] = decrypt(urldecode($params['page_page_id']));

      $result = $this->query->update(
        'gallery',
        array(
          'gallery_id' => $id
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

  public function add_new_gallery($params = []){
    $response['code'] = 0;
    $response['message'] = 'Success';

    try {
      if (empty($params)) {
        $response['code'] = -1;
        throw new Exception('ADD_NEW_GALLERY: Invalid parameter(s).');
      }

      $doesGalleryExists = $this->load_gallery([
        'searchkey' => '',
        'start' => 0,
        'limit'=> 1,
        'id' => 0,
        'conditions' => [
          'like' => [
            'gallery_name' => $params['gallery_name']
          ],
          'and' => [
            'page_page_id' => decrypt(urldecode($params['page_page_id']))
          ]
        ],
        'slug' => $params['slug']
      ]);

      if ($doesGalleryExists['code'] == 0 && !empty($doesGalleryExists['data'])) {
        $response['code'] = -1;
        throw new Exception('Gallery already exists!');
      }

      // execute query
      $result = $this->query->insert('gallery', $params, TRUE);

      if (isset($result['response']['code'])) {
        $response = array_merge($response, $result['response']);
        throw new Exception($response['message']);
      } else {
        $response['data'] = [ 'user_id' => encrypt_id($result['id']) ];
      }
    } catch (Exception $e) {
      $response['message'] =  (ENVIRONMENT !== 'production') ? $e->getMessage() : 'Something went wrong. Please try again.';
    }
    return $response;
  }

}
?>
