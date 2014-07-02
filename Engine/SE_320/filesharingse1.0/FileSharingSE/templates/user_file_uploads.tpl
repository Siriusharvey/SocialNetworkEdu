{include file='header.tpl'}

{include file='admin_file_cat_js.tpl'}

{* $Id: user_file_uploads.tpl *}

<form action='user_file_uploads.php' method='post' name="seUserFileUpload">
<input type='hidden' name='p' value='{$p|default:1}' />

<div class='fileupload_main'>

 <table cellpadding='0' cellspacing='0'>
  <tr>
    <td style='padding-left: 20px;'>
     {lang_print id=7800073}:
  </td>
  <td>
    <select class='small' id='d' name='d' onchange="window.location.href='user_file_uploads.php?sv='+this.options[this.selectedIndex].value;">
    	<option value='userfiledownload_count DESC'{if $sv == "userfiledownload_count DESC"} SELECTED{/if}>{lang_print id=7800086}</option>
    	<option value='userupload_rating DESC'{if $sv == "userupload_rating DESC"} SELECTED{/if}>{lang_print id=7800127}</option>
    	<option value='userfiledownload_time DESC'{if $sv == "userfiledownload_time DESC"} SELECTED{/if}>{lang_print id=7800128}</option>	   
    </select>
  </td>
  </tr>
  </table>
<input type='hidden' name="sv" value="{$sv}"/>
</div>

<img src='./images/icons/file_image48.gif' border='0' class='icon_big'>
<div class='page_header'>{lang_print id=7800102}</div>
<div>

  {lang_sprintf id=7800057 1=$total_files}<br>
  
</div>

<div style='margin-top: 20px;'>
  <div class='button' style='float: left;'>
    <a href='userupload.php'><img src='./images/icons/plus16.gif' border='0' class='button'>{lang_print id=7800065}</a>
  </div>
   <div style='clear: both; height: 0px;'></div>
</div>


{*------------Pagination Top-----------*}

{* DISPLAY PAGINATION MENU IF APPLICABLE *}
  {if $maxpage > 1}
    <div class='file_pages_top'>
      {if $p != 1}
        <a href='javascript:void(0);' onclick='document.seUserFileUpload.p.value={math equation="p-1" p=$p};document.seUserFileUpload.submit();'>&#171; {lang_print id=182}</a>
      {else}
        &#171; {lang_print id=182}
      {/if}
      &nbsp;|&nbsp;&nbsp;
      {if $p_start == $p_end}
        <b>{lang_sprintf id=184 1=$p_start 2=$total_files}</b>
      {else}
        <b>{lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_files}</b>
      {/if}
      &nbsp;&nbsp;|&nbsp;
      {if $p != $maxpage}
        <a href='javascript:void(0);' onclick='document.seUserFileUpload.p.value={math equation="p+1" p=$p};document.seUserFileUpload.submit();'>{lang_print id=183} &#187;</a>
      {else}
        {lang_print id=183} &#187;
      {/if}
    </div>
  {/if}
	
	{*---------xxxxxxxxxxxxxxxxx--------*}

{section name=files loop=$cat }
	<div class='fileupload' id='{$cat[files].userupload_id}'>
	<table cellpadding='0' cellspacing='0'>
        <tr>
           <td class='fileupload_left' width='1'>
      <div class='fileupload_photo'>
        <table cellpadding='0' cellspacing='0' width='100' height='100'>
        <tr><td>
        {if $cat[files].userupload_userthumbs neq '' }  
  	<a style='text-decoration:none;' href='upload_desc.php?user={$cat[files].userupload_userid}&upid={$cat[files].userupload_id}'>
	          <img  alt='Thumbnail' src='./userthumbs/thumbnail/{$cat[files].userupload_userthumbs}' border='0' />
            </a>
	{elseif $cat[files].userupload_userthumbs eq ''}
		<a style='text-decoration:none;'  href='upload_desc.php?user={$cat[files].userupload_userid}&upid={$cat[files].userupload_id}' >
		<img width="60" height="60" border="0" src="./images/nophoto.gif"/>
		</a>
	{/if}
	</td></tr></table>	
          </td>
           <td class='fileupload_right' width='100%'>
	      <div class='fileupload_title'>
			{$cat[files].userupload_title}
		</div>
		 <!--div style='margin-bottom: 8px;'>
		{*$cat[files].userupload_description*}
		</div-->
		<div class='fileupload_stats'>
        	{* days or time ago *}

		 {assign var='upload_datecreated' value=$datetime->time_since($cat[files].userupload_time)}

		{capture assign="created"}{lang_sprintf id=$upload_datecreated[0] 1=$upload_datecreated[1]}{/capture}

		{assign var='upload_dateupdated' value=$datetime->time_since($cat[files].modified_at)}
              	{capture assign="updated"}{lang_sprintf id=$upload_dateupdated[0] 1=$upload_dateupdated[1]}{/capture}

        	
		 {lang_sprintf id=7800135 1=$created}
		{if $cat[files].modify != 0}
                - {lang_sprintf id=7800136 1=$updated}
            	  {/if}	
		<br/>
		{if $cat[files].userfiledownload_count eq ''} 
		0
		{else}
		{$cat[files].userfiledownload_count}
		{/if}
		downloads
		<br/>
	{if $user_id eq $cat[files].userupload_userid}
		 <div  style='border-top:1px solid #DDDDDD;margin-top:7px;padding-top:7px;'>

		  <div style='float: left; padding-left: 15px;'><a href='userupload.php?task=edit&upload_id={$cat[files].userupload_id}'><img border="0" class="button" src="./images/icons/file_edit16.gif"/>Edit </a></div>
          	  <div style='float: left; padding-left: 15px;'><a href='javascript:void(0);' onClick="confirmDelete('{$cat[files].userupload_id}');"><img border="0" class="button" src="./images/icons/file_delete16.gif"/>Delete</a></div>     
		</div>
	{/if}
	{* For Displaying Rating *}
	<div id='star-rating' style="float: left;height:20px; padding-top:10px;width:95%;padding-bottom:10px !important">
		
		{if $cat[files].userupload_rating neq ''}
