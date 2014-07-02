<?php
/* $Id: invite.php 42 2009-01-29 04:55:14Z john $ */

$page = "coverage_list";
include "header.php";
$row = 1;
$handle=fopen("csv/standard_mt_coverage.csv","r");

while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
{	if($row >1)
	{
	$coverage[$row][cov]=$data[0];
	$coverage[$row][net]=$data[2];
	}
 	$row++;
}
$smarty->assign("coverage",$coverage);
include "footer.php";
?>