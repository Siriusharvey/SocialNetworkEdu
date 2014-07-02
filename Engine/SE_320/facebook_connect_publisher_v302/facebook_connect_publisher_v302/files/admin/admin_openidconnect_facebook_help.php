<?php
$page = "admin_openidconnect_facebook_help";
include "admin_header.php";

$openid_facebook_show_faq = semods::getpost('show','');

$smarty->assign('openid_facebook_show_faq',$openid_facebook_show_faq);
include "admin_footer.php";
?>
