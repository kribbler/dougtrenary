<?php
/**
 * The template for displaying the footer.
 *
 * Contains footer content and the closing of the
 * #main and #page div elements.
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */
?>
<div style="clear: both;"></div>
		</div><!-- #main -->
		<footer id="colophon" class="site-footer" role="contentinfo">
			<?php get_sidebar( 'main' ); ?>

			<div class="site-info">
				<!-- &copy; Copyright 2013. Doug Trenary's Fastrack, Inc All Rights Reserved     (404) 262-3339-->
				<?php echo get_post_content_by_slug( 'footer-text' ); ?>
			</div><!-- .site-info -->
		</footer><!-- #colophon -->
	</div><!-- #page -->

<script type="text/javascript">
function log_user_video(video, state) {
	var user_id = <?php echo get_current_user_id(); ?>;
console.log('here with ' + user_id);
	if (user_id) {
		jQuery.post(
			ajaxurl, { 
				action: 'increment_user_video', 
				user_id: user_id,
				video_id: video,
				state: state
			}, 
			function(output) {
				//alert(output);
			}
		);
	}
}

jQuery(".column_50 a").click(function($) {
	var user_id = <?php echo get_current_user_id(); ?>;
	if (user_id) {
		//console.log(jQuery(this).html()); return false;
		jQuery.post(
			ajaxurl, { 
				action: 'increment_user_file', 
				user_id: user_id,
				file_id: jQuery(this).attr('href'),
				file_name: jQuery(this).html()
			}, 
			function(output) {
				//alert(output);
			}
		);
	}
	
	return true;
});
</script>
	<?php wp_footer(); ?>
</body>
</html>