{include file='header.tpl'}
<table cellpadding='0' cellspacing='0' width='100%'>
  <tr>
    <td valign='top'>
      
      <img src='./images/icons/gstore_management.png' border='0' class='icon_big'>
      <div class='page_header'>{if empty($gstore_id)}{lang_print id=5555085}{else}{lang_print id=5555086}{/if}</div>
      <div>{if empty($gstore_id)}{lang_print id=5555087}{else}{lang_print id=5555088}{/if}</div>
      
    </td>
    <td valign='top' align='right'>
      
      <table cellpadding='0' cellspacing='0'>
        <tr>
          <td class='button' nowrap='nowrap'>
            <a href='user_gstore.php'><img src='./images/icons/back16.gif' border='0' class='button' />{lang_print id=5555102}</a>
          </td>
        </tr>
      </table>
      
    </td>
  </tr>
</table>
<br />
{* JAVASCRIPT FOR CATEGORIES/FIELDS *}
{literal}
<script type='text/javascript'>
<!--
  var cats = {0:{'title':'','subcats':{}}{/literal}{section name=cat_loop loop=$cats}, {$cats[cat_loop].cat_id}{literal}:{'title':'{/literal}{capture assign='cat_title'}{lang_print id=$cats[cat_loop].cat_title}{/capture}{$cat_title|replace:"&#039;":"\'"}{literal}', 'subcats':{{/literal}{section name=subcat_loop loop=$cats[cat_loop].subcats}{if !$smarty.section.subcat_loop.first}, {/if}{$cats[cat_loop].subcats[subcat_loop].subcat_id}:'{capture assign='subcat_title'}{lang_print id=$cats[cat_loop].subcats[subcat_loop].subcat_title}{/capture}{$subcat_title|replace:"&#039;":"\'"}'{/section}{literal}}}{/literal}{/section}{literal}};
  {/literal}{if $cats|@count > 0}{literal}
  window.addEvent('domready', function(){
    for(c in cats) {
      var optn = document.createElement("option");
      optn.text = cats[c].title;
      optn.value = c;
      if(c == {/literal}{$gstore->gstore_info.gstore_gstorecat_id|default:0}{literal}) { optn.selected = true; }
      $('gstore_gstorecat_id').options.add(optn);
    }
    populateSubcats({/literal}{$gstore->gstore_info.gstore_gstorecat_id|default:0}{literal});
  });
  {/literal}{/if}{literal}
  function populateSubcats(gstore_gstorecat_id) {
    var subcats = cats[gstore_gstorecat_id].subcats;
    var subcatHash = new Hash(subcats);
    $$('tr[id^=all_fields_]').each(function(el) { if(el.id == 'all_fields_'+gstore_gstorecat_id) { el.style.display = ''; } else { el.style.display = 'none'; }});
    if(gstore_gstorecat_id == 0 || subcatHash.getValues().length == 0) {
      $('gstore_gstoresubcat_id').options.length = 1;
      $('gstore_gstoresubcat_id').style.display = 'none';
    } else {
      $('gstore_gstoresubcat_id').options.length = 1;
      $('gstore_gstoresubcat_id').style.display = '';
      for(s in subcats) {
        var optn = document.createElement("option");
        optn.text = subcats[s];
        optn.value = s;
        if(s == {/literal}{$gstore->gstore_info.gstore_gstoresubcat_id|default:0}{literal}) { optn.selected = true; }
        $('gstore_gstoresubcat_id').options.add(optn);
      }
    }
  }
  function ShowHideDeps(field_id, field_value, field_type) {
    if(field_type == 6) {
      if($('field_'+field_id+'_option'+field_value)) {
        if($('field_'+field_id+'_option'+field_value).style.display == "block") {
	  $('field_'+field_id+'_option'+field_value).style.display = "none";
	} else {
	  $('field_'+field_id+'_option'+field_value).style.display = "block";
	}
      }
    } else {
      var divIdStart = "field_"+field_id+"_option";
      for(var x=0;x<$('field_options_'+field_id).childNodes.length;x++) {
        if($('field_options_'+field_id).childNodes[x].nodeName == "DIV" && $('field_options_'+field_id).childNodes[x].id.substr(0, divIdStart.length) == divIdStart) {
          if($('field_options_'+field_id).childNodes[x].id == 'field_'+field_id+'_option'+field_value) {
            $('field_options_'+field_id).childNodes[x].style.display = "block";
          } else {
            $('field_options_'+field_id).childNodes[x].style.display = "none";
          }
        }
      }
    }
  }
