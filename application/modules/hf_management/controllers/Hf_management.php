<?php
if (!defined("BASEPATH"))
    exit("No direct script access allowed");

class Hf_management extends MX_Controller {

  public function __construct()
  {
    parent::__construct();
		$this->load->model('hf_management/hf_model');
  }

  public function index() {
    $data = [];

    $this->template->build_template(
      'H.A.N.E. Finder',
      array(
        array(
          'view' => 'hane-finder',
          'data' => $data
        )
      ),
      array(
        'assets/js/modules_js/hane-finder.js',
        'assets/js/bootstrap-datetimepicker.min.js'
      ),
      array( // CSS Files
        'assets/css/bootstrap-datetimepicker.min.css',
        'assets/css/hane-finder.css'
      ),
      array( // Meta Tags

      ),
      'backend' // template page
    );
  }

  public function load_hane() {
    $data['response'] = FALSE;

    try {
      $post = (isJsonPostContentType()) ? decodeJsonPost($this->security->xss_clean($this->input->raw_input_stream)) : $this->input->post();

      $searchkey = $post['searchkey'] ?? NULL;
  		$limit = $post['limit'] ?? NULL;
  		$start = $post['start'] ?? NULL;
  		$id = $post['id'] ?? NULL;

      if ($searchkey === NULL || $start === NULL || $limit === NULL) {
  			throw new Exception("LOAD H.A.N.E.: Invalid parameter(s)");
  		}

      $params = [
        'searchkey' => $searchkey,
        'start' => $start,
        'limit' => $limit,
        'id' => urldecode($id),
      ];

      $result = $this->hf_model->load_hane($params);

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

  public function update_hane($params = [], $ajax = TRUE) {
    $data['response'] = FALSE;
    $params = ($ajax) ? json_decode($this->input->post('params'), true) : $params;
    $params = format_parameters(clean_parameters($params, []));
    $id = $params['hotel_id'] ?? 0;
    if (isset($params['hotel_id'])) {
      unset($params['hotel_id']);
    }

		try {
      if (empty($id)) {
        throw new Exception('UPDATE H.A.N.E.: Invalid parameter(s)');
      }

			$result = $this->hf_model->update_hane($id, $params);
      $data['message'] = $result['message'];

			if (!empty($result) && $result['code'] == 0) {
				$data['response'] = TRUE;
				$data['message'] = 'Successfully updated H.A.N.E.';

        if (isset($_FILES['file'])) {
          $res = $this->update_hane_photo(
            [
              'hotel_id' => $id,
              'old_photo' => $params['hotel_image']
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

  public function update_hane_photo($params = [], $ajax = TRUE) {
    $data['response'] = FALSE;
    $data['message'] = 'Failed';

    try {
      $photo = $_FILES['file'] ?? [];
      $old_photo = $params['old_photo'] ?? '';
      $hotel_id = $params['hotel_id'] ?? 0;
      $hotel_id = urldecode($hotel_id);

      if (empty($photo) || empty($hotel_id)) {
        throw new Exception('UPDATE H.A.N.E. PHOTO: Invalid parameter(s).');
      }

      $name = $photo['name'];
      $ext = explode('.', $name);
      $ext = end($ext);
      $mime = $photo['type'];
      $size = $photo['size'] * 1e-6; // in MB
      $allowedExts = ['jpg','jpeg','png','gif','PNG','JPG','JPEG','GIF'];
      $allowedMimes = ['image/jpeg','image/jpg','image/png','image/gif'];

      if (!in_array($ext, $allowedExts) || !in_array($mime, $allowedMimes) || $size > MAX_FILESIZE_MB) {
        throw new Exception('UPDATE H.A.N.E. PHOTO: Invalid file type or size. Please use image files only with no more than '.MAX_FILESIZE_MB.'MB.');
      }

      $newName = md5(decrypt($hotel_id) . date('Y-m-d H:i:s A')) . '.' . $ext;
      $source = $photo['tmp_name'];
      $folder = ENV['image_upload_path'] . 'hane/';
      $target = $folder . $newName;

      $filepath = $folder . $old_photo;
      if (file_exists($filepath) && !empty($old_photo) && $old_photo != 'default-hane.jpg') {
        unlink($filepath); // delete existing file
      }

      if(move_uploaded_file($source, $target)) {
        unset($_FILES['file']);
        $result = $this->update_hane([
          [
            'name' => 'hotel_id',
            'value' => $hotel_id
          ],
          [
            'name' => 'hotel_image',
            'value' => $newName
          ]
        ], FALSE);

        $data = $result;
        $data['data'] = ['hotel_image' => $newName];
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

  public function add_hane($params = [], $ajax = TRUE) {
    $data['response'] = FALSE;
    $params = ($ajax) ? json_decode($this->input->post('params'), true) : $params;
    $params = format_parameters(clean_parameters($params, []));

		try {
      if (empty($params)) {
        throw new Exception('ADD NEW H.A.N.E.: Invalid parameter(s)');
      }

			$result = $this->hf_model->add_new_hane($params);

      $data['message'] = $result['message'];
			if (!empty($result) && $result['code'] == 0) {
        if (isset($_FILES['file'])) {
          $res = $this->update_hane_photo(
            [
              'hotel_id' => $result['data']['hotel_id']
            ],
            FALSE
          );
        }
  			$data['response'] = TRUE;
				$data['message'] = 'Successfully added H.A.N.E.';
        if ($res && !$res['response']) {
          $data['message'] .= '<br>Please re-upload photo by editing this H.A.N.E.';
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

  public function get_hane_rooms() {
    $data['response'] = FALSE;

    try {
      $post = (isJsonPostContentType()) ? decodeJsonPost($this->security->xss_clean($this->input->raw_input_stream)) : $this->input->post();

      $searchkey = $post['searchkey'] ?? NULL;
  		$limit = $post['limit'] ?? NULL;
  		$start = $post['start'] ?? NULL;
  		$id = $post['id'] ?? NULL;
  		$hane = $post['hane'] ?? NULL;

      if ($searchkey === NULL || $start === NULL) {
  			throw new Exception("LOAD H.A.N.E. ROOMS: Invalid parameter(s)");
  		}

      $params = [
        'searchkey' => $searchkey,
        'start' => $start,
        'limit' => $limit,
        'id' => urldecode($id),
        'hane' => urldecode($hane)
      ];

      $result = $this->hf_model->get_hane_rooms($params);

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

  public function update_hane_room($params = [], $ajax = TRUE) {
    $data['response'] = FALSE;
    $params = ($ajax) ? json_decode($this->input->post('params'), true) : $params;
    $params = format_parameters(clean_parameters($params, ['inclusive_features']));
    $id = $params['room_id'] ?? 0;

    if (isset($params['room_id'])) {
      unset($params['room_id']);
    }

		try {
      if (empty($id) || empty($params)) {
        throw new Exception('UPDATE H.A.N.E. ROOM: Invalid parameter(s).');
      }

			$result = $this->hf_model->update_hane_room($id, $params);
      $data['message'] = $result['message'];

			if (!empty($result) && $result['code'] == 0) {
				$data['response'] = TRUE;
				$data['message'] = 'Successfully updated H.A.N.E room.';

        if (isset($_FILES['file'])) {
          $res = $this->update_room_photo(
            [
              'room_id' => $id,
              'old_photo' => $params['room_image']
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

  public function update_room_photo($params = [], $ajax = TRUE) {
    $data['response'] = FALSE;
    $data['message'] = 'Failed';

    try {
      $photo = $_FILES['file'] ?? [];
      $old_photo = $params['old_photo'] ?? '';
      $room_id = $params['room_id'] ?? 0;
      $room_id = urldecode($room_id);

      if (empty($photo) || empty($room_id)) {
        throw new Exception('UPDATE ROOM PHOTO: Invalid parameter(s).');
      }

      $name = $photo['name'];
      $ext = explode('.', $name);
      $ext = end($ext);
      $mime = $photo['type'];
      $size = $photo['size'] * 1e-6; // in MB
      $allowedExts = ['jpg','jpeg','png','gif','PNG','JPG','JPEG','GIF'];
      $allowedMimes = ['image/jpeg','image/jpg','image/png','image/gif'];

      if (!in_array($ext, $allowedExts) || !in_array($mime, $allowedMimes) || $size > MAX_FILESIZE_MB) {
        throw new Exception('UPDATE ROOM PHOTO: Invalid file type or size. Please use image files only with no more than '.MAX_FILESIZE_MB.'MB.');
      }

      $newName = md5(decrypt($room_id) . date('Y-m-d H:i:s A')) . '.' . $ext;
      $source = $photo['tmp_name'];
      $folder = ENV['image_upload_path'] . 'hane/';
      $target = $folder . $newName;

      $filepath = $folder . $old_photo;
      if (file_exists($filepath) && !empty($old_photo) && $old_photo != 'default-hane.jpg') {
        unlink($filepath); // delete existing file
      }

      if(move_uploaded_file($source, $target)) {
        unset($_FILES['file']);
        $result = $this->update_hane_room([
          [
            'name' => 'room_id',
            'value' => $room_id
          ],
          [
            'name' => 'room_image',
            'value' => $newName
          ]
        ], FALSE);

        $data = $result;
        $data['data'] = ['room_image' => $newName];
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

  public function add_hane_room($params = [], $ajax = TRUE) {
    $data['response'] = FALSE;
    $params = ($ajax) ? json_decode($this->input->post('params'), true) : $params;
    $params = format_parameters(clean_parameters($params, []));

    if (isset($params['room_id'])) {
      unset($params['room_id']);
    }

    if (isset($params['room_image'])) {
      unset($params['room_image']);
    }

    try {
      if (empty($params)) {
        throw new Exception('ADD H.A.N.E. ROOM: Invalid parameter(s).');
      }

      $result = $this->hf_model->add_hane_room($params);
      $data['message'] = $result['message'];

      if (!empty($result) && $result['code'] == 0) {
        if (isset($_FILES['file'])) {
          $res = $this->update_room_photo(
            [
              'room_id' => $result['data']['room_id']
            ],
            FALSE
          );
        }
        $data['response'] = TRUE;
        $data['message'] = 'Successfully added H.A.N.E room.';
        if ($res && !$res['response']) {
          $data['message'] .= '<br>Please re-upload photo by editing this room.';
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

  public function load_metrics() {
    $data['response'] = FALSE;
    $data['message'] = 'Failed';

    try {
      $post = (isJsonPostContentType()) ? decodeJsonPost($this->security->xss_clean($this->input->raw_input_stream)) : $this->input->post();

      $searchkey = $post['searchkey'] ?? NULL;
      $limit = $post['limit'] ?? NULL;
      $start = $post['start'] ?? NULL;
      $id = $post['id'] ?? NULL;
      $order = $post['order'] ?? NULL;
      $isActive = $post['active'] ?? 'all';

      if ($searchkey === NULL || $start === NULL || $limit === NULL) {
        throw new Exception("LOAD H.A.N.E. Metrics: Invalid parameter(s)");
      }

      $params = [
        'searchkey' => $searchkey,
        'start' => $start,
        'limit' => $limit,
        'id' => urldecode($id),
        'order' => $order
      ];

      if (!empty($isActive) && $isActive !== 'all') {
        $params['conditions'] = [
          'isActive' => $isActive
        ];
      }

      $result = $this->hf_model->load_metrics($params);

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

  public function add_metric() {
    $data['response'] = FALSE;
    $params = $this->input->post('params');
    $params = format_parameters(clean_parameters($params, []));

    try {
      if (empty($params)) {
        throw new Exception('ADD H.A.N.E. METRIC: Invalid parameter(s).');
      }

      if (isset($params['metric_id'])) {
        unset($params['metric_id']);
      }

      $result = $this->hf_model->add_metric($params);
      $data['message'] = $result['message'];

      if (!empty($result) && $result['code'] == 0) {
        $data['response'] = TRUE;
        $data['message'] = 'Successfully added H.A.N.E metric.';
      }
    } catch (Exception $e) {
      $data['message'] = $e->getMessage();
    }

    header( 'Content-Type: application/x-json' );
    echo json_encode( $data );
  }

  public function update_metric() {
    $data['response'] = FALSE;
    $params = $this->input->post('params');
    $params = format_parameters(clean_parameters($params, []));

    try {
      if (empty($params)) {
        throw new Exception('UPDATE H.A.N.E. METRIC: Invalid parameter(s).');
      }

      $metric_id = $params['metric_id'];
      if (isset($params['metric_id'])) {
        unset($params['metric_id']);
      }

      $result = $this->hf_model->update_metric($metric_id, $params);
      $data['message'] = $result['message'];

      if (!empty($result) && $result['code'] == 0) {
        $data['response'] = TRUE;
        $data['message'] = 'Successfully updated H.A.N.E metric.';
      }
    } catch (Exception $e) {
      $data['message'] = $e->getMessage();
    }

    header( 'Content-Type: application/x-json' );
    echo json_encode( $data );
  }

  public function load_unique_titles($hane_id, $ajax = TRUE) {
    $data['response'] = FALSE;

    if (empty($hane_id)) {
      return $data;
    }

    try {
      $result = $this->hf_model->load_unique_titles($hane_id);

      $data['message'] = $result['message'];

      if (!empty($result) && $result['code'] == 0 && !empty($result['data'])) {
        $data['response'] = TRUE;
        $data['data'] = $result['data'];
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

  public function add_hane_metrics() {
    $data['response'] = FALSE;
    $params = $this->input->post('params');
    $params = format_parameters(clean_parameters($params, []));

    try {
      if (empty($params)) {
        throw new Exception('ADD H.A.N.E Metrics: Invalid parameter(s).');
      }

      // check if title already file_exists
      $res = $this->hf_model->load_unique_titles($params['hotel_hotel_id']);
      if ($res && $res['code'] == 0) {
        foreach ($res['data']['records'] as $key => $val) {
          if ($val['unique_title'] == $params['unique_title']) {
            throw new Exception('ADD H.A.N.E. Metrics: Title already exists. Please choose a unique title.');
          }
        }
      }

      $result = $this->hf_model->add_hane_metrics($params);
      $data['message'] = $result['message'];
      if (!empty($result) && $result['code'] == 0) {
        $data['response'] = TRUE;
        $data['message'] = 'Successfully added H.A.N.E. metrics.';
      }
    } catch (Exception $e) {
      $data['message'] = $e->getMessage();
    }

    header( 'Content-Type: application/x-json' );
    echo json_encode( $data );
  }

  public function load_hane_metrics() {
    $data['response'] = FALSE;
    $data['message'] = 'Failed';

    $post = (isJsonPostContentType()) ? decodeJsonPost($this->security->xss_clean($this->input->raw_input_stream)) : $this->input->post();

    $unique_title = $post['unique_title'] ?? NULL;
    $hane_id = $post['hane_id'] ?? NULL;

    if (empty($hane_id) || empty($unique_title)) {
      return $data;
    }

    $unique_title = urldecode($unique_title);

    try {
      $result = $this->hf_model->load_hane_metrics($unique_title, $hane_id);

      $data['message'] = $result['message'];

      if (!empty($result) && $result['code'] == 0 && !empty($result['data'])) {
        $data['response'] = TRUE;
        $data['data'] = $result['data'];
        $data['message'] = 'Successfully loaded H.A.N.E. Metrics: <b>' . $unique_title . '</b>';
      }
    } catch (Exception $e) {
      $data['message'] = $e->getMessage();
    }

    header( 'Content-Type: application/x-json' );
    echo json_encode( $data );
  }

  public function update_hane_metrics() {
    $data['response'] = FALSE;
    $params = $this->input->post('params');
    $params = format_parameters(clean_parameters($params, []));

    try {
      if (empty($params)) {
        throw new Exception('UPDATE H.A.N.E Metrics: Invalid parameter(s).');
      }

      if ($params['unique_title'] != $params['old_unique_title']) {
      // check if title already file_exists
        $res = $this->hf_model->load_unique_titles($params['hotel_hotel_id']);
        if ($res && $res['code'] == 0) {
          foreach ($res['data']['records'] as $key => $val) {
            if ($val['unique_title'] == $params['unique_title']) {
              throw new Exception('UPDATE H.A.N.E. Metrics: Title already exists. Please choose a unique title.');
            }
          }
        }
      } else {
        unset($params['unique_title']);
      }

      unset($params['hotel_hotel_id']);
      unset($params['old_unique_title']);

      $result = $this->hf_model->update_hane_metrics($params);
      $data['message'] = $result['message'];
      if (!empty($result) && $result['code'] == 0) {
        $data['response'] = TRUE;
        $data['message'] = 'Successfully updated H.A.N.E. metrics.';
      }
    } catch (Exception $e) {
      $data['message'] = $e->getMessage();
    }

    header( 'Content-Type: application/x-json' );
    echo json_encode( $data );
  }

  public function searchHane(){
    $data['response'] = FALSE;

      try {
        $post = (isJsonPostContentType()) ? decodeJsonPost($this->security->xss_clean($this->input->raw_input_stream)) : $this->input->post();

        $searchkey = $post['search'] ?? " ";
        $pricerange = $post['est_price'] ?? 0;
        $hotelid = $post['hotel_id'] ?? NULL;

        if(!empty($hotelid)){
          $params = [
            'searchkey' => $searchkey,
            'pricerange' => $pricerange,
            'hotel_id' => $hotelid
          ];

          $result = $this->hf_model->load_hotelsearch($params);

          $data['message'] = $result['message'];

          if (!empty($result) && $result['code'] == 0 && !empty($result['data'])) {
            $data['response'] = TRUE;
            $data['data'] = $result['data'];
          }
        }
        else{
          if ($pricerange == NULL ) {
            throw new Exception("LOAD HANE: Invalid parameter(s)");
          }

          $params = [
            'searchkey' => $searchkey,
            'pricerange' => $pricerange,
            'hotel_id' => $hotelid
          ];

          $result = $this->hf_model->load_hotelsearch($params);

          $data['message'] = $result['message'];

          if (!empty($result) && $result['code'] == 0 && !empty($result['data'])) {
            $data['response'] = TRUE;
            $data['data'] = $result['data'];
          }
        }
      } catch (Exception $e) {
        $data['message'] = $e->getMessage();
      }

    header( 'Content-Type: application/x-json' );
    echo json_encode( $data );
  }
}
