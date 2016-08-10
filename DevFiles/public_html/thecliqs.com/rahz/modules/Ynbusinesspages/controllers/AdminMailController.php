<?php
class Ynbusinesspages_AdminMailController extends Core_Controller_Action_Admin
{

  public function templatesAction()
  {
  	$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('ynbusinesspages_admin_main', array(), 'ynbusinesspages_admin_main_emailtemplates');
      
    $this->view->form = $form = new Ynbusinesspages_Form_Admin_Mail_Templates();

    // Get language
    $this->view->language = $language = preg_replace('/[^a-zA-Z_-]/', '', $this->_getParam('language', 'en'));
    if( !Zend_Locale::isLocale($language) ) {
      $form->removeElement('submit');
      return $form->addError('Please select a valid language.');
    }

    // Check dir for exist/write
    $languageDir = APPLICATION_PATH . '/application/languages/' . $language;
    $languageFile = $languageDir . '/custom.csv';
    if( !is_dir($languageDir) ) {
      $form->removeElement('submit');
      return $form->addError('The language does not exist, please create it first');
    }
    if( !is_writable($languageDir) ) {
      $form->removeElement('submit');
      return $form->addError('The language directory is not writable. Please set CHMOD -R 0777 on the application/languages folder.');
    }
    if( is_file($languageFile) && !is_writable($languageFile) ) {
      $form->removeElement('submit');
      return $form->addError('The custom language file exists, but is not writable. Please set CHMOD -R 0777 on the application/languages folder.');
    }
    
    // Get template
    $this->view->template = $template = $this->_getParam('template', '1');
    $this->view->templateObject = $templateObject = Engine_Api::_()->getItem('ynbusinesspages_mail_template', $template);
    if( !$templateObject ) {
      $templateObject = Engine_Api::_()->getDbtable('MailTemplates', 'core')->fetchRow();
      $template = $templateObject->mailtemplate_id;
    }

    // Populate form
    $description = $this->view->translate(strtoupper("_email_".$templateObject->type."_description"));
    $description .= '<br /><br />';
    $description .= $this->view->translate('Available Placeholders:');
    $description .= '<br />';
    $description .= join(', ', explode(',', $templateObject->vars));

    $form->getElement('template')
      ->setDescription($description)
      ->getDecorator('Description')
        ->setOption('escape', false)
        ;

    // Get translate
    $translate = Zend_Registry::get('Zend_Translate');

    // Get stuff
    $subjectKey = strtoupper("_email_".$templateObject->type."_subject");
    $subject = $translate->_($subjectKey, $language);
    if( $subject == $subjectKey ) {
      $subject = $translate->_($subjectKey, 'en');
    }

    $bodyKey = strtoupper("_email_".$templateObject->type."_body");
    $body = $translate->_($bodyKey, $language);
    if( $body == $bodyKey ) {
      $body = $translate->_($bodyKey, 'en');
    }

    $form->populate(array(
      'language' => $language,
      'template' => $template,
      'subject' => $subject,
      'body' => $body,
    ));
    
    // Check method/valid
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Process
    $values = $form->getValues();
    $writer = new Engine_Translate_Writer_Csv();

    // Try to write to a file
    $targetFile = APPLICATION_PATH . '/application/languages/' . $language . '/custom.csv';
    if( !file_exists($targetFile) ) {
      touch($targetFile);
      chmod($targetFile, 0777);
    }

    // set the local folder depending on the language_id
    $writer->read(APPLICATION_PATH . '/application/languages/' . $language . '/custom.csv');

    // write new subject
    $writer->removeTranslation(strtoupper("_email_" . $templateObject->type . "_subject"));
    $writer->setTranslation(strtoupper("_email_" . $templateObject->type . "_subject"), $values['subject']);

    // write new body
    $writer->removeTranslation(strtoupper("_email_" . $templateObject->type . "_body"));
    $writer->setTranslation(strtoupper("_email_" . $templateObject->type . "_body"), $values['body']);

    $writer->write();

    // Clear cache?
    $translate->clearCache();

    $form->addNotice($this->view->translate('Your changes have been saved.'));
  }

}