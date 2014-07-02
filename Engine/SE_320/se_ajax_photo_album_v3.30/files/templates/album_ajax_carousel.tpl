{php}header("Content-type:text/html; charset=shift_jis");  {/php}
{section name=thumb_loop loop=$media} 
<div style="border: 1px solid #bbbbbb; padding: 2px; margin-right: 4px; float: left;">
<a href="javascript:nextpic({$album_id},{$media[thumb_loop].media_id},'{$owner_id}','{$profile}');"><img src='{$media[thumb_loop].file_thumb}' width='70' height='70' border='0' /></a>
</div>
{/section}       