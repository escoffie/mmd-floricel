jQuery(document).ready(function($) {
	'use strict';

	$('body').on('tabsbeforeactivate', '.wpb_tour_tabs_wrapper', function(e, ui) {
		ui.oldTab.removeClass('active');
		ui.newTab.addClass('active');
	});
});