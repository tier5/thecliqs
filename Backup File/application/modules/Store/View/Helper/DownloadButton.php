<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: DownloadButton.php 7244 2011-09-01 01:49:53Z mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Core
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Store_View_Helper_DownloadButton extends Zend_View_Helper_Abstract
{
  public function downloadButton( Store_Model_Orderitem $item )
  {

    /**
     * @var $product Store_Model_Product
     */
    if ( !$item->isDownloadable() )
      return false;


    $html =
      '<div class="store-add-to-cart download">' .
        $this->view->htmlLink(array('route'=>'store_download', 'id'=>$item->getIdentity()), $this->view->translate('STORE_Download'),
                        array(
                          'class' => 'buttonlink',
        )).
      '</div>';

		return $html;
  }
}