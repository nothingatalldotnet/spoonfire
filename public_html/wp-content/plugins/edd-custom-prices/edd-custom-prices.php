<?php
/*
Plugin Name: Easy Digital Downloads - Custom prices
Plugin URL: http://easydigitaldownloads.com/downloads/custom-prices/
Description: Allow customers to enter a custom price for a product, based on a minimum price set in the admin.
Version: 1.5.4
Author: Easy Digital Downloads
Author URI: https://easydigitaldownloads.com
EDD Version Required: 1.9
*/

/* ------------------------------------------------------------------------*
 * Constants
 * ------------------------------------------------------------------------*/

// Plugin version
if( ! defined( 'EDD_CUSTOM_PRICES' ) ) {
	define( 'EDD_CUSTOM_PRICES', '1.5.4' );
}

// Plugin Folder URL
if( ! defined( 'EDD_CUSTOM_PRICES_URL' ) ) {
	define( 'EDD_CUSTOM_PRICES_URL', plugin_dir_url( __FILE__ ) );
}

// Plugin Folder Path
if( ! defined( 'EDD_CUSTOM_PRICES_DIR' ) ) {
	define( 'EDD_CUSTOM_PRICES_DIR', plugin_dir_path( __FILE__ ) );
}

// Plugin Root File
if( ! defined( 'EDD_CUSTOM_PRICES_FILE' ) ) {
	define( 'EDD_CUSTOM_PRICES_FILE', __FILE__ );
}

// Plugin name
if( ! defined( 'EDD_CUSTOM_PRICES_PLUGIN_NAME' ) ) {
	define( 'EDD_CUSTOM_PRICES_PLUGIN_NAME', 'Custom Prices' );
}

/*
* Plugin updates/license key
*/

if( class_exists( 'EDD_License' ) && is_admin() ) {
	$license = new EDD_License( __FILE__, EDD_CUSTOM_PRICES_PLUGIN_NAME, EDD_CUSTOM_PRICES, 'EDD Team' );
}

/*
* Fix download files when using variable pricing.
* For now it will only return the files for the first variable option.
*/

function edd_cp_download_files( $files, $id, $variable_price_id ) {
	if ( ! edd_cp_has_custom_pricing( $id ) || $variable_price_id != -1 ) {
		return $files;
	}
	remove_filter( 'edd_download_files', 'edd_cp_download_files' );
	$files = edd_get_download_files( $id, 1 );
	return $files;
}
add_filter( 'edd_download_files', 'edd_cp_download_files', 10, 3 );

/*
* Show notice if EDD is disabled, and deactive Custom Prices
*/

function edd_cp_admin_notice() {
	if( ! class_exists( 'Easy_Digital_Downloads' ) ) {
		deactivate_plugins( 'edd-custom-prices/edd-custom-prices.php' ); ?>
		<div class="error"><p><strong>Error:</strong> Easy Digital Downloads must be activated to use the Custom Prices extension.</p></div>
	<?php }
}
add_action( 'admin_notices', 'edd_cp_admin_notice' );

/*
* Enqueue scripts
*/

function edd_cp_load_scripts() {
	global $edd_options;
	wp_enqueue_script( 'edd-cp-form', EDD_CUSTOM_PRICES_URL . 'js/edd-cp-form.js', array( 'jquery' ), EDD_CUSTOM_PRICES );
	$add_to_cart_text =  ! empty( $edd_options[ 'add_to_cart_text' ] ) ? $edd_options[ 'add_to_cart_text' ] : __( 'Purchase', 'edd_cp' );
	$ajax_enabled = edd_is_ajax_enabled() ? 1 : 0;
	$currency_position = ! empty( $edd_options[ 'currency_position'] ) ? $edd_options[ 'currency_position'] : 'before';
	$min_price_error = __( 'Please enter a custom price higher than the minimum amount', 'edd_cp' );
	wp_localize_script( 'edd-cp-form', 'edd_cp', array( 'currency' => edd_currency_filter( '' ), 'add_to_cart_text' => $add_to_cart_text, 'ajax_enabled' => $ajax_enabled, 'currency_position' => $currency_position, 'min_price_error' => $min_price_error ) );
}
add_action( 'wp_enqueue_scripts', 'edd_cp_load_scripts' );

