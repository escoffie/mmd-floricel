<?php
/**
 * Add shortcodes for single product page
 *
 * @author   Porto Themes
 * @category Library
 * @since    5.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PortoCustomProduct {

	protected $shortcodes = array(
		'image',
		'title',
		'rating',
		'actions',
		'price',
		'excerpt',
		'description',
		'add_to_cart',
		'meta',
		'tabs',
		'upsell',
		'related',
		'next_prev_nav',
	);

	protected $display_product_page_elements = false;

	protected $edit_post = null;

	protected $edit_product = null;

	protected $is_product = false;

	public function __construct() {
		$this->init();
	}

	protected function init() {
		remove_action( 'porto_after_content_bottom', 'porto_woocommerce_output_related_products', 10 );

		if ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
			add_filter( 'body_class', array( $this, 'filter_body_class' ) );
			add_filter( 'porto_is_product', array( $this, 'filter_is_product' ) );
		}

		foreach ( $this->shortcodes as $shortcode ) {
			add_shortcode( 'porto_single_product_' . $shortcode, array( $this, 'shortcode_single_product_' . $shortcode ) );
		}

		if ( is_admin() || ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) ) {
			add_action( 'vc_after_init', array( $this, 'load_custom_product_shortcodes' ) );
		}
	}

	public function filter_body_class( $classes ) {
		global $post;
		if ( $post && 'product_layout' == $post->post_type ) {
			$classes[] = 'single-product';
		}
		return $classes;
	}

	public function filter_is_product( $is_product ) {
		if ( $this->is_product ) {
			return true;
		}
		$post_id = (int) vc_get_param( 'vc_post_id' );
		if ( $post_id ) {
			$post = get_post( $post_id );
			if ( $post && 'product_layout' == $post->post_type ) {
				$this->is_product = true;
				return true;
			}
		}
		return $is_product;
	}

	private function restore_global_product_variable() {
		if ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
			global $post, $porto_settings;
			if ( ! $this->edit_product && $post && 'product_layout' == $post->post_type/* && isset( $porto_settings['product-single-content-builder'] ) && $porto_settings['product-single-content-builder']*/ ) {
				$query = new WP_Query( array (
					'post_type'           => 'product',
					'post_status'         => 'publish',
					'posts_per_page'      => 1,
					'numberposts'         => 1,
					'ignore_sticky_posts' => true,
				) );
				if ( $query->have_posts() ) {
					$the_post           = $query->next_post();
					$this->edit_post    = $the_post;
					$this->edit_product = wc_get_product( $the_post );
				}
			}
			if ( $this->edit_product ) {
				global $post, $product;
				$post = $this->edit_post;
				setup_postdata( $this->edit_post );
				$product = $this->edit_product;
				return true;
			}
		}
		return false;
	}

	private function reset_global_product_variable() {
		if ( $this->edit_product ) {
			wp_reset_postdata();
		}
	}

	public function shortcode_single_product_image( $atts ) {
		if ( ! is_product() && ! $this->restore_global_product_variable() ) {
			return null;
		}

		extract( shortcode_atts( array(
			'style' => '',
		), $atts ) );

		if ( 'transparent' == $style ) {
			wp_enqueue_script( 'jquery-slick' );
		}
		ob_start();
		echo '<div class="product-layout-image' . ( $style ? ' product-layout-' . esc_attr( $style ) : '' ) . '">';
			echo '<div class="summary-before">';
				woocommerce_show_product_sale_flash();
			echo '</div>';
		if ( $style ) {
			global $porto_product_layout;
			$porto_product_layout = $style;
		}
		wc_get_template_part( 'single-product/product-image' );
		echo '</div>';
		if ( $style ) {
			$porto_product_layout = 'builder';
		}

		$this->reset_global_product_variable();

		return ob_get_clean();
	}

	public function shortcode_single_product_title( $atts ) {
		if ( ! is_product() && ! $this->restore_global_product_variable() ) {
			return null;
		}

		extract( shortcode_atts( array(
			'font_size'   => '',
			'font_weight' => '',
			'color'       => '',
			'el_class'    => '',
		), $atts ) );

		global $porto_settings;

		$attrs = '';
		if ( $font_size ) {
			$unit = trim( preg_replace( '/[0-9.]/', '', $font_size ) );
			if ( ! $unit ) {
				$font_size .= 'px';
			}
			$attrs .= 'font-size:' . esc_attr( $font_size ) . ';';
		}
		if ( $font_weight ) {
			$attrs .= 'font-weight:' . esc_attr( $font_weight ) . ';';
		}
		if ( $color ) {
			$attrs .= 'color:' . esc_attr( $color ) . ';';
		}
		if ( $attrs ) {
			$attrs = ' style="' . $attrs . '"';
		}
		$result  = '<h2 class="product_title entry-title' . ( ! $porto_settings['product-nav'] ? '' : ' show-product-nav' ) . ( $el_class ? ' ' . esc_attr( trim( $el_class ) ) : '' ) . '"' . $attrs . '>';
		$result .= esc_html( get_the_title() );
		$result .= '</h2>';

		$this->reset_global_product_variable();

		return $result;
	}

	public function shortcode_single_product_rating( $atts ) {
		if ( ! is_product() && ! $this->restore_global_product_variable() ) {
			return null;
		}

		ob_start();
		woocommerce_template_single_rating();

		$this->reset_global_product_variable();

		return ob_get_clean();
	}

	public function shortcode_single_product_actions( $atts ) {
		if ( ! is_product() && ! $this->restore_global_product_variable() ) {
			return null;
		}

		extract( shortcode_atts( array(
			'action' => 'woocommerce_single_product_summary',
		), $atts ) );

		ob_start();
		do_action( $action );

		$this->reset_global_product_variable();

		return ob_get_clean();
	}

	public function shortcode_single_product_price( $atts ) {
		if ( ! is_product() && ! $this->restore_global_product_variable() ) {
			return null;
		}

		ob_start();
		woocommerce_template_single_price();

		$this->reset_global_product_variable();

		return ob_get_clean();
	}

	public function shortcode_single_product_excerpt( $atts ) {
		if ( ! is_product() && ! $this->restore_global_product_variable() ) {
			return null;
		}

		ob_start();
		woocommerce_template_single_excerpt();

		$this->reset_global_product_variable();

		return ob_get_clean();
	}

	public function shortcode_single_product_description( $atts ) {
		if ( ! is_product() && ! $this->restore_global_product_variable() ) {
			return null;
		}

		ob_start();
		the_content();

		$this->reset_global_product_variable();

		return ob_get_clean();
	}

	public function shortcode_single_product_add_to_cart( $atts ) {
		if ( ! is_product() && ! $this->restore_global_product_variable() ) {
			return null;
		}

		ob_start();
		echo '<div class="product-summary-wrap">';
		woocommerce_template_single_add_to_cart();

		if ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
			echo '<script>theme.WooQtyField.initialize();</script>';
		}
		echo '</div>';

		$this->reset_global_product_variable();

		return ob_get_clean();
	}

	public function shortcode_single_product_meta( $atts ) {
		if ( ! is_product() && ! $this->restore_global_product_variable() ) {
			return null;
		}

		ob_start();
		woocommerce_template_single_meta();

		$this->reset_global_product_variable();

		return ob_get_clean();
	}

	public function shortcode_single_product_tabs( $atts ) {
		if ( ! is_product() && ! $this->restore_global_product_variable() ) {
			return null;
		}

		extract( shortcode_atts( array(
			'style' => '', // tabs or accordion
		), $atts ) );

		ob_start();
		if ( 'vertical' == $style ) {
			echo '<style>.woocommerce-tabs .resp-tabs-list { display: none; }
.woocommerce-tabs h2.resp-accordion { display: block; }
.woocommerce-tabs h2.resp-accordion:before { font-size: 20px; font-weight: 400; position: relative; top: -4px; }
.woocommerce-tabs .tab-content { border-top: none; padding-' . ( is_rtl() ? 'right' : 'left' ) . ': 20px; }</style>';
		}
		wc_get_template_part( 'single-product/tabs/tabs' );

		$this->reset_global_product_variable();

		return ob_get_clean();
	}

	public function shortcode_single_product_upsell( $atts ) {
		if ( ! is_product() && ! $this->restore_global_product_variable() ) {
			return null;
		}

		ob_start();
		woocommerce_upsell_display();

		$this->reset_global_product_variable();

		return ob_get_clean();
	}

	public function shortcode_single_product_related( $atts ) {
		if ( ! is_product() && ! $this->restore_global_product_variable() ) {
			return null;
		}

		ob_start();
		woocommerce_output_related_products();

		$this->reset_global_product_variable();

		return ob_get_clean();
	}

	public function shortcode_single_product_next_prev_nav( $atts ) {
		if ( ! is_product() && ! $this->restore_global_product_variable() ) {
			return null;
		}

		ob_start();
		porto_woocommerce_product_nav();

		$this->reset_global_product_variable();

		return ob_get_clean();
	}

	function load_custom_product_shortcodes() {
		if ( ! $this->display_product_page_elements ) {
			if ( 'post-new.php' == $GLOBALS['pagenow'] && isset( $_GET['post_type'] ) && 'product_layout' == $_GET['post_type'] ) {
				$this->display_product_page_elements = true;
			} elseif ( 'post.php' == $GLOBALS['pagenow'] && isset( $_GET['post'] ) ) {
				$post = get_post( intval( $_GET['post'] ) );
				if ( is_object( $post ) && 'product_layout' == $post->post_type ) {
					$this->display_product_page_elements = true;
				}
			} elseif ( porto_is_ajax() && isset( $_REQUEST['post_id'] ) ) {
				$post = get_post( intval( $_REQUEST['post_id'] ) );
				if ( is_object( $post ) && ( 'product_layout' == $post->post_type || 'product' == $post->post_type ) ) {
					$this->display_product_page_elements = true;
				}
			} elseif ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
				if ( is_admin() && isset( $_GET['post_type'] ) && 'product_layout' == $_GET['post_type'] ) {
					$this->display_product_page_elements = true;
				} elseif ( ! is_admin() ) {
					$post_id = (int) vc_get_param( 'vc_post_id' );
					if ( $post_id ) {
						$post = get_post( $post_id );
						if ( is_object( $post ) && 'product_layout' == $post->post_type ) {
							$this->display_product_page_elements = true;
						}
					}
				}
			}
		}

		if ( ! $this->display_product_page_elements ) {
			return;
		}

		$custom_class = porto_vc_custom_class();

		vc_map(
			array(
				'name'     => __( 'Product Image', 'porto-functionality' ),
				'base'     => 'porto_single_product_image',
				'icon'     => 'porto_vc_woocommerce',
				'category' => __( 'Product Page', 'porto-functionality' ),
				'params'   => array(
					array(
						'type'        => 'dropdown',
						'heading'     => __( 'Style', 'porto-functionality' ),
						'param_name'  => 'style',
						'value'       => array(
							__( 'Default', 'porto-functionality' ) => '',
							__( 'Extended', 'porto-functionality' ) => 'extended',
							__( 'Grid Images', 'porto-functionality' ) => 'grid',
							__( 'Thumbs on Image', 'porto-functionality' ) => 'full_width',
							__( 'List Images', 'porto-functionality' ) => 'sticky_info',
							__( 'Left Thumbs 1', 'porto-functionality' ) => 'transparent',
							__( 'Left Thumbs 2', 'porto-functionality' ) => 'centered_vertical_zoom',
						),
						'admin_label' => true,
					),
				),
			)
		);
		vc_map(
			array(
				'name'     => __( 'Product Title', 'porto-functionality' ),
				'base'     => 'porto_single_product_title',
				'icon'     => 'porto_vc_woocommerce',
				'category' => __( 'Product Page', 'porto-functionality' ),
				'params'   => array(
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Font Size', 'porto-functionality' ),
						'param_name'  => 'font_size',
						'admin_label' => true,
					),
					array(
						'type'        => 'dropdown',
						'heading'     => __( 'Font Weight', 'porto-functionality' ),
						'param_name'  => 'font_weight',
						'value'       => array(
							''    => __( 'Default', 'porto-functionality' ),
							'100' => '100',
							'200' => '200',
							'300' => '300',
							'400' => '400',
							'500' => '500',
							'600' => '600',
							'700' => '700',
							'800' => '800',
							'900' => '900',
						),
						'admin_label' => true,
					),
					array(
						'type'       => 'colorpicker',
						'class'      => '',
						'heading'    => __( 'Color', 'porto-functionality' ),
						'param_name' => 'color',
						'value'      => '',
					),
					$custom_class,
				),
			)
		);
		vc_map(
			array(
				'name'                    => __( 'Product Description', 'porto-functionality' ),
				'base'                    => 'porto_single_product_description',
				'icon'                    => 'porto_vc_woocommerce',
				'category'                => __( 'Product Page', 'porto-functionality' ),
				'show_settings_on_create' => false,
			)
		);
		vc_map(
			array(
				'name'                    => __( 'Product Rating', 'porto-functionality' ),
				'base'                    => 'porto_single_product_rating',
				'icon'                    => 'porto_vc_woocommerce',
				'category'                => __( 'Product Page', 'porto-functionality' ),
				'show_settings_on_create' => false,
			)
		);
		vc_map(
			array(
				'name'     => __( 'Product Hooks', 'porto-functionality' ),
				'base'     => 'porto_single_product_actions',
				'icon'     => 'porto_vc_woocommerce',
				'category' => __( 'Product Page', 'porto-functionality' ),
				'params'   => array(
					array(
						'type'        => 'dropdown',
						'heading'     => __( 'action', 'porto-functionality' ),
						'param_name'  => 'style',
						'value'       => array(
							'woocommerce_before_single_product_summary'       => 'woocommerce_before_single_product_summary',
							'woocommerce_single_product_summary'              => 'woocommerce_single_product_summary',
							'woocommerce_after_single_product_summary'        => 'woocommerce_after_single_product_summary',
							'porto_woocommerce_before_single_product_summary' => 'porto_woocommerce_before_single_product_summary',
							'porto_woocommerce_single_product_summary2'       => 'porto_woocommerce_single_product_summary2',
						),
						'admin_label' => true,
					),
				),
			)
		);
		vc_map(
			array(
				'name'                    => __( 'Product Price', 'porto-functionality' ),
				'base'                    => 'porto_single_product_price',
				'icon'                    => 'porto_vc_woocommerce',
				'category'                => __( 'Product Page', 'porto-functionality' ),
				'show_settings_on_create' => false,
			)
		);
		vc_map(
			array(
				'name'                    => __( 'Product Excerpt', 'porto-functionality' ),
				'base'                    => 'porto_single_product_excerpt',
				'icon'                    => 'porto_vc_woocommerce',
				'category'                => __( 'Product Page', 'porto-functionality' ),
				'show_settings_on_create' => false,
			)
		);
		vc_map(
			array(
				'name'                    => __( 'Product Add To Cart', 'porto-functionality' ),
				'base'                    => 'porto_single_product_add_to_cart',
				'icon'                    => 'porto_vc_woocommerce',
				'category'                => __( 'Product Page', 'porto-functionality' ),
				'show_settings_on_create' => false,
			)
		);
		vc_map(
			array(
				'name'                    => __( 'Product Meta', 'porto-functionality' ),
				'base'                    => 'porto_single_product_meta',
				'icon'                    => 'porto_vc_woocommerce',
				'category'                => __( 'Product Page', 'porto-functionality' ),
				'show_settings_on_create' => false,
			)
		);
		vc_map(
			array(
				'name'     => __( 'Product Tabs', 'porto-functionality' ),
				'base'     => 'porto_single_product_tabs',
				'icon'     => 'porto_vc_woocommerce',
				'category' => __( 'Product Page', 'porto-functionality' ),
				'params'   => array(
					array(
						'type'        => 'dropdown',
						'heading'     => __( 'Style', 'porto-functionality' ),
						'param_name'  => 'style',
						'value'       => array(
							__( 'Default', 'porto-functionality' ) => '',
							__( 'Vetical', 'porto-functionality' ) => 'vertical',
						),
						'admin_label' => true,
					),
				),
			)
		);
		vc_map(
			array(
				'name'                    => __( 'Upsells', 'porto-functionality' ),
				'base'                    => 'porto_single_product_upsell',
				'icon'                    => 'porto_vc_woocommerce',
				'category'                => __( 'Product Page', 'porto-functionality' ),
				'show_settings_on_create' => false,
			)
		);
		vc_map(
			array(
				'name'                    => __( 'Related Products', 'porto-functionality' ),
				'base'                    => 'porto_single_product_related',
				'icon'                    => 'porto_vc_woocommerce',
				'category'                => __( 'Product Page', 'porto-functionality' ),
				'show_settings_on_create' => false,
			)
		);
		vc_map(
			array(
				'name'                    => __( 'Prev and Next Navigation', 'porto-functionality' ),
				'base'                    => 'porto_single_product_next_prev_nav',
				'icon'                    => 'porto_vc_woocommerce',
				'category'                => __( 'Product Page', 'porto-functionality' ),
				'show_settings_on_create' => false,
			)
		);
	}
}

new PortoCustomProduct();
