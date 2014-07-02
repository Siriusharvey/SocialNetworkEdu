<?php
$page = "admin_openidconnect_facebook_stories";
include "admin_header.php";

error_reporting( E_ALL ^ E_NOTICE ^ E_WARNING );

$task = semods::post('task', 'main');
$storyfocus = semods::getpost('storyfocus', '');

// SET RESULT VARIABLE
$result = 0;


$openid_service_id  = openidconnect_get_service_id('facebook');


// Load feed stories
$sql = "SELECT *
        FROM se_semods_openidfeedstories
        WHERE feedstory_service_id = $openid_service_id
        ";

$rows = new semods_db_iterator_assoc($sql);
while($row = $rows->next()) {
  $row['feedstory_metadata'] = !empty($row['feedstory_metadata']) ? unserialize($row['feedstory_metadata']) : array();
  $openidconnect_facebook_feed_actions[$row['feedstory_type']] = $row;
}


$error_messages = array();


// SAVE CHANGES
if($task == "addnew") {
  $actiontype_name = semods::getpost('actiontype_name','');

  $sql = "SELECT *
          FROM se_actiontypes
          WHERE actiontype_name = '{$actiontype_name}'
            AND actiontype_enabled = 1
          ";
  
  $actiontype = semods::db_query_assoc($sql);
  
  if($actiontype) {
    
    $feedstory_body = vsprintf( semods::get_language_text($actiontype['actiontype_text']), explode(",", $actiontype['actiontype_vars']));
    //$feedstory_vars = implode(",", array_filter( array_merge(array('{*actor*}','{*site-name*}','{*site-link*}'),explode(",",$actiontype['actiontype_vars'])) ));

    $feedstory_vars = array('{*actor*}','{*site-name*}','{*site-link*}');

    $feedstory_vars_ = array_filter( explode(",",$actiontype['actiontype_vars']) );
    foreach($feedstory_vars_ as $feedstory_var_) {
      // assumption - only bracket squares
      $feedstory_key = $feedstory_var_;
      $feedstory_key = substr($feedstory_key,1,strlen($feedstory_key)-2);
      $feedstory_key = '{*' . $feedstory_key . '*}';
      $feedstory_vars[] = $feedstory_key;
    }
    
    $feedstory_vars = implode(",", $feedstory_vars);
    
    

    // strip media
    $feedstory_body = str_replace("<div class='recentaction_div_media'>[media]</div>","",$feedstory_body);
    $feedstory_body = str_replace('<div class="recentaction_div_media">[media]</div>',"",$feedstory_body);

    // strip all divs
    $feedstory_body = preg_replace("/<div[^>]+>([^<]+)<\/div>/",'$1',$feedstory_body);

    $feedstory_body = preg_replace("/<a href='profile\.php\?user=\[username\]'>[^<]+<\/a>/","{*actor*}",$feedstory_body);
    $feedstory_body = preg_replace('/<a href="profile\.php\?user=\[username\]">[^<]+<\/a>/',"{*actor*}",$feedstory_body);

    $feedstory_body = preg_replace("/<a href='profile\.php\?user=\[username1\]'>[^<]+<\/a>/","{*actor*}",$feedstory_body);
    $feedstory_body = preg_replace('/<a href="profile\.php\?user=\[username1\]">[^<]+<\/a>/',"{*actor*}",$feedstory_body);

    // square brackets - curly
    $feedstory_body = preg_replace('/\[([^\]]+)\]/',"{*$1*}",$feedstory_body);

    $feedstory_body = preg_replace("/href='([^']+)'/","href='{*site-link*}$1'",$feedstory_body);
    $feedstory_body = preg_replace('/href="([^"]+)"/',"href='{*site-link*}$1'",$feedstory_body);
    
    // must start with {*actor*}
    if(substr($feedstory_body,0,9) != '{*actor*}') {
      $feedstory_body = '{*actor*} '. $feedstory_body;
    }

    $feedstory_title = '{*actor*} '. semods::get_language_text($actiontype['actiontype_desc']);
    
    $feedstory = array('feedstory_title'  => $feedstory_title,
                       'feedstory_body'   => $feedstory_body,
                       'feedstory_vars'   => $feedstory_vars,
                       'feedstory_desc'   => semods::get_language_text($actiontype['actiontype_desc']),
                       );
    
    //var_dump($feedstory);exit;
    
    openidconnect_facebook_register_feed_story($actiontype_name,$feedstory);
    
    semods::redirect("admin_openidconnect_facebook_stories.php?storyfocus={$actiontype_name}");
  }

}


