<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Modules\developer\modules_configuration;

class Db{

    function deletePermissions($moduleId){
        $sql = "delete from `".DB_PREF."user_to_mod` where `module_id` = '".mysql_real_escape_string($moduleId)."'";
        $rs = mysql_query($sql);
        if(!$rs)
        trigger_error($sql);
    }

    function addPermissions($moduleId, $userId){
        $sql = "insert into `".DB_PREF."user_to_mod` set `module_id` = '".mysql_real_escape_string($moduleId)."', `user_id` = '".mysql_real_escape_string($userId)."'";
        $rs = mysql_query($sql);
        if(!$rs)
        trigger_error($sql);
    }

}

