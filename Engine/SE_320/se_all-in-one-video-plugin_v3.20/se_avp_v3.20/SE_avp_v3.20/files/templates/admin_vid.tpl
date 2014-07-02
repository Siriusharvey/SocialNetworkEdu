{include file='admin_header.tpl'}

{literal}
<script type="text/javascript">
<!-- 
function checkChecked() {
    if($('setting_vid_embed_0').checked) {
         $('embed_0').style.display = 'none';
         $('embed_1').style.display = 'none';
    }
}
//-->
</script>
<script type="text/javascript">
<!-- 
window.addEvent('domready', function() {
    checkChecked();
    $('setting_vid_embed_0').addEvent('click', function(e){
         $('embed_0').style.display = 'none';
         $('embed_1').style.display = 'none';
    });
    $('setting_vid_embed_1').addEvent('click', function(e){
         $('embed_0').style.display = 'inline';
         $('embed_1').style.display = 'inline';
    });
});
//-->
</script>
{/literal}

<h2>{lang_print id=13500005}</h2>
{lang_print id=13500008}

<br><br>

{if $result != 0}
  <div class='success'><img src='../images/success.gif' class='icon' border='0'> {lang_print id=191}</div>
{/if}

{if $msg}
  <div class='error'><img src='../images/error.gif' class='icon' border='0'> {$msg}</div>
{/if}

<form action='admin_vid.php' method='POST'>

  {* JAVASCRIPT FOR ADDING VIDEO CATEGORIES *}
  {literal}
  <script type="text/javascript">
  <!--
  
  function createNewCategory()
  {
    // Display
    $('newCategoryInput').value = '';
    $('newCategoryContainer').style.display = '';
    $('newCategoryLink').style.display = 'none';
  }
  
  function editNewCategory()
  {
    var newCategoryTitle = $('newCategoryInput').value;
    
    // Display
    $('newCategoryInput').value = '';
    $('newCategoryContainer').style.display = 'none';
    $('newCategoryLink').style.display = '';
    
    // Ajax
    var request = new Request.JSON({
      'method' : 'post',
      'url' : 'admin_vid.php',
      'data' : {
        'task' : 'createvidcat',
        'vidcat_title' : newCategoryTitle
      },
      'onComplete':function(responseObject)
      {
        if( $type(responseObject)!="object" || !responseObject.result || responseObject.result=="failure" )
        {
          alert('ERR');
        }
        
        else
        {
          var vidcat_id = responseObject.vidcat_id;
          var vidcat_languagevar_id = responseObject.vidcat_languagevar_id;
          var innerHTML = '';
          
          //innerHTML += '<td>';
          innerHTML += '<span class="oldCategoryContainer">';
          innerHTML += '<a href="javascript:void(0);" onclick="switchOldCategory(' + vidcat_id + ');">';
          innerHTML += newCategoryTitle;
          innerHTML += '</a>';
          innerHTML += '</span>';
          innerHTML += '<span class="oldCategoryInput" style="display:none;">';
          innerHTML += "<input type='text' class='text' size='30' maxlength='50' onblur='editOldCategory(" + vidcat_id + ");' value='" + newCategoryTitle + "' />";
          innerHTML += '</span>';
          innerHTML += '<span class="oldCategoryLangVar">';
          innerHTML += '&nbsp;(Language Variable #<a href="admin_language_edit.php?language_id=1&phrase_id=' + vidcat_languagevar_id + '">';
          innerHTML += vidcat_languagevar_id;
          innerHTML += '</a>)';
          innerHTML += '</span>';
          //innerHTML += '</td>';
          
          //alert(innerHTML);
          
          var newCategoryRow = new Element('tr', {'id' : 'vidCatRow_' + vidcat_id});
          var newCategoryData = new Element('td', {'html' : innerHTML});
          
          newCategoryRow.inject($('newCategoryRow'), 'before');
          newCategoryData.inject(newCategoryRow);
        }
      }
    });
    
    request.send();
  }
  
  function switchOldCategory(vidcat_id)
  {
    var categoryRow = $('vidCatRow_' + vidcat_id);
    categoryRow.getElement('.oldCategoryContainer').style.display = 'none';
    categoryRow.getElement('.oldCategoryInput').style.display = '';
    categoryRow.getElement('input').focus();
  }
  
  function unswitchOldCategory(vidcat_id)
  {
    var categoryRow = $('vidCatRow_' + vidcat_id);
    categoryRow.getElement('.oldCategoryContainer').style.display = '';
    categoryRow.getElement('.oldCategoryInput').style.display = 'none';
  }
  
  function editOldCategory(vidcat_id)
  {
    var categoryRow = $('vidCatRow_' + vidcat_id);
    var newCategoryTitle = categoryRow.getElement('input').value;
    
    // DELETE
    if( newCategoryTitle.trim()=='' )
    {
      deleteCategory(vidcat_id);
      return;
    }
    
    categoryRow.getElement('.oldCategoryContainer').getElement('a').innerHTML = newCategoryTitle;
    unswitchOldCategory(vidcat_id);
    
    // Ajax
    var request = new Request.JSON({
      'method' : 'post',
      'url' : 'admin_vid.php',
      'data' : {
        'task' : 'editvidcat',
        'vidcat_id' : vidcat_id,
        'vidcat_title' : newCategoryTitle
      },
      'onComplete':function(responseObject)
      {
        if( $type(responseObject)!="object" || !responseObject.result || responseObject.result=="failure" )
        {
          alert('ERR');
        }
      }
    });
    
    request.send();
  }
  
  function deleteCategory(vidcat_id)
  {
    var categoryRow = $('vidCatRow_' + vidcat_id);
    
    categoryRow.destroy();
    
    // Ajax
    var request = new Request.JSON({
      'method' : 'post',
      'url' : 'admin_vid.php',
      'data' : {
        'task' : 'deletevidcat',
        'vidcat_id' : vidcat_id
      },
      'onComplete':function(responseObject)
      {
        if( $type(responseObject)!="object" || !responseObject.result || responseObject.result=="failure" )
        {
          alert('ERR');
        }
      }
    });
    
    request.send();
  }
  // -->
  </script>
  {/literal}

