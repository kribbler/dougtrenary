<?php
/**
 * Email Order Items
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates/Emails
 * @version     2.0.3
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;

foreach ( $items as $item ) :

	// Get/prep product data
	$_product = $order->get_product_from_item( $item );
	$item_meta = new WC_Order_Item_Meta( $item['item_meta'] );

	// Handle products that was removed from store
	if ( ! is_object( $_product ) ) :
		?>
		<tr>
			<td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;">
			<?php

				// Show title/image etc
				echo 	apply_filters( 'woocommerce_order_product_image', '', null, $show_image);

				// Product name
				echo 	apply_filters( 'woocommerce_order_product_title', $item['name'], $_product );

				// Product not available anymore message
				echo '<br/><small>(' . __( 'This product is no longer available', 'woocommerce' ) . ')</small>';

				// Variation
				echo 	($item_meta->meta) ? '<br/><small>' . nl2br( $item_meta->display( true, true ) ) . '</small>' : '';

			?>
			</td>
			<td style="text-align:left; vertical-align:middle; border: 1px solid #eee;"><?php echo $item['qty'] ;?></td>
			<td style="text-align:left; vertical-align:middle; border: 1px solid #eee;"><?php echo $order->get_formatted_line_subtotal( $item ); ?></td>
		</tr>
		<?php
	else :
		$attachment_image_src = wp_get_attachment_image_src( get_post_thumbnail_id( $_product->id ), 'thumbnail' );
		$image = ( $show_image ) ? '<img src="' . current( $attachment_image_src ) . '" alt="Product Image" height="' . $image_size[1] . '" width="' . $image_size[0] . '" style="vertical-align:middle; margin-right: 10px;" />' : '';

		?>
		<tr>
			<td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;"><?php

				// Show title/image etc
				echo 	apply_filters( 'woocommerce_order_product_image', $image, $_product, $show_image);

				// Product name
				echo 	apply_filters( 'woocommerce_order_product_title', $item['name'], $_product );


				// SKU
				echo 	($show_sku && $_product->get_sku()) ? ' (#' . $_product->get_sku() . ')' : '';

				// File URLs
				if ( $show_download_links && $_product->exists() && $_product->is_downloadable() ) {

					$download_file_urls = $order->get_downloadable_file_urls( $item['product_id'], $item['variation_id'], $item );

					$i = 0;

					foreach ( $download_file_urls as $file_url => $download_file_url ) {
						echo '<br/><small>';

						$filename = woocommerce_get_filename_from_url( $file_url );

						if ( count( $download_file_urls ) > 1 ) {
							echo sprintf( __('Download %d:', 'woocommerce' ), $i + 1 );
						} elseif ( $i == 0 )
							echo __( 'Download:', 'woocommerce' );

						echo ' <a href="' . $download_file_url . '" target="_blank">' . $filename . '</a></small>';

						$i++;
					}
					
					echo '<p>***NOTE: You have up to 2 chances to download your digital product(s). These download links will expire in 7 days. If you purchased The DIGITAL SalesMind, your product contains THREE separate download links above. Please allow up to 1-2 hours to download all digital content depending on your download speed. </p>';
					echo '<p>Follow the link provided to <a href="http://dougtrenary.com/login/">LOGIN</a> to the DTU Members area to view your SalesMind video content***</p>';
					
					//echo '<p>***NOTE: You have up to 2 chances to download your digital product(s). These download links will expire in 7 days. If you purchased The DIGITAL SalesMind, your product contains THREE separate download links above. Please allow up to 1-2 hours to download all digital content depending on your download speed.  Follow the link provided to <a href="http://dougtrenary.com/login/">LOGIN</a> to the DTU Members area to view your SalesMind video content***</p>';
					//echo '<p>If your order includes an eReader product, PDF fomat should work for most eReader devices. MOBI format will work for Kindle 1 and Azbooka WISEreader. If you need a different format, please contact us at info@dougtrenary.com. </p>';
					//echo '<p>For instructions on how to add your eBook to Apple and Android devices, please read our instructions <a href="http://www.dougtrenary.com/how-to-add-ebook-to-mobile-devices/">HERE</a>:</p>';
					
					echo '<p>Thank you for your purchase! </p>';
					echo '<p>Doug Trenary</p>';
				}

				// Variation
				echo 	($item_meta->meta) ? '<br/><small>' . nl2br( $item_meta->display( true, true ) ) . '</small>' : '';

			?></td>
			<td style="text-align:left; vertical-align:middle; border: 1px solid #eee;"><?php echo $item['qty'] ;?></td>
			<td style="text-align:left; vertical-align:middle; border: 1px solid #eee;"><?php echo $order->get_formatted_line_subtotal( $item ); ?></td>
		</tr>

		<?php if ($show_purchase_note && $purchase_note = get_post_meta( $_product->id, '_purchase_note', true)) : ?>
			<tr>
				<td colspan="3" style="text-align:left; vertical-align:middle; border: 1px solid #eee;"><?php echo apply_filters('the_content', $purchase_note); ?></td>
			</tr>
		<?php endif; ?>
	<?php endif; ?>
<?php endforeach; ?>