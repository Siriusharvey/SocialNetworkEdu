<?php
$page = "user_openid_facebook_settings";
include "header.php";

$task = semods::getpost('task','main');

$openid_service = 'facebook';
$result = 0;





if($task == 'dosave') {


  // GET ACTION TYPES TO PUBLISH
  $actiontype_disallowed = Array();

  $feedstory = semods::getpost('feedstory',array());
  $feedstory = implode(',',$feedstory);
  
  $openidconnect_autologin = (int)semods::getpost('openidconnect_autologin',0);

  $feedstory_nopublish = semods::db_query_count("SELECT GROUP_CONCAT(feedstory_id)
                         FROM se_semods_openidfeedstories
                         WHERE feedstory_service_id = (SELECT openidservice_id FROM se_semods_openidservices WHERE openidservice_name = '{$openid_service}')
                           AND feedstory_enabled = 1
                           AND feedstory_display_user = 1
                           AND feedstory_id NOT IN ($feedstory)
                          ");
  
  if($feedstory_nopublish === 0) {
    $feedstory_nopublish = '';
  }

  $feedstory_nopublish_keys = semods::db_query_count("SELECT GROUP_CONCAT(feedstory_type)
                         FROM se_semods_openidfeedstories
                         WHERE feedstory_id IN ($feedstory_nopublish)
                          ");

  if($feedstory_nopublish_keys === 0) {
    $feedstory_nopublish_keys = '';
  }
  
  semods::db_query("UPDATE se_usersettings
                    SET usersetting_openidconnect_publishfeeds = '$feedstory_nopublish',
                        usersetting_openidconnect_publishfeeds_keys = '$feedstory_nopublish_keys',
                        usersetting_openidconnect_autologin = $openidconnect_autologin
                    WHERE usersetting_user_id = '{$user->user_info['user_id']}'
                    ");
  
  // Flush cached usersettings
  $usersettings_static =& SEUser::getUserSettings($user->user_info['user_id']);
  $usersettings_static = NULL;
  
  $cache_object = SECache::getInstance();
  if( is_object($cache_object) ) {
    $cache_object->remove('site_user_settings_'.$user->user_info['user_id']);
  }
  
  $result = 1;
}



// POPULATE USER SETTINGS ARRAY
$user->user_settings();



// Load feed stories
$sql = "SELECT *
        FROM se_semods_openidfeedstories S
        LEFT JOIN se_actiontypes T
          ON T.actiontype_name = S.feedstory_type
        WHERE S.feedstory_service_id = (SELECT openidservice_id FROM se_semods_openidservices WHERE openidservice_name = '{$openid_service}')
          AND S.feedstory_enabled = 1
          AND S.feedstory_display_user = 1
          AND T.actiontype_enabled = 1
        ";

$user_feedstories = !empty($user->usersetting_info['usersetting_openidconnect_publishfeeds']) ? explode(',', $user->usersetting_info['usersetting_openidconnect_publishfeeds']) : array();

$openidconnect_facebook_feed_stories = array();

$rows = new semods_db_iterator_assoc($sql);
while($row = $rows->next()) {

  $feedstory_selected = 1;
  if(in_array($row['feedstory_id'], $user_feedstories)) {
    $feedstory_selected = 0;
  }

  $feedstory_desc = ($row['actiontype_desc'] != '') ? $row['actiontype_desc'] : $row['feedstory_desc'];
  
  if(is_numeric($feedstory_desc)) {
    SE_Language::_preload($feedstory_desc);
  }
  
  $openidconnect_facebook_feed_stories[] = array( 'feedstory_id'        => $row['feedstory_id'],
                                                  'feedstory_selected'  => $feedstory_selected,
                                                  'feedstory_desc'      => $feedstory_desc,
                                                );
}


// Check status publish authorization
// TODO: cache
//$openid_user = new se_user_openid($openid_service);
//$permission_status_update = $openid_user->hasPermission('status_update');

$smarty->assign('result', $result);
$smarty->assign('openidconnect_facebook_feed_stories', $openidconnect_facebook_feed_stories);
$smarty->assign('openidconnect_autologin', $user->usersetting_info['usersetting_openidconnect_autologin']);
include "footer.php";
?>