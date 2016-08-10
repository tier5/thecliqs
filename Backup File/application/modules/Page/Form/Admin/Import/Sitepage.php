<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Sitepage.php  16.12.11 16:41 ulan t $
 * @author     Ulan T
 */

class Page_Form_Admin_Import_Sitepage extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Import From SitePage');
    $this->setDescription('If you have pages in SitePage plugin, You can import them');
  }
}