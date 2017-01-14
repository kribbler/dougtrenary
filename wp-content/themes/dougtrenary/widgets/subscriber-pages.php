<?php
class Subscriber_Pages extends WP_Widget {
	
	function Subscriber_Pages() {
		parent::WP_Widget(false, $name = 'Subscriber Pages');
	}
	
	function widget($args, $instance) {	
		extract( $args );
		$title = apply_filters('widget_title', empty( $instance['title'] ) ? __( 'Cart', 'woocommerce' ) : $instance['title'], $instance, $this->id_base );
		
		echo $before_widget;
		
		if ( $title )
			echo $before_title . $title . $after_title;

		$this->get_hidden_pages();
		
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
	
	function get_hidden_pages(){
		$current_user = wp_get_current_user();
		if ($current_user){
			$user_id = $current_user->data->ID;
			//echo "<pre style='text-align:left'>";var_dump(is_admin());var_dump();die();
			$user_meta = get_user_meta($user_id);
			
			if ($user_meta['user_level'][0] || $current_user->roles[0] == 'administrator'){
				$args = array(
					'menu' => 'Subscriber Menu',
					
				);
				
				wp_nav_menu( $args );
				//var_dump($postslist);
			}
		}			
	}
}