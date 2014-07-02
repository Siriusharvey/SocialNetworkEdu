<?php
$page = "user_openid_facebook";
include "header.php";

$service = 'facebook';
$task = semods::getpost('task','main');


if($task == 'disconnect') {
  
  se_user_openid::user_openid_unlink($user->user_info['user_id'],$service);
  semods::redirect('user_openid_facebook.php');
  
}

// check if account linked
$service_connected = se_user_openid::user_openid_is_connected($user->user_info['user_id'], $service);

if($service_connected) {
  $openid_user = new se_user_openid('facebook');
  $linked_friends_stats = $openid_user->get_linked_friends_stats();
  $linked_friends = $openid_user->get_linked_friends(0, 21); 
  // out of sync data
  if(count($linked_friends) == 0) {
    $linked_friends_stats['connected_friends']= 0;
  }
  $unlinked_friends = $openid_user->get_unlinked_friends();
}


$smarty->assign('linked_friends', $linked_friends);
$smarty->assign('unlinked_friends', $unlinked_friends);
$smarty->assign('linked_friends_stats', $linked_friends_stats);
$smarty->assign('service_connected', $service_connected);
include "footer.php";
?>