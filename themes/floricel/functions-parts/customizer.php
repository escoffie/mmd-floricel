<?php

// Customizer & theme support

function floricel_theme_support()
{
	add_theme_support('custom-logo');
	add_theme_support('post-thumbnails');
}

add_action('after_setup_theme', 'floricel_theme_support');

// Add custom fields to customizer
if (!function_exists('floricel_customizer_register')) :
	function floricel_customizer_register($wp_customize)
	{

		// Contact fields settings
		$wp_customize->add_setting('contact_email', array('default' => '', 'capability' => 'edit_theme_options', 'sanitize_callback' => 'floricel_sanitize_email'));
		$wp_customize->add_setting('contact_phone', array('default' => '', 'capability' => 'edit_theme_options', 'sanitize_callback' => 'floricel_sanitize_text'));
		$wp_customize->add_setting('contact_mobile', array('default' => '', 'capability' => 'edit_theme_options', 'sanitize_callback' => 'floricel_sanitize_text'));
		$wp_customize->add_setting('contact_whatsapp', array('default' => '', 'capability' => 'edit_theme_options', 'sanitize_callback' => 'floricel_sanitize_text'));
		$wp_customize->add_setting('contact_url', array('default' => '', 'capability' => 'edit_theme_options', 'sanitize_callback' => 'floricel_sanitize_url'));
		$wp_customize->add_setting('contact_address', array('default' => '', 'capability' => 'edit_theme_options', 'sanitize_callback' => 'floricel_sanitize_textarea'));
		$wp_customize->add_setting('contact_open', array('default' => '', 'capability' => 'edit_theme_options', 'sanitize_callback' => 'floricel_sanitize_textarea'));

		// Contact fields section
		$wp_customize->add_section('contact_section', array(
			'title' => __('Datos de contacto', 'floricel'),
			'priority' => 30,
		));

		// Contact fields controls
		$wp_customize->add_control(new WP_Customize_Control(
			$wp_customize,
			'contact_email',
			array(
				'label' => __('Correo electrónico', 'floricel'),
				'section' => 'contact_section',
				'settings' => 'contact_email',
				'type' => 'email'
			)
		));

		$wp_customize->add_control(new WP_Customize_Control(
			$wp_customize,
			'contact_phone',
			array(
				'label' => __('Teléfono', 'floricel'),
				'section' => 'contact_section',
				'settings' => 'contact_phone',
				'type' => 'text'
			)
		));

		$wp_customize->add_control(new WP_Customize_Control(
			$wp_customize,
			'contact_mobile',
			array(
				'label' => __('Teléfono móvil', 'floricel'),
				'section' => 'contact_section',
				'settings' => 'contact_mobile',
				'type' => 'text'
			)
		));

		$wp_customize->add_control(new WP_Customize_Control(
			$wp_customize,
			'contact_whatsapp',
			array(
				'label' => __('Whatsapp', 'floricel'),
				'section' => 'contact_section',
				'settings' => 'contact_whatsapp',
				'type' => 'text'
			)
		));

		$wp_customize->add_control(new WP_Customize_Control(
			$wp_customize,
			'contact_url',
			array(
				'label' => __('URL de contacto', 'floricel'),
				'section' => 'contact_section',
				'settings' => 'contact_url',
				'type' => 'url'
			)
		));

		$wp_customize->add_control(new WP_Customize_Control(
			$wp_customize,
			'contact_address',
			array(
				'label' => __('Dirección postal', 'floricel'),
				'section' => 'contact_section',
				'settings' => 'contact_address',
				'type' => 'textarea'
			)
		));

		$wp_customize->add_control(new WP_Customize_Control(
			$wp_customize,
			'contact_open',
			array(
				'label' => __('Horario de atención', 'floricel'),
				'section' => 'contact_section',
				'settings' => 'contact_open',
				'type' => 'textarea'
			)
		));
	}
endif;
add_action('customize_register', 'floricel_customizer_register');

function encode_email($e)
{
	$output = '';
	for ($i = 0; $i < strlen($e); $i++) {
		$output .= '&#' . ord($e[$i]) . ';';
	}
	return $output;
}

function floricel_sanitize_email($url)
{
	return sanitize_email($url);
}

function floricel_sanitize_url($url)
{
	return esc_url_raw($url);
}

function floricel_sanitize_text($url)
{
	return sanitize_text_field($url);
}

function floricel_sanitize_textarea($url)
{
	return sanitize_textarea_field($url);
}

function contactShortcode($atts)
{
	$a = shortcode_atts(array(
		'type' => 'email',
		'fa-icon' => '',
		'content' => '',
		'text-only' => false,
	), $atts);
	$content = get_theme_mod('contact_' . $a['type']);
	if ($a['text-only']) {
		return $content;
	}
	if ($a['content'] != '') {
		$content = $a['content'];
	}
	if ($a['fa-icon'] != '') {
		$fa = '<i class="fa fa-fw fa-' . $a['fa-icon'] . '"></i> ';
	}
	if ($a['type'] == 'email') {
		return '<a href="mailto:' . encode_email(get_theme_mod('contact_' . $a['type'])) . '">' . $fa . encode_email($content) . '</a>';
	}
	if ($a['type'] == 'phone' or $a['type'] == 'mobile') {
		return '<a href="tel:' . get_theme_mod('contact_' . $a['type']) . '">' . $fa . $content . '</a>';
	}
	if ($a['type'] == 'whatsapp') {
		$newWindow = (wp_is_mobile()) ? '' : 'target="_blank"';
		return '<a ' . $newWindow . ' href="https://api.whatsapp.com/send?phone=' . preg_replace('/[^0-9]/', '', get_theme_mod('contact_' . $a['type'])) . '">' . $fa . $content . '</a>';
	}
	if ($a['type'] == 'url') {
		return '<a href="' . get_theme_mod('contact_' . $a['type']) . '">' . $fa . $content . '</a>';
	}
	return $fa . $content;
}

add_shortcode('floricel-contact', 'contactShortcode');
