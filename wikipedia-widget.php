<?php
	/*
	Plugin Name: Wikipedia Widget
	Plugin URI: 
	Description: Shows wikipedia search results depending on a given string or the current post title.
	Author: Simeon Ackermann
	Version: 0.13.03
	Author URI: http://a-simeon.de
	*/

include_once dirname( __FILE__ ) . "/widget.php";
// register widget class
add_action( 'widgets_init', create_function( '', 'register_widget( "wikipedia_widget" );' ) );

// register javascripts and css
add_action('init', 'ww_init');
function ww_init() {
	/* TODO: onhly on widget page and content-page if widgetist active */
	wp_enqueue_script('jquery');
	wp_register_script( 'ww.js', plugins_url("/ww.js" , __FILE__ ), array('jquery') );
	wp_enqueue_script( 'ww.js' );

	wp_register_style( 'ww.css', plugins_url("/ww.css" , __FILE__ ), array() );
	wp_enqueue_style( 'ww.css');
}

/**
 *	Display wikipedia search-results 
 *
 *	@param array $result 	Search result from wikipedia-request
 */
function ww_result_print( $result ) {
	$xml_result = simplexml_load_string($result);
	
	echo "<ul>";
	if (! $xml_result->Section->Item ) {
		echo "<li>Kein Ergebnis.</li>";
	}
	foreach($xml_result->Section->Item as $data => $value) {	
		echo "<li>";
		if(isset($value->Image[0]['source'])){
			echo "<img src='" . $value->Image[0]['source'] . "' />";
		}
		echo "<a href='" . $value->Url . "'>" . $value->Text . "</a> | " . $value->Description . "</li>";
	}
	echo "</ul>";
}

//add ajax action
add_action('wp_ajax_nopriv_ww_request', 'ww_wikipedia_request');
add_action('wp_ajax_ww_request', 'ww_wikipedia_request');

/**
 *	Do wikipedia-API request for ajax-call
 */
function ww_wikipedia_request() {	//$url='', $data=''
	if ( !isset($_POST['url']) || !isset($_POST['data']) || !isset($_POST['limit']) ) {
		echo "<p>Error while getting POST-Parameters.</p>";
		die();
	}
	global $wp_version;

	$data = array('format' => 'xml', 'action' => 'opensearch', 'search' => $_POST['data'], 'limit' => $_POST['limit'], 'namespace' => '0');
	$url = parse_url( $_POST['url'] . 'w/api.php' );
	//http://en.wikipedia.org/w/api.php?format=xml&action=query&titles=Titanic&prop=revisions&rvprop=content&redirects
    $host = $url['host'];
    $path = $url['path'];

	if ( function_exists( 'wp_remote_post' ) ) {
		$http_args = array(
			'body'			=> $data,
			'method'		=> 'POST',
			'headers'		=> array(
				'Content-Type'	=> 'application/x-www-form-urlencoded; charset=' . get_option( 'blog_charset' ),
				'Host'			=> $host,
				'User-Agent'	=> 'User-Agent: WordPress/' . $wp_version . '; ' . get_bloginfo('url')
			),
			'httpversion'	=> '1.0',
			'timeout'		=> 15
		);
		$wikipedia_url = "http://{$host}{$path}";
		$response = wp_remote_post( $wikipedia_url, $http_args );

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			echo "<p>Something went wrong: $error_message</p>";
		} else {
			ww_result_print($response['body']);
		}
	} else {
		echo "<p>Function wp_remote_post() doesn\'t exist.</p>";
	}
	die(); //this is required to return a proper result
}