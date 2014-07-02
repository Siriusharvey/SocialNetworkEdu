{include file='header.tpl'}















<div class='page_header'>



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







{* SHOW gstore ENTRIES *}



{section name=gstore_loop loop=$gstores}



<div id='segstore_{$gstores[gstore_loop].gstore->gstore_info.gstore_id}' class="segstore {cycle values='segstore1,segstore2'}">







  <table cellpadding='0' cellspacing='0' width='100%'>



    <tr>



      <td class='segstoreLeft' width='1'>



        <div class='segstorePhoto' style='width: 140px;'>



          <table cellpadding='0' cellspacing='0' width='140'>



            <tr>



              <td>



                <a href='{$url->url_create("gstore", $owner->user_info.user_username, $gstores[gstore_loop].gstore->gstore_info.gstore_id)}'>



                  <img src='{$gstores[gstore_loop].gstore->gstore_photo("./images/nophoto.gif")}' border='0' width='{$misc->photo_size($gstores[gstore_loop].gstore->gstore_photo("./images/nophoto.gif"),"140","140","w")}' />



                </a>



              </td>



            </tr>



          </table>



        </div>



      </td>



      <td class='segstoreRight' width='100%'>



      



        {* SHOW gstore TITLE *}



        <div class='segstoreTitle'>



          <a href='{$url->url_create("gstore", $owner->user_info.user_username, $gstores[gstore_loop].gstore->gstore_info.gstore_id)}'>



            {if !$gstores[gstore_loop].gstore->gstore_info.gstore_title}<i>{lang_print id=589}</i>{else}{$gstores[gstore_loop].gstore->gstore_info.gstore_title|truncate:70:"...":false|choptext:40:"<br>"}{/if}



          </a>



        </div>



		Price: <span class="segstoreLargePrice">{lang_print id=$setting.gstore_currency} {$gstores[gstore_loop].gstore->gstore_info.gstore_price}</span>



        



        {* SHOW gstore CATEGORY *}



        {if !empty($gstores[gstore_loop].gstore->gstore_info.main_category_title)}



        <div class='segstoreCategory'>



          {lang_print id=5555058}



          {* SHOW PARENT CATEGORY *}



          {if !empty($gstores[gstore_loop].gstore->gstore_info.parent_category_title)}



            <a href="browse_gstores.php?gstorecat_id={$gstores[gstore_loop].gstore->gstore_info.parent_category_id}">{lang_print id=$gstores[gstore_loop].gstore->gstore_info.parent_category_title}</a>



            -



          {/if}



          <a href="browse_gstores.php?gstorecat_id={$gstores[gstore_loop].gstore->gstore_info.main_category_id}">{lang_print id=$gstores[gstore_loop].gstore->gstore_info.main_category_title}</a>



        </div>



        {/if}



        



        {* SHOW gstore STATS *}



        <div class='segstoreStats'>



          {assign var='gstore_datecreated' value=$datetime->time_since($gstores[gstore_loop].gstore->gstore_info.gstore_date)}



          {capture assign="created"}{lang_sprintf id=$gstore_datecreated[0] 1=$gstore_datecreated[1]}{/capture}



          {assign var='gstore_dateupdated' value=$datetime->time_since($gstores[gstore_loop].gstore->gstore_info.gstore_dateupdated)}



          {capture assign="updated"}{lang_sprintf id=$gstore_dateupdated[0] 1=$gstore_dateupdated[1]}{/capture}



          



          {lang_sprintf id=5555072 1=$gstores[gstore_loop].gstore->gstore_info.gstore_views}



          - {lang_sprintf id=507 1=$gstores[gstore_loop].gstore->gstore_info.total_comments}



          - {lang_sprintf id=5555135 1=$created}



          {if $gstores[gstore_loop].gstore->gstore_info.gstore_dateupdated && $created!=$updated}



            - {lang_sprintf id=5555136 1=$updated}



          {/if}



        </div>



        



        {* SHOW gstore DESCRIPTION *}



        <div class='segstoreDescription' style='margin-top: 8px; margin-bottom: 8px;'>



          {$gstores[gstore_loop].gstore->gstore_info.gstore_body|strip_tags|truncate:197:"...":true}



        </div>



      </td>



    </tr>



  </table>



</div>



{/section}







<div style='clear: both; height: 0px;'></div>











{* DISPLAY PAGINATION MENU IF APPLICABLE *}



{if $maxpage > 1}



  



  <div class='center'>



    {if $p != 1}



      <a href='{$url->url_create("gstores", $owner->user_info.user_username)}&p={math equation="p-1" p=$p}'>&#171; {lang_print id=182}</a>



    {else}



      <font class='disabled'>&#171; {lang_print id=182}</font>



    {/if}



    {if $p_start == $p_end}



      &nbsp;|&nbsp; {lang_sprintf id=184 1=$p_start 2=$total_gstores} &nbsp;|&nbsp; 



    {else}



      &nbsp;|&nbsp; {lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_gstores} &nbsp;|&nbsp; 



    {/if}



    {if $p != $maxpage}



      <a href='{$url->url_create("gstores", $owner->user_info.user_username)}&p={math equation="p+1" p=$p}'>{lang_print id=183} &#187;</a>



    {else}



      <font class='disabled'>{lang_print id=183} &#187;</font>



    {/if}



  </div>



{/if}







{include file='footer.tpl'}