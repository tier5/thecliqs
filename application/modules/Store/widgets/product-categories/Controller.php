<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Widget_ProductCategoriesController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps('store_product');
    $optionsData = Engine_Api::_()->getApi('core', 'fields')->getFieldsOptions('store_product');

    $topLevelMaps = $mapData->getRowsMatching(array('field_id' => 0, 'option_id' => 0));
  	$topLevelFields = array();
    foreach( $topLevelMaps as $map ) {
      $field = $map->getChild();
      $topLevelFields[$field->field_id] = $field;
    }

    $topLevelField = array_shift($topLevelFields);

    foreach( $optionsData->getRowsMatching('field_id', $topLevelField->field_id) as $option ) {
      $topLevelOptions[$option->option_id]['label'] = $option->label;

			$secondLevelMaps = $mapData->getRowsMatching(array('field_id' => 1, 'option_id' => $option->option_id));
			$secondLevelFields = array();
			foreach( $secondLevelMaps as $map ) {
				$field = $map->getChild();
				$secondLevelFields[$field->field_id] = $field;
			}

			$secondLevelField = array_shift($secondLevelFields);
	    $field_id = ($secondLevelField !== null) ? $secondLevelField->field_id : 0;
			foreach( $optionsData->getRowsMatching('field_id', $field_id) as $op ) {
				$topLevelOptions[$option->option_id]['children'][$op->option_id] = $op->label;
			}
    }

		$this->view->categories = $topLevelOptions;
  }
}