/*
* Enqueue admin scripts
*/

function edd_cp_load_admin_scripts($hook) {
	global $post;

	if ( is_object( $post ) && $post->post_type != 'download' ) {
	    return;
	}

	wp_enqueue_script( 'edd-cp-admin-scripts', EDD_CUSTOM_PRICES_URL . 'js/edd-cp-admin.js', array( 'jquery' ), EDD_CUSTOM_PRICES );
}
add_action( 'admin_enqueue_scripts', 'edd_cp_load_admin_scripts' );

/*
* Check if product has custom pricing enabled
*/

function edd_cp_has_custom_pricing( $post_id ) {
	return get_post_meta( $post_id, '_edd_cp_custom_pricing', true );
}

/*
* Add custom price fields in metabox
*/

function edd_cp_render_custom_price_field( $post_id ) {
	global $edd_options;
	$custom_pricing = edd_cp_has_custom_pricing( $post_id );
	$default_price = get_post_meta( $post_id, 'edd_cp_default_price', true );
	if ( ! empty( $default_price ) ) {
		$default_price = edd_format_amount( $default_price );
	}
	$min_price = edd_format_amount( get_post_meta( $post_id, 'edd_cp_min', true ) );
	$bonus_item = get_post_meta( $post_id, 'bonus_item', true );
	if( !$bonus_item ) {
		$bonus_item = '';
	}
	$button_text = get_post_meta( $post_id, 'cp_button_text', true ); ?>
	<p>
		<strong><?php _e( 'Custom Pricing:', 'edd_cp' ); ?></strong>
	</p>

    <p>
		<label for="edd_cp_custom_pricing">
			<input type="checkbox" name="_edd_cp_custom_pricing" id="edd_cp_custom_pricing" value="1" <?php checked( 1, $custom_pricing ); ?> />
			<?php _e( 'Enable custom pricing', 'edd_cp' ); ?>
		</label>
	</p>

   	<div id="edd_cp_container" <?php echo $custom_pricing ? '' : 'style="display: none;"'; ?>>

		<p>
        <label for="edd_cp_default_price">
            <?php _e( 'Default: ', 'edd_cp' ); ?>
            <?php if ( ! isset( $edd_options['currency_position'] ) || $edd_options['currency_position'] == 'before' ) : ?>
                <?php echo edd_currency_filter( '' ); ?><input type="text" name="edd_cp_default_price" id="edd_cp_default_price" value="<?php echo isset( $default_price ) ? esc_attr( $default_price ) : ''; ?>" size="30" style="width: 60px;" placeholder="2.00"/>
                <?php else : ?>
                    <input type="text" name="edd_cp_default_price" id="edd_cp_default_price" value="<?php echo isset( $default_price ) ? esc_attr( $default_price ) : ''; ?>" size="30" style="width: 60px;" placeholder="2.00"/><?php echo edd_currency_filter( '' ); ?>
            <?php endif; ?>
            <?php _e( 'Leave empty for no default price', 'edd_cp' ); ?>

        </label>
        </p>

        <p>
        <label for="edd_cp_price_min">
            <?php _e( 'Min: ', 'edd_cp' ); ?>
            <?php if ( ! isset( $edd_options['currency_position'] ) || $edd_options['currency_position'] == 'before' ) : ?>
                <?php echo edd_currency_filter( '' ); ?><input type="text" name="edd_cp_min" id="edd_cp_price_min" value="<?php echo isset( $min_price ) ? esc_attr( $min_price ) : ''; ?>" size="30" style="width: 40px;" placeholder="1.99"/>
                <?php else : ?>
                    <input type="text" name="edd_cp_min" id="edd_cp_price_min" value="<?php echo isset( $min_price ) ? esc_attr( $min_price ) : ''; ?>" size="30" style="width: 40px;" placeholder="1.99"/><?php echo edd_currency_filter( '' ); ?>
            <?php endif; ?>
            <?php _e( 'Enter 0 for no min price', 'edd_cp' ); ?>

        </label>
        </p>

        <p>
        	<label for="edd_cp_button_text"><?php _e( 'Button text: ', 'edd_cp' ); ?></label>
            <input type="text" name="cp_button_text" id="edd_cp_button_text" value="<?php echo isset( $button_text ) ? esc_attr( $button_text ) : ''; ?>" size="30" style="width: 140px;" placeholder="<?php esc_attr_e( 'Name your price', 'edd_cp' ); ?>" />
            <?php _e( 'Edit the default button text, displays the price by default', 'edd_cp' ); ?>
        </p>

        <p><strong><?php _e( 'Bonus item', 'edd_cp' ); ?></strong></p>
        <p><?php _e( 'A bonus item allow you to give away an item for free when the custom price meets set conditions', 'edd_cp' ); ?></p>
        <table class="widefat" width="100%" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th><?php _e( 'Condition', 'edd_cp' ); ?></th>
                    <th style="width: 20%"><?php _e( 'Price', 'edd_cp' ); ?></th>
                    <th style="width: 60%"><?php _e( 'Bonus Item', 'edd_cp' ); ?></th>
                    <th style="width: 2%"></th>
                </tr>
            </thead>
            <tbody>
            	<tr>
                    <td>
                        <select name="bonus_item[condition]">
                            <option value="more_than" <?php if( isset( $bonus_item['condition'] ) ) selected( $bonus_item['condition'], 'more_than' ); ?>>More than</option>
                            <option value="less_than" <?php if( isset( $bonus_item['condition'] ) ) selected( $bonus_item['condition'], 'less_than' ); ?>>Less than</option>
                            <option value="equal_to" <?php if( isset( $bonus_item['condition'] ) ) selected( $bonus_item['condition'], 'equal_to' ); ?>>Equal to</option>
                        </select>
                    </td>
                     <td>
                        <input type="text" name="bonus_item[price]" value="<?php echo isset( $bonus_item['price'] ) ? esc_attr( $bonus_item['price'] ) : ''; ?>" placeholder="<?php esc_attr_e( 'Price', 'edd_cp' ); ?>" />
                    </td>
                    <td>
                    <select name="bonus_item[product]">
                        <option value="0"><?php _e( 'None', 'edd_cp' ); ?></option>
                        <?php
                        $downloads = get_posts( array( 'post_type' => 'download', 'nopaging' => true ) );
						if( empty( $bonus_item ) )
							$bonus_item = array();

                        if( $downloads ) :
                            foreach( $downloads as $download ) :
                                echo '<option value="' . esc_attr( $download->ID ) . '" '.selected( true, in_array( $download->ID, $bonus_item ), false ).'>' . esc_html( get_the_title( $download->ID ) ) . '</option>';
                            endforeach;
                        endif;
                        ?>
                    </select>
                    </td>
                    <td>
                    	<a href="#" class="edd_remove_repeatable edd_cp_remove_repeatable1" style="background: url(<?php echo admin_url('/images/xit.gif'); ?>) no-repeat;">&times;</a>
                  	</td>
                </tr>
            </tbody>
        </table>
  	</div>
<?php
}
add_filter( 'edd_after_price_field', 'edd_cp_render_custom_price_field' );


