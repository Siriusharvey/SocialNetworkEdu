<?php

/* $Id: user_userconnection_contacts_3degree.php 42 2009-09-16 04:55:14Z SocialEngineAddOns $ */

$page = "user_userconnection_contacts_3degree";
include "header.php";

// $third_degree_contacts_id VALUE OF THIS VARIABLE IS ALREADY ASSIGNED IN HEADER_USERCONNNECTION.PHP
$third_degree_contacts_users_information = userconnection_users_information($third_degree_contacts_id);
$smarty->assign('third_degree_contacts_users_information', $third_degree_contacts_users_information);

include "footer.php";
?>