<?php
/**
 * The sidebar containing the secondary widget area, displays on posts and pages.
 *
 * If no active widgets in this sidebar, it will be hidden completely.
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */

if ( is_active_sidebar( 'sidebar-2' ) || 1==1) : ?>
	<div id="tertiary" class="sidebar-container" role="complementary">
		<div class="sidebar-inner">
			<div class="widget-area">
				<div id="ads">
					<?php /*
					<div id="portlet1">
						<?php echo get_post_content_by_slug( 'save-44-95' ); ?>
					</div>
					
					<div id="portlet2">
						<?php echo get_post_content_by_slug( 'get-the-products-now' ); ?>
					</div>
					*/?>
					<?php if ( is_active_sidebar( 'sidebar-top' ) ) :?>
						<div id="portlet1">
							<?php dynamic_sidebar( 'sidebar-top' ); ?>
						</div>
		 			<?php endif; ?>
					
					<?php if ( is_active_sidebar( 'sidebar-bottom' ) ) :?>
						<div id="portlet2">
							<?php dynamic_sidebar( 'sidebar-bottom' ); ?>
						</div>
		 			<?php endif; ?>
		 			
		 			<?php if (can_view()){?>
			 			<?php if ( is_active_sidebar( 'sidebar-subscriber-resources' ) ) :?>
							<div id="portlet2">
								<?php dynamic_sidebar( 'sidebar-subscriber-resources' ); ?>
							</div>
			 			<?php endif; ?>
			 		<?php }?>
			 		
					<?php if ( is_active_sidebar( 'sidebar-categories' ) ) :?>
						<div id="portlet2">
							<?php dynamic_sidebar( 'sidebar-categories' ); ?>
						</div>
		 			<?php endif; ?>
		 			
		 			<?php if ( is_active_sidebar( 'sidebar-woocommerce-cart' ) ) :?>
						<div id="portlet4">
							<?php dynamic_sidebar( 'sidebar-woocommerce-cart' ); ?>
						</div>
		 			<?php endif; ?>
		 			
		 			
				</div>
				<?php //dynamic_sidebar( 'sidebar-2' ); ?>
			</div><!-- .widget-area -->
		</div><!-- .sidebar-inner -->
	</div><!-- #tertiary -->
<?php endif; ?>