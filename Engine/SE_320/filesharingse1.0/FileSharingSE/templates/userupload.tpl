{include file='header.tpl'}

{* JAVASCRIPT FOR CATEGORIES/FIELDS *}
{literal}
<script type='text/javascript'>
		


	function isEmpty(aTextField){
	var re = /\s/g; //Match any white space including space, tab, form-feed, etc.
	var str = aTextField.replace(re, "");
	if (str.length == 0) {
	return true;
	} else {return false;}
	} 
			
	function checkForm() {
		//alert("sdgggdfgdfdf");
		
		var discription=document.getElementById('discription').value;
		var check_discription =isEmpty(discription);

		var title=document.getElementById('title').value;
		var check_title =isEmpty(title);

		if(check_discription)
		{
			alert("Please enter the description.");
			document.getElementById('discription').focus();
			return false ;
		}else{		
			return true ;
		}
		if(check_title)
		{
			alert("Please enter the Title.");
			document.getElementById('title').focus();
			return false ;
		}else{		
			return true ;
		}
			
	}
</script>
{/literal}

<table width="100%" border="0">

<tr><td colspan='2'>
<div class='page_header'>
<img border="0" class="icon_big" src="./images/icons/file_image48.gif"/>
	{lang_print id=7800065}
</div>
<div style='color:#A52A2A;'>
{if $Actionmsg neq ''}
<br/>
{lang_print id=$Actionmsg}
{/if}
 </div>
</td></tr>
<tr>
<td align='center' style='padding-top:5px; vertical-align: top;'>
<form name="upload" action="userupload.php" method="POST" enctype="multipart/form-data" onsubmit="javascript:return checkForm();">
<div style='padding: 10px; border: 1px solid #CCCCCC; margin-bottom: 10px;'>
	<table width="100%"  align="center">
	<tr><td colspan=2 style='color:#CC9900;size:9pt;'>{lang_print id=7800160}</td></tr>
		<tr>
			<td ><b>{lang_print id=7800091}&nbsp;<span style='color:#A52A2A'>*</span>:</b></td>
			<td >


			{*html_options name=selcat options=$myCategory selected=$mySelect*}
			<select name='Category' class='small'>
		
			{section name=opt loop=$myCategory }
		
			<option value='{$myCategory[opt].fileuploadcat_id}' {if $c == $myCategory[opt].fileuploadcat_id} SELECTED {/if}>{$myCategory[opt].fileuploadcat_name}</option>
		
			{/section}
		<input type='hidden' name='uid' value='{$uid}'  >	
			</select>
			</td>
		</tr>
		<tr>  
		<td><b>{lang_print id=7800089}&nbsp;<span style='color:#A52A2A'>*</span>:</b></td><td><input type='text' id='title' class='text' size='30' maxlength='100' name='title' value='{$upTitle|stripslashes}' ></td>      
        	</tr>
		<tr>  
		<td class='fileupload_left'><b>{lang_print id=7800090}&nbsp;<span style='color:#A52A2A'>*</span>:</b></td><td><textarea rows="6"  cols="50" name="discription" id="discription">{$upDesc|stripslashes}</textarea></td>      
        	</tr>
		
		<tr>		
	     <td><b>{lang_print id=7800112}&nbsp;<span style='color:#A52A2A'>*</span>:</b></td><td><input class='text' type="file"  name="userfiles" ></td>
		</tr>
		<tr>		
	     <td><b>{lang_print id=7800114}:</b></td><td><input class='text' type="file"  name="userthumb" > &nbsp;{lang_print id=7800159}</td>
		</tr>

		<!--tr>		
	     <td>Image size should be 100*100</td>
		</tr-->
		
		<tr>
			<td colspan="2" >
<input type='hidden' name='level_id' value='{$level_info.level_id}'>
<input type="submit" class='button' name="{if $update neq ''}update{else}add{/if}" value="{if $update neq ''}Save Changes{else}{lang_print id=7800113}{/if}">
			
			<!--input type="submit" class='button' name="{lang_print id=7800107}" value="{lang_print id=7800107}"-->
			</td>
		</tr>

	</table>
</div>
</form>
</td>
</tr>
</table>

{include file='footer.tpl'}