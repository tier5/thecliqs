<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
class Ynultimatevideo_Form_Admin_Global extends Engine_Form {

    public function init() {
        $this->setTitle('Global Settings')->setDescription('These settings affect all members in your community.');

        $this->addElement('Text', 'ynultimatevideo_ffmpeg_path', array(
            'label' => 'Path to FFMPEG',
            'description' => 'Please enter the full path to your FFMPEG installation. (Environment variables are not present)',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynultimatevideo.ffmpeg.path', ''),
        ));

        $this->addElement('Text', 'ynultimatevideo_jobs', array(
            'label' => 'Encoding Jobs',
            'description' => 'How many jobs do you want to allow to run at the same time?',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynultimatevideo.jobs', 2),
            'validators' => array(
                array('Int', true),
                new Engine_Validate_AtLeast(1),
            ),
        ));

        $this->addElement('Radio', 'ynultimatevideo_embeds', array(
            'label' => 'Allow Embedding of Videos?',
            'description' => 'Enabling this option will give members the ability to embed videos on this site in other pages using an iframe (like YouTube).',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynultimatevideo.embeds', 1),
            'multiOptions' => array(
                '1' => 'Yes, allow embedding of videos.',
                '0' => 'No, do not allow embedding of videos.',
            ),
        ));

        $this->addElement('Text', 'ynultimatevideo_friend_emails', array(
            'label' => 'Number of Emails',
            'description' => 'Number of emails a person can send each time on each video? (Enter a number between 1 and 50)',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynultimatevideo.friend.emails', 5),
            'validators' => array(
                                array('Int', true),
                                array('GreaterThan', true, array(0)),
                                array('LessThan', true, array(51)),
                            )
        ));

        $this->addElement('Text', 'ynultimatevideo_addthis_pubid', array(
            'label' => 'AddThis - Profile ID',
            'required' => true,
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynultimatevideo.addthis.pubid', 'younet'),
        ));
		
		$this->addElement('Text', 'ynultimatevideo_youtube_apikey', array(
            'label' => 'YouTube Data API v3 key',
            'description' => 'Please fill in the api key for parsing youtube video (YouTube Data API v3)',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynultimatevideo.youtube.apikey', 'AIzaSyDpUPT_nafV_MFSAlc-8AH4e1Gy578iK0M'),
        	'required' => true,
        	'allowEmpty' => false
		));
		
        // Add submit button
        $this->addElement('Button', 'submit', array(
            'label' => 'Save changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}