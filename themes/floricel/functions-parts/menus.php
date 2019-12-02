<?php

// Menús
function floricel_menus() {
    register_nav_menus(array(
        'menu-header-1'     => __( 'Menú cabecera 1', 'floricel' ),
        'menu-social-1'     => __( 'Menú social 1', 'floricel' ),
    ));
}

add_action( 'init', 'floricel_menus' );
