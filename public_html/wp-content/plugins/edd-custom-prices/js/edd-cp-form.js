jQuery(document).ready(function ($) {

	if(edd_cp.ajax_enabled == '1') {
		var cart_label = '.edd-add-to-cart-label'; 			
	} else {
		var cart_label = '.edd-add-to-cart';
	}
	
	$('.edd_price_options :input').change(function() {
		var edd_form = $(this).parents('.edd_download_purchase_form');
		if($(this).is(':checked') && $(this).hasClass('edd_cp_radio')) {
			$('.edd-cp-container', edd_form).fadeIn();
		} else {
			if(edd_cp.ajax_enabled == '1') {
				$(cart_label, edd_form).html(edd_cp.add_to_cart_text);
			} else {
				$(cart_label, edd_form).val(edd_cp.add_to_cart_text);
			}
			$('.edd-cp-container', edd_form).fadeOut('fast');		
		}
	});	
	
	$('.edd_cp_price').keyup(function() {
		
		var edd_form = $(this).parents('.edd_download_purchase_form');
		
		var min_price = parseFloat($('.edd_cp_price', edd_form).data('min'), 10);
			
		var new_price = parseFloat($(this).val()).toFixed(2);

		var defaultText = $(this).data('default-text');

		$('.edd-cp-container', edd_form).show(); // Ensure the field is shown
		
		if(isNaN(new_price) || new_price < min_price)
			new_price = min_price.toFixed(2);
		
		if(edd_cp.currency_position == 'before') {
			var price_formatted = edd_cp.currency+new_price;
		} else {
			var price_formatted = new_price+edd_cp.currency;
		}

		if ( defaultText == "" ) {
			if(edd_cp.ajax_enabled == '1') {			
				$(cart_label, edd_form).html(price_formatted + ' - ' + edd_cp.add_to_cart_text);			
			} else {				
				$(cart_label, edd_form).val(price_formatted + ' - ' + edd_cp.add_to_cart_text);
			}
		}

		$('a.edd-add-to-cart', edd_form).attr('data-price', new_price);
				
	});
	
	if($('.edd_cp_price').length > 0 && $('.edd_cp_price').val().length > 0) {
		$('.edd_cp_price').keyup();
	}
	
	$('.edd-add-to-cart').click(function(e) {
		$('.edd_errors').remove();
		
		var edd_form = $(this).parents('.edd_download_purchase_form');
				
		if($(this).data('variable-price') == 'yes' && !$('.edd_cp_radio', edd_form).is(':checked')) {
			return true;
		}	
				
		var min_price = parseFloat($('.edd_cp_price', edd_form).data('min'), 10);	
		
		if(isNaN(min_price)) {
			return true; // Custom price isn't enabled
		}
		
		if($('.edd_cp_price', edd_form).val() >= min_price) {			
			return true;
		} else {
			var $errorContainer = $( '<div class="edd_errors">' ),
				$error = $( '<p class="edd_error">' );

			$errorContainer.append( $error );

			$error.text( edd_cp.min_price_error );
			$(edd_form).append( $errorContainer );	
			return false;
		}
	});
});