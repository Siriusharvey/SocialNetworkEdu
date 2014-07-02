<?php
  $secure = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 1 : 0;
  if($secure) {
    $script_src = "https://www.connect.facebook.com/js/api_lib/v0.4/XdCommReceiver.js";
  } else {
    $script_src = "http://static.ak.connect.facebook.com/js/api_lib/v0.4/XdCommReceiver.js";
  }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml" >
 <body> 
 <script src="<?php echo $script_src ?>" type="text/javascript"></script> 
 </body>
 </html>