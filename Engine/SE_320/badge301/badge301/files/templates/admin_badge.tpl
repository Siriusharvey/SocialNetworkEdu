{include file='admin_header.tpl'}

<h2>{lang_print id=11270116}</h2>
{lang_print id=11270118}
<br />
<br />



{* JAVASCRIPT FOR ADDING CATEGORIES AND FIELDS *}
{literal}
<script type="text/javascript">
<!-- 
var categories;
var cat_type = 'badge';
var showCatFields = 0;
var showSubcatFields = 0;
var subcatTab = 0;
var hideSearch = 1;
var hideDisplay = 1;
var hideSpecial = 1;

function createSortable(divId, handleClass) {
  new Sortables($(divId), {handle:handleClass, onComplete: function() { changeorder(this.serialize(), divId); }});
}

Sortables.implement({
  serialize: function(){
    var serial = [];
    this.list.getChildren().each(function(el, i){
      serial[i] = el.getProperty('id');
    }, this);
    return serial;
  }
});


window.addEvent('domready', function(){ createSortable('categories', 'img.handle_cat'); });

//-->
</script>
{/literal}

{* INCLUDE JAVASCRIPT AND FIELD DIV *}
{include file='admin_fields_js.tpl'}





{if $result != 0}
  <div class='success'><img src='../images/success.gif' class='icon' border='0'> {lang_print id=191}</div>
{/if}

{if $is_error != 0}
<div class='error'><img src='../images/error.gif' border='0' class='icon'> {$error_message}</div>
{/if}

  

<form action='admin_badge.php' method='POST'>

<table cellpadding='0' cellspacing='0' width='600'>
  <tr><td class='header'>{lang_print id=11000013}</td></tr>
  <tr><td class='setting1'>{lang_print id=11000014}</td></tr>
  <tr><td class='setting2'><input type='text' name='setting_badge_license' value='{$setting.setting_badge_license}' size='30' maxlength="200" /> {lang_print id=11000015}</td>
  </tr>
</table>

<br>

<table cellpadding='0' cellspacing='0' width='600'>


<tr>
<td class='header'>{lang_print id=192}</td>
</tr>
<td class='setting1'>
  {lang_print id=11270119}
</td>
</tr>
<tr>
<td class='setting2'>
  <table cellpadding='2' cellspacing='0'>
  <tr>
  <td><input type='radio' name='setting_permission_badge' id='permission_badge_1' value='1'{if $setting.setting_permission_badge == 1} checked='checked'{/if}></td>
  <td><label for='permission_badge_1'>{lang_print id=11270120}</label></td>
  </tr>
  <tr>
  <td><input type='radio' name='setting_permission_badge' id='permission_badge_0' value='0'{if $setting.setting_permission_badge == 0} checked='checked'{/if}></td>
  <td><label for='permission_badge_0'>{lang_print id=11270121}</label></td>
  </tr>
  </table>
</td>
</tr>
</table>

<br>

<table cellpadding='0' cellspacing='0' width='600'>
<tr>
<td class='header'>{lang_print id=11270078}</td>
</tr>
<tr>
<td class='setting1'>
{lang_print id=11270079}
</td>
</tr>
<tr>
<td class='setting2'>
<input type='radio' name='setting_badge_profile_show' value='tab' {if $setting.setting_badge_profile_show == 'tab'}checked='checked'{/if} id='setting_badge_profile_show_tab' />
<label for='setting_badge_profile_show_tab'>{lang_print id=11270067}</label>
<br>
<input type='radio' name='setting_badge_profile_show' value='side' {if $setting.setting_badge_profile_show == 'side'}checked='checked'{/if} id='setting_badge_profile_show_side' />
<label for='setting_badge_profile_show_side'>{lang_print id=11270068}</label>
<br>
<input type='radio' name='setting_badge_profile_show' value='none' {if $setting.setting_badge_profile_show == 'none'}checked='checked'{/if} id='setting_badge_profile_show_none' />
<label for='setting_badge_profile_show_none'>{lang_print id=11270069}</label>
</td>
</tr>

