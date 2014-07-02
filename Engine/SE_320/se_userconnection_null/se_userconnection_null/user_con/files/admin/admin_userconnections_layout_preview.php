<?php

/* $Id: admin_userconnections_layout_preview.php 1 2009-09-16 09:36:11Z SocialEngineAddOns $ */

$page = "admin_userconnections_layout_preview";
include "admin_header.php";

$preview_number = $_GET['preview_number'] ;

$smarty->assign('preview_number', $preview_number);
include "admin_footer.php";
?>