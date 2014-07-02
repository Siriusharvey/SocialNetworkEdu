<?php



$page = "user_gstore_settings";
include "header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }


// ENSURE gstoreS ARE ENABLED FOR THIS USER
if( !$user->level_info['level_gstore_allow'] )
{
  header("Location: user_home.php");
  exit();
}

// SET VARS
$result = FALSE;

// SAVE NEW PAYPAL EMAIL
if($task == "dosave")
{
  $paypal_email = $_POST['paypal_email'];
  $usersetting_notify_gstorecomment = $_POST['usersetting_notify_gstorecomment'];
  
  // PAYPAL EMAIL
  $sql = "UPDATE se_gstore_settings SET paypal_email='{$paypal_email}' WHERE gstore_settings_user_id='{$user->user_info[user_id]}'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  // USERSETTINGS
  $sql = "
    UPDATE
      se_usersettings
    SET
      usersetting_notify_gstorecomment='{$usersetting_notify_gstorecomment}'
    WHERE
      usersetting_user_id='{$user->user_info['user_id']}'
    LIMIT
      1
  ";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  
  $user->user_lastupdate();
  $user = new se_user(Array($user->user_info['user_id'])); // HUH?
  $result = TRUE;
}


// GET THIS USER'S gstore SETTINGS
$sql = "SELECT paypal_email FROM se_gstore_settings WHERE gstore_settings_user_id='{$user->user_info['user_id']}' LIMIT 1";
$resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");

if( $database->database_num_rows($resource) )
{ 
  $paypal_settings = $database->database_fetch_assoc($resource); 
}
else
{
  $sql = "INSERT INTO se_gstore_settings (gstore_settings_user_id, paypal_email) VALUES ('{$user->user_info['user_id']}', '')";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  $paypal_settings = array
  (
    'gstore_settings_id'      => $database->database_insert_id(),
    'gstore_settings_user_id' => $user->user_info['user_id'],
    'paypal_email'     => ''
  );
}


// ASSIGN USER SETTINGS
$user->user_settings();


// ASSIGN SMARTY VARIABLES AND DISPLAY gstore STYLE PAGE
$smarty->assign('paypal_email', $paypal_settings['paypal_email']);
$smarty->assign('result', $result);
include "footer.php";
?>