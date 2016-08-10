<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ravshanbek
 * Date: 20.07.12
 * Time: 11:34
 * To change this template use File | Settings | File Templates.
 */
class Donation_Model_DbTable_Donations extends Engine_Db_Table
{
  protected $_rowClass = "Donation_Model_Donation";

  public function getDonationsSelect($params = array())
  {
    $select = $this->select();

    $select->where('status <> ?','cancelled');

    if(!empty($params['user_id']) && is_numeric($params['user_id'])){
      $select->where('owner_id = ?',$params['user_id']);
    }

    if(!empty($params['page_id']) && is_numeric($params['page_id'])){
      $select->where('page_id = ?', $params['page_id']);
    }

    if(!empty($params['type'])){
      $select->where('type = ?', $params['type']);
    }

    if(!empty($params['status'])){
      $select->where('status =?', $params['status']);
    }

    if(!empty($params['approved'])){
      $select->where('approved = ?',$params['approved']);
    }

    if(!empty($params['order'])){
      $select->order('modified_date '.$params['order']);
    }

    if(!empty($params['orderBy'])){
      $select->order($params['orderBy']. ' DESC');
    }

    if(!empty($params['search'])){
      $select->where('title LIKE ? OR description LIKE ?', '%' . $params['search'] . '%');
    }

    if(!empty($params['category_id'])){
      $select->where('category_id = ?', $params['category_id']);
    }

    return $select;
  }

  public function getDonationsPaginator($params = array())
  {
    $paginator = Zend_Paginator::factory($this->getDonationsSelect($params));

    if(!empty($params['page'])){
      $paginator->setCurrentPageNumber($params['page']);
    }

    if(!empty($params['ipp'])){
      $paginator->setItemCountPerPage($params['ipp']);
    }

    if( empty($params['ipp']) )
    {
      $page = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('donation_browse_count', 10);
      $paginator->setItemCountPerPage($page);
    }

    return $paginator;
  }

  public function getFundraises($params = array())
  {
    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    return $this->select()
      ->where('parent_id = ?', $params['parent_id'])
      ->where('status = ?', 'active')
      ->order('raised_sum DESC')
      ->limit($settings->getSetting('fundraises.per.page', 10));
  }

  public function getDonationsCount($params = array())
  {

    $select = $this->select();

    $select->from($this->info('name'),new Zend_Db_Expr('COUNT(1) as count'));

    if(!empty($params['user_id']) && is_numeric($params['user_id'])){
      $select->where('owner_id = ?',$params['user_id']);
    }

    if(!empty($params['page_id']) && is_numeric($params['page_id'])){
      $select->where('page_id = ?', $params['page_id']);
    }

    if(!empty($params['type'])){
      $select->where('type = ?', $params['type']);
    }

    if(!empty($params['status'])){
      $select->where('status =?', $params['status']);
    }

    if(!empty($params['approved'])){
      $select->where('approved = ?',$params['approved']);
    }

    $data = $select->query()->fetchAll();
    return (int) $data[0]['count'];
  }

  public function deleteDonations($ids)
  {
    if (empty($ids)){
      return $this;
    }

    foreach ($ids as $donation_id){
      $donation = Engine_Api::_()->getItem('donation', $donation_id);
      if($donation){
        $donation->deleteDonation();
      }
    }

    return $this;
  }

}
