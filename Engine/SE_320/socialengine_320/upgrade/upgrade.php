<?php

/* $Id: upgrade.php 207 2009-08-07 01:54:51Z john $ */

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "step0"; }
if(isset($_POST['license'])) { $license = $_POST['license']; } else { $license = 0; }
$ignore = !empty($_REQUEST['ignore']);

include "./include/database_config.php"; 
include "./include/class_database.php";
$database = new se_database($database_host, $database_username, $database_password, $database_name);


// SET EMPTY VARS
$result = "";
$success = 0;
$remote_stats = ( isset($_POST['remotestats']) ? (bool) $_POST['remotestats'] : TRUE );

// SET ERROR REPORTING
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);



echo "
<html>
<head>
<title>Upgrade SocialEngine</title>
<style type='text/css'>
body
{
  background-color: #eeeeee;
  text-align: center;
}

body, td, div
{
	font-family: \"Trebuchet MS\", tahoma, verdana, arial, serif;
	font-size: 10pt;
	color: #333333;
	line-height: 16pt;
}

table.install_body_table
{
  width: 775px;
	border: 1px solid #BBBBBB;
}

td.install_body
{
  background-color: #ffffff;
  padding: 25px;
}

h2
{
	font-size: 16pt;
	margin-bottom: 4px;
}

