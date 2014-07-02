
<div class='profile_gstore_page_header'>
  {lang_sprintf id=5555060 1=$owner->user_displayname 2=$url->url_create("profile", $owner->user_info.user_username)}
</div>






{* SHOW NO ENTRIES MESSAGE IF NECESSARY *}
{if !$total_gstores }
  <table cellpadding='0' cellspacing='0'>
    <tr>
      <td class='result'>
        <img src='./images/icons/bulb22.gif' border='0' class='icon' />
        {lang_sprintf id=5555061 1=$owner->user_displayname 2=$url->url_create("profile", $owner->user_info.user_username)}
      </td>
    </tr>
  </table>
  
{/if}








{* SHOW ENTRIES *}
    <table width="100%" cellpadding='0' cellspacing='0' align='center'>
      {section name=gstore_loop loop=$gstores}
      {cycle name="startrow3" values="<tr>,,"}
      <td class='portal_member' valign="bottom">
	  
	  
	  
	  
	  <table width="100%" align="center" border="0" cellspacing="0" cellpadding="0" style="padding:8px; border:1px solid #cccccc;">
  <tr>
    <td align="center" class="segstoreTitle" style="padding-bottom:5px;">
		<a href='{$url->url_create("gstore", $gstores[gstore_loop].gstore_author->user_info.user_username, $gstores[gstore_loop].gstore->gstore_info.gstore_id)}'>
		{$gstores[gstore_loop].gstore->gstore_info.gstore_title|truncate:25:"...":true}
		</a>
		<br />
	</td>
  </tr>
  
  <tr>
    <td align="center">
		<a href='{$url->url_create("gstore", $gstores[gstore_loop].gstore_author->user_info.user_username, $gstores[gstore_loop].gstore->gstore_info.gstore_id)}'>
		<img src='{$gstores[gstore_loop].gstore->gstore_photo("./images/nophoto.gif", TRUE)}' border='0' width='130' height='130' />
		</a>
	</td>
  </tr>
  
  <tr>
    <td align="center">
				<div style="padding-top:5px;">
				
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center" style="padding-bottom:3px;">
	Price: <span class="segstoreLargePrice">{lang_print id=$setting.gstore_currency}{$gstores[gstore_loop].gstore->gstore_info.gstore_price}</span>  
	</td>
	</tr>
	<tr>
    <td align="center">
	<div align="center" style=" width:100px; border:1px solid #CCCCCC; padding:2px 4px 2px 4px; background-color:#f4f4f4;">
	<a href='{$url->url_create("gstore", $owner->user_info.user_username, $gstores[gstore_loop].gstore->gstore_info.gstore_id)}' style="text-decoration:none;">
          <div><img src='./images/icons/gstore_gstore16.gif' border='0' class='icon' style="vertical-align:middle" /> View Item</div>
       </a>
	   </div>
	</td>
  </tr>
</table>

				
				
		<br />
			
			
			
			
			          

			<span style='color: #777777; font-size: 7pt; margin-bottom: 5px;'>

              {assign var='gstore_datecreated' value=$datetime->time_since($gstores[gstore_loop].gstore->gstore_info.gstore_date)}

              {capture assign="created"}{lang_sprintf id=$gstore_datecreated[0] 1=$gstore_datecreated[1]}{/capture}

              {assign var='gstore_dateupdated' value=$datetime->time_since($gstores[gstore_loop].gstore->gstore_info.gstore_dateupdated)}

              {capture assign="updated"}{lang_sprintf id=$gstore_dateupdated[0] 1=$gstore_dateupdated[1]}{/capture}

              

              {lang_sprintf id=5555072 1=$gstores[gstore_loop].gstore->gstore_info.gstore_views}

              - {lang_sprintf id=507 1=$gstores[gstore_loop].gstore->gstore_info.total_comments}

             <br /> {lang_sprintf id=5555135 1=$created}

            </span>

			</div>
	</td>
  </tr>
</table>
	  
	  
	  
 
      </td>
      {cycle name="endrow3" values=",,</tr>"}
      {if (~$smarty.section.gstore_loop.index & 1) && $smarty.section.gstore_loop.last}</tr>{/if}
      {/section}
      </table>













<div style='clear: both; height: 0px;'></div>
{* DISPLAY PAGINATION MENU IF APPLICABLE *}
{if $maxpage > 1}
  
  <div class='center'>
    {if $p != 1}
      <a href='profile.php?user={$owner->user_info.user_username}&v={$profile_tab_args.name}&p={math equation="p-1" p=$p}'>&#171; {lang_print id=182}</a>
    {else}
      <font class='disabled'>&#171; {lang_print id=182}</font>
    {/if}
    {if $p_start == $p_end}
      &nbsp;|&nbsp; {lang_sprintf id=184 1=$p_start 2=$total_gstores} &nbsp;|&nbsp; 
    {else}
      &nbsp;|&nbsp; {lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_gstores} &nbsp;|&nbsp; 
    {/if}
    {if $p != $maxpage}
      <a href='profile.php?user={$owner->user_info.user_username}&v={$profile_tab_args.name}&p={math equation="p+1" p=$p}'>{lang_print id=183} &#187;</a>
    {else}
      <font class='disabled'>{lang_print id=183} &#187;</font>
    {/if}
  </div>
{/if}
