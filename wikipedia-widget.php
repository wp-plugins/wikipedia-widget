<?php
	/*
	Plugin Name: Wikipedia Widget
	Description: Shows wikipedia search results depending on a given string or the current post title.
	Author: Simeon Ackermann
	Version: 0.13.04
	Author URI: http://a-simeon.de
	*/

include_once dirname( __FILE__ ) . "/widget.php";
// register widget class
add_action( 'widgets_init', create_function( '', 'register_widget( "wikipedia_widget" );' ) );

// register javascripts and css
add_action('wp_enqueue_scripts', 'ww_initScripts');
function ww_initScripts() {
	wp_register_script( 'wikipedia_widget_script', plugins_url("/script.js" , __FILE__ ), array('jquery') );
	wp_enqueue_script( 'wikipedia_widget_script' );

	wp_register_style( 'wikipedia_widget_style', plugins_url("/style.css" , __FILE__ ), array() );
	wp_enqueue_style( 'wikipedia_widget_style');

	$ww_option = is_array(array_shift(get_option('widget_wikipedia_widget'))) ? array_shift(get_option('widget_wikipedia_widget')) : array();
	$ww_option['ajaxurl'] = admin_url('admin-ajax.php');
	wp_localize_script('wikipedia_widget_script', 'wikipedia_widget_script', $ww_option);
}