{include file='header.tpl'}



<table cellpadding='0' cellspacing='0' width='100%'>
  <tr>
    <td valign='top'>
      
      <img src='./images/icons/gstore_gstore48.gif' border='0' class='icon_big'>
      <div class='page_header'>{lang_print id=5555103}</div>
      <div>{lang_print id=5555104}</div>
      
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


{* SHOW JUST ADDED MESSAGE *}
{if $justadded == 1}
  <div id='gstore_result'>
    <table cellpadding='0' cellspacing='0'>
      <tr>
        <td class='success'><img src='./images/success.gif' border='0' class='icon' />{lang_print id=5555105}</td>
      </tr>
    </table>
    <br />
    
    <table cellpadding='0' cellspacing='0'>
      <tr>
        <td>
          {lang_block id=5555106 var=langBlockTemp}
          <input type='button' class='button' value='{$langBlockTemp}' onClick="$('gstore_result').style.display='none';$('gstore_pagecontent').style.display='block';" />
          &nbsp;
          {/lang_block}
        </td>
        <td>
          {lang_block id=5555107 var=langBlockTemp}
          <form action='user_gstore.php' method='get'>
          <input type='submit' class='button' value='{$langBlockTemp}' />
          </form>
          {/lang_block}
        </td>
      </tr>
    </table>
  </div>
{/if}


