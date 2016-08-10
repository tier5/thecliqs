<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Global.php 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Headvancedalbum_Form_Admin_Global extends Engine_Form
{
    public function init()
    {
        //headvancedalbum.featured.albums.count
        //headvancedalbum.featured.photos.count

        $settings = Engine_Api::_()->getApi('settings', 'core');

        $this
            ->setTitle('Global Settings')
            ->setDescription('These settings affect all members in your community.');

        $this->addElement('Text', 'featured_albums_count', array(
            'label' => 'Featured Albums Count',
            'description' => 'How many featured albums will be shown on the widget',
            'value' => $settings->getSetting('headvancedalbum.featured.albums.count', 10)
        ));

        $this->addElement('Text', 'featured_photos_count', array(
            'label' => 'Featured Photos Count',
            'description' => 'How many featured photos will be shown on the widget',
            'value' => $settings->getSetting('headvancedalbum.featured.photos.count', 10)
        ));

        $this->addElement('Text', 'popular_albums_count', array(
            'label' => 'Popular Albums Count',
            'description' => 'How many popular albums will be shown on the widget',
            'value' => $settings->getSetting('headvancedalbum.popular.albums.count', 10)
        ));


        // Add submit button
        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true,
        ));
    }
}