<?php
/*
Template Name: Membership Page
 */

get_header(); ?>
<style type="text/css">
.mejs-time-rail{
	width: 60% !important;
}
</style>
<?php if (can_view()){?>
	<div id="primary" class="content-area d_pages">
		<div id="content" class="site-content" role="main">

			<?php /* The loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<header class="entry-header">
						<?php if ( has_post_thumbnail() && ! post_password_required() ) : ?>
						<div class="entry-thumbnail">
							<?php the_post_thumbnail(); ?>
						</div>
						<?php endif; ?>

						<h1 class="entry-title"><?php the_title(); ?></h1>
					</header><!-- .entry-header -->

					<div class="entry-content">
						<?php the_content(); ?>
						<?php wp_link_pages( array( 'before' => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'twentythirteen' ) . '</span>', 'after' => '</div>', 'link_before' => '<span>', 'link_after' => '</span>' ) ); ?>
					</div><!-- .entry-content -->

<?php 
$current_user = wp_get_current_user();
		if ($current_user){
			$user_id = $current_user->data->ID;
			//echo "<pre style='text-align:left'>";var_dump(is_admin());var_dump();die();
			$user_meta = get_user_meta($user_id);
			
			if ($user_meta['user_level'][0] || $current_user->roles[0] == 'administrator'){
				$args = array(
					'menu' => 'Subscriber Menu',
					
				);
				
				//wp_nav_menu( $args );
				//var_dump($postslist);
			}
		}			
?>

<?php if ($post->post_parent){?>
<script>
function goBack()
  {
  window.history.back()
  }
</script>
<br />
<br />
<button onclick="goBack()">Go Back</button>
<?php }?>
					<footer class="entry-meta">
						<?php edit_post_link( __( 'Edit', 'twentythirteen' ), '<span class="edit-link">', '</span>' ); ?>
					</footer><!-- .entry-meta -->
				</article><!-- #post -->

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
<div style="clear:both;"></div>
<?php get_footer(); ?>