<?php
class Ynchat_Plugin_Core {
    public function onRenderLayoutDefault($event) {
        // Arg should be an instance of Zend_View
        $view = $event->getPayload();
        $viewer = Engine_Api::_()->user()->getViewer();
        if( $view instanceof Zend_View && $viewer->getIdentity() ) {
            $sAgent = Engine_Api::_()->ynchat()->getBrowser();
            if(Engine_Api::_()->ynchat()->isMobile()){
                $view->headLink()->prependStylesheet($view->baseUrl() . '/ynchat/static/css/ynchatmobile.css?js_mobile_version=1&v='.time());
            }
            $view->headLink()->prependStylesheet($view->baseUrl() . '/ynchat/css.php?js_mobile_version=1&v='.time()); 
            $view->headScript()->prependFile($view->baseUrl() . '/ynchat/js.php?js_mobile_version=1&v='.time());
        }
    }
    
    public function onRenderLayoutMobileDefault($event) {
        // Arg should be an instance of Zend_View
        $view = $event->getPayload();
        $viewer = Engine_Api::_()->user()->getViewer();
    
        if( $view instanceof Zend_View && $viewer->getIdentity() ) {
            $view->headLink()->prependStylesheet($view->baseUrl() . '/ynchat/static/css/ynchatmobile.css?js_mobile_version=1&v='.time()); 
            $view->headLink()->prependStylesheet($view->baseUrl() . '/ynchat/css.php?js_mobile_version=1&v='.time());
            $view->headScript()->prependFile($view->baseUrl() . '/ynchat/js.php?js_mobile_version=1&v='.time());
        }
    }
}
