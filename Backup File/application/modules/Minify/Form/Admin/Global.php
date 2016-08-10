<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Global.php 7244 2010-09-01 01:49:53Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Minify_Form_Admin_Global extends Engine_Form {

    public function init() {
		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $servername = $_SERVER['SERVER_NAME'];
        $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $url = $protocol.$servername . $baseUrl . '/members/home';

        $this->setTitle('Global Settings');
        $description = $this->getTranslator()->translate('MINIFY_FORM_ADMIN_GLOBAL_DESCRIPTION');
        $description = vsprintf($description, array(
            'http://gtmetrix.com?url='.$url,
            'https://developers.google.com/speed/pagespeed/insights/?url='.$url
                ));
                
        $this->setDescription($description);
        
        $this->loadDefaultDecorators();
        $this->getDecorator('Description')->setOption('escape', false);
        
       
        //add css radio
        $this->addElement('radio', 'minify_mincss', array(
            'label'=>'Combine CSS',
            'required' => true,
            'multiOptions' => array(
                '1' => 'Yes ',
                '0' => 'No'
            ),
            'value'=>Engine_Api::_()->getApi('settings','core')->getSetting('minify.mincss.enable',1),
        ));

        // add js radio
        $this->addElement('radio', 'minify_minjs', array(
            'label'=>'Combine JS request',
            'required' => true,
            'multiOptions' => array(
                '1' => 'Yes ',
                '0' => 'No'
            ),
            'value'=>Engine_Api::_()->getApi('settings','core')->getSetting('minify.minjs.enable',1),
        ));
        /*
        // add number file radio
        $this->addElement('Text', 'minify_maxcombinedjs', array(
            'label' => 'Maximum file',
            'description'=>'Maximum file to combine per request(2 - 100), default value: 9',
            'required' => true,
            'value'=>Engine_Api::_()->getApi('settings','core')->getSetting('minify.maxcombinedjs.enable',9),
            'validators'=>array('Int',array('Between',false,array(2,100))),
        ));
        */
        // add reorder radio
        $this->addElement('radio', 'minify_reorderjs', array(
            'label'=>'Reoder js loading',
            'description' => 'Put inline javascript after externals javascript to reduce number of request',
            'required' => true,
            'multiOptions' => array(
                '1' => 'Yes ',
                '0' => 'No'
            ),
            'value'=>Engine_Api::_()->getApi('settings','core')->getSetting('minify.reorderjs',0),
        ));


        // Add submit button
        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}