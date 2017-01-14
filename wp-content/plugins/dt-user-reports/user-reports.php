<?php
/*
Plugin Name: DT Users Reports
Plugin URI: 
Description: DT Users Reports
Author: NetFusion Studios
Version: 1.0
Author URI: 
License: 
*/

add_action( 'admin_menu', 'dt_users_admin_actions' );
add_action( 'admin_init', 'dt_users_admin_init' );

function dt_users_admin_actions(){
	add_menu_page( 'DT Users Reports Admin', 'DT Users Reports', 'manage_options', 'dt-user-reports', 'reports_index', plugins_url( 'dt-user-reports/images/logo.png' ), 6 );
}

if ($_GET['action'] == 'dt_users_excel') {
	require_once(plugin_dir_path(__FILE__).'generate_users_excel.php');
	die();
}

function get_files_info() {
	global $wpdb;

	$query = "SELECT * FROM " . $wpdb->prefix . "posts WHERE post_type = 'product'";
	$results = $wpdb->get_results($query, ARRAY_A);

	$all_files = array();

	foreach ($results as $result) {
		$query = "SELECT * FROM " . $wpdb->prefix . 
			"postmeta WHERE meta_key = '_downloadable_files' AND post_id = " . $result['ID'];
		
		$meta = $wpdb->get_results($query, ARRAY_A);
		
		if ($meta) {
			$files = unserialize($meta[0]['meta_value']);
			foreach ($files as $key=>$value) {
				if (!in_array($files[$key], $all_files)) {
					$all_files[$key] = $files[$key];
				}
			}
		}
	}

	return $all_files;
}

function reports_index( $manager_id = NULL ){
	global $wpdb;

	$all_files = get_files_info();
	$all_videos = get_videos_info();

	if ( $manager_id ) {
		$group_name = get_user_meta( $manager_id, 'group_name', true );
		$args = array(
			'meta_query' => array(
				array(
					'key' => 'user_level',
					'value' => '2',
					'compare' => '='
				),
				array(
					'key' => 'group_name',
					'value' => $group_name,
					'compare' => '='
				)
			)
		);
	} else {
		$args = array(
			'meta_key' 		=> 'user_level',
			'meta_value'	=> '2'
		);
	}

	$users = get_users( $args );

	echo "<table class='wp-list-table widefat fixed posts'  style='font-size: 12px; line-height:15px'>";
	echo "<thead>";
		echo "<tr>";
			echo "<th width='25%'>User Name<br />First & Last Name</th>";
			echo "<th width='30%;' style='border-right: 1px solid #ddd;'><u>Downloads</u></th>";
			echo "<th style='padding-left:10px' width='45%'><u>Youtube Videos</u></th>";
		echo "</tr>";
		echo "</thead>";
		echo "<tfoot>";
		echo "<tr>";
			echo "<th width='25%'>User Name<br />First & Last Name</th>";
			echo "<th width='30%;' style='border-right: 1px solid #ddd'><u>Downloads</u></th>";
			echo "<th style='padding-left:10px'><u>Youtube Videos</u></th>";
		echo "</tr>";
	echo "</tfoot>";

	echo "<tbody>";
	
	//echo "<pre>";var_dump($resp->items[0]->snippet);echo "</pre>";

	$k = 0;
	foreach ($users as $user) {?>
		<tr <?php echo ($k++%2 == 0) ? 'class="alternate"' : '';?>>
		<td><?php echo $user->data->user_login;?><br />
			<?php
			$user_meta = get_user_meta( $user->data->ID );
			//echo "<pre>"; var_dump($user_meta); echo "</pre>";
			echo $user_meta['first_name'][0] . ' ' . $user_meta['last_name'][0];
			?>
		</td>
		<td style='border-right: 1px solid #ddd;'>
			<?php
			$query = "SELECT * FROM " . $wpdb->prefix . "user_files WHERE user_id = " . $user->data->ID . " AND file_name != ''";
			$results = $wpdb->get_results( $query, ARRAY_A );
			//pr($results); echo $user->data->ID ;
			if ($results) {
				foreach ($results as $result) {
					echo '<p>' . date("m/d/Y", strtotime($result['date'])) . ' - <br />';
					//echo $result['date'] . ' - ';
					echo '<a href="'.$result['file_id'].'">' . $result['file_name'] . "</a>";
					echo '</p>';
					/*
					$key = $result['file_id'];
					if ( $all_files[$key] ) {
						echo date("m/d/Y", strtotime($result['date'])) . ' - ';
						//echo $result['date'] . ' - ';
						echo '<a href="'.$all_files[$key]['file'].'">' . $all_files[$key]['name'] . "</a>";
						echo '<br />';
					}*/
				}
			}
			?>
		</td>
		<td  style='padding-left:10px'>
			<?php
			$query = "SELECT * FROM " . $wpdb->prefix . "user_videos WHERE user_id = " . $user->data->ID;
			$results = $wpdb->get_results( $query, ARRAY_A );
			if ($results) {
				echo "<table cellspacing=0>";
				foreach ($results as $result) {
					echo "<tr>";
					//echo "<td><b>" . $result['times'] . '</b> x ';
					echo '<td width="70%">
						<img align="left" style="margin-right:10px" src="'.$all_videos[$result['video_id']]['thumb'].'" />
						<a style="clear:both;font-size:12px;display: block; line-height:14px;" href="http://youtu.be/' . $result['video_id'] . '" target="_blank">' . $all_videos[$result['video_id']]['title'] . '</a>
					</td>';
					//echo '<td></td>';
					//echo '<td>' . $result['date'] . '<br />';
					echo '<td>' . date("M/d/Y", strtotime($result['date'])) . '<br />';
					if ($result['finish'] == '0') echo '- Ended';
					else if ($result['finish'] == '0') echo '- Playing';
					echo '</td>';
					echo '</tr>';
				}
				echo "</table>";
			}
			?>
		</td>
		</tr>
	<?php }
	echo "</tbody>";
	echo "</table>";

	echo '<br /><a class="button" href="admin.php?page=dt-user-reports&action=dt_users_excel">Export to Excel</a>';
}

