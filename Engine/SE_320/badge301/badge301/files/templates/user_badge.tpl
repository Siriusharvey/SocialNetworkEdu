{include file='header.tpl'}

<img src='./images/icons/badge_badge48.gif' border='0' class='icon_big'>
<div class='page_header'>{lang_print id=11270025}</div>
<div>{lang_sprintf id=11270103 1=$total_badgeassignments 2=$user->level_info.level_badge_maxnum}</div>

<br>
<br>

  {if $result != 0}
    <div class='success'><img src='./images/success.gif' class='icon' border='0' /> {lang_print id=$result}</div><br>
  {/if}

  {if $is_error != 0}
    <div class='error'><img src='./images/error.gif' class='icon' border='0' /> {lang_print id=$is_error}</div><br>
  {/if}


  {* DISPLAY PAGINATION MENU IF APPLICABLE *}
  {if $maxpage > 1}
    <div class='center'>
      {if $p != 1}
        <a href='user_badge.php?search={$search}&p={math equation="p-1" p=$p}'>&#171; {lang_print id=182}</a>
      {else}
        <font class='disabled'>&#171; {lang_print id=182}</font>
      {/if}
      {if $p_start == $p_end}
        &nbsp;|&nbsp; {lang_sprintf id=184 1=$p_start 2=$badges_total} &nbsp;|&nbsp; 
      {else}
        &nbsp;|&nbsp; {lang_sprintf id=185 1=$p_start 2=$p_end 3=$badges_total} &nbsp;|&nbsp; 
      {/if}
      {if $p != $maxpage}
        <a href='user_badge.php?search={$search}&p={math equation="p+1" p=$p}'>{lang_print id=183} &#187;</a>
      {else}
        <font class='disabled'>{lang_print id=183} &#187;</font>
      {/if}
    </div>
    <br />
  {/if}


