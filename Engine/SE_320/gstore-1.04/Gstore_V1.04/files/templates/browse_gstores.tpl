{include file='header.tpl'}

<img src='./images/icons/gstore.png' border='0' class='icon_big' style="margin-bottom: 10px;">

<div class='page_header'>

Network Store

</div>

<div>

  {if empty($gstorecat)}

    {lang_print id=5555124} : <span style="font-size:13px;">for the criteria you specified below</span>

  {else}

    <a href='browse_gstores.php'>{lang_print id=5555124}</a> >

    {if empty($gstoresubcat)}

      {lang_print id=$gstorecat.gstorecat_title}

    {else}

      <a href='browse_gstores.php?v={$v}&s={$s}&gstorecat_id={$gstorecat.gstorecat_id}'>{lang_print id=$gstorecat.gstorecat_title}</a> >

      {lang_print id=$gstoresubcat.gstorecat_title}

    {/if}

  {/if}

</div>

<div style="clear:both;"></div>

<form action='browse_gstores.php' method='post' name="seBrowsegstores">

<input type='hidden' name='task' value='dosearch' />

<input type='hidden' name='gstorecat_id' value='{$gstorecat_id|default:0}' />

<input type='hidden' name='p' value='{$p|default:1}' />

<table cellpadding='0' cellspacing='0' width='100%' style='margin-top: 10px;'>

<tr>

