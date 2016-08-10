<?php
class Yncredit_Plugin_Menus
{
  public function onMenuInitialize_CoreMainYncredit()
  {
  	if( !Engine_Api::_()->authorization()->isAllowed('yncredit', null, 'use_credit') )
    {
      return false;
    }
	return true;
  }
  public function onMenuInitialize_YncreditMainGeneral()
  {
  	if( !Engine_Api::_()->authorization()->isAllowed('yncredit', null, 'use_credit') )
    {
      return false;
    }
    if( !Engine_Api::_()->authorization()->isAllowed('yncredit', null, 'general_info') )
    {
      return false;
    }
    return true;
  }
  public function onMenuInitialize_YncreditMainMy()
  {
  	if( !Engine_Api::_()->authorization()->isAllowed('yncredit', null, 'use_credit') )
    {
      return false;
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer->getIdentity() )
    {
      return false;
    }
    return true;
  }
  public function onMenuInitialize_YncreditMainFaq()
  {
  	if( !Engine_Api::_()->authorization()->isAllowed('yncredit', null, 'use_credit') )
    {
      return false;
    }
    if( !Engine_Api::_()->authorization()->isAllowed('yncredit', null, 'faq') )
    {
      return false;
    }
    return true;
  }
  public function onMenuInitialize_UserProfileYncredit()
  {
  	if( !Engine_Api::_()->authorization()->isAllowed('yncredit', null, 'use_credit') )
    {
      return false;
    }
  	$viewer = Engine_Api::_() -> user() -> getViewer();
	if( !$viewer->getIdentity() )
    {
      return false;
    }
	$subject = Engine_Api::_() -> core() -> getSubject();
	$subjectRow = $subject->membership()->getRow($viewer);
	if(!$subjectRow)
	{
		 return false;
	}
	$balance = Engine_Api::_()->getItem('yncredit_balance', $viewer -> getIdentity());
    if( !Engine_Api::_()->authorization()->isAllowed('yncredit', $subject, 'receive')
	|| !Engine_Api::_()->authorization()->isAllowed('yncredit', $viewer, 'send')
	|| !$balance
	|| $viewer -> getIdentity() == $subject -> getIdentity()
	|| !$subjectRow -> active)
    {
      return false;
    }
    return array(
		'label' => "Send Credits",
		'icon' => 'application/modules/Yncredit/externals/images/profile-icon-credit.png',
		'route' => 'yncredit_general',
		'class' => 'smoothbox',
		'params' => array(
			'action' => 'profile-send-credit',
			'user_id' => $subject -> getIdentity(),
			'format' => 'smoothbox',
		)
	);
  }
}