/*
* Add fields to be saved
*/

function edd_cp_metabox_fields_save( $fields ) {
	$fields[] = '_edd_cp_custom_pricing';
	$fields[] = 'edd_cp_min';
	$fields[] = 'bonus_item';
	$fields[] = 'cp_button_text';
	$fields[] = 'edd_cp_default_price';
	return $fields;
}
add_filter( 'edd_metabox_fields_save', 'edd_cp_metabox_fields_save' );

/*
* Hook into add to cart post
*/

function edd_cp_purchase_link_top( $download_id ) {
	global $edd_options;
	$default_price = get_post_meta( $download_id, 'edd_cp_default_price', true );
	$min_price = edd_format_amount ( get_post_meta( $download_id, 'edd_cp_min', true ) );
	$custom_price = isset( $_GET ['cp_price'] ) ? edd_format_amount( $_GET ['cp_price'] ) : '';
	$button_text = get_post_meta( $download_id, 'cp_button_text', true );
	if ( empty( $custom_price ) && ! empty( $default_price ) ) {
		$custom_price = edd_format_amount( $default_price );
	}

	if ( edd_cp_has_custom_pricing( $download_id ) && ! edd_single_price_option_mode( $download_id ) ) {
		$wrapper_display = '';
		$download = new EDD_Download( $download_id );
		if ( edd_item_in_cart( $download_id, array() ) && ( ! $download->has_variable_prices() || ! $download->is_single_price_mode() ) ) {
			$wrapper_display = 'style="display:none;"';
		} ?>
        <p class="edd-cp-container" <?php echo $wrapper_display; ?>>
		<?php
      	//echo __( 'Name your price', 'edd_cp' );

        if ( ! isset( $edd_options['currency_position'] ) || $edd_options['currency_position'] == 'before' ) : ?>
            <?php echo edd_currency_filter( '' ); ?> <input type="text" name="edd_cp_price" class="edd_cp_price" value="<?php echo esc_attr( $custom_price ); ?>" size="30" data-min="<?php echo $min_price; ?>" style="width: 40px;" data-default-text="<?php echo esc_attr( $button_text ); ?>" />
     	<?php else : ?>
            <input type="text" name="edd_cp_price" class="edd_cp_price" value="<?php echo esc_attr( $custom_price ); ?>" size="30" data-min="<?php echo $min_price; ?>" style="width: 80px;" data-default-text="<?php echo esc_attr( $button_text ); ?>" /><?php echo edd_currency_filter( '' );
		endif;
		$min_no_format = floatval( $min_price );
		if( !empty( $min_no_format ) ) {
        	echo ' <small>(min '.edd_currency_filter( ( $min_price ) ).')</small>';
		} ?>
        </p>
    <?php
	}
}
add_filter( 'edd_purchase_link_top', 'edd_cp_purchase_link_top' );