<td style='width: 200px; vertical-align: top;'>

  <div style='padding: 10px; background: #F2F2F2; border: 1px solid #BBBBBB; font-weight: bold;'>

      

    <table cellpadding='0' cellspacing='0' width='100%'>

      <tr>

        <td style='text-align: left; padding-top: 5px;' nowrap='nowrap'>

          <input type="text" name="gstore_search" value="{$gstore_search}" class="text" style="width: 100px;">&nbsp;

        </td>

        <td style='text-align: left; padding-top: 5px;'>

          <input type="submit" value="{lang_print id=646}" class="button" />

        </td>

      </tr>

    </table>

    <table cellpadding='0' cellspacing='0' width='100%' style='margin-top: 10px;'>

      <tr>

        <td align='right'>{lang_print id=5555125}&nbsp;</td>

        <td>

          <select class='gstore_small' name='v' onchange="document.seBrowsegstores.submit();">

            <option value='0'{if $v == "0"} SELECTED{/if}>{lang_print id=5555127}</option>

            {if $user->user_exists}<option value='1'{if $v == "1"} SELECTED{/if}>{lang_print id=5555128}</option>{/if}

          </select>

        </td>

      </tr>

      <tr>

        <td align='right'>{lang_print id=5555126}&nbsp;</td>

        <td>

          <select class='gstore_small' name='s' onchange="document.seBrowsegstores.submit();">

            <option value='gstore_date DESC'{if $s == "gstore_date DESC"} SELECTED{/if}>{lang_print id=5555129}</option>

            <option value='gstore_dateupdated DESC'{if $s == "gstore_dateupdated DESC"} SELECTED{/if}>{lang_print id=5555130}</option>

            <option value='gstore_views DESC'{if $s == "gstore_views DESC"} SELECTED{/if}>{lang_print id=5555131}</option>

            <option value='total_comments DESC'{if $s == "total_comments DESC"} SELECTED{/if}>{lang_print id=5555132}</option>

          </select>

        </td>

      </tr>

    </table>

  </div>

  {* CATEGORY JAVASCRIPT *}

  {literal}

  <script type="text/javascript">

  <!-- 

  // ADD ABILITY TO MINIMIZE/MAXIMIZE CATS

  var cat_minimized = new Hash.Cookie('cat_cookie', {duration: 3600});

  var cat_list = new Hash();

  //-->

  </script>

  {/literal}

  

  

  <div style='margin-top: 10px; padding: 5px; background: #F2F2F2; border: 1px solid #BBBBBB; margin: 10px 0px 10px 0px; font-weight: bold;'>

    

    <div style='padding: 7px 8px 6px 8px; border: 1px solid #DDDDDD; background: #FFFFFF;'>

      <a href='browse_gstores.php?s={$s}&v={$v}'>{lang_print id=5555133}</a>

    </div>

    

    {section name=cat_loop loop=$cats}

      

      {* CATEGORY JAVASCRIPT *}

      <script type="text/javascript">

        <!-- 

        cat_list.set({$cats[cat_loop].cat_id}, {ldelim}{rdelim});

        //-->

      </script>

      

      <div style='padding: 5px 8px 5px 5px; border: 1px solid #DDDDDD; border-top: none; background: #FFFFFF;'>

        <img id='icon_{$cats[cat_loop].cat_id}' src='./images/icons/{if $cats[cat_loop].subcats|@count > 0 && $cats[cat_loop].subcats != ""}plus16{else}minus16_disabled{/if}.gif' {if $cats[cat_loop].subcats|@count > 0 && $cats[cat_loop].subcats != ""}style='cursor: pointer;' onClick="if($('subcats_{$cats[cat_loop].cat_id}').style.display == 'none') {literal}{{/literal} $('subcats_{$cats[cat_loop].cat_id}').style.display = ''; this.src='./images/icons/minus16.gif'; cat_minimized.set({$cats[cat_loop].cat_id}, 1); {literal}} else {{/literal} $('subcats_{$cats[cat_loop].cat_id}').style.display = 'none'; this.src='./images/icons/plus16.gif'; cat_minimized.set({$cats[cat_loop].cat_id}, 0); {literal}}{/literal}"{/if} border='0' class='icon' /><a href='browse_gstores.php?s={$s}&v={$v}&gstorecat_id={$cats[cat_loop].cat_id}'>{lang_print id=$cats[cat_loop].cat_title}</a>

        <div id='subcats_{$cats[cat_loop].cat_id}' style='display: none;'>

          {section name=subcat_loop loop=$cats[cat_loop].subcats}

            <div style='font-weight: normal;'><img src='./images/trans.gif' border='0' class='icon' style='width: 16px;'><a href='browse_gstores.php?s={$s}&v={$v}&gstorecat_id={$cats[cat_loop].subcats[subcat_loop].subcat_id}'>{lang_print id=$cats[cat_loop].subcats[subcat_loop].subcat_title}</a></div>

          {/section}

        </div>

      </div>

    {/section}

    

    {literal}

    <script type="text/javascript">

    <!-- 

      window.addEvent('domready', function()

      {

        cat_list.each(function(catObject, catID)

        {

          if( !cat_minimized.get(catID) ) return;

          $('subcats_'+catID).style.display = '';

          $('icon_'+catID).src = './images/icons/minus16.gif';

        });

      });

    //-->

    </script>

    {/literal}

  </div>

  

  {if !empty($fields)}

  

  <div class='header'>{lang_print id=1089}</div>

  <div class='browse_fields'>

    

    {section name=field_loop loop=$fields}

    

    <div style='font-weight: bold; margin-top: 5px;'>{lang_print id=$fields[field_loop].field_title}</div>

    

      {* TEXT FIELD *}

      {if $fields[field_loop].field_type == 1 || $fields[field_loop].field_type == 2}

        

        {* RANGED SEARCH *}

        {if $fields[field_loop].field_search == 2}

          <input type='text' class='text' size='5' name='field_{$fields[field_loop].field_id}_min' value='{$fields[field_loop].field_value_min}' maxlength='64' />

          - 

          <input type='text' class='text' size='5' name='field_{$fields[field_loop].field_id}_max' value='{$fields[field_loop].field_value_max}' maxlength='64' />	  

        

        {* EXACT VALUE SEARCH *}

        {else}

          <input type='text' class='text' size='15' name='field_{$fields[field_loop].field_id}' value='{$fields[field_loop].field_value}' maxlength='64' />

        {/if}

        

        

      {* SELECT BOX *}

      {elseif $fields[field_loop].field_type == 3}

        <div>

          <select name='field_{$fields[field_loop].field_id}' id='field_{$fields[field_loop].field_id}' onchange="ShowHideDeps('{$fields[field_loop].field_id}', this.value);" style='{$fields[field_loop].field_style}'>

            <option value='-1'></option>

            {* LOOP THROUGH FIELD OPTIONS *}

            {section name=option_loop loop=$fields[field_loop].field_options}

              <option id='op' value='{$fields[field_loop].field_options[option_loop].value}'{if $fields[field_loop].field_options[option_loop].value == $fields[field_loop].field_value} SELECTED{/if}>{lang_print id=$fields[field_loop].field_options[option_loop].label}</option>

            {/section}

          </select>

        </div>

        

        

      {* RADIO BUTTONS *}

      {elseif $fields[field_loop].field_type == 4}

        

        {* LOOP THROUGH FIELD OPTIONS *}

        <div id='field_options_{$fields[field_loop].field_id}'>

        {section name=option_loop loop=$fields[field_loop].field_options}

          <div>

            <input type='radio' class='radio' onclick="ShowHideDeps('{$fields[field_loop].field_id}', '{$fields[field_loop].field_options[option_loop].value}');" style='{$fields[field_loop].field_style}' name='field_{$fields[field_loop].field_id}' id='label_{$fields[field_loop].field_id}_{$fields[field_loop].field_options[option_loop].value}' value='{$fields[field_loop].field_options[option_loop].value}'{if $fields[field_loop].field_options[option_loop].value == $fields[field_loop].field_value} CHECKED{/if}>

            <label for='label_{$fields[field_loop].field_id}_{$fields[field_loop].field_options[option_loop].value}'>{lang_print id=$fields[field_loop].field_options[option_loop].label}</label>

          </div>

          

        {/section}

        </div>

        

        

      {* DATE FIELD *}

      {elseif $fields[field_loop].field_type == 5}

        <div>

          <select name='field_{$fields[field_loop].field_id}_1' style='{$fields[field_loop].field_style}'>

          {section name=date1 loop=$fields[field_loop].date_array1}

            <option value='{$fields[field_loop].date_array1[date1].value}'{$fields[field_loop].date_array1[date1].selected}>{if $smarty.section.date1.first}[ {lang_print id=$fields[field_loop].date_array1[date1].name} ]{else}{$fields[field_loop].date_array1[date1].name}{/if}</option>

          {/section}

          </select>

          

          <select name='field_{$fields[field_loop].field_id}_2' style='{$fields[field_loop].field_style}'>

          {section name=date2 loop=$fields[field_loop].date_array2}

            <option value='{$fields[field_loop].date_array2[date2].value}'{$fields[field_loop].date_array2[date2].selected}>{if $smarty.section.date2.first}[ {lang_print id=$fields[field_loop].date_array2[date2].name} ]{else}{$fields[field_loop].date_array2[date2].name}{/if}</option>

          {/section}

          </select>

          

          <select name='field_{$fields[field_loop].field_id}_3' style='{$fields[field_loop].field_style}'>

          {section name=date3 loop=$fields[field_loop].date_array3}

            <option value='{$fields[field_loop].date_array3[date3].value}'{$fields[field_loop].date_array3[date3].selected}>{if $smarty.section.date3.first}[ {lang_print id=$fields[field_loop].date_array3[date3].name} ]{else}{$fields[field_loop].date_array3[date3].name}{/if}</option>

          {/section}

          </select>

        </div>

        

        

      {* CHECKBOXES *}

      {elseif $fields[field_loop].field_type == 6}

        

        {* LOOP THROUGH FIELD OPTIONS *}

        <div id='field_options_{$fields[field_loop].field_id}'>

        {section name=option_loop loop=$fields[field_loop].field_options}

          <div>

            <input type='checkbox' onclick="ShowHideDeps('{$fields[field_loop].field_id}', '{$fields[field_loop].field_options[option_loop].value}', '{$fields[field_loop].field_type}');" style='{$fields[field_loop].field_style}' name='field_{$fields[field_loop].field_id}[]' id='label_{$fields[field_loop].field_id}_{$fields[field_loop].field_options[option_loop].value}' value='{$fields[field_loop].field_options[option_loop].value}'{if $fields[field_loop].field_options[option_loop].value|in_array:$fields[field_loop].field_value} CHECKED{/if}>

            <label for='label_{$fields[field_loop].field_id}_{$fields[field_loop].field_options[option_loop].value}'>{lang_print id=$fields[field_loop].field_options[option_loop].label}</label>

          </div>

          

        {/section}

        </div>

        

      {/if}

    

    {/section}

    

    {* SHOW SUBMIT BUTTON *}

    <div>

      <div style='padding-top: 10px; padding-bottom: 5px;'>

        <input type='submit' class='button' value='{lang_print id=1090}' />&nbsp;&nbsp;

      </div>

    </div>

  {/if}

  

  

  {* MOST INTEREST *}

  <div class="header" style="width:190px;">Most Interesting Items</div>

  <div class="portal_content" style="width:190px; padding:5px; background-color:#f2f2f2;">

  {section name=most_interest_loop loop=$most_interest max=5}

    <div style='padding: 10px; border: 1px solid #CCCCCC; margin-bottom: 5px; background-color:#ffffff;'>

      <table cellpadding='0' cellspacing='0'>

        <tr>

          <td valign="top">

            <a href='{$url->url_create("gstore", $most_interest[most_interest_loop].gstore_author->user_info.user_username, $most_interest[most_interest_loop].gstore->gstore_info.gstore_id)}'>

              <img src='{$most_interest[most_interest_loop].gstore->gstore_photo("./images/nophoto.gif", TRUE)}' border='0' width='40' height='40' />

            </a>

          </td>

          <td style='vertical-align: top; padding-left: 10px;'>

            <div style='font-weight: bold; font-size: 10pt;'>

              <a href='{$url->url_create("gstore", $most_interest[most_interest_loop].gstore_author->user_info.user_username, $most_interest[most_interest_loop].gstore->gstore_info.gstore_id)}'>

                {$most_interest[most_interest_loop].gstore->gstore_info.gstore_title|truncate:15:"...":true}

              </a>

            </div>

			

			 <div style='color: #777777; font-size: 7pt; margin-bottom: 5px;'>

                <span class="segstoreLargePrice" style="font-size:10px;">{lang_print id=$setting.gstore_currency}{$most_interest[most_interest_loop].gstore->gstore_info.gstore_price}</span>&nbsp;&nbsp;&nbsp;{lang_sprintf id=5555072 1=$most_interest[most_interest_loop].gstore->gstore_info.gstore_views} 

            </div>

			

          </td>

        </tr>

      </table>

    </div>

  {/section}

  </div>

