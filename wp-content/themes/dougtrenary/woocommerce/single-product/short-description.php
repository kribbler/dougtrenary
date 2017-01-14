<?php
/**
 * Single product short description
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post;
global $product;
//echo $product->post->post_content;
//if ( ! $post->post_excerpt ) return;
?>
<div itemprop="description">
    <?php echo apply_filters( 'woocommerce_short_description', $product->post->post_content ) ?>
<?php echo apply_filters( 'woocommerce_short_description', $post->post_excerpt ) ?>
</div>