<table cellpadding='0' cellspacing='0' width='600'>
<tr>
<td class='header'>{lang_print id=192}</td>
</tr>
<tr>
<td class='setting1'>
  {lang_print id=13500009}
</td>
</tr>
<tr>
<td class='setting2'>
  <table cellpadding='2' cellspacing='0'>
  <tr>
  <td><input type='radio' name='setting_permission_vid' id='permission_vid_0' value='0'{if $vid_settings.permission == 0} checked='checked'{/if}></td>
  <td><label for='permission_vid_0'>{lang_print id=13500011}</label></td>
  </tr>
  <tr>
  <td><input type='radio' name='setting_permission_vid' id='permission_vid_1' value='1'{if $vid_settings.permission == 1} checked='checked'{/if}></td>
  <td><label for='permission_vid_1'>{lang_print id=13500010}</label></td>
  </tr>
  </table>
</td>
</tr>
<tr><td class='setting1'>
  {lang_print id=13500162}
  </td></tr>
  <tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    {section name=prov loop=$providers[0]}
    <tr><td><input type='checkbox' name='{$providers[2][prov]}' value='1' {if strstr($vid_settings.disable, $providers[1][prov])}CHECKED{/if}>&nbsp;{$providers[0][prov]}</td></tr>
    {/section}
    </table>
</td></tr>
</table>

<br>

<table cellpadding='0' cellspacing='0' width='600'>
<tr>
<td class='header'>{lang_print id=13500018}</td>
</tr>
<tr>
<td class='setting1'>
  {lang_print id=13500019}
</td>
</tr>
<tr>
<td class='setting2'>
  <input type='text' class='text' name='setting_vid_ffmpeg_path' id='setting_vid_ffmpeg_path' value='{$vid_settings.ffmpeg}' maxlength='255' size='60'>
