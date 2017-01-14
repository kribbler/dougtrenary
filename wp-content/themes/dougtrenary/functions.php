<?php

function dougtrenary_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Header Widget Area', 'dougtrenary' ),
		'id'            => 'sidebar-3',
		'description'   => __( 'Appears in the header section of the site.', 'dougtrenary' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
	
	register_sidebar( array(
		'name'          => __( 'Home Sidebar', 'dougtrenary' ),
		'id'            => 'home_widget_sidebar',
		'description'   => __( 'Appears in the home page sidebar.', 'dougtrenary' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
	
	register_sidebar( array(
		'name'          => __( 'Sidebar Top', 'dougtrenary' ),
		'id'            => 'sidebar-top',
		'description'   => __( 'Appears in the sidebar.', 'dougtrenary' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
	
	register_sidebar( array(
		'name'          => __( 'Sidebar Bottom', 'dougtrenary' ),
		'id'            => 'sidebar-bottom',
		'description'   => __( 'Appears in the sidebar.', 'dougtrenary' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
	
	register_sidebar( array(
		'name'			=> 'Subscriber Resources',
		'id'			=> 'sidebar-subscriber-resources',
		'description'	=> 'Sidebar Subscriber Resources',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
	
	register_sidebar( array(
		'name'          => __( 'Sidebar Categories', 'dougtrenary' ),
		'id'            => 'sidebar-categories',
		'description'   => __( 'Woocommerce Categories - Appears in the sidebar.', 'dougtrenary' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
	
	register_sidebar( array(
		'name'          => __( 'Sidebar WooCommerce Cart', 'dougtrenary' ),
		'id'            => 'sidebar-woocommerce-cart',
		'description'   => __( 'Woocommerce Cart - Appears in the sidebar.', 'dougtrenary' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
	
	register_sidebar( array(
		'name'          => __( 'Future DTU Text', 'dougtrenary' ),
		'id'            => 'future-dtu-text',
		'description'   => __( 'Future DTU Text', 'dougtrenary' ),
		'before_widget' => '',
		'after_widget'  => '',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
	
	
	
	include_once( 'widgets/subscriber-pages.php' );
	register_widget( 'Subscriber_Pages' );
	
	include_once( 'widgets/class-wc-widget-cart.php' );
	register_widget('Custom_WooCommerce_Widget_Cart');
	
	include_once( 'widgets/subscriber-account.php' );
	register_widget( 'Subscriber_Account_Links' );
	

}
add_action( 'widgets_init', 'dougtrenary_widgets_init' );

function get_post_content_by_slug($slug){
	$post = get_page_by_path( $slug, OBJECT, 'post' );
	$postcontent = apply_filters('the_content', $post->post_content); 
	return $postcontent;
}

/**
 * woocommerce
 */

//woocommerce_download_product

add_filter( 'woocommerce_download_product' , 'custom_woocommerce_download_product' );
function custom_woocommerce_download_product($content) {
	save_file_download_info($content);
	return $content;
}

function save_file_download_info($content) {
	//pr($_GET);
	//pr($content); //die();
	global $wpdb;

	$current_user = wp_get_current_user();
	//pr($current_user); die();
	$wpdb->insert(
		$wpdb->prefix . 'user_files',
		array(
			'user_id' => $current_user->data->ID,
			'file_id' => $_GET['key'],
			'date' => date('Y-m-d h:i:s')
		)
	);

}

add_action( 'wp_enqueue_scripts', 'remove_gridlist_styles', 30 );
function remove_gridlist_styles() {
	wp_dequeue_style( 'grid-list-button' );
}

function can_view(){
	$can_view = false;
	$current_user = wp_get_current_user();
	$user_id = $current_user->data->ID;
	if ($user_id)
		$can_view = true;
	
	$post_custom = get_post_custom($post->ID);
	if (isset($post_custom['User level'])){
	
		$current_user = wp_get_current_user();
		//pr($current_user);
		$user_id = $current_user->data->ID;
		$user_meta = get_user_meta($user_id);
		//var_dump($post_custom['User level'][0]);
		//var_dump($user_meta['user_level'][0]);
		if ((int)$user_meta['user_level'][0] >= (int)$post_custom['User level'] || is_admin() ||
			$current_user->roles[0] == 'administrator' ){
			$can_view = true;
		} else {
			$can_view = false;
		}
	}
	return $can_view;
}

add_action( 'edit_user_profile', 'my_show_user_level_manager_group_fields' );

function my_show_user_level_manager_group_fields( $user ) { 
	global $wpdb;

	$user_meta = get_user_meta($user->ID);
	$user_level = $user_meta['user_level'][0];
	$group_name = $user_meta['group_name'][0];
	$user_manager = $user_meta['manager'][0];
	?>
	<h3>User Level</h3>

	<table class="form-table">
		<tr>
			<th><label for="twitter">User Level</label></th>
			<td>
				<input type="text" name="user_level" id="user_level" value="<?php echo $user_level; ?>" class="regular-text" /><br />
				<span class="description">Please enter the user level - default is 1.</span>
			</td>
		</tr>

		<tr>
			<th><label for="twitter">User Group</label></th>
			<td>
				<input type="text" name="group_name" id="group_name" value="<?php echo $group_name; ?>" class="regular-text" /><br />
				<span class="description">User belongs to specified group name</span>
			</td>
		</tr>

		<tr>
			<th><label for="gm">Is Group's Manager</label></th>
			<td>
				<?php
				//var_dump($user_manager);
				//$checked = ""; 
				//var_dump(checked(1, $user_manager, true));
				?>
				<input type="checkbox" name="manager" <?php checked(1, $user_manager, true); ?>/>

			</td>
		</tr>

		<tr>
			<th><label for="twitter">Group's Manager</label></th>
			<td>
				<?php 
				$query = "SELECT * FROM " . $wpdb->prefix . "usermeta WHERE meta_key='group_name' AND meta_value='".$group_name."'";
				$results = $wpdb->get_results( $query, ARRAY_A );
				//pr($results);
				foreach ($results as $result) {
					$um = get_user_meta($result['user_id'], 'manager', true);
					if ($um) {
						$manager_id = $result['user_id'];
						$manager_name = get_user_meta($manager_id, 'first_name', true) . ' ' . 
							get_user_meta($manager_id, 'last_name', true);
						break;
					}
				}
				if ($manager_id) {
					echo '<span class="description">' . $manager_name . '</span>';
				}
				?>
				
			</td>
		</tr>

	</table>
<?php }

add_action( 'edit_user_profile_update', 'my_save_user_level_field' );

function my_save_user_level_field($user_id){
	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;

	if ($_POST['manager'] && $_POST['manager'] == 'on') 
		$_POST['manager'] = 1;
	else 
		$_POST['manager'] = 0;
	update_user_meta( $user_id, 'user_level', $_POST['user_level'] );
	update_user_meta( $user_id, 'manager', $_POST['manager'] );
	update_user_meta( $user_id, 'group_name', $_POST['group_name'] );
	//pr($_POST); die();
}

function d_check_if_cart_for_user_level_2(){
	global $woocommerce;
	$is_user_level_2 = false;
	
	foreach ($woocommerce->cart->cart_contents as $product){
		//pr($product);
		$pm = get_post_meta($product['product_id']);
		//pr($pm['User Level'][0]);
		if ($pm['User Level'][0])
			return array('user_level' => 2, 'products_number' => $product['quantity']);
	}
	
	return false;
}

function pr($s){
	echo "<pre>";var_dump($s);echo "</pre>";
}

function d_check_if_order_for_user_level_2($order){
	global $woocommerce;
	$is_user_level_2 = false;
	
	$items = $order->get_items();
	foreach ($items as $product){
		if (get_post_meta($product['product_id'], 'User level', true) == 2){
			$is_user_level_2 = true;
		}
	}
	
	return $is_user_level_2;
}

add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );
function custom_override_checkout_fields( $fields ) {
   // echo "<pre>";var_dump($fields);echo "</pre>";
    $fields['billing']['billing_city']['label'] = 'City';
    $fields['billing']['billing_city']['placeholder'] = 'City';
    $fields['shipping']['shipping_city']['label'] = 'City';
    $fields['shipping']['shipping_city']['placeholder'] = 'City';
     return $fields;
}

add_action('woocommerce_created_customer', 'admin_email_on_registration');
function admin_email_on_registration() {
    $user_id = get_current_user_id();
    wp_new_user_notification( $user_id );
}

function admin_default_page() {
  return 'membership';
}

add_filter('login_redirect', 'admin_default_page');
/*
add_action('woocommerce_after_checkout_billing_form', 'daniel_custom_checkout_field');

function daniel_custom_checkout_field($checkout){
	$user_level_2 = d_check_if_cart_for_user_level_2();
	woocommerce_form_field( 'xx' , array(
        'type'          => 'text',
        'class'         => array('wccs-field-class wccs-form-row-wide'), 
        'label'         =>  wpml_string_wccm('labelxx'),
        'required'  => true,
        'placeholder'       => wpml_string_wccm('myplace'),
        ), $checkout->get_value( ''.$btn['cow'].'' )); 
}

function daniel_custom_checkout_field_process() {
	//echo 'daniel';var_dump($_POST);die();
	//$woocommerce->add_error( '<strong>'.$btn['label'].'</strong> '. __('is a required field', 'woocommerce-checkout-manager' ) . ' ');
}
add_action('woocommerce_checkout_process', 'daniel_custom_checkout_field_process');

*/

/** DANIELS tweaked code **/
function get_good_label($key){
  $key_array = explode("_", $key);
  return ucfirst($key_array[1]) . ' ' . $key_array[2];
}

/**
* Update the order meta with field value
*/
add_action( 'woocommerce_checkout_update_order_meta', 'my_custom_checkout_field_update_order_meta' );
 
function my_custom_checkout_field_update_order_meta( $order_id ) {
	foreach ($_POST as $key => $value){
		if (stristr($key, "extra_firstname_") || stristr($key, "extra_lastname_") || stristr($key, "extra_email_")){
			update_post_meta( $order_id, $key, sanitize_text_field( $value ) );
		}
	}
	
	$new_users = array(); $k = 0;
	foreach ($_POST as $key => $value){
		if (stristr($key, "extra_firstname_")){
			$new_users[$k]['first_name'] = $value;
		} else if (stristr($key, "extra_lastname_")){
			$new_users[$k]['last_name'] = $value;
		} else if (stristr($key, "extra_email_")){
			$new_users[$k]['email'] = $value;
			$k++;
		}
	}

	//generate extra users::
	foreach ($new_users as $new_user) {
		$random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
		//wp_create_user( $new_user['email'], $random_password, $new_user['email'] );
		$userdata = array(
			'user_login'	=> $new_user['email'],
			'user_pass'		=> $random_password,
			'user_email'	=> $new_user['email'],
			'first_name'	=> $new_user['first_name'],
			'last_name'		=> $new_user['last_name'],
			'display_name'	=> $new_user['first_name'] . ' ' . $new_user['last_name'],
			'user_level'	=> 2,
			'role'			=> 'customer'
		);
		$user_id = wp_insert_user( $userdata );
		send_email_to_extra_users( $order, $new_user['email'], $random_password, $new_user['email'] );
		/*
		$subject = 'You are registered to Doug Trenary\'s SUCCESSMIND website';
		$message = "";
		$message .= 'You can access your account area here: ' . get_permalink(woocommerce_get_page_id('myaccount'));
		$message .= 'Your username: ' . $new_user['email'];
		$message .= 'Your password: ' . $random_password;
		//$message .= $order_id;
		$headers = 'From: Doug Trenary <doug@dougtrenary.com>' . "\r\n";
		wp_mail( $new_user['email'], $subject, $message, $headers);
		*/
	}
}

/**
* Display field value on the order edit page
*/
add_action( 'woocommerce_admin_order_data_after_billing_address', 'my_custom_checkout_field_display_admin_order_meta', 10, 1 );
 
function my_custom_checkout_field_display_admin_order_meta($order){
	for($i = 1; $i <= 100; $i++){
		if (get_post_meta( $order->id, 'extra_firstname_' . $i, true )){
			echo '<p><strong>'. get_good_label('extra_firstname_' . $i) .':</strong> ' . get_post_meta( $order->id, 'extra_firstname_' . $i, true ) . '</p>';
			echo '<p><strong>'. get_good_label('extra_lastname_' . $i) .':</strong> ' . get_post_meta( $order->id, 'extra_lastname_' . $i, true ) . '</p>';
			echo '<p><strong>'. get_good_label('extra_email_' . $i) .':</strong> ' . get_post_meta( $order->id, 'extra_email_' . $i, true ) . '</p>';
		} else break;
	}
}

add_filter('wp_mail_content_type','set_content_type');

function set_content_type($content_type){
	return 'text/html';
}

function send_email_to_extra_users($order, $username, $password, $email){
	////////
	$subject = 'You are registered to Doug Trenary\'s SUCCESSMIND website';
	$headers = 'From: Doug Trenary <doug@dougtrenary.com>' . "\r\n";
	ob_start();
	
	$email_heading = '<h1 style="text-align: center; margin-top: -20px;">Welcome to Doug Trenary\'s SuccessMind</h1>';

	do_action( 'woocommerce_email_header', $email_heading );
	do_action( 'woocommerce_email_before_order_table', $order, true );

	?>
	<h2>Thanks for creating an account on Doug Trenary's SuccessMind.</h2> 
	<p>Your username is <?php echo $username; ?>.</p>
	<p>Your password is <?php echo $password; ?></p>
	<!--<p>You can change your password here: <a href="<?php echo get_permalink(woocommerce_get_page_id('lostpassword')); ?>"><?php echo get_permalink(woocommerce_get_page_id('lostpassword')); ?></a>.</p>-->
	<p>You can change your password here: <a href="http://www.dougtrenary.com/lostpassword/">http://www.dougtrenary.com/lostpassword/</a>.</p>
	lostpassword
	<?php

	//do_action('woocommerce_email_after_order_table', $order, true);
	//do_action( 'woocommerce_email_order_meta', $order, true );
	//woocommerce_get_template( 'emails/email-addresses.php', array( 'order' => $order ) );

	do_action( 'woocommerce_email_footer' );

	//include("email-footer.php");
	$message = ob_get_contents();
	ob_end_clean();
	wp_mail($email, $subject, $message, $headers);
}

/** 20150630 **/

function test_modify_user_table( $column ) {
    $column['user_level'] = 'User Level';
    $column['manager'] = 'Manager';
    $column['group_name'] = 'Group';

    return $column;
}

add_filter( 'manage_users_columns', 'test_modify_user_table' );

function test_modify_user_table_row( $val, $column_name, $user_id ) {
    $user_level = get_user_meta( $user_id, 'user_level', true ); 
    if ($user_level == 2) {
    	$user_level = '2 - Subscriber';
    }
    
    $manager = get_user_meta( $user_id, 'manager', true ); 

    $group_name = get_user_meta( $user_id, 'group_name', true );

    switch ($column_name) {
        case 'user_level' :
            return $user_level;
            break;
        case 'manager' : 
        	return ( $manager == 1 ) ? 'Yes' : '';
        	break;
        case 'group_name' :
        	return ( $group_name ) ? $group_name : '';
        	break;
        default:
    }

    return $return;
}

add_filter( 'manage_users_custom_column', 'test_modify_user_table_row', 10, 3 );

add_action('wp_head','ajaxurl');
function ajaxurl() {
?>
<script type="text/javascript">
var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
</script>
<?php
}

add_action('wp_ajax_increment_user_video', 'incrementUserVideo');
add_action('wp_ajax_nopriv_increment_user_video', 'incrementUserVideo');

function incrementUserVideo___old() {
	global $wpdb;

	$user_id = $_POST['user_id'];
	$video_id = $_POST['video_id'];	

	$query = "SELECT * FROM " . $wpdb->prefix . "user_videos WHERE user_id = " . $user_id . " AND video_id = '" . $video_id . "' LIMIT 1";
	//echo $query; 
	$results = $wpdb->get_results($query, ARRAY_N);
	//var_dump($results); die();
	if ($results) {
		//update then
		$times = $results[0][3] + 1;
		$query = "UPDATE " . $wpdb->prefix . "user_videos SET times = '" . $times . "' WHERE user_id = " . $user_id . " AND video_id = '" . $video_id . "';";
		$wpdb->query($query);
	} else {
		$query = "INSERT INTO " . $wpdb->prefix . "user_videos (`id`, `user_id`, `video_id`, `times`) 
			VALUES (NULL, '" . $user_id . "', '" . $video_id . "', '1');";
		$wpdb->query($query);
	}

}

function incrementUserVideo() {
	global $wpdb;

	$user_id = $_POST['user_id'];
	$video_id = $_POST['video_id'];	
	$state = $_POST['state'];

	$query = "INSERT INTO " . $wpdb->prefix . "user_videos (`id`, `user_id`, `video_id`, `date`, `finish`) 
			VALUES (NULL, '" . $user_id . "', '" . $video_id . "', '" . date("Y-m-d h:i:s") . "', " . $state . ");";
			echo $query;
	$wpdb->query($query);

}

add_action('wp_ajax_increment_user_file', 'incrementUserFile');
add_action('wp_ajax_nopriv_increment_user_file', 'incrementUserFile');

function incrementUserFile() {
	global $wpdb;
	$user_id = $_POST['user_id'];
	$file_id = $_POST['file_id'];
	$file_name = $_POST['file_name'];	
//echo 'here';
//$query = " ALTER TABLE " . $wpdb->prefix ."user_files MODIFY COLUMN id INT auto_increment";
//var_dump( $query );
//$xx = $wpdb->query($query);
//var_dump($xx);
foreach ( $wpdb->get_col( "DESC " . $wpdb->prefix ."user_files`", 0 ) as $column_name ) {

  //var_dump( $column_name );

}
 //die();


	$query = "INSERT INTO " . $wpdb->prefix . "user_files 
			(`id`, `user_id`, `file_id`, `date`, `file_name`) 
			VALUES 
			(	NULL, '" . $user_id . "', '" . $file_id . "', 
				'" . date("Y-m-d h:i:s") . "', '" . $file_name . "'
			);";
	//		echo $query;
	$x = $wpdb->query($query) or die(mysql_error());
	//var_dump($x); 
	die();
}

add_action('wp_logout','go_home');

function go_home(){
  wp_redirect( home_url() );
  exit();
}

add_filter( 'wp_nav_menu_items', 'add_loginout_link', 10, 2 );
function add_loginout_link( $items, $args ) {
	$current_user = wp_get_current_user();
	
	$user_meta = get_user_meta($current_user->data->ID);
	$user_manager = $user_meta['manager'][0];

	if (is_user_logged_in() && $args->menu == 'Subscriber Menu' && $user_manager) {
		$items .= '<li><a class="user_reports" href="'.site_url().'/dt-user-reports/">User Reports</a></li>';
		//$items .= '<li><a href="'.site_url().'/dt-file-reports/">File Reports</a>';
	}

	if (is_user_logged_in() && $args->menu == 'Subscriber Menu') {
		$items .= '<li><a href="'.site_url().'/tech-support-contact-page/">Members Support</a></li>';
	}

    if (is_user_logged_in() && $args->menu == 'My Account') {
        $items .= '<li><a href="'. wp_logout_url() .'">Log Out</a></li>';
    }
    elseif (!is_user_logged_in() && $args->menu == 'My Account') {
        $items .= '<li><a href="'. site_url('wp-login.php') .'">Log In</a></li>';
    }
    return $items;
}