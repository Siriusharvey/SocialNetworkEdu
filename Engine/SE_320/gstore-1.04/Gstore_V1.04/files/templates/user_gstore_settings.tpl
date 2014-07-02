{include file='header.tpl'}



<table cellpadding='0' cellspacing='0' width='100%'>
  <tr>
    <td valign='top'>
      
      <img src='./images/icons/gstore_gstore48.gif' border='0' class='icon_big' />
      <div class='page_header'>{lang_print id=5555115}</div>
      <div>{lang_print id=5555116}</div>
      
    </td>
    <td valign='top' align='right'>
      
      <table cellpadding='0' cellspacing='0'>
        <tr>
          <td class='button' nowrap='nowrap'>
            <a href='user_gstore.php'><img src='./images/icons/back16.gif' border='0' class='button' />{lang_print id=5555102}</a>
          </td>
        </tr>
      </table>
      
    </td>
  </tr>
</table>
<br />


{* SHOW SUCCESS MESSAGE *}
{if $result != 0}
  <table cellpadding='0' cellspacing='0'>
    <tr>
      <td class='success'>
        <img src='./images/success.gif' border='0' class='icon' />
        {lang_print id=191}
      </td>
    </tr>
  </table>
{/if}


   <hr />
	 

		

		<br />
		<br />
		<br />




<form action='user_gstore_settings.php' method='post'>

		{* PAYPAL SETTINGS *}
		{if $user->level_info.level_gstore_style}
		<div style=" background-color:#e3e3e3; border:1px solid #CCCCCC; padding:10px;">
				<table align="center" width="100%" cellpadding='0' cellspacing='0' class='form' style="border:1px solid #CCCCCC; background-color:#FFFFFF;">
					  <tr>
						<td>&nbsp;&nbsp;<img src="../images/icons/paypal.png" border="0" /> &nbsp;</td>
						<td class='form1'>Paypal Email Address*</td>
						<td align="left" class='form2'>
						<input type='text' class='text' name='paypal_email' value='{$paypal_email}' maxlength='100' size='30'><br />
						This is the Email address you are authorized to recive payments via Paypal
						</td>
					  </tr>
				</table>
		</div>
			  <br />
			  <br />
			  <br />
		{/if}

{* NOTIFICATION SETTINGS *}
<div><b>{lang_print id=5555119}</b></div>
<br />

{assign var="comment_options" value=$user->level_info.level_blog_comments|unserialize}
{if !("0"|in_array:$comment_options) || $comment_options|@count != 1}
  <table cellpadding='0' cellspacing='0' class='editprofile_options'>
    <tr>
      <td><input type='checkbox' value='1' id='gstorecomment' name='usersetting_notify_gstorecomment'{if $user->usersetting_info.usersetting_notify_gstorecomment} checked{/if}></td>
      <td><label for='gstorecomment'>{lang_print id=5555120}</label></td>
    </tr>
  </table>
  <br />
  <br />
{/if}

{lang_block id=173 var=langBlockTemp}<input type='submit' class='button' value='{$langBlockTemp}' />{/lang_block}
<input type='hidden' name='task' value='dosave' />
</form>



{include file='footer.tpl'}