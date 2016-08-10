<?php

class Transformer_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
  public function __construct($application)
  {
    parent::__construct($application);
    $this->initViewHelperPath();
    
    $themes = Engine_Api::_()->getDbtable('themes', 'core')->fetchAll();
    $activeTheme = $themes->getRowMatching('active', 1);
    
    if(stristr($activeTheme->name, 'transformer')){
        $headStyle = new Zend_View_Helper_HeadLink();        
        $headStyle->prependStylesheet('http://fonts.googleapis.com/css?family=Open+Sans:300,400,700');
    }

  }
}