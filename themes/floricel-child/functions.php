<?php

add_action( 'wp_enqueue_scripts', 'porto_child_css', 1001 );

// Load CSS
function porto_child_css() {
	// porto child theme styles
	wp_deregister_style( 'styles-child' );
	wp_register_style( 'styles-child', esc_url( get_stylesheet_directory_uri() ) . '/style.css' );
	wp_enqueue_style( 'styles-child' );

	if ( is_rtl() ) {
		wp_deregister_style( 'styles-child-rtl' );
		wp_register_style( 'styles-child-rtl', esc_url( get_stylesheet_directory_uri() ) . '/style_rtl.css' );
		wp_enqueue_style( 'styles-child-rtl' );
	}
}

// WooCommerce custom fields

add_filter( 'woocommerce_checkout_fields' , 'woocommerce_checkout_field_editor' );
// Our hooked in function - $fields is passed via the filter!
function woocommerce_checkout_field_editor( $fields ) {
    $fields['billing']['nombre_recibe'] = array(
        'label'     => __('Nombre de quién recibe', 'woocommerce'),
        'placeholder'   => _x('', 'placeholder', 'woocommerce'),
        'required'  => false
    );
    $fields['billing']['razon_social'] = array(
        'label'     => __('Razón Social', 'woocommerce'),
        'placeholder'   => _x('', 'placeholder', 'woocommerce'),
        'required'  => false
    );
    $fields['billing']['rfc'] = array(
        'label'     => __('RFC', 'woocommerce'),
        'placeholder'   => _x('', 'placeholder', 'woocommerce'),
        'required'  => false
    );
    $fields['billing']['uso_cfdi'] = array(
        'label'       => __('Uso CFDI', 'woocommerce'),
        'placeholder' => _x('', 'placeholder', 'woocommerce'),
        'required'    => false,
        'clear'       => false,
        'type'        => 'select',
        'options'     => array(
            'G01' => __('G01 - Adquisición de mercancias', 'woocommerce' ),
            'G03' => __('G03 - Gastos en general', 'woocommerce' ),
            'I01' => __('I01 - Construcciones', 'woocommerce' ),
            'I02' => __('I02 - Mobilario y equipo de oficina por inversiones', 'woocommerce' ),
            'I05' => __('I05 - Dados, troqueles, moldes, matrices y herramental	', 'woocommerce' ),
            'I08' => __('I08 - Otra maquinaria y equipo', 'woocommerce' ),
            'P01' => __('P01 - Por definir', 'woocommerce' ),
            )
        );
    return $fields;
}