/*
* Add additional list item if variable pricing is enabled
*/

function edd_cp_after_price_options_list( $key, $price, $download_id ) {
	if( ! edd_cp_has_custom_pricing( $download_id ) ) {
		return;
	}

	if ( ! edd_single_price_option_mode( $download_id ) ) {
		return;
	}

	global $edd_options;
	$default_price_option = edd_get_default_variable_price( $download_id );
	$display = 'none';
	if ( $key === $default_price_option ) {
		$display = 'block';
	} ?>

	<div class="edd-cp-price-option-wrapper" style="display: <?php echo esc_attr( $display ); ?>;">
		<?php
		echo __( 'Name your price', 'edd_cp' );
		if ( ! isset( $edd_options['currency_position'] ) || $edd_options['currency_position'] == 'before' ) : ?>
			<?php echo edd_currency_filter( '' ); ?> <input type="text" name="edd_cp_price[<?php echo esc_attr( $key ); ?>]" class="edd_cp_price" value="<?php echo esc_attr( $price['amount'] ); ?>" size="30" data-min="" />
		<?php else : ?>
			<input type="text" name="edd_cp_price[<?php echo esc_attr( $key ); ?>]" class="edd_cp_price" value="<?php echo esc_attr( $price['amount'] ); ?>" size="30" data-min="" data-default-text="<?php echo esc_attr( $button_text ); ?>" /><?php echo edd_currency_filter( '' );
		endif; ?>
	</div>
<?php }
add_action( 'edd_after_price_option', 'edd_cp_after_price_options_list', 10, 3 );

/*
* Update price before adding item to card
*/

