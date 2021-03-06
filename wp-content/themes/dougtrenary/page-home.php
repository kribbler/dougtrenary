<?php
/*
Template Name: Home Page
*/

get_header(); ?>
<style type="text/css">
	#main{min-height: 735px !important;}
</style>
	<div id="primary" class="content-area home">
		<div id="content" class="site-content" role="main">
			
			<div id="portlet" class="portlet1_home">
				<?php //echo get_post_content_by_slug( 'its-all-about-the-results' ); ?>
				<?php if( function_exists('cyclone_slider') ) cyclone_slider('slideshow-homepage'); ?>
			</div>
			<?php /*
			<div id="portlet" class="portlet2_home">
				<?php echo get_post_content_by_slug( 'save-44-95' ); ?>
			</div>
			*/?>
			<?php if ( is_active_sidebar( 'home_widget_sidebar' ) ) :?>
				<div id="portlet" class="portlet2_home">
					<?php dynamic_sidebar( 'home_widget_sidebar' ); ?>
				</div>
 			<?php endif; ?>
			
			<div id="portlet" class="portlet3_home">
				<?php echo get_post_content_by_slug( 'watch-doug-live' ); ?>
			</div>
			
			<div id="portlet" class="portlet4_home">
				<?php echo get_post_content_by_slug( 'see-what-people-are-saying-about-dougs-programs' ); ?>
			</div>
			
			<div id="portlet" class="portlet5_home">
				<?php echo get_post_content_by_slug( 'get-the-products-now-2' ); ?>
			</div>
			
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

					<footer class="entry-meta">
						<?php edit_post_link( __( 'Edit', 'twentythirteen' ), '<span class="edit-link">', '</span>' ); ?>
					</footer><!-- .entry-meta -->
				</article><!-- #post -->

				<?php comments_template(); ?>
			<?php endwhile; ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php //get_sidebar(); ?>
<?php get_footer(); ?>