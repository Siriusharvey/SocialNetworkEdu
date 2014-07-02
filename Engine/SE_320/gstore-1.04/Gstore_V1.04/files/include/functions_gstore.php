<?php




//
//  THIS FILE CONTAINS gstore-RELATED FUNCTIONS
//
//  FUNCTIONS IN THIS CLASS:
//
//    search_gstore()
//    deleteuser_gstore()
//    site_statistics_gstore()
//


defined('SE_PAGE') or exit();








//
// THIS FUNCTION IS RUN DURING THE SEARCH PROCESS TO SEARCH THROUGH gstore ENTRIES
//
// INPUT:
//
// OUTPUT: 
//

function search_gstore()
{
	global $database, $url, $results_per_page, $p, $search_text, $t, $search_objects, $results, $total_results;
  
  /*
	// GET gstore FIELDS
	$gstorefields = $database->database_query("SELECT gstorefield_id, gstorefield_type, gstorefield_options FROM se_gstorefields WHERE gstorefield_type<>'5'");
	$gstorevalue_query = "se_gstores.gstore_title LIKE '%$search_text%' OR se_gstores.gstore_body LIKE '%$search_text%'";
  
	// LOOP OVER gstore FIELDS
	while($gstorefield_info = $database->database_fetch_assoc($gstorefields)) {
    
	  // TEXT FIELD OR TEXTAREA
	  if($gstorefield_info[gstorefield_type] == 1 | $gstorefield_info[gstorefield_type] == 2) {
	    if($gstorevalue_query != "") { $gstorevalue_query .= " OR "; }
	    $gstorevalue_query .= "se_gstorevalues.gstorevalue_".$gstorefield_info[gstorefield_id]." LIKE '%$search_text%'";

	  // RADIO OR SELECT BOX
	  } elseif($gstorefield_info[gstorefield_type] == 3 | $gstorefield_info[gstorefield_type] == 4) {
	    // LOOP OVER FIELD OPTIONS
	    $options = explode("<~!~>", $gstorefield_info[gstorefield_options]);
	    for($i=0,$max=count($options);$i<$max;$i++) {
	      if(str_replace(" ", "", $options[$i]) != "") {
	        $option = explode("<!>", $options[$i]);
	        $option_id = $option[0];
	        $option_label = $option[1];
	        if(strpos($option_label, $search_text)) {
	          if($gstorevalue_query != "") { $gstorevalue_query .= " OR "; }
	          $gstorevalue_query .= "se_gstorevalues.gstorevalue_".$gstorefield_info[gstorefield_id]."='$option_id'";
	        }
	      }
	    }
	  }
	}
  */
  
  /*
  $field = new se_field("gstore");
  $text_columns = $field->field_index(TRUE);
  
  if( !is_array($text_columns) )
    $text_columns = array();
  */
  
	// CONSTRUCT QUERY
  $sql = "
    SELECT
      se_gstores.gstore_id,
      se_gstores.gstore_title,
      se_gstores.gstore_body,
      se_gstores.gstore_photo,
      se_users.user_id,
      se_users.user_username,
      se_users.user_photo,
      se_users.user_fname,
      se_users.user_lname
    FROM
      se_gstores
    LEFT JOIN
      se_users
      ON se_gstores.gstore_user_id=se_users.user_id
    LEFT JOIN
      se_levels
      ON se_users.user_level_id=se_levels.level_id
    LEFT JOIN
      se_gstorevalues
      ON se_gstores.gstore_id=se_gstorevalues.gstorevalue_gstore_id
    WHERE
      (se_gstores.gstore_search=1 || se_levels.level_gstore_search=0)
  ";
  
  /*
  $sql .= " && (MATCH (`gstore_title`, `gstore_body`) AGAINST ('{$search_text}' IN BOOLEAN MODE)";
  
  if( !empty($text_columns) )
    $sql .= " || MATCH (`".join("`, `", $text_columns)."`) AGAINST ('{$search_text}' IN BOOLEAN MODE)";
  
  $sql .= ")";
  */
  
  $text_columns[] = 'gstore_title';
  $text_columns[] = 'gstore_body';
  $sql .= " && MATCH (`".join("`, `", $text_columns)."`) AGAINST ('{$search_text}' IN BOOLEAN MODE)";
  
  
	// GET TOTAL ENTRIES
  $sql2 = $sql . " LIMIT 201";
  $resource = $database->database_query($sql2) or die($database->database_error()." <b>SQL was: </b>{$sql2}");
	$total_entries = $database->database_num_rows($resource);

	// IF NOT TOTAL ONLY
	if( $t=="gstore" )
  {
	  // MAKE gstore PAGES
	  $start = ($p - 1) * $results_per_page;
	  $limit = $results_per_page+1;
    
	  // SEARCH gstoreS
    $sql3 = $sql . " ORDER BY gstore_id DESC LIMIT {$start}, {$limit}";
    $resource = $database->database_query($sql3) or die($database->database_error()." <b>SQL was: </b>{$sql3}");
    
	  while( $gstore_info=$database->database_fetch_assoc($resource) )
    {
	    // CREATE AN OBJECT FOR AUTHOR
	    $profile = new se_user();
	    $profile->user_info['user_id']        = $gstore_info['user_id'];
	    $profile->user_info['user_username']  = $gstore_info['user_username'];
	    $profile->user_info['user_photo']     = $gstore_info['user_photo'];
	    $profile->user_info['user_fname']     = $gstore_info['user_fname'];
	    $profile->user_info['user_lname']     = $gstore_info['user_lname'];
	    $profile->user_displayname();
      
	    // IF EMPTY TITLE
	    if( !trim($gstore_info['gstore_title']) )
        $gstore_info['gstore_title'] = SE_Language::get(589);
      
      $gstore_info['gstore_body'] = cleanHTML($gstore_info['gstore_body'], '');
      
	    // IF BODY IS LONG
	    if( strlen($gstore_info['gstore_body'])>150 )
        $gstore_info['gstore_body'] = substr($gstore_info['gstore_body'], 0, 147)."...";
      
	    // SET THUMBNAIL, IF AVAILABLE
      $thumb_path = NULL;
      if( !empty($gstore_info['gstore_photo']) )
      {
        $gstore_dir = se_gstore::gstore_dir($gstore_info['gstore_id']);
        $gstore_photo = $gstore_info['gstore_photo'];
        $gstore_thumb = substr($gstore_photo, 0, strrpos($gstore_photo, "."))."_thumb".substr($gstore_photo, strrpos($gstore_photo, "."));
        
        if( file_exists($gstore_dir.$gstore_thumb) )
          $thumb_path = $gstore_dir.$gstore_thumb;
        elseif( file_exists($gstore_dir.$gstore_photo) )
          $thumb_path = $gstore_dir.$gstore_photo;
      }
      
      if( !$thumb_path )
        $thumb_path = "./images/icons/file_big.gif";
      
      
      $result_url = $url->url_create('gstore', $gstore_info['user_username'], $gstore_info['gstore_id']);
      $result_name = 5555137;
      $result_desc = 5555138;
      
      
	    $results[] = array(
        'result_url'    => $result_url,
				'result_icon'   => $thumb_path,
				'result_name'   => $result_name,
				'result_name_1' => $gstore_info['gstore_title'],
				'result_desc'   => $result_desc,
				'result_desc_1' => $url->url_create('profile', $gstore_info['user_username']),
				'result_desc_2' => $profile->user_displayname,
				'result_desc_3' => $gstore_info['gstore_body']
      );
      
      unset($profile);
	  }
    
	  // SET TOTAL RESULTS
	  $total_results = $total_entries;
	}

	// SET ARRAY VALUES
	SE_Language::_preload_multi(5555137, 5555138, 5555139);
	if( $total_entries>200 )
    $total_entries = "200+";
  
	$search_objects[] = array(
    'search_type'   => 'gstore',
    'search_lang'   => 5555139,
    'search_total'  => $total_entries
  );
}