{* LOOP THROUGH BADGES *}
{section name=badgeassignment_loop loop=$badgeassignments}    
  {assign var=badge value=$badgeassignments[badgeassignment_loop].badge}
  {assign var=badgeassignment value=$badgeassignments[badgeassignment_loop].badgeassignment}
    <div class='seBadge'>
      <table cellpadding='0' cellspacing='0' width="100%">
      <tr>
      <td class='seBadgeLeft'>
        <div class='seBadgePhoto' style='width: 180px'>
          <a href='{$url->url_create("badge", NULL, $badge->badge_info.badge_id)}'><img src='{$badge->badge_photo("./images/badge_placeholder.gif")}' border='0' ></a>
        </div>
      </td>
      <td class='seBadgeRight'  width="100%">
        <div class='seBadgeTitle'><a href='{$url->url_create("badgeassignment", NULL, $badgeassignment->badgeassignment_info.badgeassignment_id)}'>{$badge->badge_info.badge_title}</a></div>
        <div class='seBadgeStats'>
            {assign var=badgeassignment_datecreated value=$datetime->timezone($badgeassignment->badgeassignment_info.badgeassignment_datecreated, $global_timezone)}
            {assign var=badgeassignment_dateapproved value=$datetime->timezone($badgeassignment->badgeassignment_info.badgeassignment_dateapproved, $global_timezone)}

            {lang_print id=11270024}
            {lang_sprintf id=11270026 1=$datetime->cdate($setting.setting_dateformat, $badgeassignment_datecreated) 2=$datetime->cdate($setting.setting_timeformat, $badgeassignment_datecreated)}
            
            {if $badgeassignment->badgeassignment_info.badgeassignment_approved }
              - {lang_print id=11270030}
              {lang_sprintf id=11270026 1=$datetime->cdate($setting.setting_dateformat, $badgeassignment_dateapproved) 2=$datetime->cdate($setting.setting_timeformat, $badgeassignment_dateapproved)}
            {else}
              - <span style='color: red'>{lang_print id=11270031}</span>
              {if $badgeassignment->badgeassignment_info.badgeassignment_epayment && !$badgeassignment->badgeassignment_info.epaymenttransaction_id}
              - <span style='color: red'>{lang_print id=11270032}</span>
              {/if}
            {/if}
        </div>
        {if $badgeassignment->badgeassignment_info.badgeassignment_desc}
        <div class='seBadgeDesc'>
          {$badgeassignment->badgeassignment_info.badgeassignment_desc|strip_tags|truncate:165:"...":true}
        </div>
        {/if}
        {if $user->level_info.level_badge_edit}
        <div class='seBadgeActions'>
          <div class='seBadgeAction'>
            <a href='javascript:void(0);' onClick="editBadge('{$badgeassignment->badgeassignment_info.badgeassignment_id}', {$badgeassignment->badgeassignment_info.badgeassignment_profile});"><img src='./images/icons/badge_edit16.gif' border='0' class='button'>{lang_print id=11270107}</a>
          </div>
        {/if}
        {if $user->level_info.level_badge_delete} 
          <div class='seBadgeAction'>
            <a href='javascript:void(0);' onClick="confirmDelete('{$badgeassignment->badgeassignment_info.badgeassignment_id}');"><img src='./images/icons/badge_delete16.gif' border='0' class='button'>{lang_print id=11270108}</a>
          </div>
        {/if}  
           {if !$badgeassignment->badgeassignment_info.badgeassignment_approved && $badgeassignment->badgeassignment_info.badgeassignment_epayment && !$badgeassignment->badgeassignment_info.epaymenttransaction_id}
           <div class="seJobpostAction">
             <a href="user_epayment_checkout.php?item_type=badgeassignment&item_id={$badgeassignment->badgeassignment_info.badgeassignment_id}">
               <img src='./images/icons/epayment_epayment16.gif' border='0' class='button' />
               {lang_print id=11270034}
             </a>
           </div>
           {/if}
          
          <div style='clear: both; height: 0px;'></div>
        </div>

        {* HAS TRANSACTION *}
        {if $badgeassignment->badgeassignment_info.epaymenttransaction_id}
          {assign var=epaymenttransaction_createdat value=$datetime->timezone($badgeassignment->badgeassignment_info.epaymenttransaction_createdat, $global_timezone)}
          <div class='seBadgePaymentPaid'>
              {lang_print id=11270040}: <a href="user_epayment_transaction_view.php?transaction_id={$badgeassignment->badgeassignment_info.epaymenttransaction_id}"><strong>{$badgeassignment->badgeassignment_info.epaymenttransaction_id}</strong></a>  
            - {lang_print id=11270041}: <strong>{$badgeassignment->badgeassignment_info.epaymenttransaction_status}</strong>
            - {lang_print id=11270042}: <strong>{$badgeassignment->badgeassignment_info.epaymenttransaction_amount} {$badgeassignment->badgeassignment_info.epaymenttransaction_currency}</strong>  
            <br>
              {lang_print id=11270043}: <strong>{$badgeassignment->badgeassignment_info.epaymenttransaction_txn_id}</strong>
            - {lang_print id=11270044}: <strong>{lang_sprintf id=11270026 1=$datetime->cdate($setting.setting_dateformat, $epaymenttransaction_createdat) 2=$datetime->cdate($setting.setting_timeformat, $epaymenttransaction_createdat)}</strong>
          </div>
        {elseif !$badgeassignment->badgeassignment_info.badgeassignment_approved && $badgeassignment->badgeassignment_info.badgeassignment_epayment}
          <div class='seBadgePaymentUnPaid'>
            {lang_print id=11270039}
            <a href="user_epayment_checkout.php?item_type=badgeassignment&item_id={$badgeassignment->badgeassignment_info.badgeassignment_id}">{lang_print id=11270034}</a>
          </div>
        {/if}

      </td>
      </tr>
      </table>
    </div>    

    {cycle values=",<div style='clear: both; height: 0px;'></div>"}
        
