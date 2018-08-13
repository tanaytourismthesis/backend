<?php
if (!defined("BASEPATH"))
    exit("No direct script access allowed");

class News_management extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('news_model');
	}

  public function index(){
    $data = [];

    $this->template->build_template(
      'News', //Page Title
      array( // Views
        array(
          'view' => 'news_management',
          'data' => $data
        )
      ),
      array( // JavaScript Files
        'assets/js/modules_js/news_management.js'
      ),
      array( // CSS Files

      ),
      array( // Meta Tags

      ),
      'backend' // template page
    );
  }

  public function load_news() {
    $searchkey = $this->input->post('searchkey') ?? NULL;
		$limit = $this->input->post('limit') ?? NULL;
		$start = $this->input->post('start') ?? NULL;
		$id = $this->input->post('id') ?? NULL;

		$data['response'] = FALSE;
    $data['message'] = 'Failed to retrieve data.';

		try {
      if ($searchkey === NULL || $start === NULL || $limit === NULL) {
  			throw new Exception("Invalid parameter");
  		}

      $params = [
        'searchkey' => $searchkey,
        'start' => $start,
        'limit' => $limit,
        'id' => $id
      ];

      if (!empty($id)) {
        $params['additional_fields'] = 'news.content content, news.news_type_type_id news_type_type_id';
      }
      
			$result = $this->news_model->load_news($params);

			if (!empty($result)) {
				$data['data'] = $result;
				$data['response'] = TRUE;
				$data['message'] = 'Successful';
			}

		} catch (Exception $e) {
			$data['message'] = $e->getMessage();
		}

		header( 'Content-Type: application/x-json' );
		echo json_encode( $data );
  }

  public function update_news(){
    $exceptions = ['content'];
    $params = format_parameters(clean_parameters($this->input->post('params'), $exception));
    $id = $this->input->post('id');
    $data['response'] = FALSE;
    $data['message'] = 'Failed to update data.';

    try {
      if(empty($params) || empty($id)){
        throw new Exception("Invalid parameters");
      }

      $params['date_updated'] = date('Y-m-d H:i:s');
      $params['slug'] = url_title($params['title'],'-',true);
      $result = $this->news_model->update_news($id,$params);

      if (!empty($result)) {
        $data['response'] = $result;
        $data['message'] = 'Successful';
      }

    } catch (Exception $e) {
      $data['message'] = $e->getMessage();
    }

    header( 'Content-Type: application/x-json' );
    echo json_encode( $data );
  }

  public function add_news(){
    $data['response'] = FALSE;
		$data['message'] = 'Please check required fields or check your network connection.';

    $exception = ['content'];
    $params = format_parameters(clean_parameters($this->input->post('params'), $exception));
    $params['users_user_id'] = $this->session->userdata('user_info')['user_id'];

		try {
			$res = $this->news_model->add_news($params);

			if ($res === TRUE)
			{
				$data['response'] = TRUE;
				$data['message'] = 'Successfully added new news.';
			}
		}
		catch (Exception $e) {
			$data['message'] = $e->getMessage();
		}

		header( 'Content-Type: application/x-json' );
		echo json_encode( $data );
  }




}
?>
