<?php

/**
 * SocialEngine
 *
 * @package    Donation
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2012-07-25 15:57:57 adilet $
 * @author     Adilet
 */

class Donation_Widget_DonationTitleController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        if (!Engine_Api::_()->core()->hasSubject()) return;
        $this->view->donation = $donation = Engine_Api::_()->core()->getSubject();

    }
}