<?php

if (!function_exists('currency_format')){
	function currency_format( $pera = '' ){
		return number_format($pera, 2, '.', ',');
	}
}

if (!function_exists('clean_parameters')){
	function clean_parameters( $args, $exceptions ){
		if( is_array( $args ) ):
			foreach( $args as $key => $values ):
				if( !is_array( $values ) )
					if (!in_array($key, $exceptions)) {
						$args[ $key ] = clean_input( $values );
					}
				else
					$args[ $key ] = clean_parameters( $args[ $key ] );
			endforeach;
		endif;

		return $args;
	}
}

if (!function_exists('format_parameters')){
	function format_parameters( $args ){
		$new_format = [];
		if( is_array( $args ) ):
			foreach( $args as $key => $values ):
				$new_format[$values['name']] = $values['value'];
			endforeach;
		endif;

		return $new_format;
	}
}

if (!function_exists('clean_input')){
	function clean_input($input){
		if( !is_array( $input ) ){
			$search = array(
				'@<script[^>]*?>.*?</script>@si',   /* strip out javascript */
				'@<[\/\!]*?[^<>]*?>@si',            /* strip out HTML tags */
				'@<style[^>]*?>.*?</style>@siU',    /* strip style tags properly */
				'@<![\s\S]*?--[ \t\n\r]*>@'         /* strip multi-line comments */
			);

			$output = preg_replace($search, '', $input);
			return $output;
		} else
			return $input;
	}
}

if (!function_exists('debug')){
  function debug($var = '', $die = FALSE){
		echo '<pre>';
			var_dump($var);
		echo '</pre>';

		if ($die) {
			die;
		}
  }
}

if (!function_exists('get_rest_auth')){
  function get_rest_auth(){
		$ci =& get_instance();

		$response = [
			'user' => $ci->input->server('PHP_AUTH_USER'),
			'pass' => $ci->input->server('PHP_AUTH_PW')
		];

		return $response;
	}
}

if (!function_exists('get_rest_headers')){
  function get_rest_headers(){
		$ci =& get_instance();
		$response = $ci->input->request_headers();
		$ignore_headers = array('connection', 'content-length', 'user-agent', 'origin', 'authorization', 'content-type', 'accept', 'accept-encoding', 'accept-language', 'cookie');

		foreach ($response as $header => $value){
			switch( strtolower( $header ) ){
				case 'host':
					$response['client_host'] = $response[ $header ];
					unset( $response[ $header ] );
				break;
				default:
					if( in_array(strtolower($header), $ignore_headers) )
						unset( $response[$header] );
				break;
			}
		}

		return $response;
  }
}

if( !function_exists('encrypt') ){
	function encrypt( $string ) {
		$key = ENV['e_key']; //"MAL_979805"; //key to encrypt and decrypts.
	  	$result = '';
	  	$test = "";
	   	for($i=0; $i<strlen($string); $i++) {
	     	$char = substr($string, $i, 1);
	     	$keychar = substr($key, ($i % strlen($key))-1, 1);
	     	$char = chr(ord($char)+ord($keychar));

	     	$test[$char]= ord($char)+ord($keychar);
	     	$result.=$char;
	   	}

	   	return urlencode(base64_encode($result));
	}
}

if( !function_exists('decrypt') ){
	function decrypt( $string ) {
	    $key = ENV['e_key']; //"MAL_979805"; //key to encrypt and decrypts.
	    $result = '';
	    $string = base64_decode(urldecode($string));
	   	for($i=0; $i<strlen($string); $i++) {
	     	$char = substr($string, $i, 1);
	     	$keychar = substr($key, ($i % strlen($key))-1, 1);
	     	$char = chr(ord($char)-ord($keychar));
	     	$result.=$char;
	   	}
	   	return $result;
	}
}

if( !function_exists('isJson') ){
	function isJson($string) {
		json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE);
	}
}

if( !function_exists('set_alert') ){
	function set_alert($type, $title, $msg, $var = '', $free_form = '') {
		$ci =& get_instance();

		$ci->session->set_flashdata('alert_type' . $var, $type ?? 'success');

		if( !empty( $free_form ) ){
			$ci->session->set_flashdata('alert_html' . $var, $free_form ?? '');
		} else {
			$ci->session->set_flashdata('alert_msg' . $var, $msg ?? '');
			$ci->session->set_flashdata('alert_title' . $var, $title ?? '');
		}

		return TRUE;
	}
}

if(!function_exists('remove_unwanted_chars')){
	function remove_unwanted_chars( $str ){
		return preg_replace( ['/[|]/', '/(?<=[a-zA-Z])[.](?![\s$])/'], ['', '. '], $str );
	}
}

if(!function_exists('get_route_alias')){
	function get_route_alias( $module, $routes ) {
		foreach ($routes as $key => $val) {
			if ($module == $val) {
				return $key;
			}
		}
		return '';
	}
}

if (!function_exists('get_page_caption')) {
	function get_page_caption($slug = NULL, $pages = NULL) {
		if (!(empty($slug) || empty($pages))) {
			$menu_items = $pages;
			foreach ($menu_items as $menu) {
				if ($menu['url'] == $slug || $menu['controller'] == $slug) {
					return $menu['caption'];
				}

				if (!empty($menu['sub-menu'])) {
					foreach ($menu['sub-menu'] as $submenu) {
						$URIs = array(
							$menu['url'] . "/" . $submenu['url'],
							$menu['controller'] . "/" . $submenu['url']
						);
						if (in_array($slug, $URIs)) {
							return $menu['caption'] . " &rsaquo; " . $submenu['caption'];
						}
					}
				}
			}
		}
		return 'All Pages';
	}
}

if (!function_exists('get_page_icon')) {
	function get_page_icon($slug = NULL, $pages = NULL) {
		if (!(empty($slug) || empty($pages))) {
			$menu_items = $pages;
			foreach ($menu_items as $menu) {
				if ($menu['url'] == $slug || $menu['controller'] == $slug) {
					return $menu['icon'];
				}

				if (!empty($menu['sub-menu'])) {
					foreach ($menu['sub-menu'] as $submenu) {
						$URIs = array(
							$menu['url'] . "/" . $submenu['url'],
							$menu['controller'] . "/" . $submenu['url']
						);
						if (in_array($slug, $URIs)) {
							if ($submenu['url'] != 'gallery') {
								return $submenu['icon'];
							} else {
								return $menu['icon'];
							}
						}
					}
				}
			}
		}
		return 'fas fa-ban';
	}
}

if(!function_exists('encrypt_id')){
	function encrypt_id($data) {
    if (is_array($data)) {
      foreach ($data as $key => $val) {
				if (is_array($val)) {
	        foreach($val as $k => $v) {
						$id_key = explode('_', $k);
						if (in_array('id', $id_key)) {
							$data[$key][$k] = encrypt($v);
						}
					}
				} else {
					$id_key = explode('_', $key);
					if (in_array('id', $id_key)) {
						$data[$key] = encrypt($val);
					}
				}
      }
    } else {
			$data = encrypt($data);
		}

		return $data;
  }
}

if(!function_exists('get_header_content_type')){
	function get_header_content_type() {
		return $_SERVER['CONTENT_TYPE'];
	}
}

if(!function_exists('isJsonPostContentType')){
	function isJsonPostContentType() {
		return get_header_content_type() === 'application/json';
	}
}

if(!function_exists('decodeJsonPost')){
	function decodeJsonPost($raw_input) {
		if (isJson($raw_input)) {
			return json_decode($raw_input, TRUE);
		}
		return [];
	}
}

?>
