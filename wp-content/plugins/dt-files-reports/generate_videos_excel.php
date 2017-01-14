<?php 
require plugin_dir_path( __FILE__ ) . '/php-excel.class.php';

$all_videos = get_all_videos();

$data = array();
$data[] = array('Video Title', 'Video link', 'Views');

foreach ($all_videos as $key => $value) {
	$info = get_youtube_title_and_thumbnail($all_videos[$key]['video_id']);
	$data[] = array(
		$info['title'],
		'http://youtu.be/' . $all_videos[$key]['video_id'],
		$all_videos[$key]['watched'],
	);
}

// generate file (constructor parameters are optional)
$xls = new Excel_XML('UTF-8', false, 'My Videos Sheet');
$xls->addArray($data);
$xls->generateXML(date('Y-m-d-h-i-s') . '-DT-Videos');
die();

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
