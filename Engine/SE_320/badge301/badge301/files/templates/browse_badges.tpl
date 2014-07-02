{include file='header.tpl'}



{literal}
<script type="text/javascript">
<!--
  // ADD TIP FUNCTION

  window.addEvent('load', function()
  {
		var badgeTips = new Tips($$('.badge_tip'), {
		  className: 'badge_tooltip'
		});
  });
//-->

</script>
{/literal}


<div class='page_header'>
{if $badgecat_id > 0 || $badgecat_id == -1}
  {capture assign="badgecat_title"}{lang_print id=$badgecat_languagevar_id}{/capture}
  {lang_sprintf id=11270160 1=$badgecat_title}  
{else}
  {lang_print id=11270029}
{/if}
</div>


<table cellspacing="0" cellpadding="0" width="100%" style="margin-top: 10px;">
	<tr>

	<td style="vertical-align: top;">
  {* START RIGHT COLUMN *}
  


		{* DISPLAY PAGINATION MENU IF APPLICABLE *}
		{if $maxpage > 1}
		  <div style='text-align: center; padding-bottom: 10px;'>
		  {if $p != 1}<a href='browse_badges.php?badgecat_id={$badgecat_id}&s={$s}&p={math equation='p-1' p=$p}'>&#171; {lang_print id=182}</a>{else}&#171; {lang_print id=182}{/if}
		  &nbsp;|&nbsp;&nbsp;
		  {if $p_start == $p_end}
		    <b>{lang_sprintf id=184 1=$p_start 2=$total_badges}</b>
		  {else}
		    <b>{lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_badges}</b>
		  {/if}
		  &nbsp;&nbsp;|&nbsp;
		  {if $p != $maxpage}<a href='browse_badges.php?badgecat_id={$badgecat_id}&s={$s}&p={math equation='p+1' p=$p}'>{lang_print id=183} &#187;</a>{else}{lang_print id=183} &#187;{/if}
		  </div>
		{/if}
		
		
		<div id='badgeBrowseEntries'>
		  {section name=badge_loop loop=$badges}
		    {assign var=badge value=$badges[badge_loop].badge}
		
        <div class='badgeBrowseEntry'>
          <div class='badge_row_photo'>
            <a href='{$url->url_create("badge", $badge->badge_info.badge_author->user_info.user_username, $badge->badge_info.badge_id)}'  ><img src='{$badge->badge_photo("./images/badge_placeholder.gif")}' border='0'></a>  
          </div>
          <div class='badge_row_details'>
            <div class='badge_row_title'>
              <a href='{$url->url_create("badge", $badge->badge_info.badge_author->user_info.user_username, $badge->badge_info.badge_id)}'>{$badge->badge_info.badge_title}</a>
            </div>
            <div class='badge_row_info'>{lang_sprintf id=11270091 1=$badges[badge_loop].total_approved}</div>
          </div>
        </div>
		
		   {cycle values=",,<div class='badge_clear'></div>"}
		 {sectionelse}
				<table cellspacing="0" cellpadding="0" align="center">
				   <tr>
				     <td class="result"><img border="0" class="icon" src="./images/icons/bulb16.gif"/>{lang_print id=11270156}</td>
				   </tr>
				</table>
		  {/section}
		  <div class='badge_clear'></div>
		
		</div>
  

    {* DISPLAY PAGINATION MENU IF APPLICABLE *}
    {if $maxpage > 1}
      <div style='text-align: center; padding: 10px;'>
      {if $p != 1}<a href='browse_badges.php?badgecat_id={$badgecat_id}&s={$s}&p={math equation='p-1' p=$p}'>&#171; {lang_print id=182}</a>{else}&#171; {lang_print id=182}{/if}
      &nbsp;|&nbsp;&nbsp;
      {if $p_start == $p_end}
        <b>{lang_sprintf id=184 1=$p_start 2=$total_badges}</b>
      {else}
        <b>{lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_badges}</b>
      {/if}
      &nbsp;&nbsp;|&nbsp;
      {if $p != $maxpage}<a href='browse_badges.php?badgecat_id={$badgecat_id}&s={$s}&p={math equation='p+1' p=$p}'>{lang_print id=183} &#187;</a>{else}{lang_print id=183} &#187;{/if}
      </div>
    {/if}
      
  
  
  {* END RIGHT COLUMN *}
	</td>
	
  <td style="width: 200px; vertical-align: top;  padding-left: 10px;">
  {* START LEFT COLUMN *}
    
    {* START BROWSE OPTIONS *}
    <div class='badge_browse_options'>
      <table cellpadding='0' cellspacing='0'>
      <tr>
      <td align="right" width="45">
        {lang_print id=11270074}&nbsp;
      </td>
      <td align="left">
        <select class='small' name='s' onchange="window.location.href='browse_badges.php?badgecat_id={$badgecat_id}&s='+this.options[this.selectedIndex].value;">
        <option value='date'{if $s == "date"} SELECTED{/if}>{lang_print id=11270075}</option>
        <option value='member'{if $s == "member"} SELECTED{/if}>{lang_print id=11270144}</option>
        </select>
      </td>
      </tr>
      </table>
    </div>
    {* END BROWSE OPTIONS *}

     
  {* CATEGORY JAVASCRIPT *}
  {literal}
  <script type="text/javascript">
  <!-- 

  // ADD ABILITY TO MINIMIZE/MAXIMIZE CATS
  var cat_minimized = new Hash.Cookie('cat_cookie', {duration: 3600});

  //-->
  </script>
  {/literal}


  <div class='badge_browse_categories'>

    <div class='badge_browse_category_item_all'>
      <a href='browse_badges.php?s={$s}'>{lang_print id=11270159}</a>
    </div>
    {section name=cat_loop loop=$cats}

      {* CATEGORY JAVASCRIPT *}
      {literal}
      <script type="text/javascript">
      <!-- 
        window.addEvent('domready', function() { 
          if(cat_minimized.get({/literal}{$cats[cat_loop].cat_id}{literal}) == 1) {
      $('subcats_{/literal}{$cats[cat_loop].cat_id}{literal}').style.display = '';
      $('icon_{/literal}{$cats[cat_loop].cat_id}{literal}').src = './images/icons/minus16.gif';
    }
  });
      //-->
      </script>
      {/literal}

      <div class='badge_browse_category_item'>
        <img id='icon_{$cats[cat_loop].cat_id}' src='./images/icons/{if $cats[cat_loop].subcats|@count > 0 && $cats[cat_loop].subcats != ""}plus16{else}minus16_disabled{/if}.gif' {if $cats[cat_loop].subcats|@count > 0 && $cats[cat_loop].subcats != ""}style='cursor: pointer;' onClick="if($('subcats_{$cats[cat_loop].cat_id}').style.display == 'none') {literal}{{/literal} $('subcats_{$cats[cat_loop].cat_id}').style.display = ''; this.src='./images/icons/minus16.gif'; cat_minimized.set({$cats[cat_loop].cat_id}, 1); {literal}} else {{/literal} $('subcats_{$cats[cat_loop].cat_id}').style.display = 'none'; this.src='./images/icons/plus16.gif'; cat_minimized.set({$cats[cat_loop].cat_id}, 0); {literal}}{/literal}"{/if} border='0' class='icon'><a href='browse_badges.php?s={$s}&badgecat_id={$cats[cat_loop].cat_id}'>{lang_print id=$cats[cat_loop].cat_title}</a>
        <div id='subcats_{$cats[cat_loop].cat_id}' style='display: none;'>
          {section name=subcat_loop loop=$cats[cat_loop].subcats}
            <div style='font-weight: normal;'><img src='./images/trans.gif' border='0' class='icon' style='width: 16px;'><a href='browse_badges.php?s={$s}&badgecat_id={$cats[cat_loop].subcats[subcat_loop].subcat_id}'>{lang_print id=$cats[cat_loop].subcats[subcat_loop].subcat_title}</a></div>
          {/section}
        </div>
      </div>
    {/section}
  </div>
     
  
  {* END LEFT COLUMN *}
  </td>	
	
	</tr>
</table>

{include file='footer.tpl'}