<div id='gstore_pagecontent' style='{if $justadded == 1}display: none;{/if}'>

  {if $user->level_info.level_gstore_photo}
  <form action='user_gstore_media.php' method='post' enctype='multipart/form-data'>
  <table cellpadding='0' cellspacing='0' width='100%'>
    <tr>
      <td class='header'>{lang_print id=5555108}</td>
    </tr>
    <tr>
      <td class='gstore_box'>
        <table cellpadding='0' cellspacing='0'>
          <tr>
            <td valign='top'>
              <img src='{$gstore->gstore_photo("./images/nophoto.gif", TRUE)}' width='{$misc->photo_size($gstore->gstore_photo("./images/nophoto.gif"),"140","140","w")}' />
            </td>
            <td style='padding-left: 10px;' valign='top'>
              <div>{lang_print id=5555109}</div>
              <input type='file' name='photo' class='text' size='40' />
              {lang_block id=5555113 var=langBlockTemp}<input type='submit' class='button' value='{$langBlockTemp}' />{/lang_block}
              <input type='hidden' name='task' value='upload' />
              <input type='hidden' name='MAX_FILE_SIZE' value='5000000' />
              <input type='hidden' name='gstore_id' value='{$gstore_id}' />
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  </form>
  <br />
  {/if}
  
  
  {literal}
  <script type='text/javascript'>
  <!--
  function doUpload(spot)
  {
    $('uploadform'+spot).style.display='none';
    $('uploadform_uploading'+spot).style.display='block';
  }

  function uploadComplete(result_code, response, spot, gstoremedia_id)
  {
    if(result_code == 1)
    {
      alert(response);
      $('uploadform'+spot).style.display='block';
      $('uploadform_uploading'+spot).style.display='none';
    }
    else
    {
      $('uploadform_uploading'+spot).style.display='none';
      $('uploadform_uploaded'+spot).style.display='block';
      uploadform_newmedia = document.getElementById('uploadform_newmedia'+spot);
      uploadform_newmedia.src = response;
      var nextspot = parseInt(spot);
      nextspot = nextspot + 1;
      $('uploadbox'+nextspot).style.display='block';
      var deletelink = document.getElementById('deletelink'+spot);
      deletelink.innerHTML = "[ <a href='javascript:void(0)' onClick=\"deletePhoto2('"+gstoremedia_id+"', '"+spot+"')\">{/literal}{lang_print id=5555110}{literal}</a> ]";
    }
  }

  function deletePhoto(gstoremedia_id, spot)
  {
    document.getElementById('photo'+spot).style.display = "none";
    document.getElementById('photo'+spot+'_deleting').style.display = "block";
    var divname = 'photo' + spot + '_deleting';
    var uploadframe = document.getElementById('uploadframe'+spot);
    uploadframe.src = 'user_gstore_media.php?task=deletemedia&gstore_id={/literal}{$gstore_id}{literal}&gstoremedia_id='+gstoremedia_id;
    setTimeout("$('"+divname+"').style.display='none';", 1500);
  }

  function deletePhoto2(gstoremedia_id, spot)
  {
    document.getElementById('uploadbox'+spot).style.display = "none";
    document.getElementById('photo'+spot+'_deleting').style.display = "block";
    var divname = 'photo' + spot + '_deleting';
    var uploadframe = document.getElementById('uploadframe'+spot);
    uploadframe.src = 'user_gstore_media.php?task=deletemedia&gstore_id={/literal}{$gstore_id}{literal}&gstoremedia_id='+gstoremedia_id;
    setTimeout("$('"+divname+"').style.display='none';", 1500);
  }

  //-->
  </script>
  {/literal}


  <table cellpadding='0' cellspacing='0' width='100%'>
    <tr>
      <td class='header'>{lang_print id=5555114}</td>
    </tr>
    <tr>
      <td class='gstore_box'>
        
        {* SHOW FILES IN THIS ALBUM *}
        {section name=file_loop loop=$files}
          
          {* IF IMAGE, GET THUMBNAIL *}
          {if $files[file_loop].gstoremedia_ext == "jpeg" OR $files[file_loop].gstoremedia_ext == "jpg" OR $files[file_loop].gstoremedia_ext == "gif" OR $files[file_loop].gstoremedia_ext == "png" OR $files[file_loop].gstoremedia_ext == "bmp"}
            {assign var='file_dir' value=$gstore->gstore_dir($gstore->gstore_info.gstore_id)}
            {assign var='file_src' value="`$file_dir``$files[file_loop].gstoremedia_id`.jpg"}
          {* SET THUMB PATH FOR AUDIO *}
          {elseif $files[file_loop].gstoremedia_ext == "mp3" OR $files[file_loop].gstoremedia_ext == "mp4" OR $files[file_loop].gstoremedia_ext == "wav"}
            {assign var='file_src' value='./images/icons/audio_big.gif'}
          {* SET THUMB PATH FOR VIDEO *}
          {elseif $files[file_loop].gstoremedia_ext == "mpeg" OR $files[file_loop].gstoremedia_ext == "mpg" OR $files[file_loop].gstoremedia_ext == "mpa" OR $files[file_loop].gstoremedia_ext == "avi" OR $files[file_loop].gstoremedia_ext == "swf" OR $files[file_loop].gstoremedia_ext == "mov" OR $files[file_loop].gstoremedia_ext == "ram" OR $files[file_loop].gstoremedia_ext == "rm"}
            {assign var='file_src' value='./images/icons/video_big.gif'}
          {* SET THUMB PATH FOR UNKNOWN *}
          {else}
            {assign var='file_src' value='./images/icons/file_big.gif'}
          {/if}
          
          {* SHOW MEDIA *}
          <div id='photo{$smarty.section.file_loop.iteration}' style='margin: 30px; text-align: left;'>
            <div class='album_thumb2' style='width: 300px;'>
              <img src='{$file_src}' border='0' width='{$misc->photo_size($file_src,"300","250","w")}' class='photo' />
            </div>
            <div style='margin-top: 5px; font-weight: bold;'>
              [ <a href='javascript:void(0)' onClick="deletePhoto('{$files[file_loop].gstoremedia_id}', '{$smarty.section.file_loop.iteration}')">{lang_print id=5555110}</a> ]
            </div>
          </div>
          <div id='photo{$smarty.section.file_loop.iteration}_deleting' style='margin: 30px; width: 300px; min-height: 260px; display: none; text-align: left;'>
            <div class='album_thumb2' style='border: 1px solid #DDDDDD;'>
              <div style='margin-top: 90px; font-weight: bold; text-align: center;'>
                {lang_print id=5555111}
                <br />
                <img src='./images/icons/gstore_working.gif' border='0' />
              </div>
            </div>
          </div>
          <iframe id='uploadframe{$smarty.section.file_loop.iteration}' name='uploadframe{$spot}' style='display: none;' frameborder='no' src='about:blank'></iframe>
          
        {/section}
        
        {assign var='totalspots' value=11}
        {assign var='formstoshow' value=$totalspots-$smarty.section.file_loop.iteration}
        {if $smarty.section.file_loop.iteration > 0}
          {assign var='media_shown_already' value=$smarty.section.file_loop.iteration-1}
        {else}
          {assign var='media_shown_already' value=0}
        {/if}
        
        {* PREPARE UPLOAD SLOTS *}
        {section name='form_loop' loop=$formstoshow}
          
          {assign var='spot' value=$smarty.section.form_loop.iteration+$media_shown_already}
          <div id='uploadbox{$spot}' style='margin: 30px; text-align: left; {if $smarty.section.form_loop.first != true} display: none;{/if}'>
            <div id='uploadform{$spot}' class='gstore_uploadform' style='width: 300px; min-height: 260px;'>
              <form action='user_gstore_media.php' method='post' target='uploadframe{$spot}' enctype='multipart/form-data' onSubmit="doUpload('{$spot}')">
              <div style='margin-top: 50px;'>{lang_print id=5555112}</div>
              <br />
              
              <input type='file' name='file' name='photo' class='text' />
              <br />
              <br />
              
              {lang_block id=5555113 var=langBlockTemp}<input type='submit' class='button' value='{$langBlockTemp}' />{/lang_block}
              <input type='hidden' name='task' value='uploadmedia' />
              <input type='hidden' name='MAX_FILE_SIZE' value='5000000' />
              <input type='hidden' name='gstore_id' value='{$gstore_id}' />
              <input type='hidden' name='spot' value='{$spot}' />
              </form>
            </div>
            <div id='uploadform_uploading{$spot}' class='gstore_uploadform_uploading' style='width: 300px; height: 260px; display: none;'>
              <img src='./images/icons/gstore_working.gif' border='0' style='margin-top: 90px;' />
            </div>
            <div id='uploadform_uploaded{$spot}' style='display: none;'>
              <div>
                <img src='./trans.gif' id='uploadform_newmedia{$spot}' border='0' class='photo' width='300' />
              </div>
              <div style='margin-top: 5px; font-weight: bold;'>
                <span id='deletelink{$spot}'></span>&nbsp;
              </div>
            </div>
          </div>
          <div id='photo{$spot}_deleting' style='display: none; margin: 30px; text-align: left;'>
            <div class='album_thumb2' style='width: 300px; min-height: 260px; border: 1px solid #DDDDDD;'>
              <div style='margin-top: 90px; font-weight: bold; text-align: center;'>
                {lang_print id=5555111}
                <br />
                <img src='./images/icons/gstore_working.gif' border='0' />
              </div>
            </div>
          </div>
          <iframe id='uploadframe{$spot}' name='uploadframe{$spot}' style='display: none;' frameborder='no' src='about:blank'></iframe>
          
        {/section}
        
      </td>
    </tr>
  </table>
  <br />
  
  
  <form action='user_gstore.php' method='get'>
    {lang_block id=5555102 var=langBlockTemp}<input type='submit' class='button' value='{$langBlockTemp}' />{/lang_block}
  </form>

</div>

{include file='footer.tpl'}