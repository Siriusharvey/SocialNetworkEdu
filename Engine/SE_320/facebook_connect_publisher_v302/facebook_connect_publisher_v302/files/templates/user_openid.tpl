{include file='header.tpl'}

<table class='tabs' cellpadding='0' cellspacing='0'>
<tr>
<td class='tab0'>&nbsp;</td>
<td class='tab1' NOWRAP><a href='user_account.php'>{lang_print id=655}</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_account_privacy.php'>{lang_print id=1055}</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_account_pass.php'>{lang_print id=756}</a></td>
{if $user->level_info.level_profile_delete != 0}<td class='tab'>&nbsp;</td><td class='tab2' NOWRAP><a href='user_account_delete.php'>{lang_print id=757}</a></td>{/if}
<td class='tab3'>&nbsp;</td>
</tr>
</table>

<img src='./images/icons/settings48.gif' border='0' class='icon_big'>
<div class='page_header'>{lang_print id=755}</div>
<div>{lang_print id=808}</div>
<br />
<br />

{* SHOW ERROR OR SUCCESS MESSAGES *}
{if $result != 0}
  <table cellpadding='0' cellspacing='0'><tr><td class='success'>
  {capture assign="old_subnet_name"}{lang_print id=$old_subnet_name}{/capture}
  {capture assign="new_subnet_name"}{lang_print id=$new_subnet_name}{/capture}
  <img src='./images/success.gif' border='0' class='icon'>{lang_sprintf id=$result 1=$old_subnet_name 2=$new_subnet_name}
  </td></tr></table>
{elseif $is_error != 0}
  <table cellpadding='0' cellspacing='0'>
  <tr><td class='error'><img src='./images/error.gif' border='0' class='icon'>{lang_print id=$is_error}</td></tr>
  </table>
{/if}

<table cellspacing=10 cellpadding=0>
{foreach from=$linked_openid_services item=openid_service}
<tr>
  <td>
    <img border='0' src="./images/brands/{$openid_service.openidservice_logo_large}"
  </td>
  <td style="padding-left: 10px">
    {if empty($openid_service.openid_user_key)}
    
    {if $openid_service.openidservice_name == 'facebook'}

      {assign var=openid_facebook_landingpage value="`$url->url_base`login_openid.php?openidservice=facebook&task=confirmlink&next=user_openid_invite_facebook.php"}
      {include file='openidconnect_facebook_button.tpl'}

    {else}
    
    <a href="http://{$openid_relay_url}/login/{$openid_service.openidservice_name}" title="{$openid_service.openidservice_displayname}"> Connect Now </a>
    
    {/if}
    
    
    {else}

    {*    
    {if $openid_service.openidservice_name == 'facebook'}

      <a href="javascript:void(0)" onclick=" FB.Connect.logout(function() {ldelim} window.location = 'user_openid.php?task=disconnect&service={$openid_service.openid_service_id}'; {rdelim});return false;">
      <img border='0' id="fb_logout_image" src="http://static.ak.fbcdn.net/images/fbconnect/logout-buttons/logout_medium.gif" alt="Facebook Connect" border="0" />
      </a>
    
    {else}
    *}
    <a href="user_openid.php?task=disconnect&service={$openid_service.openid_service_id}">Disconnect</a>
    
    {*/if*}
    
    {/if}
  </td>
</tr>  
{/foreach}
</table>


{include file='footer.tpl'}