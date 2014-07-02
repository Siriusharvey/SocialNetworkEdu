{if $user->user_exists AND ($user->level_info.level_vid_allow == 1 OR $user->level_info.level_vid_allow == 2 OR $user->level_info.level_vid_allow == 3)}
<div style='float:right;border:1px solid #CCCCCC;background-image: url(./images/vid_bk_01.gif);background-position: top left;'>
<div style='padding:5px;float:left;{if $num_vids != 0 OR $num_favs != 0}border-right:1px solid #CCCCCC;{/if}color:#994800;'><img src="./images/icons/vid_addvid16.gif" border='0' class='icon'><a href='user_vid_add.php{if $user->level_info.level_vid_allow == 2}?task=youtube{/if}'>{lang_print id=13500202}</a></div>
{if $num_vids != 0}<div style='padding:5px;float:left;{if $num_favs != 0}border-right:1px solid #CCCCCC;{/if}color:#994800;'><img src="./images/icons/vid_myvid16.gif" border='0' class='icon'><a href='user_vid.php'>{lang_sprintf id=13500203 1=$num_vids}</a></div>{/if}
{if $num_favs != 0}<div style='padding:5px;float:left;color:#994800;'><img src="./images/icons/vid_favvid16.gif" border='0' class='icon'><a href='user_vid_favs.php'>{lang_sprintf id=13500204 1=$num_favs}</a></div>{/if}
</div>
{/if}