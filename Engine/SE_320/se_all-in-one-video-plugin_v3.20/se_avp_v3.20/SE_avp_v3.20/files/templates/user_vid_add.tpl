{include file='header.tpl'}

{if $task == 'youtube'}

{literal}
<script type="text/javascript">
<!--
document.onmousemove = Check;

function Check() {
	if ($('file2').value != '') {
	    $('yt').style.display='inline';
            $('url').value=$('file2').value;
	} else {
	    $('yt').style.display='none';
        }
}
//-->
</script>

<script type="text/javascript">
<!-- 
window.addEvent('domready', function() {
    $('file2').addEvent('keyup', function(e){
	Check();
    });
    $('file2').addEvent('mousemove', function(e){
	Check();
    });
    $('file2').addEvent('mouseout', function(e){
	Check();
    });
});
//-->
</script>
{/literal}



<table cellpadding='0' cellspacing='0' width='100%'>
<tr>
<td valign='top'>
<div style='float:left;width:700px;'>
  <div>
  <div class='page_header'>{lang_print id=13500084}</div>
  <div>{lang_print id=13500168}</div>
  </div>
</div>
<div style='float:right;margin-top:10px;'>
  <table cellpadding='0' cellspacing='0' width='130'>
  <tr><td class='button' nowrap='nowrap'><a href='user_vid.php'><img src='./images/icons/back16.gif' border='0' class='button'>Back to Videos</a></td></tr>
  </table>
</div>  
</td>
</tr>
</table>

<div style='margin-top:20px;margin-bottom:20px; background:#F9F9F9;border:1px dashed #CCCCCC; padding:6px;'>{lang_sprintf id=13500100 1=$allowed_providers}</div>

{if $user->level_info.level_vid_allow != 4}
  {if ($user->level_info.level_vid_allow == 3 OR $user->level_info.level_vid_allow == 1) AND $count_vids < $user->level_info.level_vid_maxnum}

  <div style='margin-bottom: 20px;'>
  <table cellpadding='0' cellspacing='0'>
    <tr>
      <td>
        <div><img src="./images/icons/bulb16.gif" class='button' /><a href="user_vid_add.php?task=upload">{lang_print id=13500133}</a></div>
      </td>
    </tr>
  </table>
  <div style='clear: both; height: 0px;'></div>
  </div>
  {/if}
{/if}



{* START LEFT COLUMN *}
<div style='float:left;width:400px;'>

  <form action='user_vid.php' id='vid_data' class='vid_data' method='POST' enctype="multipart/form-data" onSubmit="return VidValidate('file2');">
  <b>*{lang_print id=13500024}</b><br>
  <input name='vid_title' id='vid_title' type='text' maxlength='80' size='30' value='{$title}' class="text">

  <br><br>

  <b>*{lang_print id=13500025}</b><br>
  <textarea name='vid_desc' id='vid_desc' rows='6' cols='50' class="text">{$description}</textarea>

  <br><br>

