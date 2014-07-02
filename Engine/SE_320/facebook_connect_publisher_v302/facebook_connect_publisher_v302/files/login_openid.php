<?php
$page = "login_openid";
include "header.php";

$task = semods::getpost('task','step1');
$next = semods::getpost('next','user_home.php');
$confirm = semods::getpost('confirm',0);


/*** TRY OPENID LOGIN ***/


$openidservice_name = semods::getpost('openidservice','api');

$openid_user = new se_user_openid($openidservice_name);

// try login via openid
$openid_user->user_login_openid();

// IF USER IS LOGGED IN SUCCESSFULLY, FORWARD THEM TO HOME
if($openid_user->is_error == 0) {
  semods::redirect($next);
}


// USER IS LOGGED IN
if($user->user_exists != 0) {
  
  // see if user has already linked account to this network
  if(se_user_openid::user_openid_is_connected($user->user_info['user_id'], $openid_user->openid_service_id)) {

    $task = "linkerror";

  } else {

  if($task == 'confirmlink') {
    $openid_user->user_openid_link($user->user_info['user_id']);
    $openid_user->user_login_openid();
    semods::redirect($next);
  }
  
  $task = "confirmlink";
  
  }
  
  
  $smarty->assign('task', $task);
  $smarty->assign('next', $next);
  $smarty->assign('openidsession', $openid_user->openidapi->session);
  $smarty->assign('openidservice_name', $openidservice_name);

  
  include "footer.php";
  
  //semods::redirect("user_home.php");
}







/*** NEW USER - QUICK SIGNUP ***/

$openid_service_id = intval($openid_user->openidapi->user_details['openid_service_id']);
$openid_service = semods::db_query_assoc("SELECT * FROM se_semods_openidservices WHERE openidservice_id = $openid_service_id");

// Clear errors & all but openid_user_id
$openid_user->user_clear();
$new_user = &$openid_user;

$openid_signup_mode = semods::get_setting('openidconnect_signupmode',OPENIDCONNECT_SIGNUP_EXPRESS);
$openid_signup_default_profilecat = semods::get_setting('openidconnect_default_profilecat','1');

$signup_cat = semods::getpost('signup_cat',$openid_signup_default_profilecat);
if(semods::db_query_count("SELECT COUNT(*) FROM se_profilecats WHERE profilecat_id='$signup_cat' AND profilecat_dependency='0'") != 1) {
  //$cat_info = $database->database_fetch_assoc($database->database_query("SELECT profilecat_id FROM se_profilecats WHERE profilecat_dependency='0' ORDER BY profilecat_order LIMIT 1"));
  $cat_info = semods::db_query_assoc("SELECT profilecat_id FROM se_profilecats WHERE profilecat_dependency='0' ORDER BY profilecat_order LIMIT 1");
  $signup_cat = $cat_info[profilecat_id];
}

// requried fields - global - timezone, etc
$openid_signup_required_fields = semods::get_setting('openidconnect_signupfields','');
$openid_signup_required_fields = !empty($openid_signup_required_fields)  ? explode(',',$openid_signup_required_fields) : array();

// requried fields - per profile category
$openid_signup_required_fields_percat = semods::db_query_count("SELECT openidreqfield_fields FROM se_semods_openidreqfields WHERE openidreqfield_cat_id = $signup_cat");
$openid_signup_required_fields_percat = !empty($openid_signup_required_fields_percat)  ? explode(',',$openid_signup_required_fields_percat) : array();

$openid_signup_required_fields = array_merge($openid_signup_required_fields, $openid_signup_required_fields_percat);

// check if need username
if($setting['setting_username']) {
  $openid_signup_required_fields[] = "username";
}

// check if need email
//if(($openid_user->openidapi->user_details['email'] == '')) {
  $openid_signup_need_email = true;
  $openid_signup_required_fields[] = "email";
//}




// PREPOPULATE some fields - username (nickname), timezone
$signup_username = semods::g($openid_user->openidapi->user_details,'nickname','');
$signup_timezone = semods::g($openid_user->openidapi->user_details,'timezone','');
$signup_email = semods::g($openid_user->openidapi->user_details,'email','');
$openid_user_displayname = $openid_user->user_openid_displayname();
$openid_user_photo = $openid_user->user_openid_photo();
$openid_user_thumb = $openid_user->user_openid_thumb();


// DEFAULT TIMEZONE
if($signup_timezone == '') {
  $signup_timezone = $setting['setting_timezone'];
}




