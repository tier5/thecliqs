<?php
class Ynultimatevideo_Form_Admin_Settings_Youtube extends Engine_Form {
    public function init() {
        $this
        	->setTitle('YouTube Settings');
			
		$view = Zend_Registry::get('Zend_View');
		$pageURL = 'http';
		if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")
		{
			$pageURL .= "s";
		}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80")
		{
			$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"];
		}
		else
		{
			$pageURL .= $_SERVER["SERVER_NAME"];
		}
		$description = $this->getTranslator()->translate("YNULTIMATEVIDEO_ADMIN_SETTINGS_YOUTUBE_DESCRIPTION");
		$description = vsprintf($description, array(
	      'https://developers.google.com/identity/protocols/OpenIDConnect?hl=en#appsetup',
	    ));
        $description .= "<strong>" .$this->getTranslator()->translate("Authorized redirect URIs"). "</strong>: ". $pageURL.$view -> url(array('action' => 'oauth2callback'), 'ynultimatevideo_general', true);
        $this -> setDescription($description);
        $this->loadDefaultDecorators();
    	$this->getDecorator('Description')->setOption('escape', false);
        
        $settings = Engine_Api::_()->getApi('settings', 'core');
		
		$this->addElement('Radio', 'ynultimatevideo_youtube_allow', array(
	      'label' => 'Allow to upload videos to YouTube?',
	      'value' => $settings->getSetting('ynultimatevideo_youtube_allow', 0),
	      'multiOptions' => array(
	        '1' => "Yes, allow users to save their uploaded videos to YouTube.",
	        '0' => "No, do not allow users to save their uploaded videos to YouTube.",
	      ),
	    ));
        
        $this->addElement('Text', 'ynultimatevideo_youtube_clientid', array(
            'label' => 'Client ID',
            'value' => $settings->getSetting('ynultimatevideo_youtube_clientid', ""),
        ));
        
        $this->addElement('Text', 'ynultimatevideo_youtube_secret', array(
            'label' => 'Client Secret',
            'value' => $settings->getSetting('ynultimatevideo_youtube_secret', ""),
        ));
		
		
        $this->addElement('Button', 'submit_btn', array(
          'label' => 'Save Changes',
          'type' => 'submit',
          'ignore' => true
        ));
    }
}