{include file='header.tpl'}

<div class='page_header'>{lang_print id=658}</div>

{lang_print id=673}
{if $setting.setting_signup_verify == 1}{lang_print id=674}{/if}
<br><br>

{* SHOW ERROR MESSAGE *}
{if $is_error != 0}
  <table cellpadding='0' cellspacing='0'>
  <tr><td class='error'><img src='./images/error.gif' border='0' class='icon'>{lang_print id=$is_error}</td></tr></table>
<br>
{/if}

{if $openid_services|@count > 0}
<table cellpadding='0' cellspacing='0' Xwidth="100%">
<tr>
  <td style="vertical-align: top; padding-top: 12px">
{/if}
    
<form action='login.php' method='POST' name='login'>
<table cellpadding='0' cellspacing='0'>
  {if isset($openidconnect_extension_facebookpublisher) AND $openidconnect_extension_facebookpublisher == 1}
  <tr>
    <td class='form1'>&nbsp;</td>
    <td class='form2'>
        <div style="padding-left: 5px">
        {include file='openidconnect_facebook_button.tpl'}
        </div>
    </td>
  </tr>
  {/if}
  <tr>
    <td class='form1'>{lang_print id=89}:</td>
    <td class='form2'><input type='text' class='text' name='email' id='email' value='{$email}' size='30' maxlength='70'></td>
  </tr>
  <tr>
    <td class='form1'>{lang_print id=29}:</td>
    <td class='form2'><input type='password' class='text' name='password' id='password' size='30' maxlength='50'></td>
  </tr>
  {if !empty($setting.setting_login_code) || (!empty($setting.setting_login_code_failedcount) && $failed_login_count>=$setting.setting_login_code_failedcount)}
  <tr>
    <td class='form1'>&nbsp;</td>
    <td class='form2'>
      <table cellpadding='0' cellspacing='0'>
        <tr>
          <td><input type='text' name='login_secure' class='text' size='6' maxlength='10' />&nbsp;</td>
          <td>
            <table cellpadding='0' cellspacing='0'>
              <tr>
                <td align='center'>
                  <img src='./images/secure.php' id='secure_image' border='0' height='20' width='67' class='signup_code' /><br />
                  <a href="javascript:void(0);" onClick="$('secure_image').src = './images/secure.php?' + (new Date()).getTime();">{lang_print id=975}</a>
                </td>
                <td>{capture assign=tip}{lang_print id=691}{/capture}<img src='./images/icons/tip.gif' border='0' class='Tips1' title='{$tip|escape:quotes}'></td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  {/if}
  <tr>
    <td class='form1'>&nbsp;</td>
    <td class='form2'>
      <input type='submit' class='button' value='{lang_print id=30}' />&nbsp; 
      <input type='checkbox' class='checkbox' name='persistent' id='persistent' value='1'>
      <label for='persistent'>{lang_print id=660}</label>
      <br />
      <br />
      <img src='./images/icons/help16.gif' border='0' class='icon' />
      <a href='lostpass.php'>{lang_print id=675}</a>
      <NOSCRIPT><input type='hidden' name='javascript_disabled' value='1' /></NOSCRIPT>
      <input type='hidden' name='task' value='dologin' />
      <input type='hidden' name='return_url' value='{$return_url}' />
    </td>
  </tr>
</table>
</form>

{if $openid_services|@count > 0}

</td>
  
<td style="padding-left: 50px; padding-top: 32px; font-size: 20px; font-style: bold; vertical-align: top">

{lang_print id=100051045}
  
</td>

<td style="padding-left: 50px;vertical-align: top">

  <table cellspacing=0 cellpadding=0>

    <tr>
      <td colspan=4 align=center>
        <div style="font-size: 14px; font-style: bold; margin-bottom: 10px">
          {lang_print id=100051041}
        </div>
      </td>
    </tr>

    {foreach from=$openid_services item=openid_service}
    {cycle name="startopenidservicerow" values="<tr>,,,"}
    <td>
      <div style="padding-left: 15px; padding-bottom: 15px;">
        <a href="http://{$openid_relay_url}/login/{$openid_service.openidservice_name}" title="{$openid_service.openidservice_displayname}"><img border='0' src="./images/brands/{$openid_service.openidservice_logo_large}" alt="{$openid_service.openidservice_displayname}"></a>
      </div>
    </td>
    {cycle name="endopenidservicerow" values=",,,</tr>"}
    {/foreach}
  
  </table>


</td>

</tr>
</table>

{/if}

{literal}
<script language="JavaScript">
<!--
window.addEvent('domready', function() {
	if($('email').value == "") {
	  $('email').focus();
	} else {
	  $('password').focus();
	}
});
// -->
</script>
{/literal}

{include file='footer.tpl'}