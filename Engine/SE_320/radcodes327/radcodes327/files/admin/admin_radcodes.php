<?php

$page = "admin_radcodes";
include "admin_header.php";

$task = rc_toolkit::get_request('task','main');


$rc_plugins = rc_toolkit::remote_check_plugins();

foreach($global_plugins as $temp) {
  $client_plugin_versions[$temp['plugin_type']] = number_format($temp['plugin_version'],2);
}

foreach($rc_plugins as $k=>$v) {
  if (array_key_exists($v['type'], $client_plugin_versions)) {
    $v['installed'] = true;
    $v['clientversion'] = $client_plugin_versions[$v['type']];
    if ($v['version'] > $v['clientversion']) {
      $v['action'] = 'Upgrade Now';
      $v['url'] = "http://www.radcodes.com/plugin/{$v['type']}";
    }
    else
    {
      $v['action'] = 'None Required';
      $v['url'] = "";
    }
    
  }
  else
  {
    $v['clientversion'] = 'None';
    $v['action'] = 'More details';
    $v['url'] = "http://www.radcodes.com/plugin/{$v['type']}";
  }
  
  $rc_plugins[$k] = $v;
}

$smarty->assign('PLUGIN_RADCODES_VERSION', $client_plugin_versions['radcodes']);
$smarty->assign('RADCODES_LIBRARY_VERSION', number_format(RADCODES_LIBRARY_VERSION,2));

$smarty->assign('rc_plugins', $rc_plugins);
include "admin_footer.php";
exit();