<tr>
<td class='setting1'>
{lang_print id=11270163}
</td>
</tr>
<tr>
<td class='setting2'>
<input type='text' class='text' name='setting_badge_menu_badge_ids' value='{$setting.setting_badge_menu_badge_ids}' size=40 />
{lang_print id=11270164}
</td>
</tr>


</table>


<br>

<table cellpadding='0' cellspacing='0' width='600'>
<tr>
<td class='header'>{lang_print id=11270124}</td>
</tr>
<tr>
<td class='setting1'>
{lang_print id=11270127}<br>
{lang_print id=11270126}
</td>
</tr>
<tr>
<td class='setting2'>
<input name='setting_badge_exts' class='text' value='{$setting.setting_badge_exts}' style='width: 100%'>
</td>
</tr>
<tr>
<td class='setting1'>
{lang_print id=11270129}
</td>
</tr>
<tr>
<td class='setting2'>
{lang_print id=11270132}: <input type='text' class='text' name='setting_badge_width' value='{$setting.setting_badge_width}' maxlength='4' size='5'>px &nbsp; {lang_print id=11270131}: <input type='text' class='text' name='setting_badge_height' value='{$setting.setting_badge_height}' maxlength='4' size='5'>px 
</td>
</tr>
</table>


<br>

<table cellpadding='0' cellspacing='0' width='600'>
<tr>
<td class='header'>{lang_print id=11270125}</td>
</tr>
<tr>
<td class='setting1'>{lang_print id=11270123}</td>
</tr>
<tr>
<td class='setting2'>
  <table>
    {foreach from=$levels item=level}
      {assign var='level_id' value=$level.level_id}
      {assign var='level_badge_id' value=`$setting_badge_levels.$level_id`}
      <tr>
        <td class="form1" width="120">{$level.level_name}:</td>
        <td class="form2">
          <select name="setting_badge_levels[{$level.level_id}]">
            <option value="0">{lang_print id=11270117}</option>
					{section name=badge_loop loop=$badges}
					  {assign var='badge' value=$badges[badge_loop].badge}
					  <option value="{$badge->badge_info.badge_id}" {if $level_badge_id == $badge->badge_info.badge_id}selected='selected'{/if}>{$badge->badge_info.badge_title} #{$badge->badge_info.badge_id}</option>  
					{/section}    
          </select>  
        </td>
      </tr>
    {/foreach}  
  </table>
</td>
</tr>


<tr>
<td class='setting1'>{lang_print id=11270128}</td>
</tr>
<tr>
<td class='setting2'>
  <table>
    {foreach from=$subnets item=subnet}
      {assign var='subnet_id' value=$subnet.subnet_id}
      {assign var='subnet_badge_id' value=`$setting_badge_subnets.$subnet_id`}
      <tr>
        <td class="form1" width="120">{lang_print id=$subnet.subnet_name}:</td>
        <td class="form2">
          <select name="setting_badge_subnets[{$subnet.subnet_id}]">
            <option value="0">{lang_print id=11270117}</option>
          {section name=badge_loop loop=$badges}
            {assign var='badge' value=$badges[badge_loop].badge}
            <option value="{$badge->badge_info.badge_id}" {if $subnet_badge_id == $badge->badge_info.badge_id}selected='selected'{/if}>{$badge->badge_info.badge_title} #{$badge->badge_info.badge_id}</option>  
          {/section}    
          </select>  
        </td>
      </tr>
    {/foreach}  
  </table>
</td>
</tr>

<tr>
<td class='setting1'>{lang_print id=11270130}</td>
</tr>
<tr>
<td class='setting2'>
  <table>
    {foreach from=$profilecats item=profilecat}
      {assign var='profilecat_id' value=$profilecat.profilecat_id}
      {assign var='profilecat_badge_id' value=`$setting_badge_profilecats.$profilecat_id`}
      <tr>
        <td class="form1" width="120">{lang_print id=$profilecat.profilecat_title}:</td>
        <td class="form2">
          <select name="setting_badge_profilecats[{$profilecat.profilecat_id}]">
            <option value="0">{lang_print id=11270117}</option>
          {section name=badge_loop loop=$badges}
            {assign var='badge' value=$badges[badge_loop].badge}
            <option value="{$badge->badge_info.badge_id}" {if $profilecat_badge_id == $badge->badge_info.badge_id}selected='selected'{/if}>{$badge->badge_info.badge_title} #{$badge->badge_info.badge_id}</option>  
          {/section}    
          </select>  
        </td>
      </tr>
    {/foreach}  
  </table>
