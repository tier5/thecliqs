<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: Categories.php
 * @author     Minh Nguyen
 */
class Ynauction_Model_DbTable_Locations extends Ynauction_Model_DbTable_Nodes {

	protected $_rowClass = 'Ynauction_Model_Location';
	protected $_rootLabel = 'All Locations';
	protected $_relationTableName = 'engine4_ynauction_location_relations';
	protected $_primary = 'location_id';

	/**
	 * new node with supply data will be added append to $node
	 * @param   Ynauction_Model_Node  $node
	 * @throw Exception
	 */
	public function deleteNode(Ynauction_Model_Node $node, $node_id = NULL) {

		$result = $node -> getDescendent(true);
		$db = $this -> getAdapter();
		$result = $this -> getDescendent($node -> getIdentity());
		$sql = 'update engine4_ynauction_products set location_id =  '.$node_id.'  where location_id in (' . implode(',', $result) . ',0)';
		$db -> query($sql);
		parent::deleteNode($node);
	}

}