{* IF THERE ARE NO CATEGORIES, HIDE SELECT*}
{if $count_cats != 0}
  <b>{lang_print id=13500086}</b><br>
  <select name='vid_cat'>
  {section name=num loop=$all_cats}
       <option value='{$all_cats[num].id}'>{$all_cats[num].title}</option>
  {/section}
  </select>

  <br />
  <br />
{/if}

  <b>*{lang_print id=13500026}</b><br>
  <textarea name='vid_tags' id='vid_tags' rows='6' cols='50' class="text">{$tags}</textarea>

  <br />
  <br />
  
  {* SHOW SEARCH PRIVACY OPTIONS IF ALLOWED BY ADMIN *}
  {if $user->level_info.level_vid_search == 1}
    <b>{lang_print id=13500146}</b><br>
    <table cellpadding='0' cellspacing='0'>
      <tr>
        <td><input type='radio' name='vid_search' id='vid_search_1' value='1' CHECKED></td>
        <td><label for='vid_search_1'>{lang_print id=13500147}</label></td>
      </tr>
      <tr>
        <td><input type='radio' name='vid_search' id='vid_search_0' value='0'></td>
        <td><label for='vid_search_0'>{lang_print id=13500148}</label></td>
      </tr>
    </table>
    <br />
  {/if}

  {* SHOW PRIVACY OPTIONS IF ALLOWED BY ADMIN *}
  {if $privacy_options|@count > 1}
    <b>{lang_print id=13500149}</b><br>
    <table cellpadding='0' cellspacing='0'>
      {foreach from=$privacy_options name=privacy_loop key=k item=v}
      <tr>
        <td><input type='radio' name='vid_privacy' id='privacy_{$k}' value='{$k}' {if $smarty.foreach.privacy_loop.first}CHECKED{/if}></td>
        <td><label for='privacy_{$k}'>{lang_print id=$v}</label></td>
      </tr>
      {/foreach}
    </table>
    <br />
  {/if}

  {* SHOW COMMENT OPTIONS IF ALLOWED BY ADMIN *}
  {if $comment_options|@count > 1}
    <b>{lang_print id=13500150}</b><br>
    <table cellpadding='0' cellspacing='0'>
    {foreach from=$comment_options name=comment_loop key=k item=v}
      <tr>
      <td><input type='radio' name='vid_comments' id='comments_{$k}' value='{$k}' {if $smarty.foreach.comment_loop.first}CHECKED{/if}></td>
      <td><label for='comments_{$k}'>{lang_print id=$v}</label></td>
      </tr>
    {/foreach}
    </table>
    <br />
  {/if}

  <div id='file2_data' style='display: inline;' name='file2_data'><b>{lang_print id=13500088}</b><br>
  <input type='text' class="text" maxlength='100' size='64' name='file2' id='file2' value='{$location}'></div>
  <div>
      <table cellpadding='0' cellspacing='0'>
      <tr><td>
  {section name=array loop=$providers_array}
  {if $smarty.section.array.first}{lang_print id=13500135}&nbsp;</td><td>{$providers_array[array]}</td></tr>{/if}
   {if !$smarty.section.array.first}<tr><td>&nbsp;</td><td>{$providers_array[array]}</td></tr>{/if}
  {/section}
      </table>
  </div>

<div id='notvalid' name='notvalid' style='display: none'>
<br>
  <table cellpadding='0' cellspacing='0'>
  <tr><td class='result'>
      <div id='error' name='error' class='error'><img src='./images/error.gif' border='0' class='icon'>{lang_print id=13500128}</div>
  </td></tr></table>
</div>

<br>

  <div style='clear: both;'>
  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td>
    <input type='submit' name='sub' id='sub' class='button' value='{lang_print id=13500089}'>&nbsp;
    <input type='hidden' name='task' id='task' value='add_vid_youtube'>
  </form>
  </td>
  <td>
    <form action='user_vid.php' method='GET'>
    <input type='submit' class='button' value='{lang_print id=13500090}'>&nbsp;
    </form>
  </td>
  <td style='padding-right: 5px;'>
    <form action='user_vid_add.php?task=youtube' method='POST'>
    <input type='submit' name='yt' id='yt' style='display: none;' class='button' value='{lang_print id=13500127}'>
    <input type='hidden' name='url' id='url' value=''>
    </form>
  </td>
  <td>
    <div id='bar' name='bar' style='visibility: hidden;'>&nbsp;<img src='./images/icons/vid_uploading_bar.gif'></div>
  </td>
  </tr>
  </table>
  </div>
  
</div>
{* END LEFT COLUMN *}

{if $providers_img_array}
{* START RIGHT COLUMN *}
<div style='float:left;width:500px;text-align:center;'>
<div class='page_header' style='margin-bottom:20px;'>{lang_print id=13500166}</div>
<div style='padding-left: 30px;'>
<table width='420px'>
{section name=array2 loop=$providers_img_array}
{cycle name='1' values='<tr>,'}
<td style='width:210px;'>
  <a href='{$providers_url_array[array2]}' target='_blank'><img src='{$providers_img_array[array2]}' border='0'></a>