// END search_gstore() FUNCTION








//
// THIS FUNCTION IS RUN WHEN A USER IS DELETED
//
// INPUT:
//    $user_id REPRESENTING THE USER ID OF THE USER BEING DELETED
//
// OUTPUT: 
//

function deleteuser_gstore($user_id)
{
	global $database;

	// DELETE gstore ENTRIES AND COMMENTS AND VALUES
	$database->database_query("DELETE se_gstores.*, se_gstorecomments.*, se_gstorevalues.* FROM se_gstores LEFT JOIN se_gstorecomments ON se_gstorecomments.gstorecomment_gstore_id=se_gstores.gstore_id LEFT JOIN se_gstorevalues ON se_gstorevalues.gstorevalue_gstore_id=se_gstores.gstore_id WHERE se_gstores.gstore_user_id='{$user_id}'");

	// DELETE COMMENTS POSTED BY USER
	$database->database_query("DELETE FROM se_gstorecomments WHERE gstorecomment_authoruser_id='{$user_id}'");

	// DELETE STYLE
	$database->database_query("DELETE FROM se_gstorestyles WHERE gstorestyle_user_id='{$user_id}'");
}

// END deleteuser_gstore() FUNCTION









// THIS FUNCTION IS RUN WHEN GENERATING SITE STATISTICS
// INPUT: 
// OUTPUT: 
function site_statistics_gstore(&$args)
{
  global $database;
  
  $statistics =& $args['statistics'];
  
  // NOTE: CACHING WILL BE HANDLED BY THE FUNCTION THAT CALLS THIS
  
  $total = $database->database_fetch_assoc($database->database_query("SELECT COUNT(gstore_id) AS total FROM se_gstores"));
  $statistics['gstores'] = array(
    'title' => 5555145,
    'stat'  => (int) ( isset($total['total']) ? $total['total'] : 0 )
  );
  
  /*
  $total = $database->database_fetch_assoc($database->database_query("SELECT COUNT(gstorecomment_id) AS total FROM se_gstorecomments"));
  $statistics['gstorecomments'] = array(
    'title' => 5555146,
    'stat'  => (int) ( isset($total['total']) ? $total['total'] : 0 )
  );
  
  $total = $database->database_fetch_assoc($database->database_query("SELECT COUNT(gstoremedia_id) AS total FROM se_gstoremedia"));
  $statistics['gstoremedia'] = array(
    'title' => 5555147,
    'stat'  => (int) ( isset($total['total']) ? $total['total'] : 0 )
  );
  */
}

// END site_statistics_gstore() FUNCTION

?>