<?php

add_action('after_setup_theme', function () {
    add_theme_support('responsive-embeds');
    add_theme_support( 'title-tag' );
    add_theme_support( 'woocommerce' );
});

add_action('init', function () {
    add_post_type_support('page', 'excerpt');
});
