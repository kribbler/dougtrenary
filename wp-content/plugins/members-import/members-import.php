<?php

/*
Plugin Name: Members Import
Plugin URI: 
Description: Allows the batch importation of users/members via an uploaded CSV file.
Author: Soumi Das
Author URI: http://www.youngtechleads.com
Version: 1.1
Author Emailid: soumi.das1990@gmail.com/skype:soumibgb
*/


// add admin menu
add_action('admin_menu', 'memberimport_menu');

function memberimport_menu() {	
	add_submenu_page( 'users.php', 'Members Import', 'Members Import', 'manage_options', 'members-import', 'memberimport_page');	
}

// Redefine user notification function
if ( !function_exists('wp_new_user_notification') ) {
    function wp_new_user_notification( $user_id, $plaintext_pass = '' ) {
        $user = get_userdata( $user_id );

		// The blogname option is escaped with esc_html on the way into the database in sanitize_option
		// we want to reverse this for the plain text arena of emails.
		$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

		$message  = sprintf(__('New user registration on your site %s:'), $blogname) . "\r\n\r\n";
		$message .= sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
		$message .= sprintf(__('E-mail: %s'), $user->user_email) . "\r\n";

		@wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), $blogname), $message);

		if ( empty($plaintext_pass) )
			return;

		$message = "<h2>Welcome to The Digital SalesMind!!</h2>";
		$message .= "<p>Doug Trenary, a high-powered corporate trainer since 1985, has devoted decades to capturing a 12-point profile of power that will take you way beyond outdated sales strategies to the new world of leveraging yourself, buyers, and time. Now Doug brings his strategies to you digitally!</p>";
		$message .= "<p>To access your digital content, please visit our login page and use your credentials provided below:</p>";
		$message .= "Login URL:  " . wp_login_url() . "<br />";
		$message .= "Username: (your complete email address) " . $usee->user_login . "<br />";
		$message .= "Password (case sensitive): " . $plaintext_pass . "<br />";
		$message .= "**you can change the password at My Account**<br />";
		$message .= "<p>Here's what you will get:</p>";
		$message .= "<h3>Your Digital SalesMind Content:</h3>";
		$message .= "*      Streaming: 23 short video segments, each of the Twelve Laws has a 10-point summary of topics.<br />";
		$message .= "*      Streaming:  (Brand New!) 3 longer play video segments (Action, Leverage, and Priority).<br />";
		$message .= "*      Download: 6 hours of audio MP3 files to download and play/listen to.<br />";
		$message .= "*      Download: SalesMind e-book for you to download to your eReader.<br />";
		$message .= "<p>This media will easily play on any device; laptops, tablets, Android and Apple Phones/Devices.</p>";
		$message .= "<p>If you have any problems or comments, please click on Members Support in the right sidebar under \"DTU Members\"</p>";
		
		$message .= "To Success!,<br />";
		$message .= "Doug Trenary";
		//$message .= sprintf(__('Username: %s'), $user->user_login) . "\r\n";
		//$message .= sprintf(__('Password: %s'), $plaintext_pass) . "\r\n";
		//$message .= wp_login_url() . "\r\n";

		$headers = 'From: Doug Trenary University <admin@dougtrenary.com>' . "\r\n";
		wp_mail($user->user_email, sprintf(__('[%s] Your username and password'), $blogname), $message, $headers);
    }
}

