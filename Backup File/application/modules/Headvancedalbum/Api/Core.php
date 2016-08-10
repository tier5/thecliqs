<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Invite
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Core.php 9747 2012-07-26 02:08:08Z john $
 * @author     Steve
 */

/**
 * @category   Application_Extensions
 * @package    Invite
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Headvancedalbum_Api_Core extends Core_Api_Abstract
{

    public function sort_by_ids($params = array(), $ids = array())
    {
        $ids = array_flip($ids);
        $result = array();

        foreach($params as $param) {
            $index = $ids[$param['photo_id']];
            $result[$index] = $param;
        }
        ksort($result);
        return $result;
    }
}