
{*
<fb:serverfbml style="width: 776px;">
*}

{if !isset($openid_invite_facebook_max)}
{assign var=openid_invite_facebook_max value=250}
{/if}

<fb:serverfbml style="width: 100%">
  <script type="text/fbml">
  <fb:fbml>
  <fb:request-form action="{$openid_invite_facebook_action}"
          method="POST"
          invite="true"
          type="{$openid_invite_facebook_type}"
          content="{$openid_invite_facebook_content}"
          <fb:multi-friend-selector
                  max="{$openid_invite_facebook_max}"
                  showborder="false"
                  actiontext="{$openid_invite_facebook_actiontext}" />
  </fb:request-form>
  </fb:fbml>
  </script>
</fb:serverfbml>


{*
<fb:serverfbml style="width: 776px;">
    <script type="text/fbml">
    <fb:fbml>
    <fb:request-form action="http://example.com/ignore/fb_friends_msg"
            method="POST"
            invite="true"
            type="MyWebsite"
            content="<fb:name uid='(facebook id of user goes here)' useyou='false' /> is a member of MyWebsite.com 
                    and would like to share that experience with you.  To register, simply click on the "Register" 
                    button below.<fb:req-choice url="http://mywebsite.com?signup.php?signup_referer=USERNAME" label="Register" />[% END %]">
            <fb:multi-friend-selector
                    showborder="false"
                    actiontext="Invite your Facebook Friends to use MyWebSite"
                    condensed="true" />
    </fb:request-form>
    </fb:fbml>
    </script>
</fb:serverfbml>

*}