</td>
{if $smarty.section.array2.last}
{if $int}
<td style='width:210px;'>
&nbsp;
</td>
</tr>
{/if}
{/if}
{cycle name='2' values=',</tr>'}
{/section}
</table>
</div>
<div class='page_header' style='clear:both;padding-top:20px;color:#999999;'>{lang_print id=13500167}</div>     
</div>
{/if}
{* END RIGHT COLUMN *}

{elseif $task == 'upload'}

{literal}
<script type="text/javascript">
<!--
window.addEvent('domready', function() {
	$('fallback_link').addEvent('click', function(event) {
		//prevent the page from changing
		event.stop();
		//make the ajax call, replace text
		var req = new Request.HTML({
			method: 'get',
			url: 'vid_request.php',
			data: { 'task' : 'simple' },
			update: $('uploader_placeholder'),
			onComplete: function(response) {
                               deletereadonly(); 
			}
		}).send();
	});
});
//-->
</script>
{/literal}

<table cellpadding='0' cellspacing='0' width='100%'>
<tr>
<td valign='top'>
<div style='float:left;width:700px;'>
  <div>
  <div class='page_header'>{lang_print id=13500134}</div>
  <div>{lang_print id=13500169}</div>
  </div>
</div>
<div style='float:right;margin-top:10px;'>
  <table cellpadding='0' cellspacing='0' width='130'>
  <tr><td class='button' nowrap='nowrap'><a href='user_vid.php'><img src='./images/icons/back16.gif' border='0' class='button'>Back to Videos</a></td></tr>
  </table>
</div>  
</td>
</tr>
</table>

<div style='margin-top:20px;margin-bottom:20px; background:#F9F9F9;border:1px dashed #CCCCCC; padding:6px;'>{lang_print id=13500131}</div>

{if $user->level_info.level_vid_allow != 4}
  {if ($user->level_info.level_vid_allow == 3 OR $user->level_info.level_vid_allow == 2) AND $count_yt < $user->level_info.level_vid_prov_maxnum}
  <div style='margin-bottom: 20px;'>
  <table cellpadding='0' cellspacing='0'>
    <tr>
      <td>
        <div><img src="./images/icons/bulb16.gif" class='button' /><a href="user_vid_add.php?task=youtube">{lang_print id=13500051}</a></div>
      </td>
    </tr>
  </table>
  <div style='clear: both; height: 0px;'></div>
  </div>
  {/if}
{/if}


  <form action='user_vid.php' id='vid_data' class='vid_data' method='POST' enctype="multipart/form-data" onsubmit="return result()">
  <b>*{lang_print id=13500024}</b><br>
  <input name='vid_title' id='vid_title' onkeydown='Check2()' onkeyup='Check2()' type='text' maxlength='80' size='30' value='{$title}' class="text">

  <br><br>

  <b>*{lang_print id=13500025}</b><br>
  <textarea name='vid_desc' id='vid_desc' onkeydown='Check2()' onkeyup='Check2()' rows='6' cols='50' class="text">{$description}</textarea>

  <br><br>

