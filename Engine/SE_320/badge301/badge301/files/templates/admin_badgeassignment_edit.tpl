{include file='admin_header.tpl'}

{assign var=badge value=$badgeassignment->badgeassignment_badge}
{assign var=user value=$badgeassignment->badgeassignment_user}

<h2>{lang_print id=11270006}</h2>
{lang_print id=11270166}
<br />
<br />
<a href="{$url->url_create('badgeassignment', null, $badgeassignment->badgeassignment_info.badgeassignment_id)}" target="_blank">{lang_print id=11270167}</a>
<br />
<br />

{if $is_error}
  <div class='error'><img src='../images/error.gif' class='icon' border='0'> {lang_print id=$is_error}</div>
{/if}

{if $result}
  <div class='success'><img src='../images/success.gif' class='icon' border='0'> {lang_print id=191}</div>
{/if}

<form action="admin_badgeassignment_edit.php" method="POST" >
<input type="hidden" name="badgeassignment_id" value="{$badgeassignment->badgeassignment_info.badgeassignment_id}" />
<input type="hidden" name="task" value="dosave" />

<table cellspacing="0" cellpadding="0" width="640" id="badge_edit_form">
  <tr>
    <td class="form1">{lang_print id=11270021}:</td>
    <td class="form2"><a href="{$url->url_create('badge', null, $badge->badge_info.badge_id)}" target="_blank">{$badge->badge_info.badge_title}</a></td>
  </tr>
  <tr>
    <td class="form1">{lang_print id=11270028}:</td>
    <td class="form2"><a href="{$url->url_create('profile', $user->user_info.user_username)}" target="_blank">{$user->user_info.user_displayname}</a></td>
  </tr>
  <tr>
    <td class="form1">{lang_print id=11270010}:</td>
    <td class="form2">
 
      <script type="text/javascript" src="../include/fckeditor/fckeditor.js"></script>
		  <script type="text/javascript">
		  <!--
		  var sBasePath = "../include/fckeditor/" ;
		  var sToolbar = "badge_admin";
		  var oFCKeditor = new FCKeditor( 'badgeassignment_desc' ) ;
		  oFCKeditor.Config["CustomConfigurationsPath"] = "../../js/badge_fckconfig.js";
		  oFCKeditor.BasePath = sBasePath ;
		  oFCKeditor.Height = "250" ;
		  if ( sToolbar != null )
		    oFCKeditor.ToolbarSet = sToolbar ;
		  oFCKeditor.Value = '{$badgeassignment->badgeassignment_info.badgeassignment_desc}' ;
		  oFCKeditor.Create() ;
		  //-->
		  </script>
		  
    </td>
  </tr>

  <tr>
    <td class="form1"></td>
    <td class="form2">
      <input type="submit" value="{lang_print id=173}" class="button" />    
    - <a href="admin_badgeassignments.php">{lang_print id=11270085}</a>  
    </td>
  </tr> 
</table>
  

  
</form>

<br><br>


{include file='admin_footer.tpl'}
