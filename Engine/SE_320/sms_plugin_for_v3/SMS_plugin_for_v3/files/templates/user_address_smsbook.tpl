{include file='header.tpl'}
<table class='tabs' cellpadding='0' cellspacing='0'>
<tr>
<td class='tab0'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_sms_settings.php'>SMS Settings</a></td>

<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_compose_sms.php'>Compose New SMS</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab1' NOWRAP><a href='user_address_smsbook.php'>Address Book</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_sms_history.php'>SMS History</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_view_addressbook.php'>View Address Book</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_view_smscredits.php'>View SMS Credits</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_buy_credits.php'>Buy SMS Credits</a></td>
<td class='tab3'>&nbsp;</td>
</tr>
</table>
<br>
{* JAVASCRIPT FOR SHOWING DEP FIELDS *}
  {literal}
<script type="text/javascript">
 function ValidateForm(){
if(document.form1.nickname.value=="")
{
alert("Please enter your Username.");
document.form1.nickname.focus();
return false;
}

if(document.form1.mobile.value=="")
{
alert("Please enter your Phone no.");
document.form1.mobile.focus();
return false;
}
  }
</script>
{/literal}
<table width="100%" >
<tr>
<td valign="top" width="50%" >


<form name="form1" id="form1" method="POST" action="user_address_smsbook.php" onSubmit="return ValidateForm()">
<table width="100%"  border="0" cellpadding="5" cellspacing="1" class="box_inner" id="table2">
{if $msg !=""}
<tr><td colspan="2" align="center" style="color:#FF0000">{$msg}</td></tr>
<tr>
{/if}
<td width="408" class='form1'>Nickname<font color="#FF0000"> *</font></td>
<td width="401" class='form2'><input name="nickname" id="nickname" type="text" class="o"  value="{$nickname}" size="30" maxlength="65" /></td>
</tr>
<tr>
<td width="408" class='form1'>Mobile Phone<font color="#FF0000">*</font> </td>
<td class='form2'><input name="mobile" type="text" class="o" id="mobile"  value="{$phone}" size="30" maxlength="65" /></td>
</tr>
<tr>
<td width="408" class='form1'>Fax</td>
<td class='form2'><input name="fax" type="text" class="o"  value="{$fax}" size="30" maxlength="65" /> </td>
 </tr>
<tr>
<td width="408" class='form1'>Home Phone</td>
<td class='form2'><input name="home" type="text" class="o"  value="{$home}" size="30" maxlength="65" /> </td>
</tr>
<tr>
<td width="408" class='form1'>Group</td>
<td class='form2'><select size="1" name="group" class="o">

<option selected value="">No group</option>
{section name=pm_loop loop=$pms}
 <option value='{$pms[pm_loop].pmconvo_grup}'{if $grup == $pms[pm_loop].pmconvo_grup} SELECTED{/if}>{$pms[pm_loop].pmconvo_grup}</option>
    {/section}
</select> 
</td>
</tr>
<tr>
<td width="508" class='form1'>First Name</td>
<td class='form2'><input name="first" class="o"  value="{$first}" maxlength="65" size="30" /></td>
</tr>
<tr>
<td width="208" class='form1'>Last Name</td>
<td class='form2'><input name="last" class="o"  value="{$last}" maxlength="65" size="30" /></td>
</tr>
<tr>
<td width="208" class='form1'>E-mail</td>
<td class='form2'><input name="email" type="text" class="o"  value="{$email}" size="30" maxlength="65" /> </td>
</tr>
<tr>
<td width="208" class='form1'>Address</td>
<td class='form2'><input name="address" type="text" class="o"  value="{$address}" size="30" maxlength="65" /> </td>
</tr>
<tr>
<td width="208" class='form1'>City</td>
<td class='form2'><input name="city" type="text" class="o"  value="{$city}" size="30" maxlength="65" /> </td>
</tr>
<tr>
 <td width="208" class='form1'>State</td>
 <td class='form2'><input name="state" type="text" class="o"  value="{$state}" size="30" maxlength="65" /> </td>
</tr>
<tr>
<td width="208" class='form1'>ZIP</td>
<td class='form2'><input name="zip" type="text" class="o"  value="{$zip}" size="30" maxlength="65" /> </td>
</tr>
<tr>
<td width="208" class='form1'>Country</td>
<td class='form2'><input name="country" type="text" class="o"  value="{$country}" size="30" maxlength="65" /> </td>
</tr>
<tr>
<td width="208" class='form1'>Details</td>
<td class='form2'><textarea rows="6" name="details" cols="42">{$details}</textarea> </td></tr>
 {if $update == 1}
<tr>
<td colspan="2" class='form2' align="center"><input type="submit" name="Update" class="o" value="Update">
<input type="hidden" name="id" class="o" value="{$id}">
 </td>
</tr>
{/if}
{if $update == ''}
<tr>
<td colspan="2" align="center" class='form2'><input type="submit" name="Submit" class="o" value="Submit"> </td>
</tr>
{/if}
</table>
</form>

</td>
<td colspan="12" valign="top" width="50%" >

<form method="post" action="address_smsbook.php">
<table width="100%" >
<tr>
<td width="208" class='form1' valign="top" align="left">Add new user group:</td>
<td class='form2'><input type="text" name="add_group" size="20" class="o" value=""> </td>
<td><input type="submit" name="add" class="o" value="Add Group">
</td></tr>
</table>
</form>


</td>
</tr>
</table>
{include file='footer.tpl'}


