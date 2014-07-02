
  {counter name='openidconnect_signup_inclusion_round' assign='openidconnect_signup_inclusion_round'}

  {* SECOND INCLUDE ROUND *}
  {if $openidconnect_signup_inclusion_round == 2}

  
    </td><td valign="top" style="padding-left: 50px;">
    
      <div class="signup_header" style="font-size: 14px; font-style: bold; margin-bottom: 10px; width: 150px; text-transform: uppercase">
      {lang_print id=100051047}
      </div>
      
      {if isset($openidconnect_extension_facebookpublisher) AND $openidconnect_extension_facebookpublisher == 1}
      <div style="Xpadding-left: 15px; padding-bottom: 15px;">
      {include file='openidconnect_facebook_button.tpl'}
      </div>
      {/if}
      
      {foreach from=$openid_services item=openid_service}
      <div style="padding-left: 15px; padding-bottom: 15px;">
        <a href="http://{$openid_relay_url}/login/{$openid_service.openidservice_name}" title="{$openid_service.openidservice_displayname}"><img border='0' src="./images/brands/{$openid_service.openidservice_logo_large}" alt="{$openid_service.openidservice_displayname}"></a>
      </div>
      {/foreach}
      
    </td>  
    </tr>
    </table>

  
  {* FIRST INCLUDE ROUND *}
  {else}

    <table cellspacing=0 cellpadding=0>
    <tr>
    <td>
    
  {/if}


