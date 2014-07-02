<?php

/* $Id: video_server_test.php 13 2009-01-11 06:04:29Z john $ */


/**
* VIDEO SERVER TEST
* This test script verifies that you are able to run the video plugin on your server.
* 
*
* Upload this file to your server and execute it. You will be guided through the server test.
*
*/

?>
<html>
<head>
	<title></title>
</head>
<body>

	<h1>Video Plugin Server Test</h1>

<?php

	if( !isset($_GET['ffmpeg_path']) )
  {
		// show form
		
    if( ini_get('safe_mode') )
    {
			// exec is disabled on this server
			echo 'The php directive "safe_mode" disables "exec" [<a href="http://www.php.net/manual/en/function.exec.php" target="_blank">http://www.php.net/manual/en/function.exec.php</a>].
				  <br><br>Please enable this function by setting "safe_mode" to off in your php.ini file';
			
			exit;
    }
    
		if( in_array('exec', explode(',', ini_get('disable_functions'))) )
    {
			// exec is disabled on this server
			echo 'The php function "exec" [<a href="http://www.php.net/manual/en/function.exec.php" target="_blank">http://www.php.net/manual/en/function.exec.php</a>] is disabled on this server.
				  <br><br>Please enable this function by editing the "disable_functions" variable in your php.ini file';
			
			exit;
		}
    
		$ffmpeg_path = '';
		$error = false;
	}
  
	else
  {
		// verify that ffmpeg is installed
		$ffmpeg_path = escapeshellcmd(strip_tags($_GET['ffmpeg_path']));
		$error = false;
		
		$result = null;
		exec($ffmpeg_path.' -version 2>&1', $result);
		
		if(empty($result) || !isset($result[0]) || !strstr($result[0], 'FFmpeg'))
    {
			$error = true;			
		}
		else
    {
			echo 'Your server has all of the video plugin requirements.';
			exit;
		}
	}

?>	



	Please specify the full path to your FFmpeg installation:<br>
	(e.g. /usr/local/bin/ffmpeg)<br>
	
	<form name='install_video_plugin' action='video_server_test.php' method='get'>
	
	<input type="text" name="ffmpeg_path" class="text" size="60" value="<?=$ffmpeg_path;?>">
	<? if($error) { ?>
		<br><strong style="color:#ff0000">The FFmpeg installation could not be found. Try again.</strong>
	<? } ?>
	
	<br><br>
	
	<input type="submit" value="Find FFmpeg installation">
	
	</form>

<br><br><br>

<a href="http://socialengine.net" target="_blank">SocialEngine.net</a>

</body>
</html>