function edd_cp_add_to_cart_item( $cart_item ) {
	remove_filter( 'edd_add_to_cart_item', 'edd_cp_add_to_cart_item' );
	remove_filter( 'edd_ajax_pre_cart_item_template', 'edd_cp_add_to_cart_item' );
	if( !empty( $_POST['post_data'] ) || isset( $_POST['edd_cp_price'] ) ) {
		if( !empty( $_POST['post_data'] ) ) {
			$post_data = array();
			parse_str( $_POST['post_data'], $post_data );
			$custom_price = isset( $post_data['edd_cp_price'] ) ? $post_data['edd_cp_price'] : null;
		} else {
			$custom_price = $_POST['edd_cp_price'];
		}

		// Multi-option purchase mode.
		if ( ! empty( $_POST['price_ids'] ) && is_array( $custom_price ) && edd_single_price_option_mode( $cart_item['id'] ) ) {
			foreach ( $_POST['price_ids'] as $price_id ) {
				$original_price = edd_get_price_option_amount( $cart_item['id'], $price_id );

				// It's only a custom price if the price doesn't match the original.
				if ( isset( $custom_price[ $price_id ] ) && $custom_price[ $price_id ] !== $original_price ) {
					$cart_item['options']['custom_price'][ $price_id ] = $custom_price[ $price_id ];
				}
			}

		} else {
			if ( ( ! is_null( $custom_price ) && "" !== $custom_price ) && ( ( edd_has_variable_prices( $cart_item['id'] ) ) || !edd_has_variable_prices( $cart_item['id'] ) ) ) {
				$cart_item['options']['custom_price'] = edd_sanitize_amount( $custom_price );
			}
		}
	}

	$bonus_item = get_post_meta( $cart_item['id'], 'bonus_item', true );

	if( !empty( $bonus_item['product'] ) && !empty( $custom_price ) ) {
		if( $bonus_item['condition'] == 'equal_to' && $custom_price == $bonus_item['price'] ) {
			edd_add_to_cart( $bonus_item['product'], array( 'price_id' => -1, 'is_cp_bonus' => true, 'cp_bonus_parent' => $cart_item['id'] ) );
		} else if( $bonus_item['condition'] == 'less_than' && $custom_price < $bonus_item['price'] ) {
			edd_add_to_cart( $bonus_item['product'], array( 'price_id' => -1, 'is_cp_bonus' => true, 'cp_bonus_parent' => $cart_item['id'] ) );
		} else if( $bonus_item['condition'] == 'more_than' && $custom_price > $bonus_item['price'] ) {
			edd_add_to_cart( $bonus_item['product'], array( 'price_id' => -1, 'is_cp_bonus' => true, 'cp_bonus_parent' => $cart_item['id'] ) );
		}
	}

	add_filter( 'edd_add_to_cart_item', 'edd_cp_add_to_cart_item' );
	add_filter( 'edd_ajax_pre_cart_item_template', 'edd_cp_add_to_cart_item' );

	return $cart_item;
}
add_filter( 'edd_add_to_cart_item', 'edd_cp_add_to_cart_item' );
add_filter( 'edd_ajax_pre_cart_item_template', 'edd_cp_add_to_cart_item' );

/*
* Update cart options before sending to cart
*/

function edd_cp_pre_add_to_cart( $download_id, $options ) {
	remove_filter( 'edd_pre_add_to_cart', 'edd_cp_pre_add_to_cart', 10, 2 );
	if( !empty( $_POST['post_data'] ) || isset( $_POST['edd_cp_price'] ) ) {
		if( !empty($_POST['post_data'] ) ) {
			$post_data = array();
			parse_str( $_POST['post_data'], $post_data );
			$custom_price = isset( $post_data['edd_cp_price'] ) ? $post_data['edd_cp_price'] : null;
		} else {
			$custom_price = $_POST['edd_cp_price'];
		}
		if( !is_null($custom_price) && ( ( edd_has_variable_prices( $download_id ) && $options['price_id'] == -1 ) || !edd_has_variable_prices( $download_id ) ) ) {
			$options['custom_price'] = edd_sanitize_amount( $custom_price );
		}
	}
	add_filter( 'edd_pre_add_to_cart', 'edd_cp_pre_add_to_cart', 10, 2 );
	return $options;
}
add_filter( 'edd_pre_add_to_cart', 'edd_cp_pre_add_to_cart', 10, 2 );

/*
* Update price if custom price exists and meets criteria
*/

