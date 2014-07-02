<?php


/******************  CLASS semods_actionshook  ******************/


class semods_actionshook extends se_actions {


  // actions_add hook
  function actions_add($user, $actiontype_name, $replace = Array(), $action_media = Array(), $timeframe = 0, $replace_media = false, $action_object_owner = "", $action_object_owner_id = 0, $action_object_privacy = 0) {

    // CALL OUR HOOK
    ($hook = SE_Hook::exists('semods_action')) ? SE_Hook::call($hook, array($user, $actiontype_name, $replace, $action_media, $timeframe, $replace_media, $action_object_owner, $action_object_owner_id, $action_object_privacy) ) : NULL;

    // CALL OUR HOOK
    ($hook = SE_Hook::exists('semods_action_'.$actiontype_name)) ? SE_Hook::call($hook, array($user, $actiontype_name, $replace, $action_media, $timeframe, $replace_media, $action_object_owner, $action_object_owner_id, $action_object_privacy) ) : NULL;


    /* CALL PARENTS */
    return parent::actions_add($user, $actiontype_name, $replace, $action_media, $timeframe, $replace_media, $action_object_owner, $action_object_owner_id, $action_object_privacy);

  }



/*
  // good only on paper

  // actions_add hook
  function actions_add() {

    $func_args = func_get_args();

    // CALL OUR HOOK
    ($hook = SE_Hook::exists('semods_actions_add')) ? SE_Hook::call($hook, $func_args ) : NULL;

    // CALL OUR HOOK
    ($hook = SE_Hook::exists('semods_actions_add'.$actiontype_name)) ? SE_Hook::call($hook, $func_args ) : NULL;

    /* CALL PARENTS * /
    return parent::actions_add( $args = $func_args );

  }
*/

}

?>