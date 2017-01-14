<?php
/**
 * The template for displaying all single posts
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */

get_header(); ?>

<?php
/*
$can_view = true;

$post_custom = get_post_custom($post->ID);
if (isset($post_custom['User level'])){

	$current_user = wp_get_current_user();
	$user_id = $current_user->data->ID;
	$user_meta = get_user_meta($user_id);
	var_dump($post_custom['User level'][0]);
	var_dump($user_meta['user_level'][0]);
	if ((int)$user_meta['user_level'][0] >= (int)$post_custom['User level'] ){
		$can_view = true;
	} else {
		$can_view = false;
	}
}
*/

if (can_view()){
?>
</pre>
	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">

			<?php /* The loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'content', get_post_format() ); ?>
				<?php twentythirteen_post_nav(); ?>
				<?php comments_template(); ?>

			<?php endwhile; ?>

		</div><!-- #content -->
	</div><!-- #primary -->
<?php } else {?>
	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
			This page is restricted!
		</div><!-- #content -->
	</div><!-- #primary -->
<?php }?>
<?php get_sidebar(); ?>
<?php get_footer(); ?>