function get_videos_info() {
	global $wpdb;
	$all_videos = array();
	$query = "SELECT DISTINCT video_id FROM " . $wpdb->prefix . "user_videos";
	$results = $wpdb->get_results( $query, ARRAY_A );
	
	foreach ($results as $key=>$value) {
		$all_videos[$value['video_id']] = get_youtube_title_and_thumbnail($results[$key]['video_id']);
	}

	return $all_videos;
}

function dt_users_admin_init(){
	wp_register_style( 'myPluginStylesheet', plugins_url('style.css', __FILE__) );
}

function my_plugin_admin_styles() {
	wp_enqueue_style( 'myPluginStylesheet' );
}

function get_youtube_title_and_thumbnail($video_id) {
	$youtube_key = 'AIzaSyCsBrR1gUfzbHaeIkZxE-x-WQpAPsqB4SI';

	$url = 'https://www.googleapis.com/youtube/v3/videos?part=id%2Csnippet&id='.$video_id.'&key=' . $youtube_key;
	
	$curl = curl_init();
	curl_setopt_array($curl, array(
	    CURLOPT_RETURNTRANSFER => 1,
	    CURLOPT_URL => $url,
	    CURLOPT_USERAGENT => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)'
	));

	$resp = curl_exec($curl);
	curl_close($curl);

	$resp = json_decode($resp);
	//echo "<pre>"; var_dump($resp); die();

	$info = array();
	$info['title'] = $resp->items[0]->snippet->title;
	$info['thumb'] = $resp->items[0]->snippet->thumbnails->default->url;
	return $info;
}

function dt_user_reports( $atts ) {
    /*$a = shortcode_atts( array(
        'foo' => 'something',
        'bar' => 'something else',
    ), $atts );*/

	$current_user = wp_get_current_user();
    return reports_index( $current_user->data->ID );
}
add_shortcode( 'dt-user-reports', 'dt_user_reports' );