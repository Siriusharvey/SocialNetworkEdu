<?php
//
// THIS FILE CONTAINS VIDEO-RELATED FUNCTIONS
//
// FUNCTIONS IN THIS CLASS:
// deleteuser_vid()
//
defined('SE_PAGE') or exit();

// THIS FUNCTION IS RUN WHEN A USER IS DELETED
// INPUT: $user_id REPRESENTING THE USER ID OF THE USER BEING DELETED
// OUTPUT: 
function deleteuser_vid($user_id) {
    global $database;

    $video = new se_vid();

    $sql = "SELECT * FROM se_vids WHERE vid_user_id=$user_id";
    $result = $database->database_query($sql);

    while ($row = $database->database_fetch_assoc($result)) {

          $tag_clean = $row['vid_tags'];

          $data = explode(',', $row['vid_location']);

          if (!$data[1]) {
              $video_path = $this->video_dir($user_id).$data[0].".flv";
              if(file_exists($video_path)) { unlink($video_path); }
              $thumb_path = $this->video_dir($user_id).$data[0]."_thumb_0.jpg";
              if(file_exists($thumb_path)) { unlink($thumb_path); }
              $thumb_path = $this->video_dir($user_id).$data[0]."_thumb_1.jpg";
              if(file_exists($thumb_path)) { unlink($thumb_path); }
              $thumb_path = $this->video_dir($user_id).$data[0]."_thumb_default.jpg";
              if(file_exists($thumb_path)) { unlink($thumb_path); }
              $thumb_path = $this->video_dir($user_id).$data[0]."_hd.flv";
              if(file_exists($thumb_path)) { unlink($thumb_path); }
          }
          
          $database->database_query("DELETE FROM se_vids WHERE vid_id='".$row['vid_id']."'");
          $database->database_query("DELETE FROM se_vidcomments WHERE vidcomment_vid_id='".$row['vid_id']."'");

          $tags = explode(" ", $tag_clean);

          for($i = 0; $i < count($tags); $i++) {
            $database->database_query("UPDATE se_vidtags SET value = value-1 WHERE tag = '".$tags[$i]."'");
          }

          $database->database_query("DELETE FROM se_vidtags WHERE value=0");
    }

} // END deleteuser_video() FUNCTION






// THIS FUNCTION IS RUN DURING THE SEARCH PROCESS TO SEARCH THROUGH VIDEOS
// INPUT: 
// OUTPUT: 
function search_vid()
{
	global $database, $url, $results_per_page, $p, $search_text, $t, $search_objects, $results, $total_results, $user;

	// CONSTRUCT QUERY
	$video_query = "SELECT 
			  se_vids.*, 
			  se_users.user_id, 
			  se_users.user_username,
			  se_users.user_photo,
			  se_users.user_fname,
			  se_users.user_lname
			FROM
			  se_vids,
			  se_users,
			  se_levels
			WHERE
			  se_vids.vid_user_id=se_users.user_id AND
			  se_users.user_level_id=se_levels.level_id AND
			  (
			    se_vids.vid_search='1' OR
			    se_levels.level_vid_search='0'
			  )
			  AND
			  (
			    se_vids.vid_title LIKE '%$search_text%' OR
			    se_vids.vid_desc LIKE '%$search_text%'
			  )
			  AND
			    se_vids.vid_is_converted = '1'"; 

	// IF NOT TOTAL ONLY
	if($t == "vid") {

	  // MAKE VIDEO PAGES
	  $start = ($p - 1) * $results_per_page;
	  $limit = $results_per_page+1;

	  $total_vids = 0;

	  // SEARCH VIDEOS
	  $video = new se_vid();
	  $videos = $database->database_query($video_query." ORDER BY vid_id DESC LIMIT $start, $limit");
	  while($video_info = $database->database_fetch_assoc($videos)) {

	    // CREATE AN OBJECT FOR USER
	    $profile = new se_user();
	    $profile->user_info[user_id] = $video_info[user_id];
	    $profile->user_info[user_username] = $video_info[user_username];
	    $profile->user_info[user_fname] = $video_info[user_fname];
	    $profile->user_info[user_lname] = $video_info[user_lname];
	    $profile->user_info[user_photo] = $video_info[user_photo];
	    $profile->user_displayname();

	    // SET RESULT VARS
	    $result_url = $url->url_create("vid_file", $video_info[user_username], $video_info[vid_id]);
	    $result_name = 13500125;
	    $result_desc = 13500124;

	    // SET DIRECTORY
	    $video_info[vid_dir] = $video->video_dir($video_info[user_id]);

            $vid_locations = explode(',', $video_info['vid_location']);

	    // CHECK FOR THUMBNAIL
            if (count($vid_locations) == 2) {
              $thumb_path = $video_info[vid_dir].$video_info[vid_id].'.jpg';
            } else {
              $thumb_path = $video_info[vid_dir].$vid_locations[0].'_thumb_0.jpg';
            }

	    // IF DESCRIPTION IS LONG
            if (preg_match('/<br>/', $video_info[vid_desc])) {
                     $video_info[vid_desc] = preg_split('/<br>/', $video_info[vid_desc]);
                     $video_info[vid_desc] = $video_info[vid_desc][0].'...';
            }


	    if(strlen($video_info[vid_desc]) > 150) { $video_info[vid_desc] = substr($video_info[vid_desc], 0, 147)."..."; }
                 
      $vid_p_max = $profile->user_privacy_max($user);
                 	          	   
      if(!($video_info[vid_privacy] & $vid_p_max)) {
            continue;
      } else {
      	    $total_vids += 1;
	          $results[] = Array('result_url' => $result_url,
				                       'result_icon' => $thumb_path,
				                       'result_name' => $result_name,
				                       'result_name_1' => $video_info[vid_title],
				                       'result_desc' => $result_desc,
				                       'result_desc_1' => $url->url_create('profile', $video_info[user_username]),
				                       'result_desc_2' => $profile->user_displayname,
				                       'result_desc_3' => $video_info[vid_desc]);
			}	
	  }

	  // SET TOTAL RESULTS
	  $total_results = $total_vids;

	}

	// SET ARRAY VALUES
	SE_Language::_preload_multi(13500125, 13500124, 13500126);
	if($total_vids > 200) { $total_vids = "200+"; }
	$search_objects[] = Array('search_type' => 'vid',
				'search_lang' => 13500126,
				'search_total' => $total_vids);


} // END search_vid() FUNCTION






