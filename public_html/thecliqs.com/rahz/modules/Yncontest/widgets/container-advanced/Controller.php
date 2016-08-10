<?php

class Yncontest_Widget_ContainerAdvancedController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        // Set up element
        $element = $this -> getElement();

        $element -> clearDecorators()
        // ->   addDecorator('Children', array('placement' => 'APPEND'))
        -> addDecorator('Container');

        $this -> view -> widths = explode(';', $this -> _getParam('separate_width', ''));
        $this -> view -> padding= $this -> _getParam('padding_width', '10px');

        // Iterate over children
        $tabs = array();
        $childrenContent = '';
        $this -> view -> elements = $this -> getElement() -> getElements();

    }

}
