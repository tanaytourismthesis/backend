<?php
if (!defined("BASEPATH"))
    exit("No direct script access allowed");

class Dashboard_model extends CI_Model {

	public function __construct()
	{
		$this->load->database();
		$this->load->library('query');
	}

  public function getVisitsAndClicks() {
    $response['code'] = 0;
    $response['message'] = 'Success';

    try {
      $num_days = 6;
      $from_date = date('Y-m-d 00:00:00', strtotime('-'.$num_days.' days'));
      $to_date = date('Y-m-d 00:00:00');

      $labels = [];
      for ($i = $num_days; $i >= 1; $i--) {
        $labels[] = date('m/d/y', strtotime('-'.$i.' days'));
      }
      $labels[] = date('m/d/y');

      $site_visits = [0, 0, 0, 0, 0, 0, 0];
      $page_clicks = [0, 0, 0, 0, 0, 0, 0];
      $news_clicks = [0, 0, 0, 0, 0, 0, 0];

      // Site Visits
      $query = "SELECT visit_count, visit_date FROM site_visit
                WHERE visit_date BETWEEN '" . $from_date . "' AND '" . $to_date . "'";
      $result = $this->query->native_query($query);

      if (!isset($result['code'])) {
        for ($i = $num_days, $k = 0; $i >= 0; $i--, $k++) {
          if ($i == 0) {
            $date = $to_date;
          } else {
            $date = date('Y-m-d 00:00:00', strtotime('-'.$i.' days'));
          }
          if (!empty($result)) {
            foreach($result as $key => $val) {
              if ($val['visit_date'] == $date) {
                $site_visits[$k] = $val['visit_count'];
              }
            }
          }
        }
      }

      // Page Clicks
      $query = "SELECT SUM(num_clicks) click_count, click_date FROM page_click
                WHERE click_date BETWEEN '" . $from_date . "' AND '" . $to_date . "'
                GROUP BY click_date";
      $result = $this->query->native_query($query);

      if (!isset($result['code'])) {
        for ($i = $num_days, $k = 0; $i >= 0; $i--, $k++) {
          if ($i == 0) {
            $date = $to_date;
          } else {
            $date = date('Y-m-d 00:00:00', strtotime('-'.$i.' days'));
          }
          if (!empty($result)) {
            foreach($result as $key => $val) {
              if ($val['click_date'] == $date) {
                $page_clicks[$k] = $val['click_count'];
              }
            }
          }
        }
      }

      // News Clicks
      $query = "SELECT SUM(num_clicks) click_count, click_date FROM news_clicks
                WHERE click_date BETWEEN '" . $from_date . "' AND '" . $to_date . "'
                GROUP BY click_date";
      $result = $this->query->native_query($query);

      if (!isset($result['code'])) {
        for ($i = $num_days, $k = 0; $i >= 0; $i--, $k++) {
          if ($i == 0) {
            $date = $to_date;
          } else {
            $date = date('Y-m-d 00:00:00', strtotime('-'.$i.' days'));
          }
          if (!empty($result)) {
            foreach($result as $key => $val) {
              if ($val['click_date'] == $date) {
                $news_clicks[$k] = $val['click_count'];
              }
            }
          }
        }
      }

      $response['data'] = [
        'labels' => $labels,
        'datasets' => [
          [
            'label' => 'Site Visits',
            'data' => $site_visits,
            'backgroundColor' => COLORS[0],
            'borderColor' => COLORS[0],
            'fill' => false
          ],
          [
            'label' => 'Page Clicks',
            'data' => $page_clicks,
            'backgroundColor' => COLORS[1],
            'borderColor' => COLORS[1],
            'fill' => false
          ],
          [
            'label' => 'News Clicks',
            'data' => $news_clicks,
            'backgroundColor' => COLORS[2],
            'borderColor' => COLORS[2],
            'fill' => false
          ]
        ]
      ];
    } catch (Exception $e) {
      $response['message'] = $e->getMessage();
    }

    return $response;
  }

  public function getUserStatistics() {
    $response['code'] = 0;
    $response['message'] = 'Success';

    try {
      $queryOptions = [
        'table' => 'user_type',
        'fields' => 'type_name, (SELECT COUNT(user_id) FROM users WHERE user_type_type_id = type_id) user_count',
      ];

      $result = $this->query->select($queryOptions);

      $response['data'] = [
        'labels' => [],
        'datasets' => []
      ];

      if (isset($result['code'])) {
        $response = array_merge($response, $result);
      } else if(!empty($result)) {
        $count = COUNT($result) - 1;
        foreach ($result as $key => $val) {
          $response['data']['labels'][] = $val['type_name'];
          $response['data']['datasets'][0]['data'][] = $val['user_count'];
          $response['data']['datasets'][0]['backgroundColor'][] = COLORS[$count];
          $count--;
        }
      }
    } catch (Exception $e) {
      $response['message'] = $e->getMessage();
    }

    return $response;
  }
}
