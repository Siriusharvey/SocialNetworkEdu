<?php
$page = "user_openid_invite_facebook";
include "header.php";


// CHECK IF INVITE CODES SET TO ADMINS ONLY
if($setting['setting_signup_invite'] == 1) {
  semods::redirect("user_openid_facebook.php");
}


$justinvited = semods::getpost('justinvited',0);
$openidservice_name = semods::getpost('openidservice','api');





// posting back invite id's
if($_POST) {
  $ids = semods::getpost('ids',array());

  // invite only site
  if($setting['setting_signup_invite'] == 2) {
    $invites_left = $user->user_info['user_invitesleft'];
    
    if($invites_left > 0) {
      $invites_left = $invites_left - count($ids);
      if($invites_left < 0) {
       $invites_left = 0;
      }
      
      semods::db_query("UPDATE se_users SET user_invitesleft = '$invites_left' WHERE user_id = {$user->user_info[user_id]}");
      $facebook_service_id = openidconnect_get_service_id('facebook');
      foreach($ids as $id) {
        semods::db_query("INSERT INTO se_semods_openidinvites (invite_user_id, invite_date, invite_user_key, invite_service_id) VALUES ('{$user->user_info['user_id']}', '".time()."', '$id', '$facebook_service_id')");
      }
    }
  }

  //exit;
  semods::redirect('user_openid_invite_facebook.php?justinvited=1');
  //var_dump($_POST);
}



//$openid_user = new se_user_openid('facebook');

// try login via openid
//$openid_user->user_login_openid();

// If user is not connected - error
//if($openid_user->is_error != 0) {
  //semods::redirect("user_home.php");
//}

// TODO
// Load already invited users



$invitation_message = str_replace( array('[displayname]',
                                         '[sitename]',
                                         '[signup-link]'),

                                   array($user->user_displayname,
                                         semods::get_setting('openidconnect_feed_public_site_name'),
                                         $url->url_base . 'signup.php?signup_referer=' . $user->user_info['user_username']
                                        ),
                                  semods::get_setting('openidconnect_facebook_invitemessage')
                                 );


$smarty->assign('justinvited', $justinvited);
$smarty->assign('openidconnect_facebook_invitemessage', $invitation_message);
$smarty->assign('openidconnect_feed_public_site_name', semods::get_setting('openidconnect_feed_public_site_name'));
$smarty->assign('openidconnect_facebook_inviteactiontext', semods::get_setting('openidconnect_facebook_inviteactiontext'));
include "footer.php";
?>