<ul class='star-rating' style="float:left;padding:0px;margin:0px" >
{assign var=item  value=$cat[files].userupload_rating}

		<li class='current-rating' style='width:{math equation="$item*25" format="%.0f"}px;'></li>
		</ul>
		{/if}

        	</td>
	</tr>
</table>	
				
		</div>
		{/section} 
	<div style='clear: both; margin-top: 10px;'></div>
{*------------Pagination Bottom-----------*}

{* DISPLAY PAGINATION MENU IF APPLICABLE *}
  {if $maxpage > 1}
    <div class='file_pages_top'>
      {if $p != 1}
        <a href='javascript:void(0);' onclick='document.seUserFileUpload.p.value={math equation="p-1" p=$p};document.seUserFileUpload.submit();'>&#171; {lang_print id=182}</a>
      {else}
        &#171; {lang_print id=182}
      {/if}
      &nbsp;|&nbsp;&nbsp;
      {if $p_start == $p_end}
        <b>{lang_sprintf id=184 1=$p_start 2=$total_files}</b>
      {else}
        <b>{lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_files}</b>
      {/if}
      &nbsp;&nbsp;|&nbsp;
      {if $p != $maxpage}
        <a href='javascript:void(0);' onclick='document.seUserFileUpload.p.value={math equation="p+1" p=$p};document.seUserFileUpload.submit();'>{lang_print id=183} &#187;</a>
      {else}
        {lang_print id=183} &#187;
      {/if}
    </div>
  {/if}
	
	{*---------xxxxxxxxxxxxxxxxx--------*}

</form>

{literal}
<style>
	.star-rating li.current-rating{
		background: url(./images/icons/alt_star.gif) left center;
		position: absolute;
		height: 25px;
		display: block;
		text-indent: -9000px;
		z-index: 1;
	}

	.file_pages_top{
		margin-bottom: 10px; 
		text-align: center; 
		padding: 7px 5px 7px 5px; 
		background: #F3F3F3; 
		border-top: 1px solid #CCCCCC;
	
	}
</style>
{/literal}
{literal}
<script>
  function confirmDelete(id) {
	file_id = id;
if(confirm('{/literal}{lang_print id=7800122}{literal}')){	
	     window.location = 'user_file_uploads.php?task=delete&upload_id='+file_id;
	}else{
		return false;
	}
  }

/*
 var file_id = 0;
  function confirmDelete(id) {
//alert(id);
    file_id = id;
    TB_show('{/literal}{lang_print id=7800076}{literal}', '#TB_inline?height=150&width=300&inlineId=confirmdelete', '', '../images/trans.gif');
  }

  function deleteFile() {
    window.location = 'user_file_uploads.php?task=delete&upload_id='+file_id;
  }
*/
  // -->
  </script>
  {/literal}

  {* HIDDEN DIV TO DISPLAY CONFIRMATION MESSAGE *}
  <div style='display: none;' id='confirmdelete'>
    <div style='margin-top: 10px;'>
      {lang_print id=7800055}
    </div>
    <br>
    <input type='button' class='button' value='{lang_print id=175}' onClick='parent.TB_remove();parent.deleteFile();'> <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
  </div>
{include file='footer.tpl'}