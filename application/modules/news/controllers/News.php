<?php
if (!defined("BASEPATH"))
    exit("No direct script access allowed");

class News extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('news/news_model');
	}

  public function index() {
    $res = $this->get_newstype()['data'] ?? [];
    $data = [
      'news_types' => $res
    ];

    $this->template->build_template(
      'News', //Page Title
      array( // Views
        array(
          'view' => 'components/search-bar',
          'data' => [
            'icon' => $this->session->userdata('active_page_icon')
          ]
        ),
        array(
          'view' => 'news_management',
          'data' => $data
        ),
        array(
          'view' => 'components/navigator',
          'data' => [
            'modal_name' => '#modalNews',
            'btn_add_label' => 'Add <span class="hidden-xs">News</span>'
          ]
        )
      ),
      array( // JavaScript Files
        'assets/js/modules_js/news_management.js',
        'assets/js/bootstrap-datetimepicker.min.js'
      ),
      array( // CSS Files
        'assets/css/bootstrap-datetimepicker.min.css',
        'assets/css/news_management.css'
      ),
      array( // Meta Tags

      ),
      'backend' // template page
    );
  }

  public function load_news($params = [], $ajax = TRUE) {
    // set default response
		$data['response'] = FALSE;
    $data['message'] = 'Failed to retrieve data.';

		try {
      if (!empty($params)) {
        $post = $params;
      } else {
        $post = (isJsonPostContentType()) ? decodeJsonPost($this->security->xss_clean($this->input->raw_input_stream)) : $this->input->post();
      }

      if (empty($post)) {
        throw new Exception("Invalid parameter(s).");
      }

      $searchkey = $post['searchkey'] ?? NULL;
  		$limit = $post['limit'] ?? NULL;
  		$start = $post['start'] ?? NULL;
  		$id = $post['id'] ?? NULL;
  		$slug = $post['slug'] ?? NULL;
      $status = $post['status'] ?? 'all';
      $newsslug = $post['newsslug'] ?? 'all';

      // check for nullity of params
      if ($searchkey === NULL || $start === NULL || $limit === NULL) {
  			throw new Exception("Invalid parameter(s).");
  		}

      // set params for SQL query
      $params = [
        'searchkey' => $searchkey,
        'start' => $start,
        'limit' => $limit,
        'id' => $id,
        'slug' => $slug,
        'status' => $status,
        'newsslug' => $newsslug
      ];

      // set id (for specific search)
      if (!empty($id) || !empty($status)) {
        $params['additional_fields'] = 'news.content content, news.news_type_type_id news_type_type_id';
      }
      // call model function (API simulation)
			$result = $this->news_model->load_news($params);

      // parse response message
      $data['message'] = $result['message'];

      // if result is not error and code is 0 and data is not empty...
      if (!empty($result) && $result['code'] == 0 && !empty($result['data'])) {
        // ...set response to true
        $data['response'] = TRUE;
        //...and, parse data
        $data['data'] = $result['data'];
      }

		} catch (Exception $e) { // catch Exception
			$data['message'] = $e->getMessage();
		}
    // return response as JSON
		if ($ajax) {
      header( 'Content-Type: application/x-json' );
		  echo json_encode( $data );
    }
    return $data;
  }


  public function add_news() {
    $data['response'] = FALSE;

    $exception = ['content'];
    $params = format_parameters(clean_parameters($this->input->post('params'), $exception));
    $newId = $this->session->userdata('user_info')['user_id'];
    $params['users_user_id'] = decrypt(urldecode($newId));
    $params['slug'] = url_title($params['title'],'-',true);

		try {
			$result = $this->news_model->add_news($params);

      $data['message'] = $result['message'];

			if (!empty($result) && $result['code'] == 0) {
				$data['response'] = TRUE;
				$data['message'] = 'Successfully added the news.';
			}
		}
		catch (Exception $e) {
			$data['message'] = $e->getMessage();
		}

		header( 'Content-Type: application/x-json' );
		echo json_encode( $data );
  }

  public function update_news() {
    $data['response'] = FALSE;

    $exceptions = ['content'];
    $params = format_parameters(clean_parameters($this->input->post('params'), $exceptions));

    $id = $this->input->post('id');
    $data['response'] = FALSE;
    $data['message'] = 'Failed to update data.';

    try {
      if(empty($params) || empty($id)) {
        throw new Exception("Invalid parameters");
      }

      $params['date_updated'] = date('Y-m-d H:i:s');
      $params['slug'] = url_title($params['title'],'-',true);

      $result = $this->news_model->update_news($id,$params);

      $data['message'] = $result['message'];

			if (!empty($result) && $result['code'] == 0) {
				$data['response'] = TRUE;
				$data['message'] = 'Successfully updated news.';
			}
		} catch (Exception $e) {
			$data['message'] = $e->getMessage();
		}

    header( 'Content-Type: application/x-json' );
    echo json_encode( $data );
  }

  private function get_newstype() {
    $data['response'] = FALSE;
    $data['message'] = 'Failed';

    try {
      $result = $this->news_model->get_newstype();
      // parse response message
      $data['message'] = $result['message'];

      // if result is not error and code is 0 and data is not empty...
      if (!empty($result) && $result['code'] == 0 && !empty($result['data'])) {
        // ...set response to true
        $data['response'] = TRUE;
        //...and, parse data
        $data['data'] = $result['data'];
      }
		} catch (Exception $e) {
			$data['message'] = $e->getMessage();
		}

    return $data;
  }

  public function gallery() {
    echo modules::run('gallery');
  }

}
?>