table.box { width: 100%; border: 1px dashed #BBBBBB; }
div.box { border: 1px dashed #BBBBBB; }
td.box, div.box
{
	padding: 10px 13px 10px 13px;
}

.box2
{
	padding: 10px 13px 10px 13px;
  background: #eff8ff;
}

ul
{
	margin-top: 2px;
	margin-bottom: 2px;
}

input.text
{
	font-family: \"Trebuchet MS\", tahoma, verdana, arial, serif;
}

input.button
{
	background: #EEEEEE;
	font-weight: bold;
	padding: 2px;
	font-family: \"Trebuchet MS\", tahoma, verdana, arial, serif;
}

form
{
	margin: 0px;
}

a:link { color: #2078C8; text-decoration: none; }
a:visited { color: #2078C8; text-decoration: none; }
a:hover { color: #3FA4FF; text-decoration: underline; }

/* sanity */
.sanityBody
{
  width: 100%;
}

.sanityCategoryTitleRow
{
  width: 100%;
}

.sanityCategoryTitleBox
{
  padding-bottom: 6px;
	font-size: 12pt;
  font-weight: bold;
}

.sanityCategoryBodyRow
{
  width: 100%;
}

.sanityCategoryBodyBox
{
  
}

.sanityCategoryBody
{
  width: 100%;
  padding-bottom: 10px;
}

.sanityTestRow
{
  width: 100%;
}

.sanityTestRow td
{
	line-height: normal;
}

.sanityTestRowAlt td
{
  background-color: #f5f5f5;
}

.sanityTestTitle
{
  text-align: left;
  padding: 3px;
  padding-left: 5px;
}

.sanityTestValue
{
  text-align: right;
  padding: 1px;
}

td.sanityTestIconBox
{
  width: 16px;
  padding: 1px;
  padding-left: 4px;
  padding-right: 5px;
}

.sanityTestMessageRow
{
  width: 100%;
}

.sanityTestMessageBox
{
	font-family: \"Trebuchet MS\", tahoma, verdana, arial, serif;
	font-size: 8pt;
	color: #333333;
	line-height: normal;
  padding-left: 15px;
  padding-bottom: 2px;
	color: #888888;
}


</style>
</head>
<body>
<table class='install_body_table' cellpadding='0' cellspacing='0' align='center'><tr><td class='install_body'>
";



if($task != "step0")
{
  $status = ""; 
  if( !preg_match("/^[0-9]{4}[-]{1}[0-9]{4}[-]{1}[0-9]{4}[-]{1}[0-9]{4}?$/", $license) )
  { 
    $status = "failure"; 
  }
  elseif( substr($license,10,1)*substr($license,11,1)*substr($license,12,1)*substr($license,13,1) != substr($license,15,4) )
  { 
    $status = "failure"; 
  }
  else
  { 
    $status = "success"; 
  } 
  
  if($status == "failure")
  { 
    $task = "step0"; 
    $status = "failure"; 
  }
}






// STEP 0
if($task == "step0")
{
  include "./include/sanity/sanity.php";
  include "./include/sanity/common.php";

  $sanity =& SESanityCommon::load();
  
  unset($sanity->tests['permission_include']);
  
  $sanity->execute();

  $test_critical = $sanity->isCritical();
  $test_categories = $sanity->getCategories();

  
  echo "
  <h2>Upgrade SocialEngine</h2>
  Welcome to the SocialEngine upgrade program. To upgrade your version of SocialEngine, click the button below. If you have questions about the upgrade process or SocialEngine in general, get in touch with us at <a href='http://www.socialengine.net'>socialengine.net</a>.
  <br><br>
  <div class='box2'>
  <b>If you are upgrading SocialEngine yourself:</b>
  <br>
  Before continuing, please be sure you have reviewed the upgrade instructions provided in upgrade.html.
  </div>
  <br>
  ";
  
  ?>
  <table class="box" cellpadding='0' cellspacing='0'><tr><td class="box">
  <table class="sanityBody" cellpadding='0' cellspacing='0'>
  <?php foreach( $test_categories as $category_name=>$category_testnames ) { $test_alt_counter = FALSE; ?>
    <tr class="sanityCategoryTitleRow">
      <td class="sanityCategoryTitleBox">
        <?php echo ( isset($sanity->categories[$category_name]['lang_title']) ? $sanity->categories[$category_name]['lang_title'] : ucfirst($category_name) ); ?>
      </td>
    </tr>
    <tr class="sanityCategoryBodyRow">
      <td class="sanityCategoryBodyBox">
        <table class="sanityCategoryBody" cellpadding='0' cellspacing='0'>
        <?php foreach( $category_testnames as $test_name ) { ?>
          <tr class="sanityTestRow<?php if( ($test_alt_counter = !$test_alt_counter) ) echo " sanityTestRowAlt"; ?>">
            <td class="sanityTestTitle">
              <?php echo $sanity->tests[$test_name]->getTitle(); ?>
            </td>
            <td class="sanityTestValue">
              <?php echo $sanity->tests[$test_name]->getValue(); ?>
            </td>
            <td class="sanityTestIconBox">
              <?php if( $sanity->tests[$test_name]->result ) { ?>
                <img class="sanityTestIcon" alt="Success" src="./images/success.gif" />
              <?php } elseif( $sanity->tests[$test_name]->is_recommendation ) { ?>
                <img class="sanityTestIcon" alt="Recommendation" src="./images/icons/tip.gif" />
              <?php } elseif( !$sanity->tests[$test_name]->is_critical ) { ?>
                <img class="sanityTestIcon" alt="Warning" src="./images/alert.gif" />
              <?php } else { ?>
                <img class="sanityTestIcon" alt="Error" src="./images/error.gif" />
              <?php } ?>
            </td>
          </tr>
        <?php if( !$sanity->tests[$test_name]->result ) {  ?>
          <tr class="sanityTestMessageRow<?php if( $test_alt_counter ) echo " sanityTestRowAlt"; ?>">
            <td colspan="2" class="sanityTestMessageBox">
              <?php if( $custom_message = $sanity->tests[$test_name]->getCustomMessage() ) echo $custom_message."<br />"; ?>
              <?php echo $sanity->tests[$test_name]->getMessage(); ?>
            </td>
            <td>&nbsp;</td>
          </tr>
        <?php } ?>
        <?php } ?>
        </table>
      </td>
    </tr>
  <?php } ?>
  </table>
  </td></tr></table>
  <?php

  if($status == "failure")
  {
    echo "<br><table cellpadding='0' cellspacing='0'><tr><td style='padding: 5px; background: #FFEFEF; color: #AB0000;'>You provided an invalid license key.</td></tr></table>";
  }
  
  if( $test_critical )
  {
    echo "
      <br>
      <table cellpadding='0' cellspacing='0'>
        <tr>
          <td style='padding: 5px; background: #FFEFEF; color: #AB0000;'>
            Please correct the above problems before continuing with the install.
            You can refer to the install.html provided with SocialEngine, the
            <a href='http://www.socialengine.net/faq.php'>FAQ</a> on our site, or our technical support.
          </td>
        </tr>
      </table>
    ";
  }

  echo "
  <br>
  <form action='upgrade.php' method='post'>
  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td style='padding: 20px; border: 1px solid #CCCCCC; background: #F1F1F1;'>
    
    <b>Enter License Key:</b><br>
    <input type='text' class='text' size='40' name='license'>
    <br />
    <br />
    
    <b>Allow us to collect information about your server environment?</b><br />
    <div style='margin-bottom:10px;'>
      With your permission, we would like to collect some information about your server to help us improve SocialEngine
      in the future. The exact information we will collect is:
      <ol>
        <li>PHP version and list of extensions</li>
        <li>MySQL version</li>
        <li>Web-server type and version</li>
        <li>SocialEngine version</li>
      </ol>
      This information will NOT be shared with any third party and will only be used by our development team as we build
      new modules. If you do not wish to send this information, please select \"no\" below. We sincerely appreciate your support!
    </div>
    
    <table>
      <tr>
        <td>
          <input type='radio' name='remotestats' value='1'".( $remote_stats ? ' checked' : '')." />
        </td>
        <td>
          <label>Yes, allow information to be reported.</label>
        </td>
      </tr>
      <tr>
        <td>
          <input type='radio' name='remotestats' value='0'".( !$remote_stats ? ' checked' : '')." />
        </td>
        <td>
          <label>No, do not allow information to be reported.</label>
        </td>
      </tr>
    </table>
    
  </td>
  </tr>
  </table>

  <br />
  <input type='submit' class='button' value='Continue...' "; if( $test_critical && !$ignore ) echo " disabled"; echo " />
  <input type='hidden' name='task' value='step1'>
  </form>
  ";
}








if($task == "step1")
{
  // RUN UPGRADE MYSQL QUERIES
  include "upgradesql.php";

  echo "
  <h2>Upgrade Complete</h2>
  You have successfully completed the SocialEngine upgrade process.
  <br><br>
  <div class='box'>
    If you are upgrading from 3.06 or earlier, you must now run the stand-alone pm system upgrade script. <a href=\"upgrade_pms.php\">Click here if you already uploaded \"upgrade_pms.php\".</a>
  </div>
  <br>
  <ul>
  <li><b>Important: You must now delete \"upgrade.php\" and \"upgradesql.php\" from your server. Failing to delete these files is a serious security risk!</b></li>
  </ul>
  ";
}





echo "
</body>
</html>
";


?>