<?php
$page = "user_openid";
include "header.php";

$task = semods::getpost('task','main');

if($task == 'disconnect') {
  $service = (int)semods::getpost('service');
  
  se_user_openid::user_openid_unlink($user->user_info['user_id'],$service);
  semods::redirect('user_openid.php');
  
}


$linked_openid_services = semods::db_query_assoc_all("SELECT *
                         FROM se_semods_openidservices S
                         LEFT JOIN se_semods_usersopenid O 
                           ON O.openid_service_id = S.openidservice_id AND O.openid_user_id = {$user->user_info['user_id']}
                         WHERE O.openid_user_id = {$user->user_info['user_id']} OR ISNULL(O.openid_user_id)
                           AND S.openidservice_enabled = 1
                           GROUP BY S.openidservice_id
                         ");


$openid_relay_url = semods::get_setting('openidconnect_rpurl');
//var_dump($linked_openid_services);exit;
$smarty->assign('openid_relay_url', $openid_relay_url);
$smarty->assign('linked_openid_services',$linked_openid_services);
include "footer.php";
?>