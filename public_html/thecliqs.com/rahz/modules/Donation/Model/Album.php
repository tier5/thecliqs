<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Donation
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     adik
 * @date       06.08.12
 * @time       12:47
 */
class Donation_Model_Album extends Core_Model_Item_Collection
{
    protected $_parent_type = 'donation';

    protected $_owner_type = 'donation';

    protected $_children_types = array('donation_photo');

    protected $_collectible_type = 'donation_photo';

    public function getHref($params = array())
    {
        $params = array_merge(array(
            'route' => 'donation_profile',
            'reset' => true,
            'id' => $this->getDonation()->getIdentity(),
            //'album_id' => $this->getIdentity(),
        ), $params);
        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);
        return Zend_Controller_Front::getInstance()->getRouter()
            ->assemble($params, $route, $reset);
    }

    public function getDonation()
    {
        return $this->getOwner();
    }

    public function getAuthorizationItem()
    {
        return $this->getParent('donation');
    }

    protected function _delete()
    {
        // Delete all child posts
        $photoTable = Engine_Api::_()->getItemTable('donation_photo');
        $photoSelect = $photoTable->select()->where('album_id = ?', $this->getIdentity());
        foreach( $photoTable->fetchAll($photoSelect) as $groupPhoto ) {
            $groupPhoto->delete();
        }

        parent::_delete();
    }
}