// if no extra fields are required and express signup and not invite-only, try to do it automatically
if(($openid_signup_mode == OPENIDCONNECT_SIGNUP_EXPRESS) && empty($openid_signup_required_fields) && ($setting['setting_signup_invite'] == 0)) {
  $task = "step1do";
}



if($task != "step1") { $cat_where = "profilecat_signup='1' AND profilecat_id='$signup_cat'"; } else { $cat_where = "profilecat_signup='1'"; }
$field = new se_field("profile");


// SIGNUP NEW USER
if($task == "step1do" || $task == "step2do")  {

  // check if have email
  //if($openid_signup_need_email) {
  //  $signup_email = semods::getpost('signup_email');
  //} else {
  //  $signup_email = $openid_user->openidapi->user_details['email'];
  //}

  $signup_email = semods::getpost('signup_email',$signup_email);

  $signup_username = semods::getpost('signup_username',$signup_username);

  $signup_timezone = semods::getpost('signup_timezone',$signup_timezone);

  $signup_invite = semods::getpost('signup_invite','');

  // GET LANGUAGE PACK SELECTION
  if($setting['setting_lang_allow'] != 1) {
    $signup_lang = 0;
  } else {
    $signup_lang = semods::getpost('signup_lang',0);
  }

  // CHECKING USER ERRORS
  $error_message = "";

  for(;;) {

    $openid_user->user_account($signup_email, $signup_username);
    if($openid_user->is_error) {
      $error_message = $openid_user->is_error;
      break;
    }


    // CHECK INVITE CODE IF NECESSARY
    if($setting['setting_signup_invite'] != 0) {
      
      // try to find in openid invites
      $invitation = semods::db_query_assoc("SELECT *
                                            FROM se_semods_openidinvites
                                            WHERE invite_user_key = '{$openid_user->openid_user_id}'
                                              AND invite_service_id = {$openid_user->openid_service_id}
                                            LIMIT 1");

      if(empty($invitation)) {
      
        //if($setting[setting_signup_invite_checkemail] != 0) {
        //  $invite = $database->database_query("SELECT invite_id FROM se_invites WHERE invite_code='$signup_invite' AND invite_email='$signup_email'");
          //$invite_error_message = $signup[5];
          //$error_message = $signup[5];
          //break;
        //} else {
          //$invite = $database->database_query("SELECT invite_id FROM se_invites WHERE invite_code='$signup_invite'");
          $invitation = semods::db_query_assoc("SELECT * FROM se_invites WHERE invite_code='$signup_invite' LIMIT 1");
          //$invite_error_message = $signup[6];
          //$invite_error_message = "Incorrect invitation code";
          //$error_message = "Incorrect invitation code";
          //break;
        //}
        //if($database->database_num_rows($invite) == 0) {
        if(empty($invitation)) {
          //$is_error = 1;
          $error_message = "Incorrect invitation code";
          break;
          //$error_message = $invite_error_message;
        }
      }
      
    }


    // CHECK TERMS OF SERVICE AGREEMENT IF NECESSARY
    if($setting[setting_signup_tos] != 0) {
      $signup_agree = $_POST['signup_agree'];
      if($signup_agree != 1) {
        //$is_error = 707;
        $error_message = 707;
        break;
      }
    }



      /* STEP 2 START */



      // data portability - map imported fields
      $openid_user->map_imported_fields($signup_cat);

      if(($task == "step1do") || ($task == "step2do") || ($openid_signup_mode == OPENIDCONNECT_SIGNUP_EXPRESS)) {
        $validate = 1;
      } else {
        $validate = 0;
      }

      if($openid_signup_mode == OPENIDCONNECT_SIGNUP_EXPRESS) {
        $field->cat_list($validate, 0, 0, $cat_where, "", "");
      } else {
        $field->cat_list($validate, 0, 0, $cat_where, "", "profilefield_signup='1'");
      }



      // IF NOT EXPRESS SIGNUP - go to step2
      if(($task == "step1do") && ($openid_signup_mode == OPENIDCONNECT_SIGNUP_REGULAR)) {
        $task = "step2";
        break;
      }


      $field_error_verified = true;

      // IF EXPRESS SIGNUP - strip down unneeded fields and verify if there is a real error
      if($openid_signup_mode == OPENIDCONNECT_SIGNUP_EXPRESS) {
        $field_error_verified = false;

        foreach($field->cats[0]['subcats'] as $subcat_key => $fieldcats) {
          $subcat_openid_required = 0;

          foreach($fieldcats['fields'] as $field_key => $field_x) {

            // in our list ?
            if(in_array($field_x['field_id'], $openid_signup_required_fields)) {
              $subcat_openid_required = 1;
              $field_x['field_openid_required'] = 1;

              // if there's an error, try to verify it
              // TBD: if got "field required error" and also another error (from our field), can errorid be overwritten?
              if($field->is_error && ($field_x['field_error'] !== 0)) {

                if($field->is_error == 96) {
                   if($field_x['field_required'] != 0) {
                    $field_error_verified = true;
                   }
                } else if($field->is_error == 97) {
                  if($field_x['field_regex'] != "") {
                    $field_error_verified = true;
                  }
                } else {
                  $field_error_verified = true;
                }

              }

            } else {
              $field_x['field_openid_required'] = 0;
            }

            $fieldcats['fields'][$field_key] = $field_x;
          }

          $fieldcats['subcat_openid_required'] = $subcat_openid_required;

          // php4
          //if(count($fieldcats['fields']) > 0) {
            $field->cats[0]['subcats'][$subcat_key] = $fieldcats;
          //} else {
          //  unset($field->cats[0]['subcats'][$subcat_key]);
          //}

        }

        // php4

      }


      $cat_array = $field->cats;
      //if($validate == 1) { $is_error = $field->is_error; }
      if($field->is_error && $field_error_verified) {

        // if express/regular signup and step1 and got error => go to step2 to fill the req fields.
        //if($task == "step1do" && ($openid_signup_mode == OPENIDCONNECT_SIGNUP_EXPRESS)) {
        if($task == "step1do") {
          $task = "step2";
          break;
        }



        $error_message = $field->is_error;
        break;




      } else {
        $field->is_error = 0;
      }




      // IF ERRORS ON STEP2
      if(($task == "step2do") && $field->is_error && ($openid_signup_mode == OPENIDCONNECT_SIGNUP_REGULAR)) {
        $task = "step2";
        break;
      }




      // CREATE ACCOUNT

      // if regular signup, reload all fields for mapping
      if($openid_signup_mode == OPENIDCONNECT_SIGNUP_REGULAR) {
        $field = new se_field("profile");
        $field->cat_list(1, 0, 0, $cat_where, "", "");
      }

        $openid_user->user_create_from_openid( $signup_email, $signup_username, $signup_timezone, $signup_lang, $signup_cat, $field->field_query );

        // INVITE CODE FEATURES
        if($setting[setting_signup_invite] != 0) {
//          if($setting[setting_signup_invite_checkemail] != 0) {
//            $invitation = $database->database_fetch_assoc($database->database_query("SELECT * FROM se_invites WHERE invite_code='$signup_invite' AND invite_email='$signup_email' LIMIT 1"));
//          } else {
//            $invitation = $database->database_fetch_assoc($database->database_query("SELECT * FROM se_invites WHERE invite_code='$signup_invite' LIMIT 1"));
//          }

          // ADD USER TO INVITER'S FRIENDLIST
          $friend = new se_user(Array($invitation[invite_user_id]));
          if($friend->user_exists == 1) {

            if($setting[setting_connection_allow] == 3 || $setting[setting_connection_allow] == 1 || ($setting[setting_connection_allow] == 2 && $new_user->user_info[user_subnet_id] == $friend->user_info[user_subnet_id])) {
              // SET RESULT, DIRECTION, STATUS
              switch($setting[setting_connection_framework]) {
                case "0":
                  $direction = 2;
                  $friend_status = 0;
                  break;
                case "1":
                  $direction = 1;
                  $friend_status = 0;
                  break;
                case "2":
                  $direction = 2;
                  $friend_status = 1;
                  break;
                case "3":
                  $direction = 1;
                  $friend_status = 1;
                  break;
              }

              // INSERT FRIENDS INTO FRIEND TABLE AND EXPLANATION INTO EXPLAIN TABLE
              $friend->user_friend_add($new_user->user_info[user_id], $friend_status, '', '');

              // IF TWO-WAY CONNECTION AND NON-CONFIRMED, INSERT OTHER DIRECTION
              if($direction == 2 && $friend_status == 1) { $new_user->user_friend_add($friend->user_info[user_id], $friend_status, '', ''); }
            }
          }


          // DELETE INVITE CODE
          // Openid table
          if(!is_null(semods::g($invitation,'invite_user_key'))) {
            $database->database_query("DELETE FROM se_semods_openidinvites WHERE invite_id='{$invitation[invite_id]}' LIMIT 1");
          } else {
            $database->database_query("DELETE FROM se_invites WHERE invite_id='{$invitation[invite_id]}' LIMIT 1");
          }

        }



      // IF EXPRESS SIGNUP - go home
      if($openid_signup_mode == OPENIDCONNECT_SIGNUP_EXPRESS) {
        
        // Login user and redirect to home
        $openid_user->user_login_openid();

        $signup_landing_page = $openid_user->get_signup_landing_page();
        if($signup_landing_page != '') {
          $next = $signup_landing_page;
        }
        
        semods::redirect($next);

      } else {

        // SE V3.11+
        
        // SET SIGNUP COOKIE
        $id = $new_user->user_info['user_id'];
        if($version > "3.11") {
          $new_user->user_salt = $new_user->user_info['user_code'];
          $em = $new_user->user_password_crypt($new_user->user_info['user_email']);
        } else {
          $em = crypt($new_user->user_info[user_email], "$1$".$new_user->user_info[user_code]."$");
        }
        $pass = $new_user->user_info['user_password'];
        setcookie("signup_id", "$id", 0, "/");
        setcookie("signup_email", "$em", 0, "/");
        setcookie("signup_password", "$pass", 0, "/");

        // SEND USER TO PHOTO UPLOAD IF SPECIFIED BY ADMIN
        // OR TO USER INVITE IF NO PHOTO UPLOAD
        if($setting[setting_signup_photo] == 0) {
          if($setting[setting_signup_invitepage] == 0) {
            $task = "step5";
          } else {
            $task = "step4";
          }
        } else {
          $task = "step3";
        }

echo <<< EOC
<html><body>
<form action='signup.php' name="signuprerouteform" id="signuprerouteform" method='POST'>
<input type="hidden" name="task" value="$task">
</form>
<script type="text/javascript">
<!--document.onload='function() { document.getElementById("signuprerouteform").submit();}';-->
document.getElementById("signuprerouteform").submit();
</script>
</body>
</html>
EOC;
        exit();



      }


      break;


  }

  if($error_message) {
    $error_message = semods::get_language_text($error_message);
  }

  // landed here on step1 with errors
  if($task == "step1do") {
    $task = "step1";
  }

  // landed here on step2 with errors
  if($task == "step2do") {
    $task = "step2";
  }

}


$hide_signup_invite = false;

if($task == "step1") {

  $validate = 0;

  $field = new se_field("profile");

  if($openid_signup_mode == OPENIDCONNECT_SIGNUP_EXPRESS) {
    $field->cat_list($validate, 0, 0, $cat_where, "", "");
  } else {
    $field->cat_list($validate, 0, 0, $cat_where, "", "profilefield_signup='1'");
  }
  
  // check if need to hide signup code
  if($setting['setting_signup_invite'] != 0) {
    
    // try to find in openid invites
    $invitation = semods::db_query_assoc("SELECT *
                                          FROM se_semods_openidinvites
                                          WHERE invite_user_key = '{$openid_user->openid_user_id}'
                                            AND invite_service_id = {$openid_user->openid_service_id}
                                          LIMIT 1");

    if(!empty($invitation)) {
      $hide_signup_invite = true;
    }
      
  }
  

}




// ASSIGN VARIABLES AND INCLUDE FOOTER
$smarty->assign('hide_signup_invite', $hide_signup_invite);
$smarty->assign('error_message', $error_message);
$smarty->assign('cats', $field->cats);
$smarty->assign('signup_email', $signup_email);
$smarty->assign('signup_username', $signup_username);
$smarty->assign('signup_timezone', $signup_timezone);
$smarty->assign('signup_lang', $signup_lang);
$smarty->assign('signup_invite', $signup_invite);
$smarty->assign('signup_agree', $signup_agree);
$smarty->assign('signup_cat', $signup_cat);
$smarty->assign('lang_packlist', $lang_packlist);

$smarty->assign('task', $task);

$smarty->assign('openid_service', $openid_service);

$smarty->assign('openid_signup_required_fields', $openid_signup_required_fields);

$smarty->assign('openid_user_displayname', $openid_user_displayname);
$smarty->assign('openid_user_photo', $openid_user_photo );
$smarty->assign('openid_user_thumb', $openid_user_thumb);

$smarty->assign('openid_signup_need_email', $openid_signup_need_email);
$smarty->assign('openid_signup_mode', $openid_signup_mode);

$smarty->assign('openidsession', $openid_user->openidapi->session);
$smarty->assign('openidservice_name', $openidservice_name);

include "footer.php";
?>