// show import form
function memberimport_page() {

	global $wpdb;
	// User data fields list used to differentiate with user meta
	$userdata_fields       = array(
		'user_login', 'user_pass',
		'user_email', 'user_url', 'user_nicename',
		'display_name', 'user_registered', 'first_name',
		'last_name', 'nickname', 'description',
		'rich_editing', 'comment_shortcuts', 'admin_color',
		'use_ssl', 'show_admin_bar_front', 'show_admin_bar_admin',
		'role'
	);
  	if (!current_user_can('manage_options'))
    	wp_die( __('You do not have sufficient permissions to access this page.') );

	// if the form is submitted
	if ($_POST['mode'] == "submit") {
	
		$arr_rows = file($_FILES['csv_file']['tmp_name']);
		$login_username        = isset( $_POST['login_username'] ) ? $_POST['login_username'] : false;
		$password_nag          = isset( $_POST['password_nag'] ) ? $_POST['password_nag'] : false;
		$new_member_notification = isset( $_POST['new_member_notification'] ) ? $_POST['new_member_notification'] : false;
		
		// loop around
		if ( is_array( $arr_rows ) ) {
			$first = true;
			$not_imported = '';
			$flag = 0;
			$not_import_message = "";
			foreach ( $arr_rows as $row ) {
				
				// If a row is empty, just skip it
				if ( empty( $row ) ) {
					if ( $first )
						break;
					else
						continue;
				}

				// If we are on the first line, the columns are the headers
				if ( $first ) {
					//replace " by null
					$calumn_names = str_replace('"', '', $row);
					// split into values
					$headers = split(",", $calumn_names);
					$first = false;
					continue;
				}

				// split into values
				$arr_values = str_replace('"', '', $row);
				$arr_values = split(",", $arr_values);
				
				// Separate user data from meta
				$userdata = $usermeta = array();
				
				foreach ( $arr_values as $ckey => $cvalue ) {
					$column_name = trim( $headers[$ckey] );
					$cvalue = trim( $cvalue );

					if ( empty( $cvalue ) )
						continue;

					if ( in_array( $column_name, $userdata_fields ) ) {
						$userdata[$column_name] = $cvalue;
					}
					else
						$usermeta[$column_name] = $cvalue;
					
				}
				
				// If no user data, bailout!
				if ( empty( $userdata ) )
					continue;
				$usermeta['user_level'] = '2';
				//echo "<pre>"; var_dump($usermeta); die();
				// If creating a new user and no password was set, let auto-generate one!
				if ( empty( $userdata['user_pass'] ) )
					$userdata['user_pass'] = wp_generate_password( 12, false );
				$_POST['user_pass'] = $userdata['user_pass'];
				
				$userdata['user_login'] = strtolower($userdata['user_login']);
				
				if ( ( $login_username ) && ( $userdata['user_email'] == '' ) )
					$userdata['user_email'] = $userdata['user_login'];
				else if ( ( $login_username ) && ( $userdata['user_login'] == '' ) )
					$userdata['user_login'] = $userdata['user_email'];
					//var_dump($userdata); die();
				$user_id = wp_insert_user( $userdata );
				
				// Is there an error?
				if ( is_wp_error( $user_id ) ) {
					$flag = 1;
					$not_imported_usernames  .= "<b>" . $userdata['user_login'] . '</b> ' . $user_id->errors[existing_user_login][0] . "<br />";
				}
				else {
					// If no error, let's update the user meta too!
					if ( $usermeta )
						foreach ( $usermeta as $metakey => $metavalue ) {
							$metavalue = maybe_unserialize( $metavalue );
							update_user_meta( $user_id, $metakey, $metavalue );
						}

					// If we created a new user, maybe set password nag and send new user notification?
					if ( $password_nag )
						update_user_option( $user_id, 'default_password_nag', true, true );

					if ( $new_member_notification || 1==1 ) {
						$x = wp_new_user_notification( $user_id, $userdata['user_pass'] );
						//var_dump($x); die();
					}

					$user_ids[] = $user_id;
				}

			}	// end of 'for each around arr_rows'

			if( $flag == 1 ) {
				$not_import_message = "Following user(s) are not imported as they are already registered in your website:<br />";
				$not_import_message .= $not_imported_usernames;
				$not_import_message .= 'Except above ';
			}
			
			$html_message = "<div class='updated'>";
			$html_message .= $not_import_message;
			$html_message .= "All users/members appear to be have been imported successfully.";
			$html_message .= "</div>";
			
		} // end of 'if arr_rows is array'
		else
			$html_message = "<div class='updated' style='color: red'>It seems the file was not uploaded correctly.</div>";
	} 	// end of 'if mode is submit'
	

?>
<div class="wrap">	
	<?php echo $html_message; ?>	
	<div id="icon-users" class="icon32"><br /></div>
	<h2>CSV Members Import</h2>
	<p>Please select the CSV file you want to import below.</p>
	
	<form action="users.php?page=members-import" method="post" enctype="multipart/form-data">
		<input type="hidden" name="mode" value="submit">
		<input type="file" name="csv_file" />		
		<input type="submit" value="Register" />

		<br/>
		<table>
			<tr valign="top">
				<th scope="row">Login with email ID: </th>
				<td>
					<label for="login_username">
						<input id="login_username" name="login_username" type="checkbox" value="1" />
						Username and e-mail ID are same.
					</label>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Notification: </th>
				<td>
					<label for="new_member_notification">
						<input id="new_member_notification" name="new_member_notification" type="checkbox" value="1" />
						Send username and password to new users.
					</label>
				</td>
			</tr>
			<tr valign="top">
					<th scope="row">Password nag: </th>
					<td>
						<label for="password_nag">
							<input id="password_nag" name="password_nag" type="checkbox" value="1" />
							Show password nag on new users signon.
						</label>
					</td>
				</tr>
			<tr>
				<th scope="row">Notice: </th>
				<td>The CSV file should be in the following format:</td>
			</tr>
			<tr>
				<th scope="row"></th>
				<td>1: Fields name should be at the top line in CSV file separated by comma(,) and delimitted by double quote(").</td>
			</tr>
		</table>
	</form>
	<p style="color: red">Please make sure to have back up your database before proceeding!</p>	
</div>
<?php
}	// end of 'function memberimport_page()'
?>