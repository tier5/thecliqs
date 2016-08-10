<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
class Ynvideochannel_Form_Admin_Global extends Engine_Form
{

    public function init()
    {
        $this->setTitle('Global Settings')->setDescription('These settings affect all members in your community.');

        $this->addElement('Text', 'ynvideochannel_channels', array(
            'label' => 'Number of Channels',
            'description' => 'Number of channels will be shown when clicking on Find Channels? (Enter a number between 1 and 50)',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynvideochannel.channels', 50),
            'validators' => array(
                array('Int', true),
                array('Between', true, array('min' => 1, 'max' => 50))
            )
        ));

        $this->addElement('Text', 'ynvideochannel_grab_videos', array(
            'label' => 'Grabbing Videos',
            'description' => 'Number of videos will be grabbed when adding a channel? (Enter a number between 1 and 50)',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynvideochannel.grab.videos', 50),
            'validators' => array(
                array('Int', true),
                array('Between', true, array('min' => 1, 'max' => 50))
            )
        ));

        $this->addElement('Text', 'ynvideochannel_update_videos', array(
            'label' => 'Automatically get Videos',
            'description' => 'Number of videos will be get automatically when updating a channel? (Enter a number between 1 and 50)',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynvideochannel.update.videos', 50),
            'validators' => array(
                array('Int', true),
                array('Between', true, array('min' => 1, 'max' => 50))
            )
        ));

        $this->addElement('Text', 'ynvideochannel_addthis_pubid', array(
            'label' => 'AddThis - Profile ID',
            'required' => true,
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynvideochannel.addthis.pubid', 'younet'),
        ));

        $description = 'Please fill in the API key for parsing Youtube video.<br>To learn how to create an API key, please read this guide: <a href="https://developers.google.com/youtube/v3/getting-started" target="_blank">https://developers.google.com/youtube/v3/getting-started</a>';

        $this->addElement('Text', 'ynvideochannel_apikey', array(
            'label' => 'YouTube Data API key',
            'description' => $description,
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynvideochannel.apikey', 'AIzaSyDpUPT_nafV_MFSAlc-8AH4e1Gy578iK0M'),
            'required' => true,
            'allowEmpty' => false,
            'filters' => array(
                'StringTrim',
            ),
        ));

        $this->ynvideochannel_apikey->getDecorator('Description')->setOption('escape', false);

        $this->addElement('Radio', 'ynvideochannel_auto_play', array(
            'label' => 'Enable Auto Play?',
            'description' => 'Enable auto playing shared videos?',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynvideochannel.auto.play', 1),
            'multiOptions' => array(
                '1' => 'Yes, enable auto playing',
                '0' => 'No, do not enable auto playing',
            ),
        ));

        $this->addElement('Radio', 'ynvideochannel_full_screen', array(
            'label' => 'Enable Full Screen?',
            'description' => 'Enable "Full Screen" feature for video?',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynvideochannel.full.screen', 1),
            'multiOptions' => array(
                '1' => 'Yes, enable Full Screen feature',
                '0' => 'No, do not enable Full Screen feature',
            ),
        ));

        $this->addElement('Radio', 'ynvideochannel_related_videos', array(
            'label' => 'Youtube Related Videos',
            'description' => 'Enable this feature to display related videos at the end of each video?',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynvideochannel.related.videos', 1),
            'multiOptions' => array(
                '1' => 'Yes, enable Youtube Related Videos feature',
                '0' => 'No, do not enable Youtube Related Videos feature',
            ),
        ));

        // Add submit button
        $this->addElement('Button', 'submit', array(
            'label' => 'Save changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}