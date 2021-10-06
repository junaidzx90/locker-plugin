jQuery(function( $ ) {
	'use strict';

	$('.edit_locked').on('click', function (e) {
		e.preventDefault();
		$(this).parent().remove()
	})

	$('#locker_for_all_user').on('change', function () {
		if ($(this).val() == 0) {
			$(this).val(1)
		} else {
			$(this).val(0)
		}
	})
});