{/section}
<div style='clear: both; height: 0px;'></div>

  {* DISPLAY PAGINATION MENU IF APPLICABLE *}
  {if $maxpage > 1}
    <br>
    <div class='center'>
      {if $p != 1}
        <a href='user_badge.php?search={$search}&p={math equation="p-1" p=$p}'>&#171; {lang_print id=182}</a>
      {else}
        <font class='disabled'>&#171; {lang_print id=182}</font>
      {/if}
      {if $p_start == $p_end}
        &nbsp;|&nbsp; {lang_sprintf id=184 1=$p_start 2=$badges_total} &nbsp;|&nbsp; 
      {else}
        &nbsp;|&nbsp; {lang_sprintf id=185 1=$p_start 2=$p_end 3=$badges_total} &nbsp;|&nbsp; 
      {/if}
      {if $p != $maxpage}
        <a href='user_badge.php?search={$search}&p={math equation="p+1" p=$p}'>{lang_print id=183} &#187;</a>
      {else}
        <font class='disabled'>{lang_print id=183} &#187;</font>
      {/if}
    </div>
    <br />
  {/if}
  
{* IF NO BADGES, SHOW NOTE *}
{if $total_badgeassignments == 0}
  <table cellpadding='0' cellspacing='0'>
  <tr><td class='result'>
    <img src='./images/icons/bulb16.gif' border='0' class='icon'>{lang_print id=11270109} <a href='browse_badges.php'>{lang_print id=11270110}</a></div>
  </td></tr>
  </table>
{/if}


{* JAVASCRIPT FOR CONFIRMING DELETION *}
{literal}
<script type="text/javascript">
<!-- 
var badgeassignment_id = 0;
function confirmDelete(id) {
	badgeassignment_id = id;
  TB_show('{/literal}{lang_print id=11270145}{literal}', '#TB_inline?height=100&width=300&inlineId=confirmdelete', '', '../images/trans.gif');

}

function deleteBadge() {
  window.location = 'user_badge.php?task=delete&badgeassignment_id='+badgeassignment_id;
}

function editBadge(id,p) {
	  $('badgeassignment_id').defaultValue = id;  
	  $('badgeassignment_id').value = id; 

	  if (p > 0) {
	        $('badgeassignment_profile').set('checked', true); 
	        $('badgeassignment_profile').setAttribute('checked' , 'checked');
	  }
	  else {
		    $('badgeassignment_profile').set('checked', false); 
		    $('badgeassignment_profile').erase('checked');
		    $('badgeassignment_profile').defaultChecked = false;
	  } 	

	TB_show('{/literal}{lang_print id=11270107}{literal}', '#TB_inline?height=100&width=300&inlineId=editbadgeform', '', '../images/trans.gif');
}

//-->
</script>
{/literal}

{* HIDDEN DIV TO DISPLAY CONFIRMATION MESSAGE *}
<div style='display: none;' id='confirmdelete'>
  <div style='margin-top: 10px;'>
    {lang_print id=11270146}
  </div>
  <br>
  <input type='button' class='button' value='{lang_print id=175}' onClick='parent.TB_remove();parent.deleteBadge();'> <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
</div>

{* HIDDEN DIV TO DISPLAY EDIT FORM *}
<div style='display: none;' id='editbadgeform'>
<form method="POST" action="user_badge.php">
<input type="hidden" name="task" value="update" />
<input type="hidden" name="badgeassignment_id" id="badgeassignment_id" value="" />
  <div style='margin: 10px 0;'>
    {lang_print id=11270113}:
  </div>  
  <input type='checkbox' name='badgeassignment_profile' id='badgeassignment_profile' value='1' />
  <label for='badgeassignment_profile'>{lang_print id=11270114}</label>
 
  <br>
  <br>

  <input type='submit' class='button' value='{lang_print id=173}'> <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
</form>
</div>


{include file='footer.tpl'}




