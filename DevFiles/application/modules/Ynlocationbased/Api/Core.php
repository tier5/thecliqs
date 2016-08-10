<?php

class Ynlocationbased_Api_Core extends Core_Api_Abstract
{
    public function mergeWithCookie($module_name = "", $params = array())
    {
        if (Engine_Api::_()->getDbTable('modules', 'ynlocationbased')->checkModule($module_name)) {
            if (isset($_COOKIE['ynlocationbased_location']) && !isset($params['location']))
                $params['location'] = $_COOKIE['ynlocationbased_location'];
            if (isset($_COOKIE['ynlocationbased_location']) && !isset($params['lat']))
                $params['lat'] = $_COOKIE['ynlocationbased_lat'];
            if (isset($_COOKIE['ynlocationbased_location']) && !isset($params['long']))
                $params['long'] = $_COOKIE['ynlocationbased_long'];
            if (isset($_COOKIE['ynlocationbased_location']) && !isset($params['within']))
                $params['within'] = $_COOKIE['ynlocationbased_radius'];
        }
        return $params;
    }

    public function getLocationBasedSelect($module_name, $table_name, $moreSelect = array())
    {
        $table = Engine_Api::_() -> getDbTable($table_name, $module_name);
        $tableName = $table->info('name');
        $based_locations = Engine_Api::_()->ynlocationbased()->mergeWithCookie($module_name);
        $select = $table -> select();
        if (!empty($based_locations['lat'])
            && !empty($based_locations['long'])
            && !empty($based_locations['within'])
            && is_numeric($based_locations['within'])
        ) {
            $base_lat = $based_locations['lat'];
            $base_lng = $based_locations['long'];
            $target_distance = $based_locations['within'];
            $arrSelect = array_merge(array(
                "$tableName.*",
                "( 3959 * acos( cos( radians('$base_lat')) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('$base_lng') ) + sin( radians('$base_lat') ) * sin( radians( latitude ) ) ) ) AS distance"
            ), $moreSelect);
            $select->from("$tableName", $arrSelect);
            $select->where("latitude <> ''");
            $select->where("longitude <> ''");
            $select->having("distance <= $target_distance");
            $select->order("distance ASC");
        } else {
            $arrSelect = array_merge(array("$tableName.*"), $moreSelect);
            $select->from("$tableName", $arrSelect);
        }
        return $select;
    }
}

?>
