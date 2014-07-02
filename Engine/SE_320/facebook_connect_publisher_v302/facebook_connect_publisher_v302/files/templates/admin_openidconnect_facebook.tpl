{include file='admin_header.tpl'}

{literal}
<script>
</script>
{/literal}

<h2>{lang_print id=100051058}</h2>
{lang_print id=100051059}

<br><br>

{if $result != 0}

  {if empty($error_message) AND empty($error_messages) }
  <div class='success'><img src='../images/success.gif' class='icon' border='0'> {lang_print id=100051004} </div>
  {else}
  
    {if !empty($error_message)}
      <div class='error'><img src='../images/error.gif' class='icon' border='0'> {if $error_message|@is_numeric}{lang_print id=$error_message}{else}{$error_message}{/if} </div>
    {/if}

    {if !empty($error_messages)}
    
      {foreach from=$error_messages item=error_message}
      <div class='error'><img src='../images/error.gif' class='icon' border='0'> {if $error_message|@is_numeric}{lang_print id=$error_message}{else}{$error_message}{/if} </div>
      {/foreach}
    
    {/if}
  
  {/if}


{/if}


<form action='admin_openidconnect_facebook.php' method='POST'>

<table cellpadding='0' cellspacing='0' width='600'>
<tr>
<td class='header'>{lang_print id=100051060}</td>
</tr>

<tr>
<td class='setting1'>

{lang_print id=100051061}

<br><br> For application setup please see our <a href="admin_openidconnect_facebook_help.php?show=20">Facebook Application installation instructions</a>
  
</td></tr><tr><td class='setting2'>
  <table cellpadding='0' cellspacing='0'>
  <tr><td width=150>Facebook API Key</td><td class='form2'><input type='text' name='setting_openidconnect_facebook_api_key' size='50' maxlength='32' class='text' value='{$setting_openidconnect_facebook_api_key}'>&nbsp;</td></tr>
  <tr><td width=150>Facebook Secret</td><td class='form2'><input type='text' name='setting_openidconnect_facebook_secret' size='50' maxlength='32' class='text' value='{$setting_openidconnect_facebook_secret}'>&nbsp;</td></tr>
  </table>
</td></tr>

<tr>
<td class='setting1'>

  <br>
  {lang_print id=100051062}
  <br><br>

</td></tr><tr><td class='setting2'>
  <table cellpadding='0' cellspacing='0'>
  <tr><td width=150>{lang_print id=100051063}</td><td class='form2'><input type='text' name='setting_openidconnect_feed_public_site_name' size='50' maxlength='250' class='text' value='{$setting_openidconnect_feed_public_site_name}'>&nbsp;</td></tr>
  </table>
</td></tr>

<tr>
<td class='setting1'>

  <br>
  {lang_print id=100051064}
  
  <div id='autologin_help_link' style='display: block;'> [ <a onClick="{literal}$('autologin_help').setStyles({display:'block'});$('autologin_help_link').setStyles({display:'none'});{/literal}" href="javascript:void(0)">{lang_print id=100051065}</a> ]</div>


<div id='autologin_help' style="display:none">
<br>

{lang_print id=100051066}
  
  <br><br>

</div>

  <br>
  {lang_print id=100051067}
  <br><br>

</td></tr><tr><td class='setting2'>

<input type='radio' name='setting_openidconnect_autologin' id='setting_openidconnect_autologin_1' value='1'{if $setting_openidconnect_autologin == 1} CHECKED{/if}><label for='setting_openidconnect_autologin_1'>{lang_print id=100051068}</label><br>
<input type='radio' name='setting_openidconnect_autologin' id='setting_openidconnect_autologin_0' value='0'{if $setting_openidconnect_autologin == 0} CHECKED{/if}><label for='setting_openidconnect_autologin_0'>{lang_print id=100051069}</label><br>

</td></tr>

<tr>
<td class='setting1'>

  {lang_print id=100051070}

  <br><br>

</td></tr><tr><td class='setting2'>

<input type='radio' name='setting_openidconnect_hook_logout' id='setting_openidconnect_hook_logout_1' value='1'{if $setting_openidconnect_hook_logout == 1} CHECKED{/if}><label for='setting_openidconnect_hook_logout_1'>{lang_print id=100051071}</label><br>
<input type='radio' name='setting_openidconnect_hook_logout' id='setting_openidconnect_hook_logout_0' value='0'{if $setting_openidconnect_hook_logout == 0} CHECKED{/if}><label for='setting_openidconnect_hook_logout_0'>{lang_print id=100051072}</label><br>

</td></tr>

<tr>
<td class='setting1'>
  <br>
{lang_print id=100051028} - {lang_print id=100051029}
  <br><br>
</td></tr><tr><td class='setting2'>

  <table cellpadding='0' cellspacing='0'>
  <tr><td><input type='radio' name='setting_openidconnect_replaceloginpage' id='openidconnect_replaceloginpage_1' value='1'{if $setting_openidconnect_replaceloginpage == 1} CHECKED{/if}>&nbsp;</td><td><label for='setting_openidconnect_replaceloginpage_1'>{lang_print id=100051030}</label></td></tr>
  <tr><td><input type='radio' name='setting_openidconnect_replaceloginpage' id='openidconnect_replaceloginpage_0' value='0'{if $setting_openidconnect_replaceloginpage == 0} CHECKED{/if}>&nbsp;</td><td><label for='setting_openidconnect_replaceloginpage_0'>{lang_print id=100051031}</label></td></tr>
  </table>

</td></tr>


</table>




<br><br>



<table cellpadding='0' cellspacing='0' width='600'>
<tr>
<td class='header'>{lang_print id=100051073}</td>
</tr>

<tr>
<td class='setting1'>
  <br>
  {lang_print id=100051074}
  <br><br>

</td></tr><tr><td class='setting2'>

  <input type='text' class='text' name="setting_openidconnect_facebook_inviteactiontext" value="{$setting_openidconnect_facebook_inviteactiontext}" size='90' >
    
</td></tr>

<tr>
<td class='setting1'>
  <br>
  {lang_print id=100051075}
  <br><br>

</td></tr><tr><td class='setting2'>

  <textarea class='textarea' cols=69 rows=6 name="setting_openidconnect_facebook_invitemessage">{$setting_openidconnect_facebook_invitemessage}</textarea>
  <br>
  {lang_print id=100051076}
    
</td></tr>

</table>



<br><br>

<input type='submit' class='button' value='{lang_print id=100051032}'>
<input type='hidden' name='task' value='dosave'>
</form>


{include file='admin_footer.tpl'}