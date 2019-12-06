jQuery(document).ready(function($) {
	'use strict';
	if (typeof elementorFrontend != 'undefined') {
		if (typeof porto_init == 'function') {
			var porto_widgets = ['porto_blog.default', 'wp-widget-recent_posts-widget.default', 'wp-widget-recent_portfolios-widget.default', 'porto_products.default', 'porto_recent_posts.default'];
			$.each(porto_widgets, function(key, element_name) {
				elementorFrontend.hooks.addAction('frontend/element_ready/' + element_name, function($obj) {
					if ($obj.find('[data-plugin-masonry]').length) {
						$obj.find('[data-plugin-masonry]').children().each(function() {
							if (!($(this).get(0) instanceof HTMLElement)) {
								Object.setPrototypeOf($(this).get(0), HTMLElement.prototype);
							}
						});
					}
					porto_init( $obj );
				});
			});
		}

		if (typeof porto_woocommerce_init == 'function') {
			var porto_woocommerce_widgets = ['porto_products.default'];
			$.each(porto_woocommerce_widgets, function(key, element_name) {
				elementorFrontend.hooks.addAction('frontend/element_ready/' + element_name, function($obj) {
					porto_woocommerce_init();
				});
			});
		}
	}
});