{* IF THERE ARE NO VIDEOS, SHOW NOTE *}
{if $count_cats != 0}
  <b>{lang_print id=13500086}</b><br>
  <select name='vid_cat'>
  {section name=num loop=$all_cats}
       <option value='{$all_cats[num].id}'>{$all_cats[num].title}</option>
  {/section}
  </select>

  <br><br>
{/if}

  <b>*{lang_print id=13500026}</b><br>
  <textarea name='vid_tags' id='vid_tags' onkeydown='Check2()' onkeyup='Check2()' rows='6' cols='50' class="text">{$tags}</textarea>

  <br />
  <br />
  
  {* SHOW SEARCH PRIVACY OPTIONS IF ALLOWED BY ADMIN *}
  {if $user->level_info.level_vid_search == 1}
    <b>{lang_print id=13500146}</b><br>
    <table cellpadding='0' cellspacing='0'>
      <tr>
        <td><input type='radio' name='vid_search' id='vid_search_1' value='1' CHECKED></td>
        <td><label for='vid_search_1'>{lang_print id=13500147}</label></td>
      </tr>
      <tr>
        <td><input type='radio' name='vid_search' id='vid_search_0' value='0'></td>
        <td><label for='vid_search_0'>{lang_print id=13500148}</label></td>
      </tr>
    </table>
    <br />
  {/if}

  {* SHOW PRIVACY OPTIONS IF ALLOWED BY ADMIN *}
  {if $privacy_options|@count > 1}
    <b>{lang_print id=13500149}</b><br>
    <table cellpadding='0' cellspacing='0'>
      {foreach from=$privacy_options name=privacy_loop key=k item=v}
      <tr>
        <td><input type='radio' name='vid_privacy' id='privacy_{$k}' value='{$k}' {if $smarty.foreach.privacy_loop.first}CHECKED{/if}></td>
        <td><label for='privacy_{$k}'>{lang_print id=$v}</label></td>
      </tr>
      {/foreach}
    </table>
    <br />
  {/if}

  {* SHOW COMMENT OPTIONS IF ALLOWED BY ADMIN *}
  {if $comment_options|@count > 1}
    <b>{lang_print id=13500150}</b><br>
    <table cellpadding='0' cellspacing='0'>
    {foreach from=$comment_options name=comment_loop key=k item=v}
      <tr>
      <td><input type='radio' name='vid_comments' id='comments_{$k}' value='{$k}' {if $smarty.foreach.comment_loop.first}CHECKED{/if}></td>
      <td><label for='comments_{$k}'>{lang_print id=$v}</label></td>
      </tr>
    {/foreach}
    </table>
    <br />
  {/if}

{literal}
<script type="text/javascript">
<!--
document.onmousemove = Check2;

function Check2() {
    var error = true;
    var error2 = true;
    if ($('vid_title').value=='') {
         error = false;
    }
    if ($('vid_desc').value=='') {
         error = false;
    }
    if ($('vid_tags').value=='') {
         error = false;
    }
    if ($('simple')) {
         error2 = false;
    }
    if (error2 == false) {
         if ($('file').value=='') {
             error = false;
         }
    }
    if (error == true) {
         $('send').disabled = false;
    } else {
         $('send').disabled = true;
    }
}
//-->
</script>
{/literal}

{literal}
<script type="text/javascript">
<!-- 
function result() {
    var returns = false;
    if ($('simple')) {
         returns = true;
    }
    return returns;
}

function makereadonly() {
    if ($('simple')) {
         $('vid_title').readOnly=true;
         $('vid_desc').readOnly=true;
         $('vid_tags').readOnly=true;
         $('bar').style.visibility = 'visible';
    }
}

function deletereadonly() {
         $('vid_title').readOnly=false;
         $('vid_desc').readOnly=false;
         $('vid_tags').readOnly=false;
}

SolmetraUploader.setEventHandler('myEvent');
function myEvent (instance_id, event_id, data) {
  if (instance_id == 'd7d12e92f93e6bdf7c1e7dbbb76f7de9') {
    switch(event_id) {
      case 'uploading':
         $('vid_title').readOnly=true;
         $('vid_desc').readOnly=true;
         $('vid_tags').readOnly=true;
         break;
    }
  }
}
//-->
</script>
{/literal}


<div id='uploader_placeholder' name='uploader_placeholder'>
  <div id='file_data' style='display: inline;' name='file_data'><b>{lang_print id=13500087}</b><br>

