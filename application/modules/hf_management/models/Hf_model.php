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

      $default_fields = 'hotel_id, hotel_name, hotel_image, isActive,
                          IF (isActive=1, "active", "inactive") hotel_status';

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
          ['hotel_name' => $searchkey]
        );
        $queryOptions['conditions']['or_like'] = ['address' => $searchkey];
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
        $response['code'] = -1;
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

  public function get_hane_rooms($params = []) {
    $response['code'] = 0;
    $response['message'] = 'Success';

    try {
      if (empty($params)) {
        $response['code'] = -1;
        throw new Exception('LOAD_HANE_ROOMS: Invalid parameter(s).');
      }

      $searchkey = $params['searchkey'];
      $start = $params['start'];
      $limit = $params['limit'];
      $id = decrypt(urldecode($params['id'])) ?? 0;
      $hane = decrypt(urldecode($params['hane'])) ?? 0;

      $default_fields = '*';

      $queryOptions = array(
        'table' => 'hotel_room',
        'fields' => $default_fields,
        'order' => 'room_name ASC',
        'start' => $start,
        'limit' => $limit
      );

      $queryOptions['conditions'] = $params['conditions'] ?? [];

      if (!empty($searchkey)) {
        $like = (count($queryOptions['conditions']) > 0) ? 'or_like' : 'like';
        $queryOptions['conditions'][$like] = array_merge(
          $queryOptions['conditions'][$like] ?? [],
          ['room_name' => $searchkey]
        );
      }

      if (!empty($hane)) {
        $queryOptions['conditions']['and'] = array_merge(
          $queryOptions['conditions']['and'] ?? [],
          ['hotel_room.hotel_hotel_id' => $hane]
        );
      }

      if (!empty($id)) {
        $queryOptions['conditions']['and'] = array_merge(
          $queryOptions['conditions']['and'] ?? [],
          ['hotel_room.room_id' => $id]
        );
      }

      $result = $this->query->select($queryOptions);

      $queryOptions['fields'] = 'COUNT(hotel_room.room_id) total_records';
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
        $response['code'] = -1;
        throw new Exception('Failed to retrieve details.');
      }
    } catch (Exception $e) {
      $response['message'] = (ENVIRONMENT !== 'production') ? $e->getMessage() : 'Something went wrong. Please try again.';
    }
    return $response;
  }

  public function update_hane_room($id = NULL, $params = []) {
    $response['code'] = 0;
    $response['message'] = 'Success';

    $id = decrypt(urldecode($id)) ?? 0;

    try {
      if (empty($id) || empty($params)) {
        $response['code'] = -1;
        throw new Exception('UPDATE_HANE_ROOM: Invalid parameter(s).');
      }

      if (isset($params['hotel_hotel_id'])) {
        $params['hotel_hotel_id'] = decrypt(urldecode($params['hotel_hotel_id']));
      }

      $result = $this->query->update(
        'hotel_room',
        array(
          'room_id' => $id
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

  public function add_hane_room($params = []) {
    $response['code'] = 0;
    $response['message'] = 'Success';

    try {
      if (empty($params)) {
        $response['code'] = -1;
        throw new Exception('ADD_HANE_ROOM: Invalid parameter(s).');
      }

      if (isset($params['hotel_hotel_id'])) {
        $params['hotel_hotel_id'] = decrypt(urldecode($params['hotel_hotel_id']));
      }

      $result = $this->query->insert('hotel_room', $params, TRUE);

      if (isset($result['response']['code'])) {
        $response = array_merge($response, $result['response']);
        throw new Exception($response['message']);
      } else {
        $response['data'] = [ 'room_id' => encrypt_id($result['id']) ];
      }
    } catch (Exception $e) {
      $response['message'] =  (ENVIRONMENT !== 'production') ? $e->getMessage() : 'Something went wrong. Please try again.';
    }
    return $response;
  }

  public function load_metrics($params = []) {
    $response['code'] = 0;
    $response['message'] = 'Success';

    try {
      if (empty($params)) {
        $response['code'] = -1;
        throw new Exception('LOAD_METRICS: Invalid parameter(s).');
      }

      $searchkey = $params['searchkey'];
      $start = $params['start'];
      $limit = $params['limit'];
      $id = decrypt(urldecode($params['id'])) ?? 0;
      $order = $params['order'];

      $default_fields = '*, IF (isActive=1, "Active", "Inactive") metric_status';

      $queryOptions = array(
        'table' => 'metric',
        'fields' => $default_fields,
        'start' => $start,
        'limit' => $limit
      );

      $queryOptions['conditions'] = $params['conditions'] ?? [];

      if (!empty($searchkey)) {
        $like = (count($queryOptions['conditions']) > 0) ? 'or_like' : 'like';
        $queryOptions['conditions'][$like] = array_merge(
          $queryOptions['conditions'][$like] ?? [],
          ['metric_name' => $searchkey]
        );
        $queryOptions['conditions']['or_like'] = [
          'variable1' => $searchkey,
          'variable2' => $searchkey
        ];
      }

      if (!empty($id)) {
        $queryOptions['conditions']['and'] = array_merge(
          $queryOptions['conditions']['and'] ?? [],
          ['metric_id' => $id]
        );
      }

      if (!empty($order)) {
        $queryOptions['order'] = $order;
      }

      $result = $this->query->select($queryOptions);

      $queryOptions['fields'] = 'COUNT(metric_id) total_records';
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

  public function add_metric($params = []) {
    $response['code'] = 0;
    $response['message'] = 'Success';

    try {
      if (empty($params)) {
        $response['code'] = -1;
        throw new Exception('ADD_HANE_METRIC: Invalid parameter(s).');
      }

      $result = $this->query->insert('metric', $params, TRUE);

      if (isset($result['response']['code'])) {
        $response = array_merge($response, $result['response']);
        throw new Exception($response['message']);
      } else {
        $response['data'] = [ 'metric_id' => encrypt_id($result['id']) ];
      }
    } catch (Exception $e) {
      $response['message'] =  (ENVIRONMENT !== 'production') ? $e->getMessage() : 'Something went wrong. Please try again.';
    }
    return $response;
  }

  public function update_metric($id, $params = []) {
    $response['code'] = 0;
    $response['message'] = 'Success';

    $id = decrypt(urldecode($id)) ?? 0;

    try {
      if (empty($id) || empty($params)) {
        $response['code'] = -1;
        throw new Exception('UPDATE_HANE_METRIC: Invalid parameter(s).');
      }

      $result = $this->query->update(
        'metric',
        array(
          'metric_id' => $id
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

  public function load_unique_titles($hane_id) {
    $response['code'] = 0;
    $response['message'] = 'Success';

    $hane_id = decrypt(urldecode($hane_id)) ?? 0;

    try {
      if (empty($hane_id)) {
        throw new Exception('LOAD_UNIQUE_TITLES: Invalid parameter(s).');
      }

      $queryOptions = array(
        'table' => 'hotel_metric',
        'fields' => 'unique_title, date_computed',
        'conditions' => [
          'hotel_hotel_id' => $hane_id
        ],
        'group' => 'unique_title'
      );

      $result = $this->query->select($queryOptions);
      $result2 = $this->query->select($queryOptions, FALSE, TRUE);

      if (isset($result['code'])) {
        $response = array_merge($response, $result);
        throw new Exception($response['message']);
      } else if (!empty($result)) {
        $response['data']['records'] = (count($result) >= 1 && empty($id)) ? encrypt_id($result) : encrypt_id($result[0]);
        $response['data']['total_records'] = $result2;
      } else {
        $response['code'] = -1;
        throw new Exception('Failed to retrieve details.');
      }
    } catch (Exception $e) {
      $response['message'] = (ENVIRONMENT !== 'production') ? $e->getMessage() : 'Something went wrong. Please try again.';
    }
    return $response;
  }

  public function add_hane_metrics($params = []) {
    $response['code'] = 0;
    $response['message'] = 'Success';
    $response['data']['clear_form'] = TRUE;

    try {
      if (empty($params)) {
        $response['code'] = -1;
        throw new Exception('ADD_HANE_METRICS: Invalid parameter(s).');
      }

      $hane_id = decrypt(urldecode($params['hotel_hotel_id'])) ?? 0;
      unset($params['hotel_hotel_id']);
      $unique_title = $params['unique_title'];
      unset($params['unique_title']);

      if (empty($hane_id) || empty($unique_title)) {
        throw new Exception('ADD_HANE_METRICS: Invalid parameter(s).');
      }

      $params = $this->build_metrics_params($params, $unique_title, $hane_id, 'hotel_hotel_id');

      $error = 0;
      $success = 0;
      $message = '';
      foreach ($params as $key => $val) {
        $val['metric_metric_id'] = decrypt(urldecode($val['metric_metric_id']));
        $result = $this->query->insert('hotel_metric', $val);
        if (isset($result['response']['code'])) {
          $message = $result['response']['message'];
          $error++;
        } else {
          $success++;
        }
      }

      if ($error > 0) {
        $response['code'] = -1;
        $message .= '<br>' . $error . ' metric(s) failed.';

        if ($success > 0) {
          $message .= 'Please edit the metric set. (Unique Title: '.$unique_title.')';
        } else {
          $response['data']['clear_form'] = FALSE;
          $message .= 'Please try again.';
        }

        throw new Exception($message);
      }
    } catch (Exception $e) {
      $response['message'] =  (ENVIRONMENT !== 'production') ? $e->getMessage() : 'Something went wrong. Please try again.';
    }
    return $response;
  }

  public function load_hane_metrics($unique_title, $hane_id) {
    $response['code'] = 0;
    $response['message'] = 'Success';

    try {
      $hane_id = decrypt(urldecode($hane_id)) ?? 0;
      $unique_title = $unique_title;

      if (empty($unique_title) || empty($hane_id)) {
        $response['code'] = -1;
        throw new Exception('LOAD_HANE_METRICS: Invalid parameter(s).');
      }

      $default_fields = 'hotelmetric_id, unique_title, hotel_metric.variable1,
                          hotel_metric.variable2, result, metric_name,
                          alias, formula, metric.variable1 variable1_caption,
                          metric.variable2 variable2_caption, variable1_allowZero,
                          variable2_allowZero, date_computed, variable1_datatype,
                          variable2_datatype';

      $queryOptions = array(
        'table' => 'hotel_metric',
        'fields' => $default_fields,
        'joins' => [
          'metric' => [
            'type' => 'left',
            'metric.metric_id' => 'metric_metric_id'
          ]
        ],
        'conditions' => [
          'hotel_hotel_id' => $hane_id,
          'unique_title' => $unique_title
        ]
      );

      $result = $this->query->select($queryOptions);
      $result2 = $this->query->select($queryOptions, FALSE, TRUE);

      if (isset($result['code'])) {
        $response = array_merge($response, $result);
        throw new Exception($response['message']);
      } else if (!empty($result)) {
        $response['data']['records'] = encrypt_id($result);
        $response['data']['total_records'] = $result2;
      } else {
        $response['code'] = -1;
        throw new Exception('Failed to retrieve details.');
      }

    } catch (Exception $e) {
      $response['message'] = $e->getMessage();
    }
    return $response;
  }

  public function update_hane_metrics($params = []) {
    $response['code'] = 0;
    $response['message'] = 'Success';

    try {
      if (empty($params)) {
        $response['code'] = -1;
        throw new Exception('UPDATE_HANE_METRICS_INFO: Invalid parameter(s).');
      }

      $unique_title = $params['unique_title'] ?? '';
      if (isset($params['unique_title'])) {
        unset($params['unique_title']);
        if (empty($unique_title)) {
          throw new Exception('UPDATE_HANE_METRICS_INFO: Invalid parameter(s).');
        }
      }

      $params = $this->build_metrics_params($params, $unique_title);

      $error = 0;
      $success = 0;
      $message = '';
      foreach ($params as $key => $val) {
        $hotelmetric_id = decrypt(urldecode($val['hotelmetric_id']));
        unset($val['hotelmetric_id']);
        $result = $this->query->update(
          'hotel_metric',
          [
            'hotelmetric_id' => $hotelmetric_id
          ],
          $val
        );
        if (isset($result['response']['code'])) {
          $message = $result['response']['message'];
          $error++;
        } else {
          $success++;
        }
      }

      if ($error > 0) {
        $response['code'] = -1;
        $message =  (($success > 0) ? 'Some' : 'All') . ' metrics failed to update. Please click Update button to try again.';
        throw new Exception($message);
      }
    } catch (Exception $e) {
      $response['message'] =  (ENVIRONMENT !== 'production') ? $e->getMessage() : 'Something went wrong. Please try again.';
    }
    return $response;
  }

  private function build_metrics_params($params, $title = '', $id = 0, $id_key = '') {
    $new_params = [];
    foreach ($params as $index => $value) {
      $idx = explode('[', $index);
      $idx[1] = str_replace(']', '', $idx[1]);
      $idx[2] = str_replace(']', '', $idx[2]);
      $new_params[$idx[1]][$idx[2]] = $value;
    }

    $formatted = [];
    foreach ($new_params as $key => $val) {
      $formatted[$key] = $val;

      if (!empty($title)) {
        $formatted[$key]['unique_title'] = $title;
      }

      if ($id > 0) {
        $formatted[$key][$id_key] = $id;
      }
    }

    return $formatted;
  }

  public function load_hotelsearch($params = []) {
    $response['code'] = 0;
    $response['message'] = 'Success';

    try {
      if (empty($params)) {
        $response['code'] = -1;
        throw new Exception('LOAD_HANEs: Invalid parameter(s).');
      }

      $searchkey = $params['searchkey'];
      $pricerange = $params['pricerange'];
      $hotel_id = $params['hotel_id'];

      if(!empty($hotel_id)){
        // $pricerange = 4000;
        $query = "SELECT *, (SELECT MIN(room_rate_day) FROM hotel_room WHERE hotel_hotel_id = ".$hotel_id.") AS min_price,
                  (SELECT MAX(room_rate_night) FROM hotel_room WHERE hotel_hotel_id = ".$hotel_id.") AS max_price FROM hotel,
                  hotel_room WHERE hotel_id = ".$hotel_id." AND isActive = 1
                  group by hotel_id";

        $result = $this->query->native_query($query);

        if (isset($result['code'])) {
          $response = array_merge($response, $result);
          throw new Exception($response['message']);
        } else if (!empty($result)) {
          $response['data']['records'] = (count($result) >= 1 && empty($id)) ? encrypt_id($result) : encrypt_id($result[0]);
          $response['data']['total_records'] = COUNT($result);
        } else {
          throw new Exception('Failed to retrieve details.');
        }
      }
      else{
        $query = "SELECT *, (SELECT MIN(room_rate_day) FROM hotel_room WHERE hotel_hotel_id = hotel_id) AS min_price,
                   (SELECT MAX(room_rate_night) FROM hotel_room WHERE hotel_hotel_id = hotel_id) AS max_price
                  FROM hotel, hotel_room
                  WHERE (".$pricerange." >= (SELECT MIN(room_rate_day) FROM hotel_room WHERE hotel_hotel_id = hotel_id)) AND hotel_hotel_id = hotel_id
                        AND hotel_name LIKE '%".$searchkey."%' AND isActive = 1
                  GROUP BY hotel_id";

        $result = $this->query->native_query($query);

        if (isset($result['code'])) {
          $response = array_merge($response, $result);
          throw new Exception($response['message']);
        } else if (!empty($result)) {
          $response['data']['records'] = (count($result) >= 1 && empty($id)) ? encrypt_id($result) : encrypt_id($result[0]);
          $response['data']['total_records'] = COUNT($result);
        } else {
          $response['code'] = -1;
          throw new Exception('Failed to retrieve details.');
        }
      }
    } catch (Exception $e) {
      $response['message'] = (ENVIRONMENT !== 'production') ? $e->getMessage() : 'Something went wrong. Please try again.';
    }
    return $response;
  }

}
?>
