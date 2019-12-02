<?php

// Cargar scripts y styles
function floricel_scripts_styles() {

    // CSS principal
    wp_enqueue_style('style', get_stylesheet_uri(), array(), '0.4');

    // JS
    wp_enqueue_script('fontawesome', 'https://kit.fontawesome.com/dacd798f5f.js', array(), '5', true);
    wp_enqueue_script('app', get_template_directory_uri() . '/js/app.js', array('jquery'), '1', true);
}

add_action( 'wp_enqueue_scripts', 'floricel_scripts_styles' );
