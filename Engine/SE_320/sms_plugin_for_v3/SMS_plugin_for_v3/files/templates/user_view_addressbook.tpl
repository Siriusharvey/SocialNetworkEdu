{include file='header.tpl'}
<table class='tabs' cellpadding='0' cellspacing='0'>
<tr>
<td class='tab0'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_sms_settings.php'>SMS Settings</a></td>

<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_compose_sms.php'>Compose New SMS</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_address_smsbook.php'>Address Book</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_sms_history.php'>SMS History</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab1' NOWRAP><a href='user_view_addressbook.php'>View Address Book</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_view_smscredits.php'>View SMS Credits</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_buy_credits.php'>Buy SMS Credits</a></td>
<td class='tab3'>&nbsp;</td>
</tr>
</table>
<br>
{* JAVASCRIPT FOR CHECK ALL MESSAGES FEATURE *}
{literal}
 <script type="text/javascript">
function confirm_delete() {
    return confirm('Are you sure you want to delete this record?');
}

function funchk(num)
{

flag=0;
more_delete1="";
more_delete2="";
if(num>1)
	{
	for(i=0;i<num;i++)
			{
				if(document.messageform.more_del[i].checked == true)
				{
				
					more_delete1=document.messageform.more_del[i].value;
					
					if(more_delete2=="")
					{
					more_delete2=more_delete1;
					}
					else
					{
					more_delete2=more_delete2+","+more_delete1;
					}
					
					more_delete1="";
					flag +=1;
				}
			}
	
		if(flag=="0")
		{
			alert("Select Atleast One");
			return false;
		}
		document.location.href="user_view_addressbook.php?id="+more_delete2;
		return true;
	}
else if(document.messageform.more_del.checked == true)
	{
	
	more_delete2=document.messageform.more_del.value;
	document.location.href="user_view_addressbook.php?id="+more_delete2;
	flag +=1;
	}
if(flag=="0")
{
			alert("Select Atleast One");
			return false;
}
return true;

}










function funsms(nums)
{
flag=0;
more_sms1="";
more_sms2="";
if(nums>1)
	{
	for(i=0;i<nums;i++)
			{
				if(document.messageform.more_sms[i].checked == true)
				{
					more_sms1=document.messageform.more_sms[i].value;
					
					if(more_sms2=="")
					{
					more_sms2=more_sms1;
					}
					else
					{
					more_sms2=more_sms2+","+more_sms1;
					}
					
					more_sms1="";
					flag +=1;
				}
			}
	
		if(flag=="0")
		{
			alert("Select Atleast One");
			return false;
		}
		document.location.href="user_compose_sms.php?phone="+more_sms2;
		return true;
	}
else if(document.messageform.more_sms.checked == true)
	{
	
	more_delete2=document.messageform.more_sms.value;
	document.location.href="user_compose_sms.php?id="+more_sms2;
	flag +=1;
	}
if(flag=="0")
{
			alert("Select Atleast One");
			return false;
}
return true;

}





</script>
{/literal}
{* DISPLAY PAGINATION MENU IF APPLICABLE *}
{if $maxpage > 1}
  <div class='center'>
  {if $p != 1}<a href='user_messages_outbox.php?p={math equation='p-1' p=$p}'>&#171; {lang_print id=182}</a>{else}<font class='disabled'>&#171; {lang_print id=182}</font>{/if}
  {if $p_start == $p_end}
    &nbsp;|&nbsp; {lang_sprintf id=184 1=$p_start 2=$total_pms} &nbsp;|&nbsp; 
  {else}
    &nbsp;|&nbsp; {lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_pms} &nbsp;|&nbsp; 
  {/if}
  {if $p != $maxpage}<a href='user_messages_outbox.php?p={math equation='p+1' p=$p}'>{lang_print id=183} &#187;</a>{else}<font class='disabled'>{lang_print id=183} &#187;</font>{/if}
  </div>
<br>
{/if}


{* CHECK IF THERE ARE NO MESSAGES IN OUTBOX *}
{if $total_pms == 0}

  <div class='center'>
    <table cellpadding='0' cellspacing='0'><tr>
    <td class='result'><img src='./images/icons/bulb16.gif' border='0' class='icon'>{lang_print id=799}</td>
    </tr></table>
  </div>


{* DISPLAY MESSAGES *}
{else}

  <form action='#' method='post' name='messageform'>
  <table class='messages_table' cellpadding='0' cellspacing='0' border="0">
  

  
 <tr bgcolor="#444444" >

				    <td width="20" class='form1' align="left"><font color="#FFFFFF">#</font></td>

				    <td width="45" class='form1' align="center"><font color="#FFFFFF">Nickname</font></td>

				    <td width="60" class='form1' align="center"><font color="#FFFFFF">Full Name</font></td>

				    <td width="80" class='form1' align="center"><font color="#FFFFFF">Email</font></td>

				    <td width="50" class='form1' align="center"><font color="#FFFFFF">Mobile/Phone</font></td>

				    <td width="60" class='form1' align="center"><font color="#FFFFFF">Group</font></td>

				    <td class='form1' width="150" align="center"><font color="#FFFFFF">Action</font></td>
				     </tr>
  {* LIST SENT MESSAGES *}
  {section name=pm_loop loop=$pms}
<tr bgcolor="#CCCCCC">
 <td class='form1' align="center">{$pms[pm_loop].pmconvo_id}</td>
<td class='form1' align="center">{$pms[pm_loop].pmconvo_nickname}</td>
<td class='form1' align="center">{$pms[pm_loop].pm_fullname}</td>
<td class='form1' align="center">{$pms[pm_loop].pm_email}</td>
<td class='form1' align="center">{$pms[pm_loop].pm_replied}</td>
<td class='form1' align="center">{$pms[pm_loop].pm_grup}</td>
<td class='form1'><a href="address_smsbook.php?id={$pms[pm_loop].pmconvo_id}">Edit</a>
<input type="checkbox"  name="more_del[]" id="more_del" value="{$pms[pm_loop].pmconvo_id}" />
<input type="checkbox"  name="more_sms[]" id="more_sms" value="{$pms[pm_loop].pm_replied}" />
</td>
</tr>	
  {/section}
  
  <tr bgcolor="#CCCCCC">
 <td class='form1' align="center" colspan="7">
  <a  href="javascript:void(0)" onClick="return funchk({$smarty.section.pm_loop.index});">Delete</a> | <a  href="javascript:void(0)" onClick="return funsms({$smarty.section.pm_loop.index});">SMS</a>  </td>
  </tr>
  
  </table>
  
  <br>
  <input type='hidden' name='p' value='{$p}'>
  </form>

{/if}

{include file='footer.tpl'}