function edd_cp_cart_item_price( $price, $item_id, $options = array() ) {
	if( ( edd_cp_has_custom_pricing( $item_id ) && isset( $options['custom_price'] ) ) || ( isset( $options['is_cp_bonus'] ) && $options['is_cp_bonus'] ) ) {
		if( isset( $options['is_cp_bonus'] ) ) {
			$price = 0;
		} else {
			$min_price = get_post_meta( $item_id, 'edd_cp_min', true );
			$custom_price = $options['custom_price'];


			if ( edd_has_variable_prices( $item_id ) && edd_single_price_option_mode( $item_id ) ) {
				$price_id = isset( $options['price_id'] ) ? $options['price_id'] : false;

				if ( $price_id && isset( $custom_price[ $price_id ] ) ) {
					$variable_price = $custom_price[ $price_id ];

					if ( ( $min_price != 0 && ( $variable_price >= $min_price ) ) ||
						( $min_price == 0 && is_numeric( $variable_price ) ) ) {
							$price = $variable_price;
					}
				}
			} else {

				if( $min_price != 0 && ( $custom_price >= $min_price ) ) {
					$price = $options['custom_price'];
				} else if( $min_price == 0 && is_numeric( $options['custom_price'] ) ) {
					$price = $options['custom_price'];
				}
			}
		}
	}
	return $price;
}
add_filter( 'edd_cart_item_price', 'edd_cp_cart_item_price', 10, 3 );

/*
* Filter option text on product item
*/

function edd_cp_get_price_name( $return, $item_id, $options ) {
	if( isset( $options['is_cp_bonus'] ) ) {
		return __( ' *bonus item*', 'edd_cp' );
	} else if( edd_cp_has_custom_pricing( $item_id ) && isset( $options['custom_price'] ) ) {
		if ( edd_has_variable_prices( $item_id ) ) {
			return __( 'custom price' , 'edd_cp' );
		} else {
			return __( ' - custom price' , 'edd_cp' );
		}
	}
	return $return;
}
add_filter( 'edd_get_price_name', 'edd_cp_get_price_name', 10, 3 );

/*
* Filter cart item price name (similar to above)
*/
function edd_cp_get_cart_item_price_name( $name, $item_id, $price_id, $item ) {
	if( isset( $item['options']['is_cp_bonus'] ) ) {
		return __( ' *bonus item*', 'edd_cp' );
	} else if( edd_cp_has_custom_pricing( $item_id ) && isset( $item['options']['custom_price'] ) ) {

		if ( edd_single_price_option_mode( $item_id ) && isset( $item['options']['custom_price'][ $price_id ] ) ) {
			return $name . __( ' (custom price)', 'edd_cp' );
		} elseif ( ! edd_single_price_option_mode( $item_id ) ) {
			return $name . __( ' (custom price)', 'edd_cp' );
		}

	}
	return $name;
}
add_filter( 'edd_get_cart_item_price_name', 'edd_cp_get_cart_item_price_name', 10, 4 );

/*
* Filter price option name
*/
function edd_cp_get_price_option_name( $price_name, $download_id, $payment_id = 0, $price_id ) {
	if( $payment_id ) {
		$cart_items =  edd_get_payment_meta_cart_details( $payment_id );
		$prices = edd_get_variable_prices( $download_id );

		if ( $prices && is_array( $prices ) ) {
			if ( isset( $prices[ $price_id ] ) ) {
				return $price_name;
			}
		}

		if( $cart_items ) {
			foreach( $cart_items as $key => $item ) {
				$item_id = $item['item_number']['id'];
				if( $item_id == $download_id ) {
					$price_options = $item['item_number']['options'];
					if ( isset( $price_options['custom_price'] ) && edd_cp_has_custom_pricing( $item_id ) ) {
						$price_name = __( 'Custom price', 'edd_cp' );
					} else if( isset( $price_options['is_cp_bonus'] ) ) {
						$price_name = __( '*Bonus item*', 'edd_cp' );
					}

					// Fallback for buy now items with custom price.
					if ( 'direct' == edd_get_download_button_behavior( $download_id ) && edd_cp_has_custom_pricing( $item_id ) ) {
						$price_name = __( 'Custom price', 'edd_cp' );
					}
				}
			}
		}
	}

	return $price_name;
}
add_filter( 'edd_get_price_option_name', 'edd_cp_get_price_option_name', 10, 4 );

