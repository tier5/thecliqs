<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
class Ynvideochannel_Form_Admin_Channel_Search extends Engine_Form
{
    public function init()
    {
        $this->clearDecorators()
            ->addDecorator('FormElements')
            ->addDecorator('Form')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
            ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'));

        $this->setAttribs(array('id' => 'filter_form', 'class' => 'global_form_box'))->setMethod('get');

        // Search Title
        $this->addElement('Text', 'title', array(
            'label' => 'Title',
        ));

        // Search Owner
        $this->addElement('Text', 'owner', array(
            'label' => 'Owner',
        ));

        // Categories
        $this->addElement('Select', 'category', array(
            'label' => 'Category',
            'multiOptions' => array(
                'all' => 'All'
            ),
        ));

        // Get category list and nest by level
        $categories = Engine_Api::_()->getItemTable('ynvideochannel_category')->getCategories();
        unset($categories[0]);
        if (count($categories) > 0) {
            foreach ($categories as $category) {
                $this->category->addMultiOption($category['option_id'], str_repeat("-- ", $category['level'] - 1) . $category['title']);
            }
        }

        // Feature
        $this->addElement('Select', 'featured', array(
            'label' => 'Featured',
            'multiOptions' => array(
                '' => 'All',
                '1' => 'Only Featured',
                '0' => 'Only Not Featured',
            ),
            'value' => '',
        ));

        // Buttons
        $this->addElement('Button', 'button', array(
            'label' => 'Search',
            'type' => 'submit',
        ));

        $this->button->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
            ->addDecorator('HtmlTag2', array('tag' => 'div'));
    }

}