</td>
</tr>
<tr>
<td class='setting1'>
  {lang_print id=13500036}
</td>
</tr>
<tr>
<td class='setting2'>
  <input type='text' class='text' name='setting_vid_flvtool2_path' id='setting_vid_flvtool2_path' value='{$vid_settings.flvtool2}' maxlength='255' size='60'>
</td>
</tr>
</table>

<br>

<table cellpadding='0' cellspacing='0' width='600'>
<tr>
<td class='header'>{lang_print id=13500096}</td>
</tr>
<tr>
<td class='setting1'>
{lang_print id=13500098}
</td>
</tr>
<tr>
<td class='setting2'>
<textarea name='setting_vid_mimes' id='setting_vid_mimes' rows='3' cols='40' class='text' style='width: 100%;'>{$vid_settings.mimes}</textarea>
</td>
</tr>
<tr>
<td class='setting1'>
{lang_print id=13500099}
</td>
</tr>
<tr>
<td class='setting2'>
<textarea name='setting_vid_exts' id='setting_vid_exts' rows='3' cols='40' class='text' style='width: 100%;'>{$vid_settings.exts}</textarea>
</td>
</tr>
</table>

<br>

<table cellpadding='0' cellspacing='0' width='600'>
<tr>
<td class='header'>{lang_print id=13500029}</td>
</tr>
<tr>
<td class='setting1'>
{lang_print id=13500030}
</td>
</tr>
<tr>
<td class='setting2'>
{lang_print id=13500031}: <input type='text' class='text' name='vid_width' value='{$vid_settings.width}' maxlength='4' size='5'>px &nbsp; {lang_print id=13500032}: <input type='text' class='text' name='vid_height' value='{$vid_settings.height}' maxlength='4' size='5'>px 
</td>
</tr>
<tr>
<td class='setting1'>
{lang_print id=13500033}
</td>
</tr>
<tr>
<td class='setting2'>
{lang_print id=13500031}: <input type='text' class='text' name='vid_thumb_width' value='{$vid_settings.thumb_width}' maxlength='4' size='5'>px &nbsp; {lang_print id=13500032}: <input type='text' class='text' name='vid_thumb_height' value='{$vid_settings.thumb_height}' maxlength='4' size='5'>px 
</td>
</tr>
</table>

<br>

<table cellpadding='0' cellspacing='0' width='600'>
<tr>
<td class='header'>{lang_print id=13500012}</td>
</tr>
<tr>
<td class='setting1'>
  {lang_print id=13500034}
</td>
</tr>
<tr>
<td class='setting2'>
  <table cellpadding='2' cellspacing='0'>
  <tr>
  <td>
    <select name='setting_vid_skin'>
    <option value='default'>default</option>
    {section name=skin loop=$skins}
    <option value='{$skins[skin]}' {if $vid_settings.skin == $skins[skin]}selected{/if}>{$skins[skin]}</option>
    {/section}
    </select>
  </td>
  </tr>
</table>
</td>
</tr>
<tr>
<td class='setting1'>
  {lang_print id=13500177}
</td>
</tr>
<tr>
<td class='setting2'>
  <table cellpadding='2' cellspacing='0'>
  <tr>
  <td><input type='radio' name='setting_vid_embed' id='setting_vid_embed_0' value='0'{if $vid_settings.embed == 0} checked='checked'{/if}></td>
  <td><label for='setting_vid_embed_0'>{lang_print id=13500178}</label></td>
  </tr>
  <tr>
  <td><input type='radio' name='setting_vid_embed' id='setting_vid_embed_1' value='1'{if $vid_settings.embed == 1} checked='checked'{/if}></td>
  <td><label for='setting_vid_embed_1'>{lang_print id=13500179}</label></td>
  </tr>
  </table>
</td>
</tr>
<tr id="embed_0">
<td class='setting1' width='600'>
  {lang_print id=13500155}
