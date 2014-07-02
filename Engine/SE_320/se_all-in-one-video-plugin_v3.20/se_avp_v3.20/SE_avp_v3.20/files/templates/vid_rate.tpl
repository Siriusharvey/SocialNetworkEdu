<html>
<head>
<link rel="stylesheet" href="./templates/styles.css" title="stylesheet" type="text/css"> 
{literal}
<script type='text/javascript'>
<!--

preload_full = new Image();
preload_full.src = "./images/icons/rating_star2.png";
preload_partial = new Image();
preload_partial.src = "./images/icons/rating_star2_half.png";
preload_empty = new Image();
preload_empty.src = "./images/icons/rating_star1.png";

function roll_over(rating) {
  for(var x=1; x<={/literal}{$max_rating}{literal}; x++) {
    if(x <= rating) {
      document.images["rate"+x].src = preload_full.src;
    } else {
      document.images["rate"+x].src = preload_empty.src;
    }
  }
}

function roll_out() {
  for(var x=1; x<={/literal}{$max_rating}{literal}; x++) {
    if(x <= {/literal}{$rating_full}{literal}) {

      document.images["rate"+x].src = preload_full.src;
    } else if({/literal}{$rating_partial}{literal} != 0 && x == {/literal}{math equation='x+1' x=$rating_full}{literal}) {
      document.images["rate"+x].src = preload_partial.src;
    } else {
      document.images["rate"+x].src = preload_empty.src;
    }
  }

}


//-->
</script>
{/literal}
</head>
<body style='margin:0; padding:0; background:#FFFFFF;'>

<table cellspacing='0' cellpadding='0' onmouseout='roll_out()'>
<tr>
  <td style='padding-right: 10px;'><font size='3'><b>Rate: </b></font></td>
{section name=full_stars start=0 loop=$rating_full}
  <td>
  {math assign='rating' equation='x+1' x=$smarty.section.full_stars.index}
  {if $rating_allowed != 0}
    <a href='vid_rate.php?task=rate&object_id={$object_id}&rating={$rating}' onmouseover='roll_over({$rating})'><img name='rate{$rating}' src='./images/icons/rating_star2.png' border='0' style='margin: 1px;'></a>
  {else}
    <img name='rate{$rating}' src='./images/icons/rating_star2.png' border='0' style='margin: 1px;'>
  {/if}
  </td>
{/section}

{section name=partial_stars start=0 loop=$rating_partial}
  <td>
  {math assign='rating' equation='x+y+1' x=$smarty.section.partial_stars.index y=$rating_full}
  {if $rating_allowed != 0}
    <a href='vid_rate.php?task=rate&object_id={$object_id}&rating={$rating}' onmouseover='roll_over({$rating})'><img name='rate{$rating}' src='./images/icons/rating_star2_half.png' border='0' style='margin: 1px;'></a>
  {else}
    <img name='rate{$rating}' src='./images/icons/rating_star2_half.png' border='0' style='margin: 1px;'>
  {/if}
  </td>
{/section}

{section name=empty_stars start=0 loop=$rating_empty}
  <td>
  {math assign='rating' equation='x+y+z+1' x=$smarty.section.empty_stars.index y=$rating_full z=$rating_partial}
  {if $rating_allowed != 0}
    <a href='vid_rate.php?task=rate&object_id={$object_id}&rating={$rating}' onmouseover='roll_over({$rating})'><img name='rate{$rating}' src='./images/icons/rating_star1.png' border='0' style='margin: 1px;'></a>
  {else}
    <img name='rate{$rating}' src='./images/icons/rating_star1.png' border='0' style='margin: 1px;'>
  {/if}
  </td>
{/section}
  <td style='padding-left: 10px;'>
  {$rating_total} ratings</div>
  </td>
</tr>
</table>


</body>
</html>