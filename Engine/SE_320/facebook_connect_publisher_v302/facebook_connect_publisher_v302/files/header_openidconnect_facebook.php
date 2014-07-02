<?php

// ENSURE THIS IS BEING INCLUDED IN AN SE SCRIPT
if(!defined('SE_PAGE')) { exit(); }

// INCLUDE FUNCTION FILE
include_once "./include/functions_openidconnect_facebook.php";

// INCLUDE CLASS FILE
include_once "./include/class_openidconnect_facebook.php";



// Internal Autologin - dangerous
/*
if( !$user->user_exists ) {

  $openidservice_name = 'facebook';
  $openid_user = new se_user_openid($openidservice_name);
  
  // try login via openid
  $openid_user->user_login_openid(false);

  // If user logged in, refresh current page
  if($openid_user->is_error == 0) {
    semods::redirect( urldecode($url->url_current()) );
  }

}
*/




// PRELOAD LANGUAGE
SE_Language::_preload_multi(100051137,100051138,100051139);


// SET USER MENU VARS
if( $user->user_exists ) {
  $plugin_vars['menu_user'] = Array('file' => 'user_openid_facebook.php',
                                    'icon' => 'openid_facebook.gif',
                                    'title' => 100051137
                                    );
  
  /*  
  // page top invite - if not connected, show "connect", otherwise show "invite"
  $plugin_vars['menu_main'] = Array('file' => 'user_openid_facebook_invite.php',
                                    'title' => 100051138
                                    );
  */

}





$openidconnect_hook_logout = semods::get_setting('openidconnect_hook_logout');
$openidconnect_autologin = semods::get_setting('openidconnect_autologin');
$openidconnect_facebook_api_key = semods::get_setting('openidconnect_facebook_api_key');
$openidconnect_facebook_user_id = $user->user_exists ? se_user_openid::user_openid_get_userid($user->user_info['user_id'], 'facebook' ) : 0;


$smarty->assign('openidconnect_facebook_api_key',$openidconnect_facebook_api_key);
$smarty->assign('openidconnect_facebook_user_id',$openidconnect_facebook_user_id);
$smarty->assign('openidconnect_hook_logout',$openidconnect_hook_logout);
$smarty->assign('openidconnect_autologin',$openidconnect_autologin);
$smarty->assign('openidconnect_extension_facebookpublisher',1);

$smarty->assign_hook('footer', "footer_openidconnect_facebook.tpl");
$smarty->register_prefilter( 'openidconnect_facebook_filter' );

?>