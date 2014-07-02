<?php
$page = "user_openid_facebook_friends";
include "header.php";

$service = 'facebook';
$task = semods::getpost('task','main');
$p = semods::getpost('p',1);

$openid_user = new se_user_openid('facebook');


$linked_friends_stats = $openid_user->get_linked_friends_stats();
$total_friends = $linked_friends_stats['connected_friends'];


$friends_per_page = 10;
$page_vars = make_page($total_friends, $friends_per_page, $p);

$linked_friends = $openid_user->get_linked_friends($page_vars[0], $friends_per_page);

// out of sync data
if(count($linked_friends) == 0) {
  $total_friends = 0;
}

$smarty->assign('total_friends', $total_friends);
$smarty->assign('friends', $linked_friends);
$smarty->assign('maxpage', $page_vars[2]);
$smarty->assign('p', $page_vars[1]);
$smarty->assign('p_start', $page_vars[0]+1);
$smarty->assign('p_end', $page_vars[0]+count($linked_friends));
include "footer.php";
?>