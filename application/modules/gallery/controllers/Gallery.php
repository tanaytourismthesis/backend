<?php
if (!defined("BASEPATH"))
    exit("No direct script access allowed");

class Gallery extends MX_Controller {

  private $page_alias;
  private $page_caption;
  private $page_icon;
  private $tag;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('gallery/gallery_model');
    $this->page_caption = $this->session->userdata('active_page_caption');
    $this->page_alias = $this->session->userdata('active_page_alias');
    $this->tag = $this->session->userdata('active_page_method');
    $this->page_icon = $this->session->userdata('active_page_icon');
	}

  public function index() {

    $slug = str_replace('manage-', '', $this->page_alias);
    $pagelist_result = modules::run('pages/load_pagelist', '', 0, 0, $slug, FALSE);

    $pagelist = ($pagelist_result['response']) ? $pagelist_result['data']['records'] : [];

    $data = [
      'slug' => $this->page_alias,
      'icon' => $this->page_icon,
      'pagelist' => $pagelist
    ];

    $this->template->build_template(
      $this->page_caption, //Page Title
      array( // Views
        array(
          'view' => 'components/search-bar',
          'data' => $data
        ),
        array(
          'view' => 'gallery/gallery',
          'data' => $data
        ),
        array(
          'view' => 'components/navigator',
          'data' => [
            'modal_name' => '#modalGallery',
            'btn_add_label' => 'Add <span class="hidden-xs">Album</span>'
          ]
        )
      ),
      array( // JavaScript Files
        'assets/js/modules_js/gallery.js'
      ),
      array( // CSS Files
        'assets/css/gallery.css'
      ),
      array( // Meta Tags

      ),
      'backend' // template page
    );
  }

  public function load_gallery() {
    $data['response'] = FALSE;

    try {
      $post = (isJsonPostContentType()) ? decodeJsonPost($this->security->xss_clean($this->input->raw_input_stream)) : $this->input->post();

      $searchkey = $post['searchkey'] ?? NULL;
  		$limit = $post['limit'] ?? NULL;
  		$start = $post['start'] ?? NULL;
  		$id = $post['id'] ?? NULL;
  		$slug = $post['slug'] ?? NULL;
      $isCarousel = $post['isCarousel'] ?? NULL;

      if ($searchkey === NULL || $start === NULL || $limit === NULL) {
  			throw new Exception("LOAD GALLERY: Invalid parameter(s)");
  		}

      $params = [
        'searchkey' => $searchkey,
        'start' => $start,
        'limit' => $limit,
        'id' => urldecode($id),
        'slug' => str_replace('manage-', '', $slug)
      ];

      if ($isCarousel !== NULL) {
        $params['conditions'] = [
          'and' => [
            'isCarousel' => $isCarousel
          ]
        ];
      }

      $result = $this->gallery_model->load_gallery($params);

      $data['message'] = $result['message'];

      if (!empty($result) && $result['code'] == 0 && !empty($result['data'])) {
        $data['response'] = TRUE;
        $data['data'] = $result['data'];
      }
    } catch (Exception $e) {
      $data['message'] = $e->getMessage();
    }

    header( 'Content-Type: application/x-json' );
    echo json_encode( $data );
  }

  public function update_gallery($params = [], $ajax = TRUE) {
    $data['response'] = FALSE;
    $params = ($ajax) ? $this->input->post('params') : $params;
    $params = format_parameters(clean_parameters($params, []));
    $id = $params['gallery_id'] ?? 0;

    if (isset($params['gallery_id'])) {
      unset($params['gallery_id']);
    }

		try {
      if (empty($id)) {
        throw new Exception('UPDATE GALLERY: Invalid parameter(s)');
      }

			$result = $this->gallery_model->update_gallery($id, $params);
      $data['message'] = $result['message'];

			if (!empty($result) && $result['code'] == 0) {
				$data['response'] = TRUE;
				$data['message'] = 'Successfully updated gallery.';
			}
		} catch (Exception $e) {
			$data['message'] = $e->getMessage();
		}

    if ($ajax) {
  		header( 'Content-Type: application/x-json' );
  		echo json_encode( $data );
    }
    return $data;
  }

  public function add_new_gallery() {
    $data['response'] = FALSE;
    $params = format_parameters(clean_parameters($this->input->post('params'), []));

		try {
      if (empty($params)) {
        throw new Exception('ADD NEW GALLERY: Invalid parameter(s)');
      }
      $params['slug'] = str_replace('manage-', '', $this->input->post('slug'));

			$result = $this->gallery_model->add_new_gallery($params);

      $data['message'] = $result['message'];
			if (!empty($result) && $result['code'] == 0) {
				$data['response'] = TRUE;
				$data['message'] = 'Successfully added new gallery.';
			}
		} catch (Exception $e) {
			$data['message'] = $e->getMessage();
		}

		header( 'Content-Type: application/x-json' );
		echo json_encode( $data );
  }

  public function get_gallery_items() {
    $data['response'] = FALSE;

    try {
      $post = (isJsonPostContentType()) ? decodeJsonPost($this->security->xss_clean($this->input->raw_input_stream)) : $this->input->post();

      $searchkey = $post['searchkey'] ?? NULL;
  		$limit = $post['limit'] ?? NULL;
  		$start = $post['start'] ?? NULL;
  		$id = $post['id'] ?? NULL;
  		$gallery = $post['gallery'] ?? NULL;

      if ($searchkey === NULL || $start === NULL) {
  			throw new Exception("LOAD GALLERY ITEMS: Invalid parameter(s)");
  		}

      $params = [
        'searchkey' => $searchkey,
        'start' => $start,
        'limit' => $limit,
        'id' => urldecode($id),
        'gallery' => urldecode($gallery)
      ];

      $result = $this->gallery_model->get_gallery_items($params);

      $data['message'] = $result['message'];

      if (!empty($result) && $result['code'] == 0 && !empty($result['data'])) {
        $data['response'] = TRUE;
        $data['data'] = $result['data'];
      }
    } catch (Exception $e) {
      $data['message'] = $e->getMessage();
    }

    header( 'Content-Type: application/x-json' );
    echo json_encode( $data );
  }

  public function update_gallery_item($params = [], $ajax = TRUE) {
    $data['response'] = FALSE;
    $params = ($ajax) ? json_decode($this->input->post('params'), true) : $params;
    $params = format_parameters(clean_parameters($params, ['caption']));
    $id = $params['gallery_item_id'] ?? 0;

    if (isset($params['gallery_item_id'])) {
      unset($params['gallery_item_id']);
    }

		try {
      if (empty($id) || empty($params)) {
        throw new Exception('UPDATE GALLERY ITEM: Invalid parameter(s)');
      }

			$result = $this->gallery_model->update_gallery_item($id, $params);
      $data['message'] = $result['message'];

			if (!empty($result) && $result['code'] == 0) {
				$data['response'] = TRUE;
				$data['message'] = 'Successfully updated gallery item.';

        if (isset($_FILES['file'])) {
          $res = $this->update_gallery_photo(
            [
              'gallery_item_id' => $id,
              'old_photo' => $params['image_filename']
            ],
            FALSE
          );

          $data = $res;
        }
			}
		} catch (Exception $e) {
			$data['message'] = $e->getMessage();
		}

    if ($ajax) {
  		header( 'Content-Type: application/x-json' );
  		echo json_encode( $data );
    }
    return $data;
  }

  public function update_gallery_photo($params = [], $ajax = TRUE) {
    $data['response'] = FALSE;
    $data['message'] = 'Failed';

    try {
      $photo = $_FILES['file'] ?? [];
      $old_photo = $params['old_photo'] ?? '';
      $gallery_item_id = $params['gallery_item_id'] ?? 0;
      $gallery_item_id = urldecode($gallery_item_id);

      if (empty($photo) || empty($gallery_item_id)) {
        throw new Exception('UPDATE GALLERY PHOTO: Invalid parameter(s).');
      }

      $name = $photo['name'];
      $ext = explode('.', $name);
      $ext = end($ext);
      $mime = $photo['type'];
      $size = $photo['size'] * 1e-6; // in MB
      $allowedExts = ['jpg','jpeg','png','gif','PNG','JPG','JPEG','GIF'];
      $allowedMimes = ['image/jpeg','image/jpg','image/png','image/gif'];

      if (!in_array($ext, $allowedExts) || !in_array($mime, $allowedMimes) || $size > MAX_FILESIZE_MB) {
        throw new Exception('UPDATE GALLERY PHOTO: Invalid file type or size. Please use image files only with no more than '.MAX_FILESIZE_MB.'MB.');
      }

      $newName = md5(decrypt($gallery_item_id) . date('Y-m-d H:i:s A')) . '.' . $ext;
      $source = $photo['tmp_name'];
      $folder = ENV['image_upload_path'] . 'gallery/';
      $target = $folder . $newName;

      $filepath = $folder . $old_photo;
      if (file_exists($filepath) && !empty($old_photo) && $old_photo != 'default-image.png') {
        unlink($filepath); // delete existing file
      }

      if(move_uploaded_file($source, $target)) {
        unset($_FILES['file']);
        $result = $this->update_gallery_item([
          [
            'name' => 'gallery_item_id',
            'value' => $gallery_item_id
          ],
          [
            'name' => 'image_filename',
            'value' => $newName
          ]
        ], FALSE);

        $data = $result;
        $data['data'] = ['image_filename' => $newName];
      }

    } catch (Exception $e) {
			$data['message'] = $e->getMessage();
		}

    if ($ajax) {
  		header( 'Content-Type: application/x-json' );
  		echo json_encode( $data );
    }
    return $data;
  }

  public function add_gallery_item($params = [], $ajax = TRUE) {
    $data['response'] = FALSE;
    $params = ($ajax) ? json_decode($this->input->post('params'), true) : $params;
    $params = format_parameters(clean_parameters($params, []));

    if (isset($params['gallery_item_id'])) {
      unset($params['gallery_item_id']);
    }

    if (isset($params['image_filename'])) {
      unset($params['image_filename']);
    }

		try {
      if (empty($params)) {
        throw new Exception('ADD GALLERY ITEM: Invalid parameter(s)');
      }

			$result = $this->gallery_model->add_gallery_item($params);
      $data['message'] = $result['message'];

			if (!empty($result) && $result['code'] == 0) {
        if (isset($_FILES['file'])) {
          $res = $this->update_gallery_photo(
            [
              'gallery_item_id' => $result['data']['gallery_item_id']
            ],
            FALSE
          );
        }
        $data['response'] = TRUE;
        $data['message'] = 'Successfully added gallery item.';
        if ($res && !$res['response']) {
          $data['message'] .= '<br>Please re-upload photo by editing this item.';
        }
			}
		} catch (Exception $e) {
			$data['message'] = $e->getMessage();
		}

    if ($ajax) {
  		header( 'Content-Type: application/x-json' );
  		echo json_encode( $data );
    }
    return $data;
  }

}
?>
