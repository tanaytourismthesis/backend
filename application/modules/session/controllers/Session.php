<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Session extends MX_Controller
{
	public $allowedwosession;
	public $allowedmenus;
	public $url;
	public $method;
	public $allowed;

	function __construct() {
		parent::__construct();
		$this->allowedwosession = (array)ENV['allowed_session'];
	}

	function session_check($show_session = '') {
		if ($show_session == 'show') {
			debug($this->session, TRUE);
		}

		$this->url = str_replace( "/", "", $this->router->fetch_module() );
		$this->method = $this->router->fetch_method();
		$this->session->set_userdata('active_page', $this->url);
		$this->session->set_userdata('active_page_caption', $this->get_page_caption());
		$this->session->set_userdata('active_page_method', ($this->method != 'index') ? $this->method : '');
		$sess = $this->session->has_userdata('user_info');
		$default_controller = ENV['default_controller'] ?? 'dashboard';
		$httpReqWith = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
		$isRestlet = $_SERVER['HTTP_X_RESTLET'] ?? false;

		if (!$isRestlet) {
			if( !$this->is_allowed() ) {
				if(!empty( $sess )) {
					$this->show_dashboard();
				} else {
					$this->logout_user();
				}
			} else {
				if(
					empty( $sess )
					&&
					!in_array( $this->url, $this->allowedwosession )
				) {
					$this->logout_user();
				} else {
					if( !in_array( $this->url, array_merge( $this->allowedwosession, $this->allowedmenus ) ) )
						$this->show_dashboard();

					if(
						($this->url == "login")
						&&
						!empty( $sess )
						&&
						empty($httpReqWith)
						&&
						strtolower($httpReqWith) != 'xmlhttprequest'
					) {
						redirect( base_url( $default_controller ) );
					}

					if(
						in_array( $this->url, $this->allowedmenus )
						&&
						!$sess
						&&
						empty( $sess )
					)
						$this->logout_user();
				}
			}

			if( $this->router->fetch_method() != 'session_checker' ){
				if($this->session->has_userdata('last_activity')){
					$current_time = time();

					if( $this->session->userdata('last_activity') >= ( $current_time - SESS_TIMEOUT ) ){

						$this->session->set_userdata('last_activity', $current_time);

					}else{
						$this->logout_user();
					}
				} else {
					$this->session->set_userdata('last_activity', time());
				}
			}
		}
	}

	public function is_allowed(){
		$this->allowedmenus = $this->fetch_user_access();

		$allowed = $this->allowed = array_merge( (array)$this->allowedwosession, (array)$this->allowedmenus );

		return in_array( $this->url, $allowed );
	}

	public function fetch_user_access(){
		$res = $this->session->userdata('user_info')['menu_items'] ?? [];
		$_res = [];

		foreach( $res as $key => $values ){
			array_push( $_res, $values['controller'] );
		}

		return $_res;
	}

	private function get_page_caption() {
		$menu_items = $this->session->userdata('user_info')['menu_items'] ?? [];
		foreach ($menu_items as $menu) {
			if ($menu['controller'] == $this->url) {
				return $menu['caption'];
			}
		}
		return '';
	}

	public function logout_user(){
		if(
				!empty($httpReqWith)
				&&
				strtolower($httpReqWith) == 'xmlhttprequest'
			) {
			$this->session->sess_destroy();

			$return = array( 'session' => FALSE );

			header( 'Content-Type: application/x-json' );
			echo json_encode($return);
			// echo '<script language="javascript" type="text/javascript"> window.location.href="logout"; </script>';
			die();
		} else {
			if( $this->url != "logout" )
				redirect( base_url( 'logout' ) );
		}
	}

	public function show_dashboard(){
		if( $this->url != "dashboard" ){
			redirect( base_url( 'dashboard' ) );
		}
	}
}
