{include file='header.tpl'}

<form action='browse_upload.php' method='post' name="seBrowseUpload">
<input type='hidden' name='p' value='{$p|default:1}' />


<input type='hidden' name="sv" value="{$sv}"/>
<input type='hidden' name="vs" value="{$vs}"/>

<table cellpadding='0' cellspacing='0' width='100%' style='margin-top: 10px;'>
<tr>
<td class='fileupload_cat_left'>
<div class='fileupload_search'>
      
    <table cellpadding='0' cellspacing='0' width='100%'>
      <tr>
        <td class='fileupload_marginal_left' nowrap='nowrap'>
          <input type="text" name="upload_search" value="{$upload_search}" class="text" style="width: 120px;">&nbsp;
        </td>
	  <td class='fileupload_marginal_left'>
          <input type="submit" value="{lang_print id=646}" name='uploadSearch' class="button" />
        </td>
      </tr>
</table><table>
	<tr>
	<td>
    	{lang_print id=7800126}&nbsp;
  	</td>
 	 <td>
		<select class='small' name='s' onchange="window.location.href='browse_upload.php?vs='+this.options[this.selectedIndex].value;">
		<option value='userupload_time DESC'{if $vs == "userupload_time DESC"} SELECTED{/if}>{lang_print id=7800129}</option>
		<option value='userupload_filetype ASC'{if $vs == "userupload_filetype ASC"} SELECTED{/if}>File Types</option>
		<option value='userupload_rating DESC'{if $vs == "userupload_rating DESC"} SELECTED{/if}>{lang_print id=7800085}</option>	
		</select>
  	</td>
	</tr>	
    </table>
</div>
<div class='fileupload_cat_list'>

<div class='fileupload_cat'>
 <a href='browse_upload.php?vs=userupload_time DESC'>{lang_print id=7800133}</a>
</div>

{section name=opt loop=$search }
 <div class='fileupload_cat'>
      <a href='browse_upload.php?selcat={$search[opt].fileuploadcat_id}&vs={$vs}'>{$search[opt].fileuploadcat_name}</a>
   </div>
{/section}

<!--select name='selcat'>
			<option value=''>Select Category</option>
			{section name=opt loop=$search }

			<option value='{$search[opt].fileuploadcat_id}'>{$search[opt].fileuploadcat_name}</option>

			{/section}
</select-->

</div>

</td>
<td style='padding-left:10px; vertical-align: top;'>
	{* NO classifiedS AT ALL *}
  	{if !$cat|@count}
   	 <br />
   	 <table cellpadding='0' cellspacing='0' align='center'>
   	   <tr>
    		    <td class='result'>
        	  <img src='./images/icons/bulb16.gif' border='0' class='icon' />
          		{lang_print id=7800158}
        	   </td>
          </tr>
  	 </table>
	{/if}
	
	{*$cat|@count*}

	{*------------Pagination Top-----------*}

{* DISPLAY PAGINATION MENU IF APPLICABLE *}
  {if $maxpage > 1}
    <div class='file_pages_top'>
      {if $p != 1}
        <a href='javascript:void(0);' onclick='document.seBrowseUpload.p.value={math equation="p-1" p=$p};document.seBrowseUpload.submit();'>&#171; {lang_print id=182}</a>
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
        <a href='javascript:void(0);' onclick='document.seBrowseUpload.p.value={math equation="p+1" p=$p};document.seBrowseUpload.submit();'>{lang_print id=183} &#187;</a>
      {else}
        {lang_print id=183} &#187;
      {/if}
    </div>
  {/if}
	
	{*---------xxxxxxxxxxxxxxxxx--------*}

	{section name=files loop=$cat }
	<div class='fileupload_browse_list'>
	<table cellpadding='0' cellspacing='0' width='99%'>
        <tr>
          <td class='fileupload_td_photo'>
	<div class='fileupload_photo'>
        {if $cat[files].userupload_userthumbs neq '' }  
  	<a style='text-decoration:none;' href='upload_desc.php?user={$cat[files].userupload_userid}&upid={$cat[files].userupload_id}'>
	          <img  alt='Thumbnail' src='./userthumbs/thumbnail/{$cat[files].userupload_userthumbs}' border='0' />
            </a>
	{elseif $cat[files].userupload_userthumbs eq ''}
		<a style='text-decoration:none;'  href='upload_desc.php?user={$cat[files].userupload_userid}&upid={$cat[files].userupload_id}' >
		<img width="100" height="100" border="0" src="./images/nophoto.gif"/>
		</a>
	{/if}	
	</div>
          </td>
          <td style='vertical-align: top; padding-left: 10px;'>
		<div style="font-weight: bold; font-size: 10pt;">
	<a style='text-decoration:none;' href='upload_desc.php?user={$cat[files].userupload_userid}&upid={$cat[files].userupload_id}'>{$cat[files].userupload_title}</a>
		</div>
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
		</div>  
		<div>
		{if $cat[files].userfiledownload_count eq ''} 
		0
		{else}
		{$cat[files].userfiledownload_count}
		{/if}
		downloads
		</div>  

	{* For Displaying Rating *}
<br/>
		<div id='star-rating'>
		
		{if $cat[files].userupload_rating neq ''}
<ul class='star-rating' style='float:left;padding:0px;margin:0px;' >
{assign var=item  value=$cat[files].userupload_rating}

		<li class='current-rating' style='width:{math equation="$item*25" format="%.0f"}px;'></li>
		</ul>
		{/if}
		</div>

        	</td>
	</tr></table>	
				
		</div>
		{/section} 
	
{*------------Pagination Bottom-----------*}

{* DISPLAY PAGINATION MENU IF APPLICABLE *}
  {if $maxpage > 1}
    <div class='file_pages_top'>
      {if $p != 1}
        <a href='javascript:void(0);' onclick='document.seBrowseUpload.p.value={math equation="p-1" p=$p};document.seBrowseUpload.submit();'>&#171; {lang_print id=182}</a>
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
        <a href='javascript:void(0);' onclick='document.seBrowseUpload.p.value={math equation="p+1" p=$p};document.seBrowseUpload.submit();'>{lang_print id=183} &#187;</a>
      {else}
        {lang_print id=183} &#187;
      {/if}
    </div>
  {/if}
	
	{*---------xxxxxxxxxxxxxxxxx--------*}
</td>


</tr>
</table>

</form>



{* JAVASCRIPT FOR CONFIRMING DELETION *}
{literal}
<script type="text/javascript">

var upload_id = 0;
function confirmDeleteFile(id) {
//alert(id);
  upload_id = id;
  TB_show('{/literal}{lang_print id=7800112}{literal}', '#TB_inline?height=100&width=300&inlineId=confirmdelete', '', '../images/trans.gif');

}

function deleteFile() {
  window.location = 'browse_upload.php?task=delete&upload_id='+upload_id;
}

</script>
{/literal}


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
{* HIDDEN DIV TO DISPLAY CONFIRMATION MESSAGE *}
<div style='display: none;' id='confirmdelete'>
  <div style='margin-top: 10px;'>
	  {lang_print id=7800122}
  </div>
  <br>
  <input type='button' class='button' value='{lang_print id=175}' onClick='parent.TB_remove();parent.deleteFile();'> <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
</div>


{include file='footer.tpl'}