{include file='header.tpl'}

<table class='tabs' cellpadding='0' cellspacing='0'>
<tr>
<td class='tab0'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_openid_facebook.php'>{lang_print id=100051104}</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_openid_facebook_friends.php'>{lang_print id=100051105}</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab1' NOWRAP><a href='user_openid_invite_facebook.php'>{lang_print id=100051106}</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_openid_facebook_settings.php'>{lang_print id=100051107}</a></td>
<td class='tab3'>&nbsp;</td>
</tr>
</table>

<img src='./images/icons/facebook48.png' border='0' class='icon_big'>
<div class='page_header'>{lang_print id=100051125}</div>
<div>{lang_print id=100051126}</div>
<br />
<br />

{if $justinvited == 1}
  <table cellpadding='0' cellspacing='0'><tr><td class='success'>
  <img src='./images/success.gif' border='0' class='icon'>{lang_print id=341}
  </td></tr></table>
  <br>
{/if}

{* SHOW ERROR OR SUCCESS MESSAGES *}
{if $result != 0}
  <table cellpadding='0' cellspacing='0'><tr><td class='success'>
  <img src='./images/success.gif' border='0' class='icon'>{lang_print id=$result}
  </td></tr></table>
{elseif $is_error != 0}
  <table cellpadding='0' cellspacing='0'>
  <tr><td class='error'><img src='./images/error.gif' border='0' class='icon'>{lang_print id=$is_error}</td></tr>
  </table>
{/if}

{* SHOW NO INVITES LEFT PAGE *}
{if $setting.setting_signup_invite == 2 && $user->user_info.user_invitesleft == 0}

  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td class='result'><img src='./images/icons/bulb16.gif' border='0' class='icon'> {lang_sprintf id=1077 1='0'}</td>
  </tr>
  </table>

{else}

  {* IF INVITE ONLY FEATURE IS TURNED OFF, HIDE NUMBER OF INVITES LEFT *}
  {if $setting.setting_signup_invite == 2}
    <table cellpadding='0' cellspacing='0'>
    <tr>
    <td class='result'><img src='./images/icons/bulb16.gif' border='0' class='icon'> {lang_sprintf id=1077 1=$user->user_info.user_invitesleft}</td>
    </tr>
    </table>
    <br>
  {/if}

  <div id="openidconnect_facebook_connect" style="display:none">

    <br>
    <br>
    <table cellpadding='0' cellspacing='0' align='center'>
    <tr><td>

      {lang_print id=100051127} <br><br>
  
      <div style="padding-bottom: 10px; padding-left: 20px">
      {assign var=openid_facebook_landingpage value="`$url->url_base`login_openid.php?openidservice=facebook&next=user_openid_invite_facebook.php"}
      {include file='openidconnect_facebook_button.tpl'}
      </div>

    </td></tr>
    </table>

    <br>
    <br>

  </div>

  <div id="openidconnect_facebook_invite_dialog" style="display:block">

    <br>
      
      {lang_print id=100051148} <a href="invite.php">{lang_print id=100051149}</a> {lang_print id=100051150}
      
    <br><br>

    {assign var=openid_invite_facebook_action value="`$url->url_base`user_openid_invite_facebook.php?justinvited=1"}
    {assign var=openid_invite_facebook_type value=$openidconnect_feed_public_site_name}
    {assign var=openid_invite_facebook_content value=$openidconnect_facebook_invitemessage}
    {assign var=openid_invite_facebook_actiontext value=$openidconnect_facebook_inviteactiontext}
    {assign var=openid_invite_facebook_max value=$user->user_info.user_invitesleft}

    {include file='openid_invite_facebook.tpl'}

  </div>

  <script type="text/javascript">
  openidconnect_register_invite_form();
  </script>
  
{/if}


{include file='footer.tpl'}