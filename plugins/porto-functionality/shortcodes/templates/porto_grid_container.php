<?php
$output = $grid_size = $gutter_size = $max_width = $animation_type = $animation_duration = $animation_delay = $el_class = '';
extract(
	shortcode_atts(
		array(
			'layout'             => '',
			'grid_layout'        => '1',
			'grid_height'        => 600,
			'grid_size'          => '0',
			'gutter_size'        => '2%',
			'max_width'          => '767px',
			'animation_type'     => '',
			'animation_duration' => 1000,
			'animation_delay'    => 0,
			'el_class'           => '',
		),
		$atts
	)
);

wp_enqueue_script( 'isotope' );

if ( ! $gutter_size ) {
	$gutter_size = '0%';
}
$valid_characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
$rand_escaped     = '';
$length           = 32;
for ( $n = 1; $n < $length; $n++ ) {
	$whichcharacter = rand( 0, strlen( $valid_characters ) - 1 );
	$rand_escaped  .= $valid_characters{$whichcharacter};
}

$el_class = porto_shortcode_extract_class( $el_class );

$output = '<div class="porto-grid-container"';
if ( $animation_type ) {
	$output .= ' data-appear-animation="' . esc_attr( $animation_type ) . '"';
	if ( $animation_delay ) {
		$output .= ' data-appear-animation-delay="' . esc_attr( $animation_delay ) . '"';
	}
	if ( $animation_duration && 1000 != $animation_duration ) {
		$output .= ' data-appear-animation-duration="' . esc_attr( $animation_duration ) . '"';
	}
}
$output .= '>';

$iso_options               = array();
$iso_options['layoutMode'] = 'masonry';
$iso_options['masonry']    = array( 'columnWidth' => '.iso-column-class' );
if ( ! ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) ) {
	$iso_options['itemSelector'] = '.porto-grid-item';
} else {
	$iso_options['itemSelector'] = '.vc_porto_grid_item';
}

$extra_attrs = '';
if ( 'preset' == $layout ) {
	global $porto_grid_layout, $porto_item_count;
	$porto_grid_layout  = porto_creative_grid_layout( $grid_layout );
	$grid_height_number = trim( preg_replace( '/[^0-9]/', '', $grid_height ) );
	$unit               = trim( str_replace( $grid_height_number, '', $grid_height ) );
	$porto_item_count   = 0;

	$ms     = 1;
	$ms_col = '';
	foreach ( $porto_grid_layout as $pl ) {
		$width_arr = explode( '-', $pl['width'] );
		if ( count( $width_arr ) > 1 ) {
			$width = (int) $width_arr[0] / (int) $width_arr[1];
		} else {
			$width = (int) $width_arr[0];
		}
		if ( $width < $ms ) {
			$ms     = $width;
			$ms_col = $pl['width'];
		}
	}
	$el_class .= ( $el_class ? ' ' : '' ) . 'porto-preset-layout';

	if ( $ms_col ) {
		$iso_options['masonry'] = array( 'columnWidth' => $iso_options['itemSelector'] . '.grid-col-' . $ms_col );
	}

	if ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
		$extra_attrs = array();
		foreach ( $porto_grid_layout as $pl ) {
			$extra_attrs[] = 'grid-col-' . $pl['width'] . ' grid-col-md-' . $pl['width_md'] . ( isset( $pl['width_lg'] ) ? ' grid-col-lg-' . $pl['width_lg'] : '' ) . ( isset( $pl['height'] ) ? ' grid-height-' . $pl['height'] : '' );
		}
		$extra_attrs = ' data-item-grid="' . esc_attr( implode( ',', $extra_attrs ) ) . '"';
	}
} else {
	preg_match_all( '/\[porto_grid_item\s[^]]*width="([^]"]*)"[^]]*\]/', $content, $matches );
	$column_width     = 0;
	$column_width_str = '';
	if ( isset( $matches[1] ) && is_array( $matches[1] ) ) {
		foreach ( $matches[1] as $index => $item ) {
			$w = preg_replace( '/[^.0-9]/', '', $item );
			if ( $column_width > (float) $w || 0 == $index ) {
				$column_width     = (float) $w;
				$column_width_str = $item;
			}
		}
	}

	if ( $column_width > 0 ) {
		$replace_count = 1;
		$content       = str_replace( array( '[porto_grid_item width="' . esc_attr( $column_width_str ) . '"', '[porto_grid_item  width="' . esc_attr( $column_width_str ) . '"' ), '[porto_grid_item width="' . esc_attr( $column_width_str ) . '" column_class="true"', $content, $replace_count );
	}
}
$iso_options['animationEngine'] = 'best-available';
$iso_options['resizable']       = false;

$output .= '<div id="grid-' . $rand_escaped . '" class="' . esc_attr( $el_class ) . ' wpb_content_element clearfix" data-plugin-masonry data-plugin-options=\'' . json_encode( $iso_options ) . '\'' . $extra_attrs . '>';
$output .= do_shortcode( $content );
$output .= '</div>';
if ( 'preset' == $layout ) {
	unset( $GLOBALS['porto_grid_layout'], $GLOBALS['porto_item_count'] );
}

$gutter_size_number  = preg_replace( '/[^.0-9]/', '', $gutter_size );
$gutter_size         = str_replace( $gutter_size_number, (float) ( $gutter_size_number / 2 ), $gutter_size );
$gutter_size_escaped = esc_html( $gutter_size );

$output .= '<style scope="scope">';
$output .= '#grid-' . $rand_escaped . ' .porto-grid-item { padding: ' . $gutter_size_escaped . '; }';
$output .= '#grid-' . $rand_escaped . ' { margin: -' . $gutter_size_escaped . ' -' . $gutter_size_escaped . ' ' . $gutter_size_escaped . '; }';
if ( 'preset' == $layout ) {
	ob_start();
	porto_creative_grid_style( $porto_grid_layout, $grid_height_number, 'grid-' . $rand_escaped, false, false, $unit, $iso_options['itemSelector'] );
	$output .= ob_get_clean();
} else {
	$output .= '@media (max-width:' . esc_html( $max_width ) . ') {';
	$output .= '#grid-' . $rand_escaped . ' { height: auto !important }';
	$output .= '#grid-' . $rand_escaped . ' .porto-grid-item:first-child { margin-top: 0 }';
	$output .= '#grid-' . $rand_escaped . ' .porto-grid-item { width: 100% !important; position: static !important; float: none }';
	$output .= '}';
}
if ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
	$output .= '.porto-grid-container .porto-grid-item { float: none; } .porto-grid-container .vc_porto_grid_item { float: left; }';
	$output .= '.porto-grid-container .porto-grid-item .wpb_single_image { margin-bottom: 0; }';
}
$output .= '</style>';

$output .= '</div>';

echo porto_filter_output( $output );
