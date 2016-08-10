<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: user auctons
 * @author     Minh Nguyen
 */
class Ynauction_Widget_UserYnauctionsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
  if($this->_getParam('max') != ''){       
            $limit = $this->_getParam('max');
            if ($limit <=0)
            {
                $limit = 5;
            }
        }else{
        $limit = 5; }
  $table = Engine_Api::_()->getDbtable('products', 'ynauction');
       $Name = $table->info('name');
       $select = $table->select()->from($Name,array("$Name.*","Count($Name.user_id) as count"))
       ->where("$Name.is_delete = 0")
        ->group("$Name.user_id")
       ->order("Count($Name.user_id) DESC")->limit($limit);
      $this->view->auctions =  $table->fetchAll($select);
      if(count($this->view->auctions) <= 0)
        $this->setNoRender();
 }
}
?>