<?php
class Socialbridge_Form_Admin_Fbsettings extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Facebook Api Settings')
     ->setDescription('SOCIALBRIDGE_ADMIN_SETTINGS_FACEBOOK_DESCRIPTION');
     
	$description = $this->getTranslator()->translate('SOCIALBRIDGE_ADMIN_SETTINGS_FACEBOOK_DESCRIPTION');
    $settings = Engine_Api::_()->getApi('settings', 'core');
	if( $settings->getSetting('user.support.links', 0) == 1 ) {
	$moreinfo = $this->getTranslator()->translate( 
        '<br>More Info: <a href="http://www.socialengine.net/support/documentation/article?q=166&question=Admin-Panel---Settings--Facebook-Integration" target="_blank"> KB Article</a>');
	} else {
	$moreinfo = $this->getTranslator()->translate( 
        '');
	}
	$description = vsprintf($description.$moreinfo, array(
      'http://www.facebook.com/developers/apps.php',
    ));
    $this->setDescription($description);


    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);
	 
    $this->addElement('Text', 'FBKey', array(
          'label' => 'Facebook APP ID',
          'size'=>80,
          'style'=>'width:400px'
    ));
     $this->addElement('Text', 'FBSecret', array(
          'label' => 'Facebook APP Secret',
          'size'=>80,
          'style'=>'width:400px'
    ));
    // Add submit button
    $this->addElement('Button', 'submit', array(
          'label' => 'Save Changes',
          'type' => 'submit',
          'ignore' => true
    ));
  }
}