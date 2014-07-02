
{if !isset($openid_facebook_landingpage) OR $openid_facebook_landingpage == ""}
  {assign var=openid_facebook_landingpage value="`$url->url_base`login_openid.php?openidservice=facebook"}
{/if}

<!--<div>-->
  <a href="javascript:void(0)" class="openidconnect_facebook_login_button">
  <img id="fb_login_image" src="http://static.ak.fbcdn.net/images/fbconnect/login-buttons/connect_light_medium_long.gif" alt="Facebook Connect" border="0" />
  </a>
<!--</div>-->

<script type="text/javascript">
openidconnect_register_facebook_login_button('{$openid_facebook_landingpage}');
</script>