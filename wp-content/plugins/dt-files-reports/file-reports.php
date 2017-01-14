<?php
/*
Plugin Name: DT Files Reports
Plugin URI: 
Description: DT Files Reports
Author: NetFusion Studios
Version: 1.0
Author URI: 
License: 
*/

add_action( 'admin_menu', 'dt_files_admin_actions' );
add_action( 'admin_init', 'dt_files_admin_init' );

if ($_GET['action'] == 'dt_files_excel') {
	require_once(plugin_dir_path(__FILE__).'generate_files_excel.php');
	die();
}

if ($_GET['action'] == 'dt_videos_excel') {
	require_once(plugin_dir_path(__FILE__).'generate_videos_excel.php');
	die();
}

function dt_files_admin_actions(){
	add_menu_page( 'DT Files Reports Admin', 'DT Files Reports', 'manage_options', 'dt-file-reports', 'f_reports_index', plugins_url( 'dt-files-reports/images/logo.png' ), 7 );
}

function f_get_files_info() {
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

function get_all_videos() {
	global $wpdb;

	$query = "SELECT DISTINCT video_id FROM " . $wpdb->prefix . "user_videos";
	$results = $wpdb->get_results( $query, ARRAY_A );

	foreach ($results as $key => $value) {
		$query = "SELECT * FROM " . $wpdb->prefix . "user_videos WHERE video_id = '" . $value['video_id'] . "'";
		$vrs = $wpdb->get_results($query, ARRAY_A);
		foreach ($vrs as $vr) {
			$results[$key]['watched'] = get_video_times_watched( $value['video_id'] );
		}
	}

	return $results;
}

function get_video_times_watched($video_id) {
	global $wpdb;

	$query = "SELECT COUNT(*) FROM " . $wpdb->prefix . "user_videos WHERE video_id = '" . $video_id . "'";
	$number = $wpdb->get_var( $query );

	return $number;
}

function get_file_downloads_info($results) {
	$info = array();
	$download_times = 0;
	foreach ($results as $result) {
		$download_times += $result['download_count'];
	}
	$info['download_times'] = $download_times;

	return $info;
}

function get_user_files() {
	global $wpdb;
	$query = "SELECT DISTINCT file_id, file_name FROM " . $wpdb->prefix . "user_files WHERE file_name != ''";
	$results = $wpdb->get_results($query, ARRAY_A);

	foreach ($results as $key => $value) {
		$query = "SELECT COUNT(*) FROM " . $wpdb->prefix . "user_files WHERE file_id = '" . $value['file_id'] . "'";
		$number = $wpdb->get_var( $query );
		$results[$key]['views'] = $number;
	}

	return $results;
}


function show_files_info($all_files) {
	echo '<h2>File Downloads</h2>';
	echo "<table class='wp-list-table widefat fixed posts'>";
	echo "<thead>";
		echo "<tr>";
			echo "<th>File Name</th>";
			echo "<th>Downloads</th>";
		echo "</tr>";
		echo "</thead>";
		echo "<tfoot>";
		echo "<tr>";
			echo "<th>File Name</th>";
			echo "<th>Downloads</th>";
		echo "</tr>";
	echo "</tfoot>";

	$k = 0;

	$user_files = get_user_files();

	foreach ($user_files as $key => $value) {?>
		<tr <?php echo ($k++%2 == 0) ? 'class="alternate"' : '';?>>
		<td><?php echo '<a href="'.$value['file_id'].'">' . $value['file_name'] . "</a>"; ?></td>
		<td><?php echo $user_files[$key]['views'];?></td>
		</tr>
	<?php }
	echo "</tbody>";
	echo "</table>";
	echo '<br /><a class="button" href="admin.php?page=dt-file-reports&action=dt_files_excel">Export to Excel</a>';
}

function show_videos_info($all_videos) {
	echo '<h2>Video Info</h2>';
	echo "<table class='wp-list-table widefat fixed posts'>";
	echo "<thead>";
		echo "<tr>";
			echo "<th>Video</th>";
			echo "<th>Views</th>";
		echo "</tr>";
		echo "</thead>";
		echo "<tfoot>";
		echo "<tr>";
			echo "<th>Video</th>";
			echo "<th>Views</th>";
		echo "</tr>";
	echo "</tfoot>";

	$k = 0;
	foreach ($all_videos as $key => $value) {
		$info = get_youtube_title_and_thumbnail($all_videos[$key]['video_id']);
		?>
		<tr <?php echo ($k++%2 == 0) ? 'class="alternate"' : '';?>>
		<td><?php echo '<img src="'.$info['thumb'].'" />';?>
		<br /><?php echo '<a href="http://youtu.be/' . $all_videos[$key]['video_id'] . '" target="_blank">' . $info['title'] . '</a>';?></td>
		<td><?php echo $all_videos[$key]['watched'];?></td>
		</tr>
	<?php }
	echo "</tbody>";
	echo "</table>";
	echo '<br /><a class="button" href="admin.php?page=dt-file-reports&action=dt_videos_excel">Export to Excel</a>';
}

function f_reports_index(){
	global $wpdb;

	$all_files = get_files_info();
	$all_videos = get_all_videos();

	foreach ($all_files as $key => $value) {
		$query = "SELECT * FROM " . $wpdb->prefix . "woocommerce_downloadable_product_permissions WHERE 
			download_id = '" . $key . "'";
		$results = $wpdb->get_results( $query, ARRAY_A );
		$all_files[$key]['file_downloads_info'] = get_file_downloads_info($results);
	}

	show_files_info($all_files);
	show_videos_info($all_videos);
}

function dt_files_admin_init(){
	wp_register_style( 'f_myPluginStylesheet', plugins_url('style.css', __FILE__) );
}

function f_my_plugin_admin_styles() {
	wp_enqueue_style( 'f_myPluginStylesheet' );
}