<?php



ob_start();
$page = "gstore_ajax";
include "header.php";


// PROCESS INPUT
$task           = ( !empty($_POST['task'])          ? $_POST['task']          : ( !empty($_GET['task'])           ? $_GET['task']           : NULL ) );
$gstore_id  = ( !empty($_POST['gstore_id']) ? $_POST['gstore_id'] : ( !empty($_GET['gstore_id'])  ? $_GET['gstore_id']  : NULL ) );



// DELETE
if( $task=="deletegstore" )
{
  $gstore = new se_gstore($user->user_info['user_id']);
  
  // OUTPUT
  ob_end_clean();
  
  if( $user->user_exists && $gstore_id && $gstore->gstore_delete($gstore_id) )
    echo '{"result":"success"}';
  else
    echo '{"result":"failure"}';
  
  exit();
}

?>