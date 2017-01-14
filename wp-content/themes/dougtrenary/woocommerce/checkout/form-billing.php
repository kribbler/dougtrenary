<?php
/**
 * Checkout billing information form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;
$user_level_2 = d_check_if_cart_for_user_level_2();
//pr($user_level_2);
?>

<?php if ( $woocommerce->cart->ship_to_billing_address_only() && $woocommerce->cart->needs_shipping() ) : ?>

	<h3><?php _e( 'Billing &amp; Shipping', 'woocommerce' ); ?></h3>

<?php else : ?>

	<h3><?php _e( 'Billing Address', 'woocommerce' ); ?></h3>

<?php endif; ?>

<?php do_action('woocommerce_before_checkout_billing_form', $checkout ); ?>

<?php foreach ($checkout->checkout_fields['billing'] as $key => $field) : ?>

	<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>

<?php endforeach; ?>

<?php do_action('woocommerce_after_checkout_billing_form', $checkout ); ?>

<?php if ( ! is_user_logged_in() && $checkout->enable_signup && $user_level_2 ) : ?>

	<?php if ( $checkout->enable_guest_checkout) : ?>

		<p class="form-row form-row-wide">
			<input class="input-checkbox" id="createaccount" <?php checked($checkout->get_value('createaccount'), true) ?> type="checkbox" name="createaccount" value="1" /> <label for="createaccount" class="checkbox"><?php _e( 'Create an account?', 'woocommerce' ); ?></label>
		</p>

	<?php endif; ?>

	<?php do_action( 'woocommerce_before_checkout_registration_form', $checkout ); ?>

	<div class="create-account">
		<p><?php _e( 'If you are purchasing THE DIGITAL SALESMIND, <b><i>YOU MUST</i></b> create an account in order to access your video streaming content. For all other purchass, you may elect to create an account by entering the information below. If you are a returning customer please login at the top of the page.', 'dougtrenary' ); ?></p>
		<p><?php //_e( 'Create an account by entering the information below. If you are a returning customer please login at the top of the page.', 'woocommerce' ); ?></p>
		<?php
		//pr($checkout->checkout_fields);
		?>
		<?php foreach ($checkout->checkout_fields['account'] as $key => $field) : ?>

			<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>

		<?php endforeach; ?>

		<div class="clear"></div>

	</div>
	<?php
	if ($user_level_2['products_number']){?>
		<h4>Extra accounts</h4>
		<?php for ($i = 1; $i < $user_level_2['products_number']; $i++){?>
			<h6 style="margin-bottom:5px; margin-top: 15px;"><?php echo $i; ?>).</h6>
<?php
	woocommerce_form_field( 'extra_firstname_' . $i , 
		array(
	        'type'          => 'text',
	        'class'         => array('form-row form-row-first validate-required'), 
	        'label'         =>  wpml_string_wccm('First Name ' . $i),
	        'required'  => true,
	    ), 
	    $checkout->get_value( ''.$btn['cow'].'' )
	); 

	woocommerce_form_field( 'extra_lastname_' . $i , 
		array(
	        'type'          => 'text',
	        'class'         => array('form-row form-row-last'), 
	        'label'         =>  wpml_string_wccm('Last Name ' . $i),
	        'required'  => true,
	    ), 
	    $checkout->get_value( ''.$btn['cow'].'' )
	);

	woocommerce_form_field( 'extra_email_' . $i , 
		array(
	        'type'          => 'text',
	        'class'         => array('form-row'), 
	        'label'         =>  wpml_string_wccm('Email ' . $i),
	        'required'  => true,
	    ), 
	    $checkout->get_value( ''.$btn['cow'].'' )
	);
?>
<!--
			<p class="form-row form-row-first validate-required">
				<label for="x_firstname_<?php echo $i;?>">First Name</label>
				<input type="text" name="x_firstname_<?php echo $i;?>" />
			</p>
			<p class="form-row form-row-last">
				<label>Last Name</label>
				<input type="text" name="x_lastname_<?php echo $i;?>" />
			</p>
			<p class="form-row">
				<label>Email</label>
				<input type="text" name="x_email_<?php echo $i;?>" class="input-text" />
			</p>
-->
		<?php }
	}
	?>
	<?php do_action( 'woocommerce_after_checkout_registration_form', $checkout ); ?>

<?php endif; ?>

<script type="text/javascript">
jQuery(document).ready(function($){
	<?php if ($user_level_2){?>
		//console.log('aaa');
	$('#createaccount').prop('checked', true);
	<?php }?>
});
</script>