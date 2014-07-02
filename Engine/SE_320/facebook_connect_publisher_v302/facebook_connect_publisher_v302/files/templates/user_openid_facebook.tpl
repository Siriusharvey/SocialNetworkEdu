{include file='header.tpl'}

{if $service_connected}
<table class='tabs' cellpadding='0' cellspacing='0'>
<tr>
<td class='tab0'>&nbsp;</td>
<td class='tab1' NOWRAP><a href='user_openid_facebook.php'>{lang_print id=100051104}</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_openid_facebook_friends.php'>{lang_print id=100051105}</a></td>
<td class='tab'>&nbsp;</td>
{if $setting.setting_signup_invite != 1}
<td class='tab2' NOWRAP><a href='user_openid_invite_facebook.php'>{lang_print id=100051106}</a></td>
<td class='tab'>&nbsp;</td>
{/if}
<td class='tab2' NOWRAP><a href='user_openid_facebook_settings.php'>{lang_print id=100051107}</a></td>
<td class='tab3'>&nbsp;</td>
</tr>
</table>
{/if}

<img src='./images/icons/facebook48.png' border='0' class='icon_big'>
<div class='page_header'>{lang_print id=100051108}</div>
<div>{lang_print id=100051109}</div>
<br />
<br />

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





{if !$service_connected}




  <br><br>
  
  <table cellspacing=0 cellpadding=0 align='center'>
  <tr>
    <td>
    {lang_print id=100051110}
    <br><br><br>
    </td>
    </tr>
    <tr>
    <td align="center">
    <!--<div style="margin: 0px auto;">-->
    {assign var=openid_facebook_landingpage value="`$url->url_base`login_openid.php?openidservice=facebook&task=confirmlink&next=user_openid_invite_facebook.php"}
    {include file='openidconnect_facebook_button.tpl'}
    <!--</div>-->
    </td>
  </tr>
  </table>

  <br><br>




{else}


  <div id="openidconnect_facebook_require_login_loading" style="display:block">

    <br>
    <br>
    <table cellpadding='0' cellspacing='0' align='center'>
    <tr><td class='result'>
      <img src='./images/icons/semods_ajaxprogress1.gif' border='0' class='icon'>{lang_print id=100051111}
    </td></tr>
    </table>

  </div>


  <div id="openidconnect_facebook_notloggedin" style="display:none">

    <br>
    <br>
    <table cellpadding='0' cellspacing='0' align='center'>
    <tr><td>

      {lang_print id=100051112} <br><br>
  
      <div style="padding-bottom: 10px; padding-left: 20px">
      {assign var=openid_facebook_landingpage value="`$url->url_base`login_openid.php?openidservice=facebook&next=user_openid_facebook.php"}
      {include file='openidconnect_facebook_button.tpl'}
      </div>

    </td></tr>
    </table>

    <br>
    <br>

  </div>


  <div id="openidconnect_facebook_loggedin" style="display:none">

    <table cellspacing=0 cellpadding=0 style="width:100%">
    <tr>
      <td style="width:50%; vertical-align: top">
        
        <div style="font-size: 14px; border-bottom: 1px solid #EEE; padding-bottom: 2px; margin-bottom: 7px;">
          <div style="float: left; font-size: 14px">{lang_print id=100051113} {$linked_friends_stats.connected_friends} {lang_print id=100051114} </div>
          <div style="float: right; font-size: 14px"><a href="user_openid_facebook_friends.php">{lang_print id=100051115}</a></div>
          <div style="clear:both"></div>
        </div>
  
        <div style="padding: 5px">
        {if count($linked_friends) > 0}
          {foreach from=$linked_friends item=linked_friend}
          <div style="width: 50px; float: left; padding-right: 5px; padding-bottom: 5px">
            <a href='{$url->url_create("profile",$linked_friend.user_username)}'>
              <img border='0' width=50 height=50 src="{$linked_friend.user_openid_thumb}" alt="{$linked_friend.user_displayname|regex_replace:"/&#039;/":"'"}">
            </a>
          </div>
          {/foreach}
        <div style="clear:both"></div>
        {else}
          {lang_print id=100051116} <a href="user_openid_invite_facebook.php"> {lang_print id=100051117} </a>
        {/if}
        </div>
  
      </td>
  
      <td style="padding-left: 50px; width:50%; vertical-align: top">
  
        <div style="font-size: 14px; border-bottom: 1px solid #EEE; padding-bottom: 2px; margin-bottom: 7px;">
          <div style="float: left; font-size: 14px">{lang_print id=100051118}</div>
          {if $linked_friends_stats.unconnected_friends != 0}
          <div style="float: right; font-size: 14px"><a href="user_openid_invite_facebook.php">{lang_print id=100051119}</a></div>
          {/if}
          <div style="clear:both"></div>
        </div>
  
        <div style="padding: 5px">
        {if count($unlinked_friends) > 0}
          {foreach from=$unlinked_friends item=unlinked_friend}
          <div style="width: 50px; float: left; padding-right: 2px; padding-bottom: 2px">
            <a href='user_openid_invite_facebook.php'>
              <img border='0' width=50 height=50 src="{$unlinked_friend.pic_square}" alt="{$unlinked_friend.name}" title="{$unlinked_friend.name}">
            </a>
          </div>
          {/foreach}
        <div style="clear:both"></div>
        {else}
          {lang_print id=100051120}
        {/if}
        </div>
        
      </td>
    </tr>  
    </table>

  </div>

  <script type="text/javascript">
  openidconnect_facebook_require_login();
  </script>

  <br><br>
  
  <div style="color: #999">
    <a style="color: #999" href="javascript:void(0)" onclick="openidconnect_facebook_disconnect('user_openid_facebook.php?task=disconnect&next=user_logout.php')">{lang_print id=100051121}</a> {lang_print id=100051122}  
  </div>




{/if}


{include file='footer.tpl'}