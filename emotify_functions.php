<?php

// Register style sheet.

function emotify_register_plugin_admin_styles() {
	wp_register_style( 'admin_style', plugins_url( 'admin_style.css', __FILE__ ) );
	wp_enqueue_style( 'admin_style' );
}
add_action( 'admin_enqueue_scripts', 'emotify_register_plugin_admin_styles' );