//-->
</script>
{/literal}
{* SHOW ERROR MESSAGE *}
{if $is_error != 0}
  <table cellpadding='0' cellspacing='0'>
    <tr>
      <td class='result'>
        <div class='error'>
          <img src='./images/error.gif' border='0' class='icon' />
          {lang_print id=$is_error}
        </div>
      </td>
    </tr>
  </table>
  <br />
{/if}
{* gstore FORM *}
<form action='user_gstore_listing.php' method='post'>
<table cellpadding='0' cellspacing='0' class='form'>
  <tr>
    <td class='form1'>{lang_print id=5555089}*</td>
    <td class='form2'><input type='text' class='text' name='gstore_title' value='{$gstore->gstore_info.gstore_title|default:''}' maxlength='100' size='30'></td>
  </tr>
  
    <tr>
    <td class='form1'>Price*</td>
    <td class='form2'>{lang_print id=$setting.gstore_currency} <input type='text' class='text' name='gstore_price' value='{$gstore->gstore_info.gstore_price|default:''}' maxlength='20' size='15'> </td>
  </tr>
  
   <tr>
    <td class='form1'>Number of Items in Stock*</td>
    <td class='form2'><input type='text' class='text' name='gstore_stock' value='{$gstore->gstore_info.gstore_stock|default:''}' maxlength='10' size='5'></td>
  </tr>
  
  
  
  {* shipping charges *}
     <tr>
    <td class='form1'>Apply shipping Charges*</td>
    <td class='form2'>Per item: 
<input type="radio" {if empty($gstore_id) | ($gstore->gstore_info.apply_shipping_charges == 'Per item')} checked="checked" {/if}
name="apply_shipping_charges" value="Per item">
&nbsp;&nbsp;
Total order: 
<input type="radio" {if $gstore->gstore_info.apply_shipping_charges == 'Total order'} checked="checked" {/if}
name="apply_shipping_charges" value="Total order">
&nbsp;&nbsp;
Do not apply shipping: 
<input type="radio" {if $gstore->gstore_info.apply_shipping_charges == 'No shipping'} checked="checked" {/if}
name="apply_shipping_charges" value="No shipping">
</td>
  </tr>
  
  
     <tr>
    <td class='form1'>Shipping Charges<br /><br /><span style="font-weight:normal; color:#990000;">" Please Leave bands &nbsp;<br />you will not ship to blank "</span></td>
    <td class='form2'>
	
	<table border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>{lang_print id=$setting.gstore_currency} <input type='text' class='text' name='band_a_charge' value='{$gstore->gstore_info.band_a_charge}' maxlength='10' size='5'></td>
    <td>&nbsp; Band A: {$setting.gstore_band_a}</td>
  </tr>
  <tr>
    <td>{lang_print id=$setting.gstore_currency} <input type='text' class='text' name='band_b_charge' value='{$gstore->gstore_info.band_b_charge}' maxlength='10' size='5'></td>
    <td>&nbsp; Band B: {$setting.gstore_band_b}</td>
  </tr>
  <tr>
    <td>{lang_print id=$setting.gstore_currency} <input type='text' class='text' name='band_c_charge' value='{$gstore->gstore_info.band_c_charge}' maxlength='10' size='5'></td>
    <td>&nbsp; Band C: {$setting.gstore_band_c}</td>
  </tr>
  <tr>
    <td>{lang_print id=$setting.gstore_currency} <input type='text' class='text' name='band_d_charge' value='{$gstore->gstore_info.band_d_charge}' maxlength='10' size='5'></td>
    <td>&nbsp; Band D: {$setting.gstore_band_d}</td>
  </tr>
