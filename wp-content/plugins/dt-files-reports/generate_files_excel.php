<?php 
require plugin_dir_path( __FILE__ ) . '/php-excel.class.php';

$all_files = g_get_files_info();

$user_files = get_user_files();
//var_dump($user_files); die();

/*
foreach ($all_files as $key => $value) {
	$query = "SELECT * FROM " . $wpdb->prefix . "woocommerce_downloadable_product_permissions WHERE 
		download_id = '" . $key . "'";
	$results = $wpdb->get_results( $query, ARRAY_A );
	$all_files[$key]['file_downloads_info'] = get_file_downloads_info($results);
}
*/

$data = array();
$data[] = array('File Name', 'File Url', 'Downloads');

foreach ($user_files as $key => $value) {
	$data[] = array(
		$user_files[$key]['file_name'],
		$user_files[$key]['file_id'],
		$user_files[$key]['views']
	);
}

// generate file (constructor parameters are optional)
$xls = new Excel_XML('UTF-8', false, 'My Files Sheet');
$xls->addArray($data);
$xls->generateXML(date('Y-m-d-h-i-s') . '-DT-Files');
die();


function g_get_files_info() {
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

