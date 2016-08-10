<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Spectacular
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2015-06-04 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Spectacular_Plugin_Core extends Zend_Controller_Plugin_Abstract {

    public function onRenderLayoutDefault($event) {

        $view = $event->getPayload();
        $view->headTranslate(array("Forgot Password?", "Login with Twitter", "Login with Facebook", "Mark as Read", "Mark as Unread"));
        $view->headLink()->appendStylesheet('https://fonts.googleapis.com/css?family=Roboto')
                ->appendStylesheet('https://fonts.googleapis.com/css?family=Source+Sans+Pro');
        $view->headLink()->appendStylesheet($view->layout()->staticBaseUrl . 'application/modules/Spectacular/externals/styles/style.css');

        $includeThemeBasedClass = <<<EOF
        en4.core.runonce.add(function(){
        window.addEvent('domready', function() {
                setTimeout(function () {
                if(($$('.layout_siteusercoverphoto_user_cover_photo').length > 0) || ($$('.layout_sitecontentcoverphoto_content_cover_photo').length > 0) || ($$('.layout_spectacular_banner_images').length > 0)) {
                   if ($('global_content')) {
                      // $('global_content').setStyles({
														//'width' : '100%',
														//'margin-top' : '-16px'
												//});
                   }
									 if ($$('.layout_main')) {
                       $$('.layout_main').setStyles({
														'width' : '1200px',
														'margin' : '0 auto'
												});
                   }
                }   
                
//                 if($('global_wrapper') && $('global_wrapper').getElementById('global_content').getElement('.layout_seaocore_scroll_top')) {
//                $('global_wrapper').getElementById('global_content').getElement('.layout_seaocore_scroll_top').setStyle('display', 'none');
//            }
//               
                
                
                }, 100);
          });      
        });
EOF;
        $view->headScript()->appendScript($includeThemeBasedClass);
    }

    public function onRenderLayoutDefaultSimple($event) {
        // Forward
        return $this->onRenderLayoutDefault($event, 'simple');
    }

}
