{include file='header.tpl'}

<table class='tabs' cellpadding='0' cellspacing='0'>
<tr>
<td class='tab0'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_openid_facebook.php'>{lang_print id=100051104}</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_openid_facebook_friends.php'>{lang_print id=100051105}</a></td>
<td class='tab'>&nbsp;</td>
{if $setting.setting_signup_invite != 1}
<td class='tab2' NOWRAP><a href='user_openid_invite_facebook.php'>{lang_print id=100051106}</a></td>
<td class='tab'>&nbsp;</td>
{/if}
<td class='tab1' NOWRAP><a href='user_openid_facebook_settings.php'>{lang_print id=100051107}</a></td>
<td class='tab3'>&nbsp;</td>
</tr>
</table>

<img src='./images/icons/facebook48.png' border='0' class='icon_big'>
<div class='page_header'>{lang_print id=100051128}</div>
<div>{lang_print id=100051129}</div>
<br />
<br />

{* SHOW SUCCESS MESSAGES *}
{if $result != 0}
  <table cellpadding='0' cellspacing='0'><tr><td class='success'>
  <img src='./images/success.gif' border='0' class='icon'>{lang_print id=191}
  </td></tr></table>
{/if}




<form action='user_openid_facebook_settings.php' method='post' name='info'>
<table cellpadding='0' cellspacing='0'>


{*
<tr>
<td class='form1' nowrap='nowrap'>Facebook Status Update:</td>
<td class='form2' id='blocks'>
  <div style='padding: 3px 0px 5px 0px;'>If you would like to have your Facebook status updated automatically without prompting, you need authorize it. </div>

  <div style='padding: 5px 0px 0px 2px;'>
    
    Status: <span id="openidconnect_facebook_statusauthorized">Authorized</span>
    &nbsp;&nbsp;
    <input type='button' class='button' onclick="openidconnect_facebook_authorize_status_update()" value="Click to authorize">
  </div>

</td>
</tr>
*}


{* SHOW STORY FEED SETTING *}
<tr>
<td class='form1' nowrap='nowrap'>{lang_print id=100051130}:</td>
<td class='form2'>
  <div style='padding: 3px 0px 5px 0px;'>{lang_print id=100051131}</div>
  <table cellpadding='0' cellspacing='0'>
  {foreach from=$openidconnect_facebook_feed_stories item=feedstory}
    <tr>
    <td><input type='checkbox' name='feedstory[]' id='feedstory_{$feedstory.feedstory_id}' value='{$feedstory.feedstory_id}'{if $feedstory.feedstory_selected == 1} checked='checked'{/if}></td>
    <td><label for='feedstory_{$feedstory.feedstory_id}'>{if is_numeric($feedstory.feedstory_desc)}{lang_print id=$feedstory.feedstory_desc}{else}{$feedstory.feedstory_desc}{/if}</label></td>
    </tr>
  {/foreach}
  </table>
</td>
</tr>


{* SHOW AUTOLOGIN SETTING *}
<tr>
<td class='form1' nowrap='nowrap'>{lang_print id=100051132}:</td>
<td class='form2'>
  <div style='padding: 3px 0px 5px 0px;'>{lang_print id=100051133}</div>
  <table cellpadding='0' cellspacing='0'>
    <tr>
    <td><input type='radio' name='openidconnect_autologin' id='openidconnect_autologin_0' value='0'{if $openidconnect_autologin == 0} checked='checked'{/if}></td>
    <td><label for='openidconnect_autologin_0'>{lang_print id=100051134}</label></td>
    </tr>
    <tr>
    <td><input type='radio' name='openidconnect_autologin' id='openidconnect_autologin_1' value='1'{if $openidconnect_autologin == 1} checked='checked'{/if}></td>
    <td><label for='openidconnect_autologin_1'>{lang_print id=100051135}</label></td>
    </tr>
    <tr>
    <td><input type='radio' name='openidconnect_autologin' id='openidconnect_autologin_2' value='2'{if $openidconnect_autologin == 2} checked='checked'{/if}></td>
    <td><label for='openidconnect_autologin_2'>{lang_print id=100051136}</label></td>
    </tr>
  </table>
</td>
</tr>
<tr>
<td class='form1'>&nbsp;</td>
<td class='form2'><input type='submit' class='button' value='{lang_print id=173}'></td>
</tr>
</table>
<input type='hidden' name='task' value='dosave'>
</form>




{include file='footer.tpl'}