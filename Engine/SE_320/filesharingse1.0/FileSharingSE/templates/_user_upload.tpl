{* JAVASCRIPT FOR FILE UPLOADER *}
<script type='text/javascript' src='./include/uploader/Swiff.Uploader.js'></script>
<script type='text/javascript' src='./include/uploader/Fx.ProgressBar.js'></script>
<script type='text/javascript' src='./include/uploader/FancyUpload2.js'></script>
{literal}
<script type="text/javascript">
<!--
  {/literal}{if $show_uploader}{literal}
  window.addEvent('domready', function() {

    var postData = {'isAjax':1, 'upload_token':'{/literal}{$upload_token}{literal}'};
    $('uploadForm').getElements('input[type=hidden]').each(function(el) { postData[el.get('name')] = el.get('value');});

    var swiffy = new FancyUpload2($('uploader'), $('fileList'), {
	'url': '{/literal}{$url->url_base}{literal}user_upload.php?session_id={/literal}{$session_id}{literal}',
	'fieldName': 'file1',
	'data': postData,
	'limitFiles': 10,
	'path': './include/uploader/Swiff.Uploader.swf',
	'onLoad': function() {
		$('uploader').style.display = 'block';
		$('fallback_link').style.display = 'block';
		$('fallback').style.display = 'none';
	}
    });
 
    $('uploader_browse').addEvent('click', function() {
	swiffy.browse();
	return false;
    });
 
    $('uploader_upload').addEvent('click', function() {
	if(swiffy.files.length == 0) {
	  alert('{/literal}{lang_print id=1194}{literal}');
	} else {
	  swiffy.upload();
	}
	return false;
    });

    $('fallback_link').addEvent('click', function() {
	$('fallback').style.display='block';
	$('fallback_link').style.display='none';
	$('uploader').style.display='none';
	return false;
    });
  });
  {/literal}{/if}{literal}

  function startStatus() {
    $('fallback_submit').disabled = true;
    $('fallback_status').value = "{/literal}{lang_print id=1190}{literal}";
    window.setTimeout("goStatus()", 400);
  }
  function goStatus() {
    $('fallback_status').value = $('fallback_status').value + '.';
    if($('fallback_status').value == '{/literal}{lang_print id=1190}{literal}'+'....') { $('fallback_status').value = '{/literal}{lang_print id=1190}{literal}'; }
    window.setTimeout("goStatus()", 400);
  }
// -->
</script>
{/literal}


<div id='uploader' style='display: none;'>
  <div style='margin-bottom: 10px;'>
    <div style='float: left; font-weight: bold;'>
      <a href='javascript:void(0);' id='uploader_browse' onClick='this.blur()'><img src='./images/uploader_browse.gif' border='0' class='button'>{lang_print id=1191}</a>
    </div>
    <div style='float: left; padding-left: 20px; font-weight: bold;'>
      <a href='javascript:void(0);' id='uploader_upload' onClick='this.blur()'><img src='./images/uploader_upload.gif' border='0' class='button'>{lang_print id=1189}</a>
    </div>
    <div style='height: 0px; clear: both;'></div>
  </div>
  <div>
    <div>{lang_print id=1192} <span class='overall-title'></span></div>
    <img src='./images/uploader_bar.gif' class='progress overall-progress' />
  </div>
  <div style='margin-top: 5px;'>
    <div>{lang_print id=1193} <span class='current-title'></span></div>
    <img src='./images/uploader_bar.gif' class='progress current-progress' />
  </div>
  <div class='uploading-text' style='display: none;'>{lang_sprintf id=1195 1="<span class='current-rate'></span>" 2="<span class='current-timeleft'></span>"}</div>
  <div class='uploaded-text' style='display: none;'>{lang_print id=1196}</div>
  <ul id='fileList'></ul>
</div>
 

<br />
<div id='fallback_link' class='fallback_link' style='display: none;'><a href='javascript:void(0)'>{lang_print id=1183}</a></div>

<div id='fallback'>

  {* SHOW IMAGES UPLOADED MESSAGE *}
  {if $file_result|@count != 0}
    <br>
    <table cellpadding='0' cellspacing='0'>
    <tr><td class='result'>
      <div class='success' style='text-align: left;'> 
        {foreach from=$file_result name=result_loop key=k item=v}
          <div>{lang_sprintf id=$v.message 1=$v.file_name}</div>
        {/foreach}
      </div>
    </td>
    </tr>
    </table>
  {/if}

  <form action='{$action}' method='POST' enctype='multipart/form-data' id='uploadForm' onSubmit='startStatus()'>

    <div id='div1'>
      <table cellpadding='0' cellspacing='0' class='form'>
      <tr>
      <td class='form1' width='65'>{lang_print id=1184}</td>
      <td class='form2'><input type='file' name='file1' size='60' class='text' onchange="$('div2').style.display = 'block';$('div_submit').style.display = 'block';"></td>
      </tr>
      </table>
    </div>

    <div id='div2' style='display: none;'>
      <table cellpadding='0' cellspacing='0' class='form'>
      <tr>
      <td class='form1' width='65'>{lang_print id=1185}</td>
      <td class='form2'><input type='file' name='file2' size='60' class='text' onchange="$('div3').style.display = 'block';"></td>
      </tr>
      </table>
    </div>

    <div id='div3' style='display: none;'>
      <table cellpadding='0' cellspacing='0' class='form'>
      <tr>
      <td class='form1' width='65'>{lang_print id=1186}</td>
      <td class='form2'><input type='file' name='file3' size='60' class='text' onchange="$('div4').style.display = 'block';"></td>
      </tr>
      </table>
    </div>

    <div id='div4' style='display: none;'>
      <table cellpadding='0' cellspacing='0' class='form'>
      <tr>
      <td class='form1' width='65'>{lang_print id=1187}</td>
      <td class='form2'><input type='file' name='file4' size='60' class='text' onchange="$('div5').style.display = 'block';"></td>
      </tr>
      </table>
    </div>

    <div id='div5' style='display: none;'>
      <table cellpadding='0' cellspacing='0' class='form'>
      <tr>
      <td class='form1' width='65'>{lang_print id=1188}</td>
      <td class='form2'><input type='file' name='file5' size='60' class='text'></td>
      </tr>
      </table>
    </div>

    <div id='div_submit' style='display: none;'>
      <table cellpadding='0' cellspacing='0'>
      <tr>
      <td class='form1' width='65'>&nbsp;</td>
      <td class='form1'>
        <input type='submit' class='button' name='submit' value='{lang_print id=1189}' id='fallback_submit'>&nbsp;
        <input type='hidden' name='task' value='doupload'>
        <input type='hidden' name='MAX_FILE_SIZE' value='50000000'>
	{foreach from=$inputs name=input_loop key=k item=v}
	  <input type='hidden' name='{$k}' value='{$v}'>
        {/foreach}
      </td>
      <td class='form2'>
        &nbsp;<input type='text' class='fallback_status' name='status' id='fallback_status' readonly='readonly'>
      </td>
      </tr>
      </table>
    </div>

  </form>
</div>