</td>

















<td width="480px" style='vertical-align: top; padding-left: 10px;'>

	  

  {* NO gstoreS AT ALL *}

  {if !$gstores|@count}

    <br />

    <table cellpadding='0' cellspacing='0' align='center'>

      <tr>

        <td class='result'>

          <img src='./images/icons/bulb16.gif' border='0' class='icon' />

          {lang_print id=5555134}

        </td>

      </tr>

    </table>

  {/if}

  {* DISPLAY PAGINATION MENU IF APPLICABLE *}

  {if $maxpage > 1}

    <div class='gstore_pages_top'>

      {if $p != 1}

        <a href='javascript:void(0);' onclick='document.seBrowsegstores.p.value={math equation="p-1" p=$p};document.seBrowsegstores.submit();'>&#171; {lang_print id=182}</a>

      {else}

        &#171; {lang_print id=182}

      {/if}

      &nbsp;|&nbsp;&nbsp;

      {if $p_start == $p_end}

        <b>{lang_sprintf id=184 1=$p_start 2=$total_gstores}</b>

      {else}

        <b>{lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_gstores}</b>

      {/if}

      &nbsp;&nbsp;|&nbsp;

      {if $p != $maxpage}

        <a href='javascript:void(0);' onclick='document.seBrowsegstores.p.value={math equation="p+1" p=$p};document.seBrowsegstores.submit();'>{lang_print id=183} &#187;</a>

      {else}

        {lang_print id=183} &#187;

      {/if}

    </div>

  {/if}

  {section name=gstore_loop loop=$gstores}

    <div style='padding: 10px; border: 1px solid #CCCCCC; margin-bottom: 10px; float:left;  width:208px;{cycle values="margin-right: 11px;,"}">'>
	
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center" class="segstoreTitle" style="padding-bottom:5px;">
		<a href='{$url->url_create("gstore", $gstores[gstore_loop].gstore_author->user_info.user_username, $gstores[gstore_loop].gstore->gstore_info.gstore_id)}'>
		{$gstores[gstore_loop].gstore->gstore_info.gstore_title|truncate:25:"...":true}
		</a>
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
			
			
			Price: <span class="segstoreLargePrice">{lang_print id=$setting.gstore_currency}{$gstores[gstore_loop].gstore->gstore_info.gstore_price}</span>  <br />
			
			          

			<span style='color: #777777; font-size: 7pt; margin-bottom: 5px;'>

              {assign var='gstore_datecreated' value=$datetime->time_since($gstores[gstore_loop].gstore->gstore_info.gstore_date)}

              {capture assign="created"}{lang_sprintf id=$gstore_datecreated[0] 1=$gstore_datecreated[1]}{/capture}

              {assign var='gstore_dateupdated' value=$datetime->time_since($gstores[gstore_loop].gstore->gstore_info.gstore_dateupdated)}

              {capture assign="updated"}{lang_sprintf id=$gstore_dateupdated[0] 1=$gstore_dateupdated[1]}{/capture}

              

              {lang_sprintf id=5555072 1=$gstores[gstore_loop].gstore->gstore_info.gstore_views}

              - {lang_sprintf id=5555135 1=$created}

            </span>

			</div>
	</td>
  </tr>