// THIS FUNCTION CHECK WHETHER THE URL EXIST
// INPUT: $url REPRESENTING THE URL
// OUTPUT: TRUE OR FALSE
function page_exists($url){
    $c = curl_init();
    $url = trim($url);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($c, CURLOPT_URL, $url);
    $contents = curl_exec($c);
    curl_close($c);
    if($contents) {
        return true;
    } else {
        return false;
    }
} // END page_exists() FUNCTION






// THIS FUNCTION RUNS WHEN GENERATING SITE STATISTICS
// INPUT: 
// OUTPUT: 
function site_statistics_vid(&$args)
{
global $database;
$statistics =& $args['statistics'];
// NOTE: CACHING WILL BE HANDLED BY THE FUNCTION THAT CALLS THIS
$total = $database->database_fetch_assoc($database->database_query("SELECT COUNT(vid_id) AS total FROM se_vids WHERE vid_is_converted=1"));
$statistics['vids'] = array(
'title' => 13500116,
'stat' => (int) ( isset($total['total']) ? $total['total'] : 0 )
);
}
// END site_statistics_vid() FUNCTION






 function http_test_existance($url, $timeout = 10) {
                        $timeout = (int)round($timeout/2+0.00000000001);
                        $return = array();

                        ### 1 ###
                        $inf = parse_url($url);

                        if (!isset($inf['scheme']) or $inf['scheme'] !== 'http') return array('status' => -1);
                        if (!isset($inf['host'])) return array('status' => -2);
                        $host = $inf['host'];

                        if (!isset($inf['path'])) return array('status' => -3);
                        $path = $inf['path'];
                        if (isset($inf['query'])) $path .= '?'.$inf['query'];

                        if (isset($inf['port'])) $port = $inf['port'];
                        else $port = 80;

                        ### 2 ###
                        $pointer = fsockopen($host, $port, $errno, $errstr, $timeout);
                        if (!$pointer) return array('status' => -4, 'errstr' => $errstr, 'errno' => $errno);
                        socket_set_timeout($pointer, $timeout);

                        ### 3 ###
                        $head =
                        'HEAD '.$path.' HTTP/1.1'."\r\n".
                        'Host: '.$host."\r\n";

                        if (isset($inf['user']))
                             $head .= 'Authorization: Basic '.
                             base64_encode($inf['user'].':'.(isset($inf['pass']) ? $inf['pass'] : ''))."\r\n";
                             if (func_num_args() > 2) {
                                  for ($i = 2; $i < func_num_args(); $i++) {
                                       $arg = func_get_arg($i);
                                       if (
                                       strpos($arg, ':') !== false and
                                       strpos($arg, "\r") === false and
                                       strpos($arg, "\n") === false
                                       ) {
                                       $head .= $arg."\r\n";
                                  }
                             }
                        }
                        else $head .= 'User-Agent: Selflinkchecker 1.0 (http://aktuell.selfhtml.org/artikel/php/existenz/)'."\r\n";

                        $head .= 'Connection: close'."\r\n"."\r\n";

                        ### 4 ###
                        fputs($pointer, $head);

                        $response = '';

                        $status = socket_get_status($pointer);
                        while (!$status['timed_out'] && !$status['eof']) {
                             $response .= fgets($pointer);
                             $status = socket_get_status($pointer);
                        }
                        fclose($pointer);
                        if ($status['timed_out']) {
                             return array('status' => -5, '_request' => $head);
                        }

                        ### 5 ###
                        $res = str_replace("\r\n", "\n", $response);
                        $res = str_replace("\r", "\n", $res);
                        $res = str_replace("\t", ' ', $res);

                        $ares = explode("\n", $res);
                        $first_line = explode(' ', array_shift($ares), 3);

                        $return['status'] = trim($first_line[1]);
                        $return['reason'] = trim($first_line[2]);

                        foreach ($ares as $line) {
                             $temp = explode(':', $line, 2);
                             if (isset($temp[0]) and isset($temp[1])) {
                                  $return[strtolower(trim($temp[0]))] = trim($temp[1]);
                             }
                        }

                        //$return['_response'] = $response;
                        //$return['_request'] = $head;

                        return $return;
}





// THIS FUNCTION RETURNS THE VALUE ROUNDED TO NEAREST EVEN NUMBER
// INPUT: $no REPRESENTING THE VALUE
// OUTPUT: RETURNS NEAREST EVEN NUMBER
function vid_round_nearest($no)  
{  
    return round($no/2)*2;  
} // END vid_round_nearest() FUNCTION
?>