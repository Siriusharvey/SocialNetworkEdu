
  <div style="padding-bottom: 5px; padding-left: 9px">
    
    <div style="float: left; {if isset($openidconnect_extension_facebookpublisher) AND $openidconnect_extension_facebookpublisher == 1}padding-top: 4px{/if}">
      {lang_print id=100051047}
    </div>

    {if isset($openidconnect_extension_facebookpublisher) AND $openidconnect_extension_facebookpublisher == 1}
    <div style="padding-bottom: 5px; padding-left: 5px; float: left">
    {include file='openidconnect_facebook_button.tpl'}
    </div>
    {/if}

    {foreach from=$openid_services item=openid_service}
    <div style="padding-bottom: 5px; padding-left: 5px; float: left">
      <a href="http://{$openid_relay_url}/login/{$openid_service.openidservice_name}" title="{$openid_service.openidservice_displayname}"><img border='0' src="./images/brands/{$openid_service.openidservice_logo_mini}" alt="{$openid_service.openidservice_displayname}"></a>
    </div>
    {/foreach}
  
    <div style="clear:both"></div>  
  </div>