<div id="solmetraUploaderPlaceholder_d7d12e92f93e6bdf7c1e7dbbb76f7de9">
<input type="file" name="file" size="43" />
</div>
<input type="hidden" name="vid_sessid" value="{$session_id}" />
<input type="hidden" name="solmetraUploaderInstance" value="d7d12e92f93e6bdf7c1e7dbbb76f7de9" />
<input type="hidden" id="solmetraUploaderData_d7d12e92f93e6bdf7c1e7dbbb76f7de9" name="solmetraUploaderData[d7d12e92f93e6bdf7c1e7dbbb76f7de9]" value="file" />
<input type="hidden" id="solmetraUploaderHijack_d7d12e92f93e6bdf7c1e7dbbb76f7de9" value="y" />
<input type="hidden" id="solmetraUploaderRequired_d7d12e92f93e6bdf7c1e7dbbb76f7de9" value="y" />
{math assign="size" equation='c*1024' c=$user->level_info.level_vid_maxsize}
{literal}
<script type="text/javascript">
<!--
  var so = new SWFObject("uploader.swf", "solmetraUploaderMovie_d7d12e92f93e6bdf7c1e7dbbb76f7de9", "354", "18", "8", "#ffffff");
  so.useExpressInstall("expressinstall.swf");
  so.addParam("allowScriptAccess", "always");
  so.addParam("allowFullScreen", "false");
  so.addVariable("baseurl", "./");
  so.addVariable("uploadurl", "vid_handler.php?PHPSESSID={/literal}{$session_id}{literal}");
  so.addVariable("language", "en");
  so.addVariable("instance", "d7d12e92f93e6bdf7c1e7dbbb76f7de9");
  so.addVariable("allowed", "");
  so.addVariable("disallowed", "php,php3,php4,php5");
  so.addVariable("verifyupload", "true");
  so.addVariable("serial", "f72d20d3510b4edec3f13df8ab77c70f");
  so.addVariable("configXml", "<?xml version=\'1.0\' encoding=\'UTF-8\'?><config><maxsize></maxsize><filetypes><type><description>Video Files</description><extension>{/literal}{if $exts_array[0] != ''}{section name=exts loop=$exts_array}*.{$exts_array[exts]};{/section}{else}*.*{/if}{literal}</extension><mactype>*</mactype></type></filetypes><window><bgcolor></bgcolor><borderwidth>1</borderwidth><bordercolor>#999999</bordercolor><borderradius>0</borderradius><padding></padding><alpha>100</alpha><image></image><stretch>yes</stretch></window><layout>horizontal</layout><upload><show>no</show><width></width><height></height></upload><cancel><show>no</show><width></width><height></height></cancel><progress><width></width><height>22</height><select><borderwidth>1</borderwidth><bordercolor>#999999</bordercolor><borderradius>0</borderradius><padding>0</padding><alpha>100</alpha><button>no</button></select><background><image>images/vid_uploader_back.gif</image><borderwidth>1</borderwidth><bordercolor>#999999</bordercolor><borderradius>0</borderradius><padding>0</padding><alpha>100</alpha><stretch>no</stretch></background><bar><image>images/vid_uploader_fore.gif</image><borderwidth>1</borderwidth><bordercolor>#999999</bordercolor><borderradius>0</borderradius><padding>0</padding><alpha>100</alpha><stretch>yes</stretch><direction>right</direction></bar><textformat><color>#000000</color><size>9</size><font>Verdana</font><bold>no</bold><shadow></shadow></textformat><errorformat><color>#FF4444</color><size>9</size><font>Verdana</font><bold>no</bold><shadow></shadow></errorformat></progress><button><defaults><width>353</width><height>16</height><borderradius>0</borderradius><bgcolor>#65AAF1,#438FDB,#598ABC</bgcolor><crystal>#B1D4F8,#509AE6</crystal><borderwidth>1</borderwidth><bordercolor>#4A9BEF</bordercolor><textformat><color>#FFFFFF</color><size>9</size><font>Verdana</font><bold>no</bold><shadow>#444444</shadow></textformat></defaults><normal><bgcolor>#65AAF1,#438FDB,#598ABC</bgcolor><crystal>#B1D4F8,#509AE6</crystal><borderwidth>1</borderwidth><bordercolor>#4A9BEF</bordercolor></normal><hover><bgcolor>#448BD4,#2873BE,#3C6FA2</bgcolor><crystal>#81ADDA,#3B82CA</crystal><borderwidth>1</borderwidth><bordercolor>#2F80D4</bordercolor></hover><disabled><bgcolor>#CACACA,#AAAAAA,#AAAAAA</bgcolor><crystal>#E9E9E9,#B9B9B9</crystal><borderwidth>1</borderwidth><bordercolor>#B6B6B6</bordercolor><textformat><color>#CCCCCC</color><shadow></shadow></textformat></disabled></button><!--Text prompt definitions for each language. Ie.:<select_file><en>{/literal}{lang_print id=13500170}{literal}</en><fr>S&#233;lectionner des fichiers</fr><de>W&#228;hlen Sie eine Datei</de></select_file>--><prompts><select_file><en>{/literal}{lang_print id=13500170}{literal}</en></select_file><too_big><en>Maximum size: {{size}}</en></too_big><disallowed><en>Selected file type is disallowed</en></disallowed><upload><en>Upload</en></upload><cancel><en>Cancel</en></cancel><uploading><en>Uploading... {{percent}}%25 ({{loaded}} / {{total}})</en></uploading><processing><en>Please wait! Processing...</en></processing><done><en>Finishing...</en></done><upload_err_not_selected><en>No file selected</en></upload_err_not_selected><upload_err_ini_size><en>File exceeds size limit set on the server</en></upload_err_ini_size><upload_err_form_size><en>The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form</en></upload_err_form_size><upload_err_partial><en>The uploaded file was only partially uploaded</en></upload_err_partial><upload_err_no_file><en>No file was uploaded</en></upload_err_no_file><upload_err_no_tmp_dir><en>Missing a temporary folder</en></upload_err_no_tmp_dir><upload_err_cant_write><en>Failed to write file to disk</en></upload_err_cant_write><upload_err_extension><en>File upload stopped by extension</en></upload_err_extension><upload_err_unauthorized><en>Unauthorized upload</en></upload_err_unauthorized><upload_err_move><en>An error occured while trying to move uploaded file</en></upload_err_move></prompts></config>");
  so.addVariable("maxsize", "{/literal}{$size}{literal}");
  so.addVariable("hijackForm", "yes");
  so.addVariable("externalErrorHandler", "SolmetraUploader.broadcastError");
  so.addVariable("externalEventHandler", "SolmetraUploader.broadcastEvent");
  so.write("solmetraUploaderPlaceholder_d7d12e92f93e6bdf7c1e7dbbb76f7de9");
  solmetraUploaderMovie_d7d12e92f93e6bdf7c1e7dbbb76f7de9 = document.getElementById('solmetraUploaderMovie_d7d12e92f93e6bdf7c1e7dbbb76f7de9');
