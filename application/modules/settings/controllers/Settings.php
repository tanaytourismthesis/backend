<?php
if (!defined("BASEPATH"))
    exit("No direct script access allowed");

class Settings extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
	}

  public function index() {
    $data = [
      'config_files' => [
        [
          'type' => 'admin',
          'caption' => 'Administration Site Configuration'
        ],
        [
          'type' => 'client',
          'caption' => 'Client Site Configuration'
        ],
      ]
    ];

    $this->template->build_template(
      'Settings', //Page Title
      array( // Views
        array(
          'view' => 'settings/settings',
          'data' => $data
        )
      ),
      array( // JavaScript Files
        'assets/js/modules_js/settings.js'
      ),
      array( // CSS Files
        'assets/css/settings.css'
      ),
      array( // Meta Tags

      ),
      'backend' // template page
    );
  }

  private function get_config_url($type = 'admin', $fileOnly = FALSE) {
    $config_file = ENV['config_files'][$type];
    $url = ($type == 'admin') ? ENV['base_url'] : ENV['client_url'];
    return ($fileOnly) ? $config_file : $url . $config_file;
  }

  public function get_config_file($type = NULL) {
    $response['response'] = FALSE;
    $response['message'] = 'Failed to load configuration file.';

    try {
      if (empty($type)) {
        throw new Exception('GET CONFIG FILE: Invalid parameter(s).');
      }

      $filepath = $this->get_config_url($type);
      if (!@fopen($filepath,'rb')) {
        throw new Exception('GET CONFIG FILE: Cannot read file. It may not exist.');
      }

      $filecontent = file_get_contents($filepath);
      if (!$filecontent) {
        throw new Exception('GET CONFIG FILE: Cannot decode file.');
      }

      $response['response'] = TRUE;
      $response['message'] = 'Successfully loaded configuration file.';
      $response['data']['filecontent'] = $filecontent;

    } catch (Exception $e) {
      $response['message'] = $e->getMessage();
    }

    header( 'Content-Type: application/x-json' );
    echo json_encode( $response );
  }

  public function save_config_file() {
    $response['response'] = FALSE;
    $response['message'] = 'Failed to save configuration file.';

    try {
      $post = $this->input->post();
      $type = $post['type'] ?? NULL;
      $content = $post['filecontent'] ?? NULL;
      $parsedContent = json_decode(base64_decode($content), TRUE);

      if (empty($post) || empty($type) || empty($content) || empty($parsedContent)) {
        throw new Exception('SAVE CONFIG FILE: Invalid parameter(s).');
      }

      $ftp_server = ENV['ftp']['server'];
      $ftp_port = ENV['ftp']['port'];
      $ftp_user_name = ENV['ftp']['username'];
      $ftp_user_pass = ENV['ftp']['passwd'];
      $file = $this->get_config_url($type, TRUE);//tobe uploaded
      $remote_file = ENV['ftp']['filepath'][$type] . $file;
      $ftp_filepath = "{$ftp_user_name}:{$ftp_user_pass}@{$ftp_server}:{$ftp_port}/{$remote_file}";

      $filehandler = @fopen($ftp_file, 'wb');

      if (!$filehandler) {
        throw new Exception('GET CONFIG FILE: Cannot read file. It may not exist.'.$ftp_filepath);
      }

      $response['response'] = TRUE;
      $response['message'] = 'Sucessfully saved configuration file.';
    } catch (Exception $e) {
      $response['message'] = $e->getMessage();
    }

    header( 'Content-Type: application/x-json' );
    echo json_encode( $response );
  }
}
