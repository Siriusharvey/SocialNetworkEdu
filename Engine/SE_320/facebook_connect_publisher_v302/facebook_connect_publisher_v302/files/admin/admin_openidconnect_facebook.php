<?php
$page = "admin_openidconnect_facebook";
include "admin_header.php";

error_reporting( E_ALL ^ E_NOTICE ^ E_WARNING );

$task = semods::post('task', 'main');

// SET RESULT VARIABLE
$result = 0;





$error_messages = array();


// SAVE CHANGES
if($task == "dosave") {

  $setting_openidconnect_facebook_api_key = semods::getpost('setting_openidconnect_facebook_api_key','');
  $setting_openidconnect_facebook_secret = semods::getpost('setting_openidconnect_facebook_secret','');
  $setting_openidconnect_feed_public_site_name = semods::getpost('setting_openidconnect_feed_public_site_name','');
  $setting_openidconnect_facebook_invitemessage = semods::getpost('setting_openidconnect_facebook_invitemessage','');
  $setting_openidconnect_facebook_inviteactiontext = semods::getpost('setting_openidconnect_facebook_inviteactiontext','');

  $setting_openidconnect_autologin = semods::getpost('setting_openidconnect_autologin',0);
  $setting_openidconnect_hook_logout = semods::getpost('setting_openidconnect_hook_logout',0);
  $setting_openidconnect_replaceloginpage = semods::getpost('setting_openidconnect_replaceloginpage',0);


  for(;;) {
    if(!preg_match('/^[A-Fa-f0-9]{32}$/',$setting_openidconnect_facebook_api_key)) {
      $error_message = 100051043;
    }

    if(!preg_match('/^[A-Fa-f0-9]{32}$/',$setting_openidconnect_facebook_secret)) {
      $error_message = 100051044;
    }

    $is_error = 0;
    break;
  }
  
  // Talk to FB
  $openid_client = new openidfacebook();
  $openid_client->facebook_api_key = $setting_openidconnect_facebook_api_key;
  $openid_client->facebook_secret = $setting_openidconnect_facebook_secret;
  
  $response = $openid_client->verify_api_keys();
  
  if($response === false) {
    $is_error = 1;
    $error_message = 'There was an error communicating with Facebook. Please make sure your API Key and Secret are correct. Facebook said: ' . $openid_client->error_message;
  }



  /*** App properties ***/

  if($is_error != 1) {

	$openid_facebook = $openid_client->api_client();

    // no need for session key
    $openid_facebook_session_key = $openid_facebook->api_client->session_key;
	$openid_facebook->api_client->session_key = null;

    try {

      $openid_facebook->api_client->admin_setAppProperties( array('uninstall_url' => $url->url_base . 'openidconnect_remove.php?openidservice=facebook',
                                                                              'base_domain'   => openidconnect_get_simple_cookie_domain(),
                                                                              'connect_url'   => $url->url_base,
                                                                              //'callback_url'  => $url->url_base . 'login_openid.php?openidservice=facebook',
                                                                              )
                                                                       );

    } catch (Exception $ex) {

    }

  }


  /*** Create Feed Template Bundles ***/


  if($is_error != 1) {


    $database->database_query("UPDATE se_semods_settings SET
              setting_openidconnect_facebook_api_key = '$setting_openidconnect_facebook_api_key',
              setting_openidconnect_facebook_secret = '$setting_openidconnect_facebook_secret'
              ");

  }


  $database->database_query("UPDATE se_semods_settings SET
            setting_openidconnect_feed_public_site_name = '$setting_openidconnect_feed_public_site_name',
            setting_openidconnect_facebook_invitemessage = '$setting_openidconnect_facebook_invitemessage',
            setting_openidconnect_facebook_inviteactiontext = '$setting_openidconnect_facebook_inviteactiontext',
            setting_openidconnect_autologin = '$setting_openidconnect_autologin',
            setting_openidconnect_hook_logout = '$setting_openidconnect_hook_logout',
            setting_openidconnect_replaceloginpage = '$setting_openidconnect_replaceloginpage'
            ");


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




// ASSIGN VARIABLES AND SHOW GENERAL SETTINGS PAGE
$smarty->assign('result', $result);
$smarty->assign('error_message', $error_message);

$smarty->assign('error_messages', $error_messages);

$smarty->assign('setting_openidconnect_facebook_api_key', semods::get_setting('openidconnect_facebook_api_key'));
$smarty->assign('setting_openidconnect_facebook_secret', semods::get_setting('openidconnect_facebook_secret'));
$smarty->assign('setting_openidconnect_feed_public_site_name', semods::get_setting('openidconnect_feed_public_site_name'));
$smarty->assign('setting_openidconnect_facebook_invitemessage', semods::get_setting('openidconnect_facebook_invitemessage'));
$smarty->assign('setting_openidconnect_facebook_inviteactiontext', semods::get_setting('openidconnect_facebook_inviteactiontext',''));

$smarty->assign('setting_openidconnect_autologin', semods::get_setting('openidconnect_autologin',''));
$smarty->assign('setting_openidconnect_hook_logout', semods::get_setting('openidconnect_hook_logout',''));
$smarty->assign('setting_openidconnect_replaceloginpage', semods::get_setting('openidconnect_replaceloginpage',''));

$smarty->assign('openidconnect_facebook_feed_actions', $openidconnect_facebook_feed_actions);

include "admin_footer.php";
?>