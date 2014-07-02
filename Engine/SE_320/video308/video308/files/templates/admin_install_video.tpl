{include file='admin_header.tpl'}

{* $Id: admin_install_video.tpl 94 2009-03-11 23:08:52Z szerrade $ *}

 <div style='display: none;' id='confirminstall'>
    <div style='margin-top: 10px;'>
  Are you sure you want to install without specifying an FFmpeg path?  Your users will still be able to post YouTube videos, but won't be able to upload new videos from their computers.  Note that you can change this setting later in the Global Video Settings section of your admin panel.
    </div>
    <br>
    <input type='button' class='button' value='Install' onClick='parent.TB_remove();document.install_video_plugin.submit();'> <input type='button' class='button' value='Cancel' onClick='parent.TB_remove();'>
  </div>
<h2>Install Video Plugin</h2>
<br />



{literal}
<script type='text/javascript'>
  

  function nextInstallPage() 
  {
    if (document.install_video_plugin.ffmpeg_path.value=="") 
    {
      TB_show('{/literal}Install Without FFmpeg?{literal}', '#TB_inline?height=150&width=300&inlineId=confirminstall', '', '../images/trans.gif');
      
    }
    else 
    {
      document.install_video_plugin.submit();
    }
  }

  function showFindFFmpeg()
  {
    $('findFfmpegDiv').style.display = 'block';
    $('findFfmpegLoadingDiv').style.display = 'none';
    $('findFfmpegCompleteDiv').style.display = 'none';
  }
  
  function findFFmpeg()
  {
    $('findFfmpegDiv').style.display = 'none';
    $('findFfmpegLoadingDiv').style.display = 'block';
    $('findFfmpegCompleteDiv').style.display = 'none';
    
    var request = new Request.JSON({
      'method' : 'get',
      'url' : 'admin_viewplugins.php?install=video&do_find=1&do_find_root='+encodeURI($('searchPath').value),
      'onComplete':function(responseObject)
      {
        findFFmpegComplete(responseObject);
      }
    });
    
    request.send();
  }
  
  function findFFmpegComplete(responseObject)
  {
    $('findFfmpegDiv').style.display = 'block';
    $('findFfmpegLoadingDiv').style.display = 'none';
    $('findFfmpegCompleteDiv').style.display = 'block';
    
    $('findFfmpegCompleteDiv').innerHTML = 'Search Complete:';
    if( !responseObject || responseObject=='' )
    {
      $('findFfmpegCompleteDiv').innerHTML += "<br /><i>Not found</i>";
    }
    else
    {
      $A(responseObject).each(function(pathResult)
      {
        $('findFfmpegCompleteDiv').innerHTML += 
          '<br />' + 
          '<a href="javascript:void(0);" onclick="$(\'ffmpeg_path\').value=\'' + pathResult + '\'">' +
            pathResult +
           '</a>';
      });
    }
  }
  
</script>
{/literal}

{if $exec_disabled}

	The php function "exec" [<a href="http://www.php.net/manual/en/function.exec.php" target="_blank">http://www.php.net/manual/en/function.exec.php</a>] is disabled on this server.
	<br><br>Please enable this function first, by editing the "disable_functions" variable in your php.ini file. 

{else}

	Please specify the full path to your FFmpeg installation.  If you do not have FFmpeg, leave this field blank:<br>
	(e.g. /usr/local/bin/ffmpeg)
  <br />
  <br />
  
  <div class="box">
    <div>Linux Systems Only: Find FFmpeg</div>
    
    <div id="findFfmpegDiv" style="margin-top:10px;">
      <input type="text" id="searchPath" value="/" /> Please specify the base folder to look for ffmpeg in. (Default: "/")
      <br />
      <a href="javascript:void(0);" onclick="findFFmpeg();">Search</a>
    </div>
    
    <div id="findFfmpegLoadingDiv" style="display:none; margin-top:10px;">
      <table><tr><td>
        <img src="../images/icons/loading2.gif" />
      </td><td>
        <span valign="middle">Please wait, this may take several minutes.</span>
      </td></tr></table>
    </div>
    
    <div id="findFfmpegCompleteDiv" style="display:none; margin-top:10px;">
      Search Complete.
    </div>
	</div>
  <br />
  
	<form name='install_video_plugin' action='admin_viewplugins.php' method='get'>
	<input type="hidden" name="install" value="video">
	
	<input type="text" id="ffmpeg_path" name="ffmpeg_path" class="text" size="60" value="{$_ffmpeg_path}">
	{if $_error}
	<br><strong style="color:#ff0000">The FFmpeg installation could not be found. Try again.</strong>
	{/if}
  {if $_error_message}
  <br><strong style="color:#ff0000">({$_error_message})</strong>
  {/if}
	
	<br><br>
	
	<input type="button" value="Install Video Plugin" onClick="nextInstallPage()"> <input type="button" value="Cancel" onClick="location.href='admin_viewplugins.php'">
	
	</form>

{/if}

{include file='admin_footer.tpl'}