</td>
</tr>

</table>


<br>


<table cellpadding='0' cellspacing='0' width='600'>
  <tr>
    <td class='header'>{lang_print id=11270136}</td>
  </tr>
  <tr>
    <td class='setting1'>{lang_print id=11270137}</td>
  </tr>
  <tr>
    <td class='setting2'>

{* SHOW ADD CATEGORY LINK *}
      <div style='font-weight: bold;'>
        &nbsp;
        {lang_print id=11270139} - <a href='javascript:addcat();'>[{lang_print id=104}]</a>
      </div>
      
      <div id='categories' style='padding-left: 5px; font-size: 8pt;'>
        
        {* LOOP THROUGH CATEGORIES *}
        {section name=cat_loop loop=$cats}
        
        {* CATEGORY DIV *}
        <div id='cat_{$cats[cat_loop].cat_id}'>
        
          {* SHOW CATEGORY *}
          <div style='font-weight: bold;'>
            <img src='../images/folder_open_yellow.gif' border='0' class='handle_cat' style='vertical-align: middle; margin-right: 5px; cursor: move;' />
            <span id='cat_{$cats[cat_loop].cat_id}_span'><a href='javascript:editcat("{$cats[cat_loop].cat_id}", "0");' id='cat_{$cats[cat_loop].cat_id}_title'>{lang_print id=$cats[cat_loop].cat_title}</a></span>
          </div>
          
          {* SHOW ADD SUBCATEGORY LINK *}
          <div style='padding-left: 20px; padding-top: 3px; padding-bottom: 3px;'>
            {lang_print id=1202} - 
            <a href='javascript:addsubcat("{$cats[cat_loop].cat_id}");'>[{lang_print id=1203}]</a>
          </div>
          
          {* JAVASCRIPT FOR SORTING CATEGORIES AND FIELDS *}
          {literal}
          <script type="text/javascript">
          <!-- 
          window.addEvent('domready', function(){ createSortable('subcats_{/literal}{$cats[cat_loop].cat_id}{literal}', 'img.handle_subcat_{/literal}{$cats[cat_loop].cat_id}{literal}'); });
          //-->
          </script>
          {/literal}
          
          {* SUBCATEGORY DIV *}
          <div id='subcats_{$cats[cat_loop].cat_id}' style='padding-left: 20px;'>
            
            {* LOOP THROUGH SUBCATEGORIES *}
            {section name=subcat_loop loop=$cats[cat_loop].subcats}
            <div id='cat_{$cats[cat_loop].subcats[subcat_loop].subcat_id}' style='padding-left: 15px;'>
              <div>
                <img src='../images/folder_open_green.gif' border='0' class='handle_subcat_{$cats[cat_loop].cat_id}' style='vertical-align: middle; margin-right: 5px; cursor: move;' />
                <span id='cat_{$cats[cat_loop].subcats[subcat_loop].subcat_id}_span'><a href='javascript:editcat("{$cats[cat_loop].subcats[subcat_loop].subcat_id}", "{$cats[cat_loop].cat_id}");' id='cat_{$cats[cat_loop].subcats[subcat_loop].subcat_id}_title'>{lang_print id=$cats[cat_loop].subcats[subcat_loop].subcat_title}</a></span>
              </div>
            </div>
            {/section}
            
          </div>
          
        </div>
      {/section}
      
      </div>
      
      
      
      
      
    </td>
  </tr>
</table>
<br>

<input type='submit' class='button' value='{lang_print id=173}'>
<input type='hidden' name='task' value='dosave'>
</form>

{include file='admin_footer.tpl'}