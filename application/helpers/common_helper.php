<?php

if (!function_exists('currency_format')){
	function currency_format( $pera = '' ){
		return number_format($pera, 2, '.', ',');
	}
}

if (!function_exists('clean_parameters')){
	function clean_parameters( $args ){
		if( is_array( $args ) ):
			foreach( $args as $key => $values ):
				if( !is_array( $values ) )
					$args[ $key ] = clean_input( $values );
				else
					$args[ $key ] = clean_parameters( $args[ $key ] );
			endforeach;
		endif;

		return $args;
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
  function debug($string = ''){
		if( !is_array( $string ) ):
			echo $string;
		else:
			echo '<pre>';
			print_r($string);
			echo '</pre>';
		endif;
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

?>