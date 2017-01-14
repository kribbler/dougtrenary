<?php
class Subscriber_Account_Links extends WP_Widget {
	
	function Subscriber_Account_Links() {
		parent::WP_Widget(false, $name = 'Subscriber Account Links');
	}
	
	function widget($args, $instance) {	
		extract( $args );
		$title = apply_filters('widget_title', empty( $instance['title'] ) ? __( 'Cart', 'woocommerce' ) : $instance['title'], $instance, $this->id_base );
		
		echo $before_widget;
		
		if ( $title )
			echo $before_title . $title . $after_title;
			
		wp_nav_menu( array('menu' => 'My Account' ));
		echo $after_widget;
	}
	
	function update( $new_instance, $old_instance ) {
		$instance['title'] = strip_tags( stripslashes( $new_instance['title'] ) );
		return $instance;
	}
	
	function form( $instance ) {
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', 'woocommerce' ) ?></label>
		<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" value="<?php if (isset ( $instance['title'])) {echo esc_attr( $instance['title'] );} ?>" /></p>
		<?php
	}
}