</table>

    </div>

  {/section}
  <div style="clear:both"></div>

  {* DISPLAY PAGINATION MENU IF APPLICABLE *}

  {if $maxpage > 1}

    <div class='gstore_pages_bottom'>

      {if $p != 1}

        <a href='javascript:void(0);' onclick='document.seBrowsegstores.p.value={math equation="p-1" p=$p};document.seBrowsegstores.submit();'>&#171; {lang_print id=182}</a>

      {else}

        &#171; {lang_print id=182}

      {/if}

      &nbsp;|&nbsp;&nbsp;

      {if $p_start == $p_end}

        <b>{lang_sprintf id=184 1=$p_start 2=$total_gstores}</b>

      {else}

        <b>{lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_gstores}</b>

      {/if}

      &nbsp;&nbsp;|&nbsp;

      {if $p != $maxpage}

        <a href='javascript:void(0);' onclick='document.seBrowsegstores.p.value={math equation="p+1" p=$p};document.seBrowsegstores.submit();'>{lang_print id=183} &#187;</a>

      {else}

        {lang_print id=183} &#187;

      {/if}

    </div>

  {/if}

  

  

  

  

   {* BEGIN RIGHT COLUM *} 

<td valign="top" style="padding-left:10px;">

  {* BEST SELLERS *}

  <div class="header" style="width:190px;">Best Selling Items</div>

  <div class="portal_content" style="width:190px; background-color:#f2f2f2; padding:5px;">

  {section name=best_sellers_loop loop=$best_sellers max=5}

    <div style='padding: 10px; border: 1px solid #CCCCCC; margin-bottom: 5px; background-color:#ffffff;'>

      <table cellpadding='0' cellspacing='0'>

        <tr>

          <td valign="top">

            <a href='{$url->url_create("gstore", $best_sellers[best_sellers_loop].gstore_author->user_info.user_username, $best_sellers[best_sellers_loop].gstore->gstore_info.gstore_id)}'>

              <img src='{$best_sellers[best_sellers_loop].gstore->gstore_photo("./images/nophoto.gif", TRUE)}' border='0' width='40' height='40' />

            </a>

          </td>

          <td style='vertical-align: top; padding-left: 10px;'>

            <div style='font-weight: bold; font-size: 10pt;'>

              <a href='{$url->url_create("gstore", $best_sellers[best_sellers_loop].gstore_author->user_info.user_username, $best_sellers[best_sellers_loop].gstore->gstore_info.gstore_id)}'>

                {$number} {$best_sellers[best_sellers_loop].gstore->gstore_info.gstore_title|truncate:15:"...":true}

              </a>

            </div>

			

			 <div style='color: #777777; font-size: 7pt; margin-bottom: 5px;'>

				

					   <span class="segstoreLargePrice" style="font-size:10px;">{lang_print id=$setting.gstore_currency}{$best_sellers[best_sellers_loop].gstore->gstore_info.gstore_price}</span>&nbsp;&nbsp;&nbsp;{$best_sellers[best_sellers_loop].gstore->gstore_info.item_sales} sales

            </div>

			

          </td>

        </tr>

      </table>

    </div>

  {/section}

  </div>
  
  
  
  
 <br />

  <div align="center" class="portal_content" style="width:190px; background-color:#f2f2f2; padding:5px; border-top:1px solid #dddddd;">
  <a href="https://www.paypal.com/uk/cgi-bin/webscr?cmd=xpt/Marketing/securitycenter/buy/Protection-outside" target="_blank">
  <img src="../images/icons/paypalprotection.png" border="0" style="margin-top:4px;" /></a>
  </div>

  
  
  
  
  
  
  
  
  

</td>

</td>

</tr>

</table>

</form>

{include file='footer.tpl'}