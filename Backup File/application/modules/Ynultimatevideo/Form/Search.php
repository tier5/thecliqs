<?php
class Ynultimatevideo_Form_Search extends Fields_Form_Search {
    public function init() {
        $this->setAttribs(array(
            'class' => 'global_form_box',
            'id' => 'filter_form'))
            ->setMethod('GET');

        $this->addElement('Text', 'keyword', array(
            'label' => 'Search Videos'
        ));

        $this -> addElement('Text', 'owner', array(
            'label' => 'Created by'
        ));

        $orderOptions = array(
                'creation_date' => 'Most Recent',
                'most_viewed' => 'Most Viewed',
                'rating' => 'Highest Rated',
                'most_liked' => 'Most Liked',
                'most_commented' => 'Most Commented',
                'featured' => 'Featured'
        );
        $request = Zend_Controller_Front::getInstance()->getRequest();
        if ($request->getControllerName() == 'playlist') {
            unset($orderOptions['rating']);
            unset($orderOptions['featured']);
        }
        $this->addElement('Select', 'order', array(
            'label' => 'Browse by',
            'multiOptions' => $orderOptions,
            'value' => 'all'
        ));

        $this->getCategoryElement();
        parent::init();
        // Buttons

        $subform = new Zend_Form_SubForm(array(
            'order' => 1000000,
            'name' => 'search_button',
            'decorators' => array(
                'FormElements',
            )
        ));

        Engine_Form::enableForm($subform);

        $subform->addElement('Button', 'search', array(
            'label' => 'Search',
            'type' => 'submit',
            'ignore' => true,
        ));

        $this->addElement('Hidden', 'tag', array(
            'order' => 101
        ));

        $this->addSubForm($subform, $subform->getName());
        $this->loadDefaultDecorators();
    }

    public function getCategoryElement() {

        $this->addElement('Select', 'category', array(
            'label' => 'Category',
            'multiOptions' => array(
                'all' => 'All'
            ),
        ));
    }

    public function loadDefaultDecorators()
    {
        if( $this->loadDefaultDecoratorsIsDisabled() )
        {
            return;
        }

        $decorators = $this->getDecorators();
        if( empty($decorators) )
        {
            $this
                ->addDecorator('FormElements')
                ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'form-elements'))
                ->addDecorator('FormMessages', array('placement' => 'PREPEND'))
                ->addDecorator('FormErrors', array('placement' => 'PREPEND'))
                ->addDecorator('Description', array('placement' => 'PREPEND', 'class' => 'form-description'))
                ->addDecorator('FormTitle', array('placement' => 'PREPEND', 'tag' => 'h3'))
                ->addDecorator('FormWrapper', array('tag' => 'div'))
                ->addDecorator('FormContainer', array('tag' => 'div'))
                ->addDecorator('Form')
            ; //->addDecorator($decorator);
        }
    }
}