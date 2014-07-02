<?php
$page = "admin_install_vid";
include "admin_header.php";

if(!isset($_GET['ffmpeg_path']) OR !isset($_GET['flvtool2_path'])) {
		// show form
		
		if(in_array('exec', explode(',', ini_get('disable_functions')))) {
			// exec is disabled on this server
			$smarty->assign('result', 'The php function "exec" [<a href="http://www.php.net/manual/en/function.exec.php" target="_blank">http://www.php.net/manual/en/function.exec.php</a>] is disabled on this server.<br><br>Please enable this function by editing the "disable_functions" variable in your php.ini file');
		}

} else {

		// verify that ffmpeg is installed
		$ffmpeg_path = escapeshellcmd(strip_tags($_GET['ffmpeg_path']));
		
                $is = false;
		$result = null;
		exec($ffmpeg_path.' -version', $result);
		
		if(empty($result) || !isset($result[0]) || !strstr($result[0], 'FFmpeg')) {
                        $is = false;
			$smarty->assign('result', 'The FFmpeg installation could not be found. Try again.');
		} else {
                        $is = true;
                }


		// verify that ffmpeg is installed
		$flvtool2_path = escapeshellcmd(strip_tags($_GET['flvtool2_path']));
		
		$result = null;
		exec($flvtool2_path.' -H', $result2);
		
		if(empty($result2) || !isset($result2[0]) || !strstr($result2[0], 'FLVTool2')) {
                        $is = false;
			$smarty->assign('result2', 'The FLVTool2 installation could not be found. Try again.');
		} else {
                        $is = true;
                }
}

if ($is == true) { $smarty->assign('is', 'Your server has got all All-in-one Video Plugin requirements.'); }

$smarty->assign('flvtool2_path', $flvtool2_path);
$smarty->assign('ffmpeg_path', $ffmpeg_path);
include "admin_footer.php";
?>