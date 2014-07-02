<?php
ob_start();
$page = "vid_request";
include "header.php";

if (isset($_GET['task'])) {
    if ($_GET['task'] == 'simple') {
        ob_end_clean();
        $data = "<div id='file_data2' name='file_data2'><b>" . SE_Language::_get(13500087) . "</b><br>" .
                "<input type='hidden' name='simple' id='simple' value='true'></div>" .
                "<input type='file' name='file' id='file' size='43'></div>";
        echo $data;
        exit();
    } elseif ($_GET['task'] == 'ok' && isset($_GET['id'])) {
        $database->database_query("DELETE FROM se_vids WHERE vid_location='".$_GET['id']."' AND vid_user_id='".$user->user_info['user_id']."'");
        ob_end_clean();
        echo "";
        exit();
    } elseif ($_GET['task'] == 'dosend') {
        if (isset($_GET['return_url']) && $_GET['return_url'] != "" && isset($_GET['report_reason']) && $_GET['report_reason'] != "" && isset($_GET['report_details']) && $_GET['report_details'] != "") {
           $return_url = $_GET['return_url'];
           $report_reason = $_GET['report_reason'];
           $report_details = $_GET['report_details'];
           // ADD TO DATABASE
           $database->database_query("INSERT INTO se_reports (report_user_id, 
				       report_url, 
				       report_reason, 
				       report_details) VALUES (
				      '{$user->user_info['user_id']}',
				      '{$return_url}', 
				      '{$report_reason}', 
				      '{$report_details}')");

           // IF DATABASE HAS OVER 5000 REPORTS, CLEAN THEM OUT
           $reports_total = $database->database_num_rows($database->database_query("SELECT report_id FROM se_reports"));
           if($reports_total > 5000)
           {
             $database->database_query("DELETE FROM se_reports WHERE report_id ORDER BY report_id ASC LIMIT 100");
           }

           $data = "<div style=\"float: left;\">".SE_Language::_get(866)."</div>";
        } else {
           $data = "<div style=\"float: left;\">".SE_Language::_get(13500180)."</div>";
        }
        ob_end_clean();
        echo $data;
        exit();
    } elseif ($_GET['task'] == 'addtofav' && isset($_GET['id'])) {
        if ($user->user_exists) {
           $if_exists = $database->database_query("SELECT vid_user_id FROM se_vids WHERE vid_id = '".$_GET['id']."'");
           if ($database->database_num_rows($if_exists) == 1) {
              $if_exists = $database->database_query("SELECT vidfav_ids FROM se_vidfavs WHERE vidfav_user_id = '".$user->user_info['user_id']."' AND vidfav_ids LIKE '%,".$_GET['id']."%' LIMIT 1");
              if ($database->database_num_rows($if_exists) == 1) {
                  ob_end_clean();
                  echo "<div style=\"float: left; padding-right: 5px;\">".SE_Language::_get(13500181)."</div><div style=\"float: right;\"><a href=\"javascript:void(0);\" onClick=\"RemoveFav()\">".SE_Language::_get(13500182)."</a></div>";
                  exit();
              } else {
                  $if_exists = $database->database_query("SELECT vidfav_ids FROM se_vidfavs WHERE vidfav_user_id = '".$user->user_info['user_id']."' LIMIT 1");
                  if ($database->database_num_rows($if_exists) == 1) {
                      $database->database_query("UPDATE se_vidfavs SET vidfav_ids = CONCAT(vidfav_ids, ',".$_GET['id']."') WHERE vidfav_user_id='".$user->user_info['user_id']."'");
                  } else {
                      $database->database_query("INSERT INTO se_vidfavs (vidfav_user_id, vidfav_ids) VALUES ('".$user->user_info['user_id']."', ',".$_GET['id']."')");
                  }
                  ob_end_clean();
                  echo "<div style=\"float: left; padding-right: 5px;\">".SE_Language::_get(13500183)."</div><div style=\"float: right;\"><a href=\"javascript:void(0);\" onClick=\"RemoveFav()\">".SE_Language::_get(13500182)."</a></div>";
                  exit();
              }
           } else {
              ob_end_clean();
              echo "<div style=\"float: left;\">".SE_Language::_get(13500184)."</div>";
              exit();
           }
        } else {
           ob_end_clean();
           echo "<div style=\"float: left;\">".SE_Language::_get(13500187)." <a href=\"login.php\">".SE_Language::_get(30)."</a> ".SE_Language::_get(13500185)." <a href=\"signup.php\">".SE_Language::_get(650)."</a> ".SE_Language::_get(13500186)."</div>";
           exit();
        }
    } elseif ($_GET['task'] == 'remfav' && isset($_GET['id'])) {
        $if_exists = $database->database_query("SELECT vid_user_id FROM se_vids WHERE vid_id = '".$_GET['id']."'");
        if ($database->database_num_rows($if_exists) == 1) {
           $if_exists = $database->database_query("SELECT vidfav_ids FROM se_vidfavs WHERE vidfav_user_id = '".$user->user_info['user_id']."' AND vidfav_ids LIKE '%,".$_GET['id']."%' LIMIT 1");
           if ($database->database_num_rows($if_exists) == 1) {
               $database->database_query("UPDATE se_vidfavs SET vidfav_ids = REPLACE(se_vidfavs.vidfav_ids, ',".$_GET['id']."', '') WHERE vidfav_user_id = '".$user->user_info['user_id']."'");
               ob_end_clean();
               echo "<div style=\"float: left; padding-right: 5px;\">".SE_Language::_get(13500188)."</div><div style=\"float: right;\"><a href=\"javascript:void(0);\" onClick=\"AddToFav()\">Undo</a></div>";
               exit();
           } else {
               ob_end_clean();
               echo "<div style=\"float: left;\">".SE_Language::_get(13500189)."</div>";
               exit();
           }
        } else {
           ob_end_clean();
           echo "<div style=\"float: left;\">".SE_Language::_get(13500184)."</div>";
           exit();
        }
    } else {
        ob_end_clean();
        exit();
    }
} else {
    ob_end_clean();
    exit();
}
?>