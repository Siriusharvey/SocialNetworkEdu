{include file='admin_header.tpl'}

{literal}
<script>
function show_upselector() {
  document.getElementById("upselector_button").style['display'] = "none";
  document.getElementById("upselector_div").style['display'] = "block";
}

function openidconnect_facebook_focus_on_story(story) {
  var myFx = new Fx.Scroll($(document.body));
  myFx.toElement('feedstory_' + story); 
}
</script>
{/literal}


<h2>{lang_print id=100051151}</h2>
{lang_print id=100051152}


<br><br>

{if $result != 0}

  {if empty($error_message) AND empty($error_messages) }
  <div class='success'><img src='../images/success.gif' class='icon' border='0'> {lang_print id=100051004} </div>
  {else}
  
    {if !empty($error_message)}
      <div class='error'><img src='../images/error.gif' class='icon' border='0'> {if $error_message|@is_numeric}{lang_print id=$error_message}{else}{$error_message}{/if} </div>
    {/if}

    {if !empty($error_messages)}
    
      {foreach from=$error_messages item=error_message}
      <div class='error'><img src='../images/error.gif' class='icon' border='0'> {if $error_message|@is_numeric}{lang_print id=$error_message}{else}{$error_message}{/if} </div>
      {/foreach}
    
    {/if}
  
  {/if}


{/if}


<table cellpadding='0' cellspacing='0' width='700'>
<tr>
<td style="text-align: right">

  <input id="upselector_button" type='button' class='button' value='{lang_print id=100051153}' onclick="show_upselector()">

  <div id="upselector_div" style="display:none">
    <form action='admin_openidconnect_facebook_stories.php' method='post' name='items'>
      {lang_print id=100051154}: &nbsp;
      <select class='text' name='actiontype_name'><option></option>
      {foreach from=$available_feed_stories item=available_feed_story}
      <option value='{$available_feed_story.actiontype_name}'>{lang_print id=$available_feed_story.actiontype_desc} ({$available_feed_story.actiontype_name})</option>
      {/foreach}
      </select>&nbsp;
    <input type='submit' class='button' value='{lang_print id=100051155}'>
    <input type='hidden' name='task' value='addnew'>
    </form>
  </div>

</td>
</tr>
</table>

<br>

<form action='admin_openidconnect_facebook_stories.php' method='POST'>

<table cellpadding='0' cellspacing='0' width='700'>
<tr>
<td class='header'>{lang_print id=100051077}</td>
</tr>

<tr>
<td class='setting1'>

{lang_print id=100051078}

<br>
<br>
For Help&FAQ on Feed Stories, please see <a href="admin_openidconnect_facebook_help.php">Help&FAQ page</a>
<br>
<br>

{lang_print id=100051079} <a target=_blank href="http://wiki.developers.facebook.com/index.php/Template_Data">Facebook documentation</a>

<br>
<br>
{lang_print id=100051080}
<br>
<br>

{if $storyfocus == ''}
<input type="button" id="feed_story_templates_warning" class='button' style="display:block" onclick="javascript:SEMods.B.toggle('feed_story_templates_warning','feed_story_templates')" value="{lang_print id=100051081}">
{/if}

<br>

</td></tr><tr><td class='setting2' style="padding: 0px">

<div id="feed_story_templates" {if $storyfocus == ''}style="display:none;"{/if}>

  <table cellpadding='3' cellspacing='0' width='100%'>
  {foreach from=$openidconnect_facebook_feed_actions item=feedstory}
    <tr>
    <td id="feedstory_{$feedstory.feedstory_type}" colspan="2" Xclass='setting1' style='background-color: white; padding: 10px; border-bottom: 1px solid #DDD; border-top: 1px solid #DDD'>
      <br>
      <span style="font-size: 16px; font-weight: bold">{$feedstory.feedstory_desc}</span>
      <br><br>
    </td>
    </tr>
    <tr>
    <td valign='top' style='padding: 10px;'>
      <b>{lang_print id=100051082}</b>
      <input name='feedstory[{$feedstory.feedstory_id}][feedstory_userprompt]' style='width: 100%;' class='text' value='{$feedstory.feedstory_userprompt}'>
      <br>
      <br>
        
      <b>{lang_print id=100051083}</b>
      <textarea name='feedstory[{$feedstory.feedstory_id}][feedstory_usermessage]' rows='3' style='width: 100%;' class='text'>{$feedstory.feedstory_usermessage }</textarea>
      <br>
      <br>
      <br>

      <b>{lang_print id=100051084}</b>
      <textarea name='feedstory[{$feedstory.feedstory_id}][feedstory_title]' rows='3' style='width: 100%;' class='text'>{$feedstory.feedstory_metadata.feedstory_title}</textarea>
      <br>
        {lang_print id=100051085}: {$feedstory.feedstory_vars}<br />
      <br>
        
      <b>{lang_print id=100051086}</b>
      <textarea name='feedstory[{$feedstory.feedstory_id}][feedstory_body]' rows='4' style='width: 100%;' class='text'>{$feedstory.feedstory_metadata.feedstory_body}</textarea>
      <br>
        {lang_print id=100051085}: {$feedstory.feedstory_vars}<br />
      <br><br>

      <b>{lang_print id=100051087}</b>
      <input name='feedstory[{$feedstory.feedstory_id}][feedstory_link_link]' style='width: 100%;' class='text' value='{$feedstory.feedstory_metadata.feedstory_link_link}'>
      <br>
        {lang_print id=100051085}: {$feedstory.feedstory_vars}<br />
      <br>
        
      <b>{lang_print id=100051088}</b>
      <textarea name='feedstory[{$feedstory.feedstory_id}][feedstory_link_text]' rows='3' style='width: 100%;' class='text'>{$feedstory.feedstory_metadata.feedstory_link_text}</textarea>
      <br>
        {lang_print id=100051085}: {$feedstory.feedstory_vars}<br />
      <br>

      <b>{lang_print id=100051089}: </b>
      <input type='text' disabled readonly style='width: 100%;' class='text' value='{$feedstory.feedstory_metadata.template_bundle_id}'>

      <input type='hidden' name='feedstory[{$feedstory.feedstory_id}][feedstory_type]' value='{$feedstory.feedstory_type}'>
      
      <br>
      <br>
      <input name='feedstory[{$feedstory.feedstory_id}][feedstory_enabled]' id='feedstory_enabled_{$feedstory.feedstory_id}' type='checkbox' value='1' {if $feedstory.feedstory_enabled == 1} checked='checked'{/if}> <label for='feedstory_enabled_{$feedstory.feedstory_id}'>{lang_print id=100051090}</label><br>

    </td>
    <td valign='top' width='80' nowrap="nowrap" style='padding-left: 10px; padding-right: 10px'>
      <b>{lang_print id=100051091}</b><br />
      {$feedstory.feedstory_type}
    </td>
    </tr>
    <tr>
    <td colspan='2'{if $smarty.section.actiontype_loop.last != true} style='padding-bottom: 50px;'{/if}>
    </td>
    </tr>
  {/foreach}
  </table>

</div>

</td></tr>

</table>

<br><br>

<input type='submit' class='button' value='{lang_print id=100051032}'>
<input type='hidden' name='task' value='dosave'>
</form>

{if $storyfocus != ''}
<script type="text/javascript">
  SEMods.B.register_onload( function() {ldelim} openidconnect_facebook_focus_on_story('{$storyfocus}') {rdelim} );
</script>
{/if}

{include file='admin_footer.tpl'}