// SAVE CHANGES
if($task == "dosave") {

  // Talk to FB
  $openid_client = new openidfacebook();
  
  $response = $openid_client->verify_api_keys();
  
  if($response === false) {
    $is_error = 1;
    $error_message = 'There was an error communicating with Facebook. Please make sure your API Key and Secret are correct. Facebook said: ' . $openid_client->error_message;
  }



  /*** Create Feed Template Bundles ***/


  if($is_error != 1) {


	$openid_facebook = $openid_client->api_client();

    // no need for session key
    $openid_facebook_session_key = $openid_facebook->api_client->session_key;
	$openid_facebook->api_client->session_key = null;


    $feedstories = semods::getpost('feedstory',array());
  
    foreach($feedstories as $feedstory_id => $feedstory) {
      
      $feedstory['feedstory_title'] = htmlspecialchars_decode( $feedstory['feedstory_title'], ENT_QUOTES );
      $feedstory['feedstory_body'] = htmlspecialchars_decode( $feedstory['feedstory_body'], ENT_QUOTES);
      $feedstory['feedstory_link_link'] = htmlspecialchars_decode( $feedstory['feedstory_link_link'], ENT_QUOTES);
      $feedstory['feedstory_link_text'] = htmlspecialchars_decode( $feedstory['feedstory_link_text'], ENT_QUOTES);
  
      $feedstory['feedstory_enabled'] = (int)semods::g($feedstory['feedstory_enabled'],0);

      semods::db_query("UPDATE se_semods_openidfeedstories
                       SET
                        feedstory_enabled = {$feedstory['feedstory_enabled']},
                        feedstory_usermessage = '{$feedstory['feedstory_usermessage']}',
                        feedstory_userprompt = '{$feedstory['feedstory_userprompt']}'
                       WHERE feedstory_id = {$feedstory_id}");
      
      if( ($openidconnect_facebook_feed_actions[$feedstory['feedstory_type']]['feedstory_metadata']['template_bundle_id'] == 0) ||
          ($feedstory['feedstory_title'] != $openidconnect_facebook_feed_actions[$feedstory['feedstory_type']]['feedstory_metadata']['feedstory_title']) ||
          ($feedstory['feedstory_body'] != $openidconnect_facebook_feed_actions[$feedstory['feedstory_type']]['feedstory_metadata']['feedstory_body']) ||
          ($feedstory['feedstory_link_link'] != $openidconnect_facebook_feed_actions[$feedstory['feedstory_type']]['feedstory_metadata']['feedstory_link_link']) ||
          ($feedstory['feedstory_link_text'] != $openidconnect_facebook_feed_actions[$feedstory['feedstory_type']]['feedstory_metadata']['feedstory_link_text']) )
      {
        
      $metadata = array('feedstory_title' => $feedstory['feedstory_title'],
                        'feedstory_body' => $feedstory['feedstory_body'],
                        'feedstory_link_link' => $feedstory['feedstory_link_link'],
                        'feedstory_link_text' => $feedstory['feedstory_link_text'],
                        'template_bundle_id' => 0,
                        );
      
      $metadata_serialized = serialize($metadata);
      $metadata_serialized = $database->database_real_escape_string($metadata_serialized);
  
      semods::db_query("UPDATE se_semods_openidfeedstories
                       SET
                        feedstory_metadata = '{$metadata_serialized}'
                       WHERE feedstory_id = {$feedstory_id}");
  
      // Talk to FB
      if(($is_error != 1) && ($feedstory['feedstory_enabled'])) {
  
        $one_line_story_templates = array();
        $one_line_story_templates[] = $feedstory['feedstory_title'];
            
        $short_story_templates = array();
        $short_story_templates[] = array('template_title' => $feedstory['feedstory_title'],
                                         'template_body'  => $feedstory['feedstory_body']
                                        );
        
        $full_story_template = null;
        
        $action_links = array();
        
        if(!empty($feedstory['feedstory_link_link']) && !empty($feedstory['feedstory_link_text'])) {
  
          $action_links[] = array( 'text' => $feedstory['feedstory_link_text'],
                                   'href' => $feedstory['feedstory_link_link']
                                   );
          
        }
  
        if($openidconnect_facebook_feed_actions[$feedstory['feedstory_type']]['feedstory_metadata']['template_bundle_id'] != 0) {
  
          try {
    
            $openid_facebook->api_client->feed_deactivateTemplateBundleByID($openidconnect_facebook_feed_actions[$feedstory['feedstory_type']]['feedstory_metadata']['template_bundle_id']);
    
          } catch (Exception $ex) {
  
          }
          
        }
  
        $register_success = true;
        
        try {
  
          $template_bundle_id = $openid_client->api_client()->api_client->feed_registerTemplateBundle($one_line_story_templates,$short_story_templates,null,$action_links);
  
        } catch (Exception $ex) {
          
          //echo "exception!";exit;
  
          $register_success = false;
          $error_messages[] = 'Failed registering story template ' . $feedstory['feedstory_type'] . '; Facebook said: ' . $ex->getMessage();    
          
        }
  
        if($register_success) {
  
          $metadata['template_bundle_id'] = $template_bundle_id;
          
          $metadata_serialized = serialize($metadata);
          $metadata_serialized = $database->database_real_escape_string($metadata_serialized);
    
          semods::db_query("UPDATE se_semods_openidfeedstories SET feedstory_metadata = '{$metadata_serialized}' WHERE feedstory_id = {$feedstory_id}");
          
        }
        
      }
  
  
      }
    }

  }


  // CACHING
  $cache_key = 'openidconnect_feed_actions_' . 'facebook';

  if(class_exists("SECache")) {
    $cache_object = SECache::getInstance();

    if( is_object($cache_object) ) {
      $cache_object->remove($cache_key);
    }
  }
  
  // reload settings
  $semods_settings_cache = null;
  
  $result = 1;
  
}


// RELOAD feed stories
$sql = "SELECT *
        FROM se_semods_openidfeedstories
        WHERE feedstory_service_id = $openid_service_id
        ";

$known_feed_stories = array();

$rows = new semods_db_iterator_assoc($sql);
while($row = $rows->next()) {
  $row['feedstory_metadata'] = !empty($row['feedstory_metadata']) ? unserialize($row['feedstory_metadata']) : array();
  $openidconnect_facebook_feed_actions[$row['feedstory_type']] = $row;
  $known_feed_stories[] = "'" . $row['feedstory_type'] . "'";
}

$known_feed_stories = implode(',', $known_feed_stories);

// Action types
// Load feed stories
$sql = "SELECT *
        FROM se_actiontypes
        WHERE actiontype_name NOT IN ($known_feed_stories)
          AND actiontype_enabled = 1
        ";

$available_feed_stories = semods::db_query_assoc_all($sql);



// ASSIGN VARIABLES AND SHOW GENERAL SETTINGS PAGE
$smarty->assign('result', $result);
$smarty->assign('error_message', $error_message);
$smarty->assign('error_messages', $error_messages);
$smarty->assign('openidconnect_facebook_feed_actions', $openidconnect_facebook_feed_actions);
$smarty->assign('available_feed_stories', $available_feed_stories);
$smarty->assign('storyfocus', $storyfocus);
include "admin_footer.php";
?>