//-->
</script>
{/literal}

</div>
</div>

  <div>{lang_sprintf id=13500101 1=$user->level_info.level_vid_maxsize}{if $exts}<br><font style='visibility: hidden;'>NB: </font>{lang_sprintf id=13500102 1=$exts}{/if}</div>

<div id='notvalid' name='notvalid' style='display: none;'>
<br><br>
  <table cellpadding='0' cellspacing='0'>
  <tr><td class='result'>
      <div class='error'><img src='./images/error.gif' border='0' class='icon'>{lang_print id=13500128}</div>
  </td></tr></table>
</div>

<br>

<div id='fallback_link' class='fallback_link' style='display: inline;'><a href='javascript:void(0)' onClick="$('fallback_link').style.display='none'">{lang_print id=13500164}</a><br><br><b></div>

  <div style='clear: both;'>
  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td>
    <input type='submit' class='button' id='send' name='send' onclick='makereadonly();' value='{lang_print id=13500130}'>&nbsp;
    <input type='hidden' name='task' id='task' value='add_vid'>
  </form>
  </td>
  <td>
    <form action='user_vid.php' method='GET'>
    <input type='submit' class='button' value='{lang_print id=13500090}'>&nbsp;
    </form>
  </td>
  <td>
    <div id='bar' name='bar' style='visibility: hidden;'>&nbsp;<img src='./images/icons/vid_uploading_bar.gif'></div>
  </td>
  </tr>
  </table>
  </div>

{/if}
{include file='footer.tpl'}