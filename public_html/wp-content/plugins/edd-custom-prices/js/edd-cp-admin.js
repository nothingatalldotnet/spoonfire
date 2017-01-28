jQuery(document).ready(function ($) {
	$('#edd_cp_custom_pricing').change(function() {
		$('#edd_cp_container').toggle();
	});
	
	$('.edd_cp_remove_repeatable').click(function(e) {
		e.preventDefault();
		
		// From EDD core
		var row   = $(this).parent().parent( 'tr' ),
				count = row.parent().find( 'tr' ).length - 1,
				type  = $(this).data('type'),
				repeatable = 'tr.edd_repeatable_' + type + 's';
				
		$( 'input, select', row ).val( '' );
		row.fadeOut( 'fast' ).remove();
		return false;
	});
});
