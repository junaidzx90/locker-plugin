jQuery(function( $ ) {
	'use strict';

	$('.edit_locked').on('click', function (e) {
		e.preventDefault();
		$(this).parent().remove()
	})

});
