<?php

// ENSURE THIS IS BEING INCLUDED IN AN SE SCRIPT
if(!defined('SE_PAGE')) { exit(); }

if( defined('SEMODS_HEADER_OPENIDCONNECT') ) return;
define('SEMODS_HEADER_OPENIDCONNECT', TRUE);



if (!headers_sent()) {
  @header('P3P: CP="HONK"');
  //@header('P3P: CP="CAO PSA OUR"');
  //@header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');
}



// INCLUDE SE VERSION
include_once "include/version.php";

// INCLUDE CLASS FILE
include_once "./include/class_semods.php";
include_once "./include/class_semods_utils.php";
include_once "./include/class_openidconnect.php";
include_once "./include/class_semods_actionshook.php";

// INCLUDE FUNCTION FILE
include_once "./include/functions_openidconnect.php";






/*

// PRELOAD LANGUAGE
SE_Language::_preload_multi(100051000);


// SET USER MENU VARS
if( $user->user_exists ) {
  $plugin_vars['menu_user'] = Array('file' => 'user_openid.php',
                                    'icon' => 'openidconnect16.gif',
                                    'title' => 100051000
                                    );
}

*/



// hook global actions
if(!is_a($actions, 'semods_actionshook')) {
  $actions = new semods_actionshook();
}





switch($page) {



  case "login":
    if(semods::get_setting('openidconnect_replaceloginpage')) {
      SE_Hook::register("se_footer", "openidconnect_hook_footer_login", -9999);
    }

    // fall through

  case "home":
  case "signup":
    $openid_relay_url = semods::get_setting('openidconnect_rpurl');
    $openid_services = openidconnect_load_services();

    $smarty->assign('openid_relay_url', $openid_relay_url);
    $smarty->assign('openid_services', $openid_services);
    
    break;
  
  
  case "user_logout":
    // clear all openid session
    openidconnect_destroy_session();
    break;
    

  
}







SE_Hook::register("semods_action", "openidconnect_hook_action");






// for all services
if(file_exists('header_openidconnect_facebook.php')) {
  include_once 'header_openidconnect_facebook.php';
}







/*** PRIMARY NETWORK CONNECT ***/


openidconnect_ensure_connect();






/*** FEED STORY ***/





// see if have feed action story queued
$openidconnect_feed_story = openidconnect_get_session_feed_story();

if(!is_null($openidconnect_feed_story)) {
  
  if($user->user_exists) {

    // check page
    if(empty($openidconnect_feed_story['page_check']) || ($openidconnect_feed_story['page_check'] == $page)) {
  
      $smarty->assign('openidconnect_feed_story', $openidconnect_feed_story);
      $smarty->assign('openidconnect_feed_story_publish', true);
      
    }
    
  } else {
    
    // destroy 
    openidconnect_destroy_session_feed_story();
  }
  
} else {
  $smarty->assign('openidconnect_feed_story_publish', false);
}




?>