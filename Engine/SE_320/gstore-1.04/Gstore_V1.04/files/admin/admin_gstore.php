<?php



$page = "admin_gstore";
include "admin_header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }


// SET RESULT VARIABLE
$result = 0;


			



// SAVE CHANGES
if($task == "dosave")
{  
 $setting['gstore_band_a'] = $_POST['gstore_band_a'];
      $setting['gstore_band_b'] = $_POST['gstore_band_b'];
	     $setting['gstore_band_c'] = $_POST['gstore_band_c'];
		    $setting['gstore_band_d'] = $_POST['gstore_band_d'];
			
			if($setting['gstore_band_a'] == ""){
$setting['gstore_band_a'] = "United KIngdom";}

			if($setting['gstore_band_b'] == ""){
$setting['gstore_band_a'] = "Europe";}

			if($setting['gstore_band_c'] == ""){
$setting['gstore_band_a'] = "Northen Hemisphere";}

			if($setting['gstore_band_d'] == ""){
$setting['gstore_band_a'] = "Southern Hemisphere";}

			
  // SAVE CHANGES TO SHIPPING BANDS
  $sql = "UPDATE se_settings SET gstore_band_a='{$setting[gstore_band_a]}'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
    $sql = "UPDATE se_settings SET gstore_band_b='{$setting[gstore_band_b]}'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
    $sql = "UPDATE se_settings SET gstore_band_c='{$setting[gstore_band_c]}'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
    $sql = "UPDATE se_settings SET gstore_band_d='{$setting[gstore_band_d]}'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");



   $setting['gstore_currency'] = $_POST['gstore_currency'];
  $setting['setting_permission_gstore'] = $_POST['setting_permission_gstore'];
  
  
    // SAVE CHANGES TO CURRENCY
  $sql = "UPDATE se_settings SET gstore_currency='{$setting[gstore_currency]}'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");

  // SAVE OTHER CHANGES
  $sql = "UPDATE se_settings SET setting_permission_gstore='{$setting[setting_permission_gstore]}'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");

  $result = 1;
  

}


// GET TABS AND FIELDS
$field = new se_field("gstore");
$field->cat_list();
$cat_array = $field->cats;



// ASSIGN VARIABLES AND SHOW GENERAL SETTINGS PAGE
$smarty->assign('result', $result);
$smarty->assign('cats', $cat_array);
include "admin_footer.php";
?>