/*
* Filter the purchase data before sending it to the direct gateway
*/
function edd_cp_straight_to_gateway_purchase_data( $purchase_data ) {
	$min_price = get_post_meta( $_POST['download_id'], 'edd_cp_min', true );

	if( isset( $_POST['edd_cp_price'] ) && $_POST['edd_cp_price'] >= $min_price ) {
		$custom_price = edd_sanitize_amount( $_POST['edd_cp_price'] );
		foreach( $purchase_data['downloads'] as $d_key => $downloads ) {
			foreach( $downloads['options'] as $o_key => $options ) {
				if ( is_array( $purchase_data['downloads'][$d_key]['options'][$o_key] ) ) {
					$purchase_data['downloads'][$d_key]['options'][$o_key]['amount'] = $custom_price;
				}

				if ( is_array( $purchase_data['cart_details'][0]['item_number']['options'][$o_key] ) ) {
					$purchase_data['cart_details'][0]['item_number']['options'][$o_key]['amount'] = $custom_price;
				}
			}
		}
		$purchase_data['cart_details'][0]['item_price'] = $custom_price;
		$purchase_data['cart_details'][0]['subtotal'] = $custom_price;
		$purchase_data['cart_details'][0]['name'] = $purchase_data['cart_details'][0]['name'] . ' - custom price';
		$purchase_data['subtotal'] = $custom_price;
		$purchase_data['price'] = $custom_price;
	}
	return $purchase_data;
}
add_filter( 'edd_straight_to_gateway_purchase_data', 'edd_cp_straight_to_gateway_purchase_data' );

/*
* Check if a custom priced product is removed, and remove the associated bonus item (if it exists)
*/

function edd_cp_post_remove_from_cart( $cart_key, $item_id ) {

	if(!edd_cp_has_custom_pricing( $item_id ) )
		return;

	$cart = edd_get_cart_contents();

	if( !empty($cart ) ) {
		// Find the bonus item
		foreach( $cart as $key => $item ) {
			if( !empty( $item['options']['is_cp_bonus'] ) && $item['options']['cp_bonus_parent'] == $item_id ) {
				edd_remove_from_cart( $key );
			}
		}
	}
}
add_filter( 'edd_post_remove_from_cart', 'edd_cp_post_remove_from_cart', 10, 2 );

/*
* Filter the purchase link defaults to allow custom button text if price is zero
*/

function edd_cp_edd_purchase_link_args( $args ) {

	if( !edd_cp_has_custom_pricing( $args['download_id'] ) )
		return $args;

	$button_text = get_post_meta( $args['download_id'], 'cp_button_text', true );
	$price = edd_get_download_price( $args['download_id'] );

	if( !empty( $button_text ) ) {
		$args['price'] = false; // Prevents 'Free' from being added
		$args['text'] = $button_text;
	}
	return $args;
}
add_filter( 'edd_purchase_link_args', 'edd_cp_edd_purchase_link_args' );

/*
* Set is_free to false if the product is buy now.
*/
function edd_cp_is_free_download( $is_free, $download_id ) {
	if ( edd_cp_has_custom_pricing( $download_id ) && 'direct' == edd_get_download_button_behavior( $download_id ) ) {
		$is_free = false;
	}

	return $is_free;
}
add_filter( 'edd_is_free_download', 'edd_cp_is_free_download', 10, 2 );

/*
* Add custom price to variable prices array on gateway page.
* This fixes an issue when the price_id doesn't exist. The actual custom price is set later on edd_straight_to_gateway_purchase_data().
*/
function edd_cp_get_variable_prices( $prices, $download_id ) {
	if ( edd_cp_has_custom_pricing( $download_id ) && doing_action('edd_straight_to_gateway') ) {
		$prices[-1] = array( 'index' => -1, 'option' => 'Custom Price', 'name' => 'Custom Price', 'amount' => 0 );
	}
	return $prices;
}
add_filter( 'edd_get_variable_prices', 'edd_cp_get_variable_prices', 10, 2 );
