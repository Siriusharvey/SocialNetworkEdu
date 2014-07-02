
<script src="http://static.ak.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php" type="text/javascript"></script> 


<script type="text/javascript">
  var openidconnect_primary_network = 'facebook';

  var openidconnect_facebook_api_key = "{$openidconnect_facebook_api_key}";
  var openidconnect_facebook_user_id = "{$openidconnect_facebook_user_id}";
</script>




{if $openidconnect_request_connect}
<div id="openidconnect_connect_prompt" style="display:none">

    <div id='form_div'>
  
      <div style="border-bottom: 1px solid #DDD; margin-bottom: 10px; padding-bottom: 5px;">
        <img style="float:left; padding-right: 5px; width: 16px" src="./images/brands/logo_facebook_mini.gif"> &nbsp; <span style="font-size: 14px">{lang_print id=100051092}</span>
      </div>
      
      <br/>
      
        {lang_print id=100051093}

      <br/>
      <br/>
        
        <table cellpadding='0' cellspacing='0' style="margin: 0px auto">
        <tr>
        <td><input type='submit' style="width: 150px" class='button openidconnect_connect_prompt_confirmed' onclick="openidconnect_facebook_request_connect_confirmed()" value='{lang_print id=100051094}'>&nbsp;&nbsp;</td>
        <td style="padding-left: 10px"> {lang_print id=100051054} <a href="javascript:void(0)" class='openidconnect_connect_prompt_cancel' onclick="openidconnect_facebook_request_connect_cancel()">{lang_print id=100051052}</a>&nbsp;&nbsp;</td>
        </tr>
        </table>

      <br/>

    </div>
  
</div>
{/if}






{*if !$user->user_exists*}
<div id="openidconnect_autologin_prompt" style="display:none">

  <div id='form_div'>

    <div style="border-bottom: 1px solid #DDD; margin-bottom: 10px; padding-bottom: 5px;">
      <img style="float:left; padding-right: 5px; width: 16px" src="./images/brands/logo_facebook_mini.gif"> &nbsp; <span style="font-size: 14px">{lang_print id=100051095}</span>
    </div>
    
    <br/>


    <div style="position: relative; text-align: center">
    
      <div style="margin: 0px auto;width: 500px">
        {lang_print id=100051096}
      </div>

    </div>

    <br/>
    <br/>
      
      <table cellpadding='0' cellspacing='0' style="margin: 0px auto">
      <tr>
      <td><input type='submit' style="width: 150px" class='button openidconnect_autologin_prompt_confirmed' onclick="openidconnect_autologin_confirmed()" value='{lang_print id=100051097}'>&nbsp;&nbsp;</td>
      <td style="padding-left: 10px"> {lang_print id=100051054} <a class="openidconnect_autologin_prompt_cancel"  href="javascript:void(0)" onclick="openidconnect_autologin_cancel()">{lang_print id=100051052}</a>&nbsp;&nbsp;</td>
      </tr>
      <tr>
      <td>

        <input class="openidconnect_autologin_remember" type='checkbox' style="vertical-align:middle">&nbsp;&nbsp;<label for='openidconnect_autologin_remember' style="vertical-align:middle">{lang_print id=100051098}</label>

      </td>
      <td> &nbsp; </td>
      </tr>
      </table>

    <br/>

  </div>
  
</div>
{*/if*}





{if $openidconnect_feed_story_publish}
  <script type="text/javascript">
  
    //var openidconnect_facebook_feed_story = eval({$openidconnect_feed_story});
  
    {if $openidconnect_feed_story.publish_prompt}
    
      var openidconnect_facebook_feed_story_template_bundle_id = '{$openidconnect_feed_story.template_bundle_id}';
      var openidconnect_facebook_feed_story_data = eval({$openidconnect_feed_story.data});
    
      var openidconnect_facebook_feed_story_user_prompt = '{$openidconnect_feed_story.user_prompt}';
      var openidconnect_facebook_feed_story_user_message = '{$openidconnect_feed_story.user_message}';
    
      var openidconnect_facebook_feed_story_params = '{$openidconnect_feed_story.story_params}';
      var openidconnect_facebook_feed_story_type = '{$openidconnect_feed_story.story_type}';
    
      openidconnect_publish_feed_story_prompt();
  
    {else}
    
      {if $openidconnect_feed_story.publish_using == 'feed'}
  
        openidconnect_facebook_publish_feed_story( '{$openidconnect_feed_story.story_type}', eval({$openidconnect_feed_story.data}), '{$openidconnect_feed_story.template_bundle_id}', '{$openidconnect_feed_story.user_prompt}', '{$openidconnect_feed_story.user_message}' );
  
      {else}
  
        openidconnect_facebook_publish_stream( '{$openidconnect_feed_story.story_type}', eval({$openidconnect_feed_story.data}), '{$openidconnect_feed_story.user_prompt}', '{$openidconnect_feed_story.user_message}' );
  
      {/if}
    
    {/if}
  
  </script>


  <div id="openidconnect_publish_feed_story_prompt" style="display:none">
  
      <div id='form_div'>
    
        <div style="border-bottom: 1px solid #DDD; margin-bottom: 10px; padding-bottom: 5px;">
          <img style="float:left; padding-right: 5px; width: 16px" src="./images/brands/logo_facebook_mini.gif"> &nbsp; <span style="font-size: 14px">{lang_print id=100051099}</span>
        </div>
        
        <br/>
        
        <div style="position: relative; text-align: center">
        
          <div style="border: 1px solid #EEE; background-color: #F6F6F6; padding: 5px; margin: 0px auto; margin-bottom: 40px; width: 500px">
            {$openidconnect_feed_story.story_preview}
          </div>
  
        </div>
        
          <table cellpadding='0' cellspacing='0' style="margin: 0px auto">
          <tr>
          <td><input type='submit' style="width: 150px" class='button openidconnect_publish_feed_story_prompt_confirmed' onclick="openidconnect_publish_feed_story_prompt_confirmed()" value='{lang_print id=100051100}'>&nbsp;&nbsp;</td>
          <td style="padding-left: 10px"><input type='submit' class='button openidconnect_publish_feed_story_prompt_wait' onclick="openidconnect_publish_feed_story_prompt_wait()" value='{lang_print id=100051101}'>&nbsp;&nbsp;</td>
          <td style="padding-left: 10px"><input type='submit' class='button openidconnect_publish_feed_story_prompt_cancel' onclick="openidconnect_publish_feed_story_prompt_cancel('{$openidconnect_feed_story.story_type}')" value='{lang_print id=100051102}'>&nbsp;&nbsp;</td>
          </tr>
  
          <tr>
          <td>
  
          </td>
          <td>&nbsp;</td>
          <td style="padding-left: 10px">
  
            <input class="openidconnect_publish_feed_story_neveragain" type='checkbox' style="vertical-align:middle">&nbsp;&nbsp;<label for='openidconnect_publish_feed_story_neveragain' style="vertical-align:middle">{lang_print id=100051103}</label>
  
          </td>
          </tr>
          </table>
  
        <br/>
  
      </div>
    
  </div>

{/if}



<script type="text/javascript">
  SEMods.B.register_onload( function() {ldelim} openidconnect_facebook_onload( {ldelim} 'user_exists' : '{$user->user_exists}', 'hook_logout' : '{$openidconnect_hook_logout}', 'autologin' : '{$openidconnect_autologin}', 'request_connect' : '{$openidconnect_request_connect}' {rdelim} ); {rdelim} );
</script>
