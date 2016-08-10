<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
class Ynvideochannel_Form_Channel_addMoreVideos extends Engine_Form
{

    protected $_videos;
    public function setVideos($value)
    {
        $this->_videos = $value;
    }

    public function init() {
        $this->setAttribs(array(
            'class'=>'global_form_popup',
        ));

        $this -> addElement('Dummy', 'videos', array(
            'decorators' => array( array(
                'ViewScript',
                array(
                    'viewScript' => '_channelVideos.tpl',
                    'videos' => $this ->_videos,
                    'itemPerPage' => 6,
                    'class' => 'form element',
                )
            )),
        ));

        if (count($this ->_videos)) {
            $this->addElement('Button', 'submit', array(
                'label' => 'Add',
                'type' => 'submit',
                'ignore' => true,
                'decorators' => array(
                    'ViewHelper',
                ),
            ));

            // Element: cancel
            $this->addElement('Cancel', 'cancel', array(
                'label' => 'cancel',
                'link' => true,
                'prependText' => ' or ',
                'onclick' => 'parent.Smoothbox.close();',
                'decorators' => array(
                    'ViewHelper',
                ),
            ));

            // DisplayGroup: buttons
            $this->addDisplayGroup(array(
                'submit',
                'cancel',
            ), 'buttons', array(
                'decorators' => array(
                    'FormElements',
                    'DivDivDivWrapper'
                ),
            ));
        } else {
            // Element: cancel
            $this->addElement('Cancel', 'cancel', array(
                'label' => 'cancel',
                'button' => true,
                'onclick' => 'parent.Smoothbox.close();',
                'decorators' => array(
                    'ViewHelper',
                ),
            ));

            // DisplayGroup: buttons
            $this->addDisplayGroup(array(
                'cancel',
            ), 'buttons', array(
                'decorators' => array(
                    'FormElements',
                    'DivDivDivWrapper'
                ),
            ));
        }
    }

}