</td>
</tr>
<tr id="embed_1">
<td class='setting2' width='600'>
  <table cellpadding='2' cellspacing='0'>
  <tr>
  <td><input type='radio' name='setting_yt_streaming' id='setting_yt_streaming_0' value='0'{if $vid_settings.yt == 0} checked='checked'{/if}></td>
  <td><label for='setting_yt_streaming_0'>{lang_print id=13500153}</label></td>
  </tr>
  <tr>
  <td><input type='radio' name='setting_yt_streaming' id='setting_yt_streaming_1' value='1'{if $vid_settings.yt == 1} checked='checked'{/if}></td>
  <td><label for='setting_yt_streaming_1'>{lang_print id=13500154}</label></td>
  </tr>
  </table>
</td>
</tr>
</div>
</table>

<br>

<table cellpadding='0' cellspacing='0' width='600'>
<tr>
<td class='header'>{lang_print id=13500156}</td>
</tr>
<tr>
<td class='setting1'>
  {lang_print id=13500159}
</td>
</tr>
<tr>
<td class='setting2'>
  <table cellpadding='2' cellspacing='0'>
  <tr>
  <td><input type='radio' name='vid_logo' id='vid_logo_1' value='1'{if $vid_settings.logo == 1} checked='checked'{/if}></td>
  <td><label for='vid_logo_1'>{lang_print id=13500157}</label></td>
  </tr>
  <tr>
  <td><input type='radio' name='vid_logo' id='vid_logo_0' value='0'{if $vid_settings.logo == 0} checked='checked'{/if}></td>
  <td><label for='vid_logo_0'>{lang_print id=13500158}</label></td>
  </tr>
  </table>
</td>
</tr>
</table>

<br>

<table cellpadding='0' cellspacing='0' width='600'>
  <tr>
    <td class='header'>Video Categories</td>
  </tr>
  <tr>
    <td class='setting1'>If you want to allow your users to categorize their videos, create the categories below. If you have no categories, your users will not be given the option of assigning a video category.</td>
  </tr>
  <tr>
    <td class='setting2'>
      <table cellpadding='2' cellspacing='0'>
        <tbody>
          <tr>
            <td><b>Categories:</b></td>
          </tr>
          {section name=vidcats_loop loop=$vidcats}
          <tr id="vidCatRow_{$vidcats[vidcats_loop].vidcat_id}">
            <td>
              <span class="oldCategoryContainer"><a href="javascript:void(0);" onclick="switchOldCategory({$vidcats[vidcats_loop].vidcat_id});">{$vidcats[vidcats_loop].vidcat_title}</a></span>
              <span class="oldCategoryInput" style="display:none;"><input type='text' class='text' size='30' maxlength='50' onblur="editOldCategory({$vidcats[vidcats_loop].vidcat_id});" value="{$vidcats[vidcats_loop].vidcat_title}" /></span>
              <span class="oldCategoryLangVar">&nbsp;(Language Variable #<a href="admin_language_edit.php?language_id=1&phrase_id={$vidcats[vidcats_loop].vidcat_languagevar_id}">{$vidcats[vidcats_loop].vidcat_languagevar_id}</a>)</span>
            </td>
          </tr>
          {/section}
          <tr id="newCategoryRow">

            <td style="padding-top: 5px;">
              <span id="newCategoryContainer" style="display:none;"><input type='text' id='newCategoryInput' class='text' size='30' maxlength='50' onblur="editNewCategory();" /></span>
              <span id="newCategoryLink"><a href="javascript:void(0);" onclick="createNewCategory();">Add Category</a></span>
            </td>
          </tr>
        </tbody>
      </table>
      <input type='hidden' name='num_vidcategories' value='{$num_cats}' />
    </td>
  </tr>
</table>

<br>

<input type='submit' class='button' value='{lang_print id=173}'>
<input type='hidden' name='task' value='dosave'>
</form>


{include file='admin_footer.tpl'}