</table>

	</td>
  </tr>
  
  
  
  <tr>
    <td class='form1'>{lang_print id=5555090}</td>
    <td class='form2'><textarea rows='6' cols='50' name='gstore_body'>{$gstore->gstore_info.gstore_body|default:''}</textarea></td>
  </tr>
  
  {* gstore CATS AND FIELDS *}
  {if $cats|@count > 0}
  <tr>
    <td class='form1'>{lang_print id=5555091}*</td>
    <td class='form2' nowrap='nowrap'>
      <select name='gstore_gstorecat_id' id='gstore_gstorecat_id' onChange='populateSubcats(this.options[this.selectedIndex].value);'></select>
      <select name='gstore_gstoresubcat_id' id='gstore_gstoresubcat_id' style='display: none;'><option value='0'></option></select>
    </td>
  </tr>
  {section name=cat_loop loop=$cats}
    {section name=field_loop loop=$cats[cat_loop].fields}
      
      <tr id='all_fields_{$cats[cat_loop].cat_id}'>
      <td class='form1'>{lang_print id=$cats[cat_loop].fields[field_loop].field_title}{if $cats[cat_loop].fields[field_loop].field_required != 0}*{/if}</td>
      <td class='form2'>
      {* TEXT FIELD *}
      {if $cats[cat_loop].fields[field_loop].field_type == 1}
        <div><input type='text' class='text' name='field_{$cats[cat_loop].fields[field_loop].field_id}' id='field_{$cats[cat_loop].fields[field_loop].field_id}' value='{$cats[cat_loop].fields[field_loop].field_value}' style='{$cats[cat_loop].fields[field_loop].field_style}' maxlength='{$cats[cat_loop].fields[field_loop].field_maxlength}'></div>
        
        {* JAVASCRIPT FOR CREATING SUGGESTION BOX *}
        {if $cats[cat_loop].fields[field_loop].field_options != "" && $cats[cat_loop].fields[field_loop].field_options|@count != 0}
        {literal}
        <script type="text/javascript">
        <!-- 
        window.addEvent('domready', function()
        {
          var options = {
            script:"misc_js.php?task=suggest_field&limit=5&{/literal}{section name=option_loop loop=$cats[cat_loop].fields[field_loop].field_options}options[]={$cats[cat_loop].fields[field_loop].field_options[option_loop].label}&{/section}{literal}",
            varname:"input",
            json:true,
            shownoresults:false,
            maxresults:5,
            multisuggest:false,
            callback: function (obj) {  }
          };
          var as_json{/literal}{$cats[cat_loop].fields[field_loop].field_id}{literal} = new bsn.AutoSuggest('field_{/literal}{$cats[cat_loop].fields[field_loop].field_id}{literal}', options);
        });
        //-->
        </script>
        {/literal}
        {/if}
        
        
      {* TEXTAREA *}
      {elseif $cats[cat_loop].fields[field_loop].field_type == 2}
        <div>
          <textarea rows='6' cols='50' name='field_{$cats[cat_loop].fields[field_loop].field_id}' style='{$cats[cat_loop].fields[field_loop].field_style}'>{$cats[cat_loop].fields[field_loop].field_value}</textarea>
        </div>
        
        
      {* SELECT BOX *}
      {elseif $cats[cat_loop].fields[field_loop].field_type == 3}
        <div>
          <select name='field_{$cats[cat_loop].fields[field_loop].field_id}' id='field_{$cats[cat_loop].fields[field_loop].field_id}' onchange="ShowHideDeps('{$cats[cat_loop].fields[field_loop].field_id}', this.value);" style='{$cats[cat_loop].fields[field_loop].field_style}'>
            <option value='-1'></option>
            {* LOOP THROUGH FIELD OPTIONS *}
            {section name=option_loop loop=$cats[cat_loop].fields[field_loop].field_options}
              <option id='op' value='{$cats[cat_loop].fields[field_loop].field_options[option_loop].value}'{if $cats[cat_loop].fields[field_loop].field_options[option_loop].value == $cats[cat_loop].fields[field_loop].field_value} SELECTED{/if}>{lang_print id=$cats[cat_loop].fields[field_loop].field_options[option_loop].label}</option>
            {/section}
          </select>
        </div>
        
        {* LOOP THROUGH DEPENDENT FIELDS *}
        <div id='field_options_{$cats[cat_loop].fields[field_loop].field_id}'>
        {section name=option_loop loop=$cats[cat_loop].fields[field_loop].field_options}
          {if $cats[cat_loop].fields[field_loop].field_options[option_loop].dependency == 1}
          
          {* SELECT BOX *}
          {if $cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_type == 3}
            <div id='field_{$cats[cat_loop].fields[field_loop].field_id}_option{$cats[cat_loop].fields[field_loop].field_options[option_loop].value}' style='margin: 5px 5px 10px 5px;{if $cats[cat_loop].fields[field_loop].field_options[option_loop].value != $cats[cat_loop].fields[field_loop].field_value} display: none;{/if}'>
              {lang_print id=$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_title}{if $cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_required != 0}*{/if}
              <select name='field_{$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_id}'>
                <option value='-1'></option>
                {* LOOP THROUGH DEP FIELD OPTIONS *}
                {section name=option2_loop loop=$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_options}
                  <option id='op' value='{$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_options[option2_loop].value}'{if $cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_options[option2_loop].value == $cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_value} SELECTED{/if}>{lang_print id=$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_options[option2_loop].label}</option>
                {/section}
              </select>
            </div>	  
            
          {* TEXT FIELD *}
          {else}
            <div id='field_{$cats[cat_loop].fields[field_loop].field_id}_option{$cats[cat_loop].fields[field_loop].field_options[option_loop].value}' style='margin: 5px 5px 10px 5px;{if $cats[cat_loop].fields[field_loop].field_options[option_loop].value != $cats[cat_loop].fields[field_loop].field_value} display: none;{/if}'>
              {lang_print id=$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_title}{if $cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_required != 0}*{/if}
              <input type='text' class='text' name='field_{$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_id}' value='{$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_value}' style='{$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_style}' maxlength='{$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_maxlength}'>
            </div>
          {/if}
          
          {/if}
        {/section}
        </div>
        
        
      {* RADIO BUTTONS *}
      {elseif $cats[cat_loop].fields[field_loop].field_type == 4}
        
        {* LOOP THROUGH FIELD OPTIONS *}
        <div id='field_options_{$cats[cat_loop].fields[field_loop].field_id}'>
        {section name=option_loop loop=$cats[cat_loop].fields[field_loop].field_options}
          <div>
            <input type='radio' class='radio' onclick="ShowHideDeps('{$cats[cat_loop].fields[field_loop].field_id}', '{$cats[cat_loop].fields[field_loop].field_options[option_loop].value}');" style='{$cats[cat_loop].fields[field_loop].field_style}' name='field_{$cats[cat_loop].fields[field_loop].field_id}' id='label_{$cats[cat_loop].fields[field_loop].field_id}_{$cats[cat_loop].fields[field_loop].field_options[option_loop].value}' value='{$cats[cat_loop].fields[field_loop].field_options[option_loop].value}'{if $cats[cat_loop].fields[field_loop].field_options[option_loop].value == $cats[cat_loop].fields[field_loop].field_value} CHECKED{/if}>
            <label for='label_{$cats[cat_loop].fields[field_loop].field_id}_{$cats[cat_loop].fields[field_loop].field_options[option_loop].value}'>{lang_print id=$cats[cat_loop].fields[field_loop].field_options[option_loop].label}</label>
          </div>
          
          {* DISPLAY DEPENDENT FIELDS *}
          {if $cats[cat_loop].fields[field_loop].field_options[option_loop].dependency == 1}
          
          {* SELECT BOX *}
          {if $cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_type == 3}
            <div id='field_{$cats[cat_loop].fields[field_loop].field_id}_option{$cats[cat_loop].fields[field_loop].field_options[option_loop].value}' style='margin: 0px 5px 10px 23px;{if $cats[cat_loop].fields[field_loop].field_options[option_loop].value != $cats[cat_loop].fields[field_loop].field_value} display: none;{/if}'>
              {lang_print id=$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_title}{if $cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_required != 0}*{/if}
              <select name='field_{$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_id}'>
                <option value='-1'></option>
                {* LOOP THROUGH DEP FIELD OPTIONS *}
                {section name=option2_loop loop=$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_options}
                  <option id='op' value='{$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_options[option2_loop].value}'{if $cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_options[option2_loop].value == $cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_value} SELECTED{/if}>{lang_print id=$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_options[option2_loop].label}</option>
                {/section}
              </select>
            </div>	  
            
          {* TEXT FIELD *}
          {else}
            <div id='field_{$cats[cat_loop].fields[field_loop].field_id}_option{$cats[cat_loop].fields[field_loop].field_options[option_loop].value}' style='margin: 0px 5px 10px 23px;{if $cats[cat_loop].fields[field_loop].field_options[option_loop].value != $cats[cat_loop].fields[field_loop].field_value} display: none;{/if}'>
              {lang_print id=$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_title}{if $cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_required != 0}*{/if}
              <input type='text' class='text' name='field_{$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_id}' value='{$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_value}' style='{$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_style}' maxlength='{$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_maxlength}'>
            </div>
          {/if}
          
          {/if}
          
        {/section}
        </div>
        
        
      {* DATE FIELD *}
      {elseif $cats[cat_loop].fields[field_loop].field_type == 5}
        <div>
          <select name='field_{$cats[cat_loop].fields[field_loop].field_id}_1' style='{$cats[cat_loop].fields[field_loop].field_style}'>
          {section name=date1 loop=$cats[cat_loop].fields[field_loop].date_array1}
            <option value='{$cats[cat_loop].fields[field_loop].date_array1[date1].value}'{$cats[cat_loop].fields[field_loop].date_array1[date1].selected}>{if $smarty.section.date1.first}[ {lang_print id=$cats[cat_loop].fields[field_loop].date_array1[date1].name} ]{else}{$cats[cat_loop].fields[field_loop].date_array1[date1].name}{/if}</option>
          {/section}
          </select>
          
          <select name='field_{$cats[cat_loop].fields[field_loop].field_id}_2' style='{$cats[cat_loop].fields[field_loop].field_style}'>
          {section name=date2 loop=$cats[cat_loop].fields[field_loop].date_array2}
            <option value='{$cats[cat_loop].fields[field_loop].date_array2[date2].value}'{$cats[cat_loop].fields[field_loop].date_array2[date2].selected}>{if $smarty.section.date2.first}[ {lang_print id=$cats[cat_loop].fields[field_loop].date_array2[date2].name} ]{else}{$cats[cat_loop].fields[field_loop].date_array2[date2].name}{/if}</option>
          {/section}
          </select>
          
          <select name='field_{$cats[cat_loop].fields[field_loop].field_id}_3' style='{$cats[cat_loop].fields[field_loop].field_style}'>
          {section name=date3 loop=$cats[cat_loop].fields[field_loop].date_array3}
            <option value='{$cats[cat_loop].fields[field_loop].date_array3[date3].value}'{$cats[cat_loop].fields[field_loop].date_array3[date3].selected}>{if $smarty.section.date3.first}[ {lang_print id=$cats[cat_loop].fields[field_loop].date_array3[date3].name} ]{else}{$cats[cat_loop].fields[field_loop].date_array3[date3].name}{/if}</option>
          {/section}
          </select>
        </div>
        
        
      {* CHECKBOXES *}
      {elseif $cats[cat_loop].fields[field_loop].field_type == 6}
        
        {* LOOP THROUGH FIELD OPTIONS *}
        <div id='field_options_{$cats[cat_loop].fields[field_loop].field_id}'>
        {section name=option_loop loop=$cats[cat_loop].fields[field_loop].field_options}
          <div>
            <input type='checkbox' onclick="ShowHideDeps('{$cats[cat_loop].fields[field_loop].field_id}', '{$cats[cat_loop].fields[field_loop].field_options[option_loop].value}', '{$cats[cat_loop].fields[field_loop].field_type}');" style='{$cats[cat_loop].fields[field_loop].field_style}' name='field_{$cats[cat_loop].fields[field_loop].field_id}[]' id='label_{$cats[cat_loop].fields[field_loop].field_id}_{$cats[cat_loop].fields[field_loop].field_options[option_loop].value}' value='{$cats[cat_loop].fields[field_loop].field_options[option_loop].value}'{if $cats[cat_loop].fields[field_loop].field_options[option_loop].value|in_array:$cats[cat_loop].fields[field_loop].field_value} CHECKED{/if}>
            <label for='label_{$cats[cat_loop].fields[field_loop].field_id}_{$cats[cat_loop].fields[field_loop].field_options[option_loop].value}'>{lang_print id=$cats[cat_loop].fields[field_loop].field_options[option_loop].label}</label>
          </div>
          
          {* DISPLAY DEPENDENT FIELDS *}
          {if $cats[cat_loop].fields[field_loop].field_options[option_loop].dependency == 1}
          
          {* SELECT BOX *}
          {if $cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_type == 3}
            <div id='field_{$cats[cat_loop].fields[field_loop].field_id}_option{$cats[cat_loop].fields[field_loop].field_options[option_loop].value}' style='margin: 0px 5px 10px 23px;{if $cats[cat_loop].fields[field_loop].field_options[option_loop].value != $cats[cat_loop].fields[field_loop].field_value} display: none;{/if}'>
              {lang_print id=$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_title}{if $cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_required != 0}*{/if}
              <select name='field_{$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_id}'>
                <option value='-1'></option>
                {* LOOP THROUGH DEP FIELD OPTIONS *}
                {section name=option2_loop loop=$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_options}
                  <option id='op' value='{$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_options[option2_loop].value}'{if $cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_options[option2_loop].value == $cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_value} SELECTED{/if}>{lang_print id=$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_options[option2_loop].label}</option>
                {/section}
              </select>
            </div>	  
          {* TEXT FIELD *}
          {else}
            <div id='field_{$cats[cat_loop].fields[field_loop].field_id}_option{$cats[cat_loop].fields[field_loop].field_options[option_loop].value}' style='margin: 0px 5px 10px 23px;{if $cats[cat_loop].fields[field_loop].field_options[option_loop].value != $cats[cat_loop].fields[field_loop].field_value} display: none;{/if}'>
              {lang_print id=$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_title}{if $cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_required != 0}*{/if}
              <input type='text' class='text' name='field_{$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_id}' value='{$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_value}' style='{$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_style}' maxlength='{$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_maxlength}'>
            </div>
          {/if}
          
          {/if}
          
        {/section}
        </div>
        
        
        
      {* NUMERIC FIELD *}
      {elseif $cats[cat_loop].fields[field_loop].field_type == 7}
        <div><input type='text' class='text' name='field_{$cats[cat_loop].fields[field_loop].field_id}' id='field_{$cats[cat_loop].fields[field_loop].field_id}' value='{$cats[cat_loop].fields[field_loop].field_value}' style='{$cats[cat_loop].fields[field_loop].field_style}' maxlength='{$cats[cat_loop].fields[field_loop].field_maxlength}'></div>
        
      {/if}
      
      <div class='form_desc'>{lang_print id=$cats[cat_loop].fields[field_loop].field_desc}</div>
      {capture assign='field_error'}{lang_print id=$cats[cat_loop].fields[field_loop].field_error}{/capture}
      {if $field_error != ""}<div class='form_error'><img src='./images/icons/error16.gif' border='0' class='icon'> {$field_error}</div>{/if}
      </td>
      </tr>
      
    {/section}
  {/section}
  {/if}
  {* SHOW SEARCH PRIVACY OPTIONS IF ALLOWED BY ADMIN *}
  {if $user->level_info.level_gstore_search}
  <tr>
    <td class='form1' width='150'>{lang_print id=5555092}</td>
    <td class='form2'>
      <table cellpadding='0' cellspacing='0'>
        <tr>
          <td><input type='radio' name='gstore_search' id='gstore_search_1' value='1'{if  $gstore->gstore_info.gstore_search} CHECKED{/if}></td>
          <td><label for='gstore_search_1'>{lang_print id=5555093}</label></td>
        </tr>
        <tr>
          <td><input type='radio' name='gstore_search' id='gstore_search_0' value='0'{if !$gstore->gstore_info.gstore_search} CHECKED{/if}></td>
          <td><label for='gstore_search_0'>{lang_print id=5555094}</label></td>
        </tr>
      </table>
    </td>
  </tr>
  {/if}
  {* SHOW ALLOW PRIVACY SETTINGS *}
  {if $privacy_options|@count > 1}
  <tr>
    <td class='form1' width='120'>{lang_print id=5555095}</td>
    <td class='form2'>
      <div class='gstore_form_desc'>{lang_print id=5555096}</div>
      <table cellpadding='0' cellspacing='0'>
      {foreach from=$privacy_options name=privacy_loop key=k item=v}
        <tr>
        <td><input type='radio' name='gstore_privacy' id='privacy_{$k}' value='{$k}'{if $gstore->gstore_info.gstore_privacy == $k} checked='checked'{/if}></td>
        <td><label for='privacy_{$k}'>{lang_print id=$v}</label></td>
        </tr>
      {/foreach}
      </table>
    </td>
  </tr>
  {/if}
  {* SHOW ALLOW COMMENT SETTINGS *}
  {if $comment_options|@count > 1}
  <tr>
    <td class='form1' width='120'>{lang_print id=5555097}</td>
    <td class='form2'>
      <div class='gstore_form_desc'>{lang_print id=5555098}</div>
      <table cellpadding='0' cellspacing='0'>
      {foreach from=$comment_options name=comment_loop key=k item=v}
        <tr>
          <td><input type='radio' name='gstore_comments' id='comment_{$k}' value='{$k}'{if $gstore->gstore_info.gstore_comments == $k} checked='checked'{/if}></td>
          <td><label for='comment_{$k}'>{lang_print id=$v}</label></td>
        </tr>
      {/foreach}
      </table>
    </td>
  </tr>
  {/if}
  
  {* PASS BACK gstore ID WHEN EDITING *}
  {if !empty($gstore->gstore_info.gstore_id)}
    <input type="hidden" name="gstore_id" value="{$gstore->gstore_info.gstore_id}" />
  {/if}
  
  <tr>
    <td>&nbsp;</td>
    <td>
      <table cellpadding='0' cellspacing='0' style='margin-top: 10px;'>
      <tr>
      <td>
        {lang_block id=5555099 var=langBlockTemp}<input type='submit' class='button' value='{$langBlockTemp}' />&nbsp;{/lang_block}
        <input type='hidden' name='task' value='dosave'>
        </form>
      </td>
      <td>
        <form action='user_gstore.php' method='get'>
        {lang_block id=39 var=langBlockTemp}<input type='submit' class='button' value='{$langBlockTemp}' />{/lang_block}
        </form>
      </td>
      </tr>
      </table>
    </td>
  </tr>
</table>
{include file='footer.tpl'}