<?php

class Yncontest_Widget_FeaturedContestController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$view = Zend_Registry::get('Zend_View');
		$headScript = new Zend_View_Helper_HeadScript();
		$headScript -> appendFile($view -> layout() -> staticBaseUrl . 'application/modules/Yncontest/externals/scripts/slideshow.js');
		$headScript -> appendFile($view -> layout() -> staticBaseUrl . 'application/modules/Yncontest/externals/scripts/slideshow.push.js');
		$headScript -> appendFile($view -> layout() -> staticBaseUrl . 'application/modules/Yncontest/externals/scripts/slideshow.flash.js');
		$headScript -> appendFile($view -> layout() -> staticBaseUrl . 'application/modules/Yncontest/externals/scripts/slideshow.fold.js');
		$headScript -> appendFile($view -> layout() -> staticBaseUrl . 'application/modules/Yncontest/externals/scripts/slideshow.kenburns.js');

		$headLink = new Zend_View_Helper_HeadLink();
		$headLink -> appendStylesheet($view -> layout() -> staticBaseUrl . 'application/modules/Yncontest/externals/styles/slideshow.css');

		// Process form
		$limit = (int)$this -> _getParam('number', 5);
		$this -> view -> height = $height = (int)$this -> _getParam('height', 300);
		$slideshowtype = $this -> _getParam('slideshowtype', 'featured');
		$this -> view -> slider_action = $this -> _getParam('slider_action', 'overlap');

		$params = array(
			"contest_status" => "published",
			'activated' => 1,
			"approve_status" => "approved",
			"limit" => $limit,
			"$slideshowtype" . "_id" => 1,
			"rand" => 1
		);

		$this -> view -> items = $paginator = Engine_Api::_() -> yncontest() -> getContestPaginator($params);

		if (defined('YNRESPONSIVE'))
		{
			if (Engine_Api::_() -> ynresponsive1() -> isMobile())
			{
				$this -> view -> html_ynresponsive_slideshow = $this -> view -> partial('_responsive_slideshow.tpl', 'yncontest', array(
					'items' => $paginator,
					'height' => $height,
					'slider_id' => '_' . uniqid()
				));
			}
		}

		if ($paginator -> getTotalItemCount() == 0)
		{
			$this -> setNoRender();
		}
	}

}
