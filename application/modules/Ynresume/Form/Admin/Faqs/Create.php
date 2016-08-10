<?php
class Ynresume_Form_Admin_Faqs_Create extends Engine_Form {

    public function init() {
        $this->setTitle('Create FAQ');
        
        $this->addElement('Text', 'title', array(
            'label' => 'Title',
            'required' => true,
            'filters' => array(
                'StripTags'
            )
        ));
        $this->addElement('Integer', 'order', array(
            'label' => 'Ordering',
            'required' => true,
        ));
        
        $this->addElement('Radio', 'status', array(
            'label' => 'Display this FAQ',
            'multiOptions' => array(
                'hide' => 'No.',
                'show' => 'Yes.'
            ),
            'value' => 'show',
        ));
        
        $allowed_html = 'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr, object , param, iframe';
    
        $editorOptions['plugins'] =  array(
            'table', 'fullscreen', 'media', 'preview', 'paste','code', 'image', 'textcolor'
        );
        $editorOptions['toolbar1'] = array(
              'undo', '|', 'redo', '|', 'removeformat', '|', 'pastetext', '|', 'code', '|', 'media', '|', 
              'image', '|', 'link', '|', 'fullscreen', '|', 'preview'
        );       
        $editorOptions['html'] = 1;
        $editorOptions['bbcode'] = 1;
        $editorOptions['mode'] = 'exact';
        $editorOptions['elements'] = 'answer';
        
        //Description
        $this->addElement('TinyMce', 'answer', array(
          'label' => 'Answer',
          'editorOptions' => $editorOptions,
          'filters' => array(
            new Engine_Filter_Censor(),
            new Engine_Filter_Html(array('AllowedTags'=>$allowed_html)))
        ));
        
        $this->addElement('Button', 'submit_btn', array(
            'type' => 'submit',
            'label' => 'Submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));
        $this->addElement('Cancel', 'cancel', array(
            'link' => true,
            'label' => 'Cancel',
            'prependText' => ' or ',
            'decorators' => array(
                'ViewHelper',
            ),
        ));
        $this->addDisplayGroup(array('submit_btn', 'cancel'), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper',
             ),
        ));
    }
}