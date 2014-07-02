<?php

define('SE_PAGE_AJAX', TRUE);

$page = "openidconnect_ajax";
include "header.php";


$task = semods::getpost('task');




if(!$user->user_exists && ($task == "autologin" || $task == 'autologinnexttime' || $task == 'autologinsuppress')) {

  $status = 1;
  $autologin = 2;
  
  $openidconnect_suppress_autologin = semods::g($_SESSION,'openidconnect_suppress_autologin',false);
  
  if(!$openidconnect_suppress_autologin) {
    $openid_service = semods::getpost('openidservice','api');
    $openid_user = new se_user_openid($openid_service);
    $openid_user->user_login_openid(false,false);
  
    // not important if enabled or verified
    if($openid_user->user_exists) {
  
      if($task == 'autologinnexttime') {
        semods::db_query("UPDATE se_usersettings
                          SET usersetting_openidconnect_autologin = 1
                          WHERE usersetting_user_id = '{$openid_user->user_info['user_id']}'
                          ");
      }
  
      if($task == 'autologinsuppress') {
        
        $_SESSION['openidconnect_suppress_autologin'] = true;
        
        $autologinremember = (int)semods::getpost('autologinremember',0);
        if($autologinremember) {
          semods::db_query("UPDATE se_usersettings
                            SET usersetting_openidconnect_autologin = 2
                            WHERE usersetting_user_id = '{$openid_user->user_info['user_id']}'
                            ");
        }
      }
      
      $status = 0;
      $autologin = $openid_user->usersetting_info['usersetting_openidconnect_autologin'];
  
    }
  }
  
  $response = array('status'    => 0,
                    'autologin' => $autologin
                    );

  echo json_encode( $response );
  exit();
  
}



/*** ACTIONS BELOW THIS LINE REQUIRE THE USER TO BE LOGGED IN ***/



if( $user->user_exists == false ) {
  echo json_encode(array('status' => 1));
  exit();
}



if($task == "storynopublish") {
  $actiontype_name = semods::getpost('story_type');
  $openid_service = 'facebook';

  $user_feedstories_keys = !empty($user->usersetting_info['usersetting_openidconnect_publishfeeds_keys']) ? explode(',', $user->usersetting_info['usersetting_openidconnect_publishfeeds_keys']) : array();
  $user_feedstories = !empty($user->usersetting_info['usersetting_openidconnect_publishfeeds']) ? explode(',', $user->usersetting_info['usersetting_openidconnect_publishfeeds']) : array();
  
  // see if user already opted out of this story
  if(!in_array($actiontype_name, $user_feedstories_keys)) {

    $story_id = semods::db_query_count("SELECT feedstory_id FROM se_semods_openidfeedstories
                                        WHERE feedstory_service_id = (SELECT openidservice_id FROM se_semods_openidservices WHERE openidservice_name = '{$openid_service}')
                                        AND feedstory_enabled = 1
                                        AND feedstory_display_user = 1
                                        AND feedstory_type = '{$actiontype_name}'");

    if($story_id != 0) {
      
      $user_feedstories_keys[] = $actiontype_name;
      $user_feedstories[] = $story_id;
      
      $user_feedstories_keys = implode(',',$user_feedstories_keys);
      $user_feedstories = implode(',',$user_feedstories);
      
      semods::db_query("UPDATE se_usersettings
                        SET usersetting_openidconnect_publishfeeds = '$user_feedstories',
                            usersetting_openidconnect_publishfeeds_keys = '$user_feedstories_keys'
                        WHERE usersetting_user_id = '{$user->user_info['user_id']}'
                        ");

      $cache_object = SECache::getInstance();
      if( is_object($cache_object) ) {
        $cache_object->remove('site_user_settings_'.$user->user_info['user_id']);
      }

    }


  }


  
  

}

if($task == "suppressconnect") {
  $_SESSION['openidconnect_suppress_connect'] = true;
}






if($task == "clearstory") {
  $story_type = semods::getpost('story_type','all');
  openidconnect_destroy_session_feed_story($story_type);
}




if($task == "getbundleid") {
  $story_type = semods::getpost('story_type');
  $openid_service = 'facebook';

  $status = 1;

  // TBD: refactor per service
  $template_bundle_id = openidconnect_facebook_get_bundle_id($story_type);

  $response = array('status'              => $status,
                    'template_bundle_id'  => $template_bundle_id
                    );
  
  echo json_encode( $response );
  exit();

}





if($task == "composestory") {

  $actiontype_name = semods::getpost('story_type');
  $story_params = semods::getpost('story_params');

  $story_params = html_entity_decode( $story_params, ENT_QUOTES );
  $story_params = json_decode($story_params);
  $story_params = (array)$story_params;
  $story_params = security($story_params);

  $openidconnect_facebook_feed_actions = openidconnect_load_feed_actions();

  $public_site_name = semods::get_setting('openidconnect_feed_public_site_name', '');

  $openidconnect_feed_story = array();
  
  $status = 1;


  if(array_key_exists($actiontype_name,$openidconnect_facebook_feed_actions)) {

    $function_name = semods::g($feed_story_template,'feedstory_compiler','');

    if($function_name == '') {
      $function_name = 'openidconnect_feedstory_compose_'.$actiontype_name;
    }


    $feed_story_template = $openidconnect_facebook_feed_actions[$actiontype_name];
    
    $feed_story_params = array();
    
    if(function_exists($function_name)) {
      
      $feed_story_params = call_user_func_array($function_name, $story_params);
      
    }

    if($feed_story_params !== false) {

      $feed_story_params['site'] = $public_site_name;
      $feed_story_params['site-link'] = $url->url_base;

      $openidconnect_feed_story = array(
                                        'template_bundle_id'  => $feed_story_template['feedstory_metadata']['template_bundle_id'],
                                        
                                        'user_message'        => semods::g($feed_story_params,'user_message', semods::g($feed_story_template,'feedstory_usermessage','') ),
                                        'user_prompt'         => semods::g($feed_story_params,'user_prompt', semods::g($feed_story_template,'feedstory_userprompt','') ),

                                        'data'                => $feed_story_params,
                                        'publish_using'       => semods::g($feed_story_params,'publish_using', semods::g($feed_story_template,'feedstory_publishusing','feed') ),

                                        'story_type'          => $actiontype_name,
                                        );

      $status = 0;
      
    }
    
    
  }
  
  $response = array('status' => $status,
                    'openidconnect_feed_story' => $openidconnect_feed_story
                    );
  
  echo json_encode( $response );
  exit();
  
}

?>