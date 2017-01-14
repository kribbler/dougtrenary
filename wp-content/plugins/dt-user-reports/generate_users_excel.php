<?php 
require plugin_dir_path( __FILE__ ) . '/php-excel.class.php';

if(!function_exists('wp_get_current_user')) {
    include(ABSPATH . "wp-includes/pluggable.php"); 
}

$all_files = get_files_info();

$current_user = wp_get_current_user();
$user_cap = $current_user->roles[0];
if ($user_cap == 'administrator') {
	$args = array(
		'meta_key' 		=> 'user_level',
		'meta_value'	=> '2'
	);
} else {
	$group_name = get_user_meta( $current_user->data->ID, 'group_name', true );
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
}
//echo "<pre>"; var_dump($args);
//die();



$users = get_users( $args );

$data = array();
$data[] = array('User Name', 'First & Last Name', 'User Email', 'Download Date', 'Filename', 'File Url', 'Video', 'View Date', 'Action');

$i = 1;

foreach ($users as $user) {
	$the_files = array();

	//$query = "SELECT * FROM " . $wpdb->prefix . "user_files WHERE user_id = " . $user->data->ID;
	$query = "SELECT * FROM " . $wpdb->prefix . "user_files WHERE user_id = " . $user->data->ID . " AND file_name != ''";
	$results = $wpdb->get_results( $query, ARRAY_A );			
	if ($results) {
		foreach ($results as $result) {
			$the_files[] = array(
				date("M/d/Y", strtotime($result['date'])),
				$result['file_name'],
				$result['file_id']
			);
			/*
			$key = $result['file_id'];
			if ( $all_files[$key] ) {
				$the_files[] = array(
					//$result['date'],
					date("M/d/Y", strtotime($result['date'])),
					$all_files[$key]['name'], 	//or use $all_files[$key]['file'] for direct link
				);
			}*/
		}
	}
//echo "<pre>"; var_dump($the_files); die();
	$the_videos = array();
	$query = "SELECT * FROM " . $wpdb->prefix . "user_videos WHERE user_id = " . $user->data->ID;
	$results = $wpdb->get_results( $query, ARRAY_A );
	if ($results) {
		foreach ($results as $result) {
			$the_videos[] = array(
				"http://youtu.be/" . $result['video_id'],
				//$result['date'],
				date("M/d/Y", strtotime($result['date'])),
				$result['finish']
			);
		}
	}

	if (count($the_files) >= count($the_videos)) {
		$show_name_times = count($the_files);
	} else {
		$show_name_times = count($the_videos);
	}
//echo "<pre>"; var_dump($the_videos);
	$user_meta = get_user_meta( $user->data->ID );
	if ($show_name_times) {
		for ($k = 0; $k < $show_name_times; $k++) {
			$data[$i][0] = $user->data->user_login;
			$data[$i][1] = $user_meta['first_name'][0] . ' ' . $user_meta['last_name'][0];
			$data[$i][2] = $user->data->user_email;
			$data[$i][3] = $the_files[$k][0];
			$data[$i][4] = $the_files[$k][1];
			$data[$i][5] = $the_files[$k][2];
			$data[$i][6] = $the_videos[$k][0];
			$data[$i][7] = $the_videos[$k][1];

			if ($the_videos[$k][2] == '0')
				$action = 'Ended';
			else 
				$action = 'Playing';
			
			$data[$i][8] = $action;
			$i++;
		}
	} else {
		$data[$i][0] = $user->data->user_login;
		$data[$i][1] = $user_meta['first_name'][0] . ' ' . $user_meta['last_name'][0];
		$data[$i][2] = $user->data->user_email;
		$data[$i][3] = "";
		$data[$i][4] = "";
		$data[$i][5] = "";
		$data[$i][6] = "";
		$data[$i][7] = "";
		$i++;
	}
}

//echo "<pre>"; var_dump($data[1]); die();

// generate file (constructor parameters are optional)
$xls = new Excel_XML('UTF-8', false, 'My Users Sheet');
$xls->addArray($data);
$xls->generateXML(date('Y-m-d-h-i-s') . '-DT-Users');
die();