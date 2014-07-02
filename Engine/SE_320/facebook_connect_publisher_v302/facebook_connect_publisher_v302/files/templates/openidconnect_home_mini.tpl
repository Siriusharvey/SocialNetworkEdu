
  <table cellpadding='0' cellspacing='0' style="padding-left: 5px">
  <tr>
    <td style="font-weight: bold; padding-top: 10px; padding-bottom: 10px" align="center">
      {lang_print id=100051042}
    </td>
  </tr>
  <tr>
    <td>
      <div style="padding-left: -5px">

        {if isset($openidconnect_extension_facebookpublisher) AND $openidconnect_extension_facebookpublisher == 1}
        <div style="padding-bottom: 10px">
        {include file='openidconnect_facebook_button.tpl'}
        </div>
        {/if}
        
        {foreach from=$openid_services item=openid_service}
        <div style="padding-bottom: 5px; padding-left: 3px; float: left">
          <a href="http://{$openid_relay_url}/login/{$openid_service.openidservice_name}" title="{$openid_service.openidservice_displayname}"><img border='0' src="./images/brands/{$openid_service.openidservice_logo_mini}" alt="{$openid_service.openidservice_displayname}"></a>
        </div>
        {/foreach}
      </div>
    </td>
  </tr>
  </table>
