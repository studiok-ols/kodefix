<?php
/*
Plugin Name: Telefon
Plugin URI: http://studiok.net.pl
Description: Zadanie testowe kodefix.pl. Wtyczka przy instalacji tworzy tabelę. Przy odinstalowaniu usuwa tablę
Author: StudioK
Version: 1.0.0
*/

include_once 'PhoneClass.php';
$phone = new PhoneClass();

register_activation_hook( __FILE__, array($phone,"activatePhonePlugin") );
register_deactivation_hook( __FILE__, array($phone,"deactivatePhonePlugin") );

add_shortcode('stary_telefon', array($phone,'fn_stary_telefon'));
add_action( 'rest_api_init', array($phone,'initRestApi') );
add_action( 'wp_enqueue_scripts', array($phone,'enqueueScript') );
add_action( 'wp_enqueue_scripts', array($phone,'enqueueStyle') );

