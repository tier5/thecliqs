<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
class Ynvideochannel_Widget_RelatedChannelsController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        if (!Engine_Api::_() -> core() -> hasSubject('ynvideochannel_channel')) {
            return $this -> setNoRender();
        }
        $channel = Engine_Api::_() -> core() -> getSubject('ynvideochannel_channel');
        if (!$channel->category_id) {
            return $this->setNoRender();
        }
        $numberOfItems = $this->_getParam('itemCountPerPage', 5);
        $itemTable = Engine_Api::_()->getItemTable($channel->getType());

        // Get other with same tags
        $select = $itemTable->select()
            ->where('category_id <> 0')
            ->where('category_id = ?', $channel->category_id)
            ->where('channel_id <> ?', $channel->getIdentity())
            ->where('search = 1')
            ->limit($numberOfItems)
            ->order(new Zend_Db_Expr(('rand()')));

        $this->view->channels = $channels = $itemTable->fetchAll($select);

        if (count($channels) <= 0) {
            return $this->setNoRender();
        }
    }
}
