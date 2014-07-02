{include file='admin_header.tpl'}

<h2>Install All-in-one Video Plugin</h2>
<br />

Please specify the full path to your FFMPEG installation:<br>
(e.g. /usr/local/bin/ffmpeg)<br>
	
<form name='install_video_plugin' action='admin_install_vid.php' method='get'>

<input type="text" name="ffmpeg_path" class="text" size="60" value="{$ffmpeg_path}">
{if $result}
    <br><strong style="color:#ff0000">{$result}</strong>
{/if}
	
<br><br>

Please specify the full path to your FLVTool2 installation:<br>
(e.g. /usr/local/bin/flvtool2)<br>

<input type="text" name="flvtool2_path" class="text" size="60" value="{$flvtool2_path}">
{if $result2}
    <br><strong style="color:#ff0000">{$result2}</strong>
{/if}

{if $is}
    <br><br>
    <strong style="color:#00ff00">{$is}</strong>
{/if}

<br><br>

<div style="float:left;"><input type="submit" value="Validate Installations"></div>
	
</form>

{if $is}
<div style="float:left;">
<form id='install_video_plugin2' name='install_video_plugin2' action='admin_viewplugins.php' method='get'>
<div style="float:left;">
<input type="hidden" name="install" value="vid">
<input type="hidden" name="do" value="1">
<input type="hidden" name="ffmpeg_path" value="{$ffmpeg_path}">
<input type="hidden" name="flvtool2_path" value="{$flvtool2_path}">
<input type="submit" value="Install">
</form>
</div>
{/if}

<div style="float:left;">
<form id='install_video_plugin3' name='install_video_plugin3' action='admin_viewplugins.php' method='get'>
<div style="float:left;">
<input type="hidden" name="install" value="vid">
<input type="hidden" name="do" value="2">
<input type="hidden" name="ffmpeg_path" value="">
<input type="hidden" name="flvtool2_path" value="">
<input type="submit" value="Skip and Install without Paths">
</form>
</div>

<br><br><br>

{include file='admin_footer.tpl'}