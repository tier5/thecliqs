<?php
/**
 * SocialEngine
 *
 * @category   Application_Themes
 * @package    Template
 * @copyright  Copyright YouNet Company
 */
 
class Ynresponsiveclean_Widget_JoinNowController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {	
	if(YNRESPONSIVE_ACTIVE != 'ynresponsive1' && 'ynresponsiveclean' != substr(YNRESPONSIVE_ACTIVE, 0, 17))
	{
		return $this -> setNoRender(true);
	}
  }

  public function getCacheKey()
  {
  }
}