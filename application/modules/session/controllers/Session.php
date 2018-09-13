<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Session extends MX_Controller
{
	public $allowedwosession;
	public $allowedmenus;
	public $user_menus;
	public $url;
	public $alias;
	public $method;
	public $uri;
	public $allowed;

	function __construct() {
		parent::__construct();
		$this->allowedwosession = (array)ENV['allowed_session'];
		$this->user_menus = $this->session->userdata('user_info')['menu_items'] ?? [];
	}

	function session_check($show_session = '') {
		if ($show_session == 'show') {
			debug($this->router->routes);
			debug($this->session, TRUE);
		}

		$this->url = $this->router->fetch_module();
		$m = $this->router->fetch_method();
		$this->method = ($m == 'index') ? NULL : $m;

		$this->session->set_userdata('active_page', $this->url);
		$this->session->set_userdata('active_page_alias', get_route_alias($this->url, $this->router->routes));
		$this->session->set_userdata('active_page_method', $this->method);
		$this->uri = (!empty($this->method)) ? $this->url . "/" . $this->method : $this->url;
		$this->session->set_userdata(
			'active_page_caption',
			get_page_caption(
				$this->uri,
				$this->user_menus
			)
		);
		$this->session->set_userdata(
			'active_page_icon',
			get_page_icon(
				$this->uri,
				$this->user_menus
			)
		);

		$sess = $this->session->has_userdata('user_info');

		$default_controller = ENV['default_controller'] ?? 'dashboard';
		$httpReqWith = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
		$isRestlet = $_SERVER['HTTP_X_RESTLET'] ? $_SERVER['HTTP_X_RESTLET'] === 'true' : false;
		$auth_user = $_SERVER['PHP_AUTH_USER'] ?? '';
		$auth_pw = $_SERVER['PHP_AUTH_PW'] ?? '';
		$verified = $this->verify_auth($auth_user, $auth_pw);
		
		if (!($isRestlet && $verified)) {
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

			if( $this->method != 'session_checker' ) {
				if($this->session->has_userdata('last_activity')) {
					$current_time = time();

					if( $this->session->userdata('last_activity') >= ( $current_time - SESS_TIMEOUT ) ) {

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

	public function is_allowed() {
		$this->allowedmenus = $this->fetch_user_access();

		$allowed = $this->allowed = array_merge( (array)$this->allowedwosession, (array)$this->allowedmenus );

		return in_array( $this->url, $allowed );
	}

	public function fetch_user_access() {
		$res = $this->user_menus;
		$_res = [];

		foreach( $res as $key => $values ) {
			array_push( $_res, $values['controller'] );
		}

		return $_res;
	}

	public function logout_user() {
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

	public function show_dashboard() {
		if( $this->url != "dashboard" ) {
			redirect( base_url( 'dashboard' ) );
		}
	}

	public function verify_auth($u = '', $p = '') {
		if (empty($u) || empty($p)) {
			return FALSE;
		}

		$env_u = ENV['auth']['user'];
		$env_p = ENV['auth']['pw'];

		if ($env_u == $u && $env_p == $p) {
			return TRUE;
		}
	}
}
