<?php

class Yncontest_Api_Mail {
	/**
	 * @var Zend_Mail_Transport_Abstract
	 */
	protected $_transport;
	/**
	 * @var boolean
	 */
	protected $_queueing;

	/**
	 * @var  boolean
	 */
	protected $_enabled;

	/**
	 * @var string
	 */
	protected $_fromAddress;

	/**
	 * @var string
	 */
	protected $_fromName;

	/**
	 * @return string
	 */
	public function getFromAddress() {

		if($this -> _fromAddress == NULL) {
			$this -> _fromAddress = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('core.mail.from', 'test1@local.younetco.com');
		}
		return $this -> _fromAddress;
	}
 public function getCharset()
  {
    return 'utf-8';
  }
	
 public function create()
  {
    return new Zend_Mail($this->getCharset());
  }
	/**
	 * @return string
	 */
	public function getFromName() {
		if($this -> _fromName == NULL) {
			$this -> _fromName = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('core.mail.name', 'Site Admin');
		}
		return $this -> _fromName;
	}

	/**
	 *
	 */
	public function __construct() {
		$this -> _enabled = (bool)Engine_Api::_() -> getApi('settings', 'core') -> getSetting('core.mail.enabled', true);
		$this -> _queueing = (bool)Engine_Api::_() -> getApi('settings', 'core') -> getSetting('core.mail.queueing', true);
	}

	/**
	 * @param    string   $template
	 * @param    array    $params
	 * @return   string
	 */
	public function parseTemplate($template, $params) {
		foreach($params as $key => $value) {
			$template = str_replace("[$key]", $value, $template);
		}
		return $template;
	}

	// Options
	public function getTransport() {
		if(null === $this -> _transport) {

			// Get config
			$mailConfig = array();
			$mailConfigFile = APPLICATION_PATH . '/application/settings/mail.php';
						
			
			if(file_exists($mailConfigFile)) {
				$mailConfig =
				include $mailConfigFile;
			} else {
				$mailConfig = array('class' => 'Zend_Mail_Transport_Sendmail', 'args' => array(), );
			}

			// Get transport
			try {
				$args = (!empty($mailConfig['args']) ? $mailConfig['args'] : array());
				$r = new ReflectionClass($mailConfig['class']);
				$transport = $r -> newInstanceArgs($args);
				if(!($transport instanceof Zend_Mail_Transport_Abstract)) {
					$this -> _transport = false;
				} else {
					$this -> _transport = $transport;
				}
			} catch( Exception $e ) {
				$this -> _transport = false;
				throw $e;
			}
		}

		if(!($this -> _transport instanceof Zend_Mail_Transport_Abstract)) {
			return null;
		}

		return $this -> _transport;
	}

	/**
	 * 
	 * @param    string $type
	 * @param    stirng $locale [OPTIONAL]
	 * @return   array [subject, body, params]
	 * @throws   NULL
	 */
  	public function getTemplate($type, $locale = 'en') {
  		
		$table = Engine_Api::_()->getDbtable('MailTemplates', 'yncontest');
		
	    $item = $table->fetchRow($table->select()->where('type = ?', $type));
		
		if(!is_object($item)){
			return array(
				'no subject',
				'no body',
				'no body',
				array(),
			);
		}
		
	    $vars = $item->vars;
		$subjectKey = strtoupper('_EMAIL_' . $item->type . '_SUBJECT');
	    $bodyTextKey = strtoupper('_EMAIL_' . $item->type . '_BODY');
    	$bodyHtmlKey = strtoupper('_EMAIL_' . $item->type . '_BODYHTML');
		
		
		return array(
			(string) $this->_translate($subjectKey,  $locale),
			(string) $this->_translate($bodyTextKey, $locale),
			(string) $this->_translate($bodyHtmlKey, $locale),
			$vars
		);
		  	
  	}
	
	protected function _validateRecipient(){
		return true;
	}
	

	/**
	 *
	 * @param  mixed  $to
	 * @return boolean
	 */
	public function isValidEmail($to) {
		if($to) {
			return true;
		}
		return false;
	}
	
	/**
	 * @param    array      $params
	 * @param    string     $name
	 * @param    mixed     $sendTo
	 * @param    string    $locale
	 */
	public function send($recipient, $type, $params, $use_mail_queue = false, $mail_priority =0) {

		// Verify mail template type
	$translate = Zend_Registry::get('Zend_Translate');
    $mailTemplateTable = Engine_Api::_()->getDbtable('MailTemplates', 'Yncontest');
    $mailTemplate = $mailTemplateTable->fetchRow($mailTemplateTable->select()->where('type = ?', $type));
	
	
	
    if( null === $mailTemplate ) {
      return;
    }
	
	$vars = array();
    $params = Engine_Api::_()->getApi('ConvertMailVars', 'Yncontest')->process($params, $vars, $type);
	    // Verify recipient(s)
    if( !is_array($recipient) && !($recipient instanceof Zend_Db_Table_Rowset_Abstract) ) {
      $recipient = array($recipient);
    }
	
		
    $recipients = array();
    foreach( $recipient as $oneRecipient ) {
      if( !$this->_validateRecipient($oneRecipient) ) {
        throw new Engine_Exception(get_class($this).'::sendSystem() requires an item, an array of items with an email, or a string email address.');
      }
      $recipients[] = $oneRecipient;
    }

    // Send

    // Get admin info
    $fromAddress = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.from', 'admin@test.com');
    $fromName = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.name', 'Site Admin');
    
    $params['admin_email'] = $fromAddress;
    $params['admin_title'] = $fromName;

    $subjectKey = strtoupper('_EMAIL_' . $mailTemplate->type . '_SUBJECT');
    $bodyTextKey = strtoupper('_EMAIL_' . $mailTemplate->type . '_BODY');
    $bodyHtmlKey = strtoupper('_EMAIL_' . $mailTemplate->type . '_BODYHTML');

    // Send to each recipient
    foreach( $recipients as $recipient ) {

      // Copy params
      $rParams = $params;

      // See if they're actually a member
      if( is_string($recipient) ) {
        $user = Engine_Api::_()->getItemTable('user')->fetchRow(array('email LIKE ?' => $recipient));
        if( null !== $user ) {
          $recipient = $user;
        }
      }

      // Check recipient
      if( $recipient instanceof Core_Model_Item_Abstract ) {
        $isMember = true;

        // Detect email and name
        $recipientEmail = $recipient->email;
        $recipientName = $recipient->getTitle();

        // Detect language
        if( !empty($rParams['language']) ) {
          $recipientLanguage = $rParams['language'];
        } else if( !empty($recipient->language) ) {
          $recipientLanguage = $recipient->language;
        } else if(is_object($translate)){
          $recipientLanguage = $translate->getLocale();
        }else{
        	$recipientLanguage = 'en';	
        }
		
        // if( !Zend_Locale::isLocale($recipientLanguage) ||
            // $recipientLanguage == 'auto' ||
            // !in_array($recipientLanguage, $translate->getList()) ) {
          // $recipientLanguage = $translate->getLocale();
        // }

        // add automatic params
        $rParams['email'] = $recipientEmail;
        $rParams['language'] = $recipientLanguage;
        $rParams['recipient_email'] = $recipientEmail;
        $rParams['recipient_title'] = $recipientName;
        $rParams['recipient_link'] = $recipient->getHref();
        $rParams['recipient_photo'] = $recipient->getPhotoUrl('thumb.normal');
        
      } else if( is_string($recipient) ) {
        $isMember = false;
        
        // Detect email and name
        if( strpos($recipient, ' ') !== false ) {
          $parts = explode(' ', $recipient, 2);
          $recipientEmail = $parts[0];
          $recipientName = trim($parts[1], ' <>');
        } else {
          $recipientEmail = $recipient;
          $recipientName = '';
        }

        // Detect language
        if( !empty($rParams['language']) ) {
          $recipientLanguage = $rParams['language'];
        //} else if( !empty($recipient->language) ) {
        //  $recipientLanguage = $recipient->language;
        } else if(is_object($translate)){
          $recipientLanguage = $translate->getLocale();
        }else{
        	$recipientLanguage = 'en';	
        }
        if(isset($translate)&& is_object($translate) && (!Zend_Locale::isLocale($recipientLanguage) ||
            $recipientLanguage == 'auto' ||
            !in_array($recipientLanguage, $translate->getList())) ) {
          $recipientLanguage = $translate->getLocale();
        }

        // add automatic params
        $rParams['email'] = $recipientEmail;
        $rParams['recipient_email'] = $recipientEmail;
        $rParams['recipient_title'] = $recipientName;
        $rParams['recipient_link'] = '';
        $rParams['recipient_photo'] = '';

      } else {
      	// continue running.
      	/*if(APPLICATION_ENV == 'development'){
      		echo "skip this email $recipient";
      	}*/
        continue;
      }

      // Get subject and body
      $subjectTemplate  = (string) $this->_translate($subjectKey,  $recipientLanguage);
      $bodyTextTemplate = (string) $this->_translate($bodyTextKey, $recipientLanguage);
      $bodyHtmlTemplate = (string) $this->_translate($bodyHtmlKey, $recipientLanguage);
		
      if( !($subjectTemplate) ) {
        throw new Engine_Exception(sprintf('No subject translation available for system email "%s"', $type));
      }
      if( !$bodyHtmlTemplate && !$bodyTextTemplate ) {
        throw new Engine_Exception(sprintf('No body translation available for system email "%s"', $type));
      }

      // Get headers and footers
      $headerPrefix = '_EMAIL_HEADER_' . ( $isMember ? 'MEMBER_' : '' );
      $footerPrefix = '_EMAIL_FOOTER_' . ( $isMember ? 'MEMBER_' : '' );
      
      $subjectHeader  = (string) $this->_translate($headerPrefix . 'SUBJECT',   $recipientLanguage);
      $subjectFooter  = (string) $this->_translate($footerPrefix . 'SUBJECT',   $recipientLanguage);
      $bodyTextHeader = (string) $this->_translate($headerPrefix . 'BODY',      $recipientLanguage);
      $bodyTextFooter = (string) $this->_translate($footerPrefix . 'BODY',      $recipientLanguage);
      $bodyHtmlHeader = (string) $this->_translate($headerPrefix . 'BODYHTML',  $recipientLanguage);
      $bodyHtmlFooter = (string) $this->_translate($footerPrefix . 'BODYHTML',  $recipientLanguage);
      
      // Do replacements
      foreach( $rParams as $var => $val ) {
      	
        $raw = trim($var, '[]');
        $var = '[' . $var . ']';
        //if( !$val ) {
        //  $val = $var;
        //}
        // Fix nbsp
        $val = str_replace('&amp;nbsp;', ' ', $val);
        $val = str_replace('&nbsp;', ' ', $val);
		if(!is_string($var) || !is_string($val)){
			continue;
		}
		
        // Replace
        $subjectTemplate  = str_replace($var, $val, $subjectTemplate);
        $bodyTextTemplate = str_replace($var, $val, $bodyTextTemplate);
        $bodyHtmlTemplate = str_replace($var, $val, $bodyHtmlTemplate);
        $subjectHeader    = str_replace($var, $val, $subjectHeader);
        $subjectFooter    = str_replace($var, $val, $subjectFooter);
        $bodyTextHeader   = str_replace($var, $val, $bodyTextHeader);
        $bodyTextFooter   = str_replace($var, $val, $bodyTextFooter);
        $bodyHtmlHeader   = str_replace($var, $val, $bodyHtmlHeader);
        $bodyHtmlFooter   = str_replace($var, $val, $bodyHtmlFooter);
      }

      // Do header/footer replacements
      $subjectTemplate  = str_replace('[header]', $subjectHeader, $subjectTemplate);
      $subjectTemplate  = str_replace('[footer]', $subjectFooter, $subjectTemplate);
      $bodyTextTemplate = str_replace('[header]', $bodyTextHeader, $bodyTextTemplate);
      $bodyTextTemplate = str_replace('[footer]', $bodyTextFooter, $bodyTextTemplate);
      $bodyHtmlTemplate = str_replace('[header]', $bodyHtmlHeader, $bodyHtmlTemplate);
      $bodyHtmlTemplate = str_replace('[footer]', $bodyHtmlFooter, $bodyHtmlTemplate);

      // Check for missing text or html
      if( !$bodyHtmlTemplate ) {
        $bodyHtmlTemplate = nl2br($bodyTextTemplate);
      } else if( !$bodyTextTemplate ) {
        $bodyTextTemplate = strip_tags($bodyHtmlTemplate);
      }
      
	  
	      $mail = $this->create()
	        ->addTo($recipientEmail, $recipientName)
	        ->setFrom($fromAddress, $fromName)
	        ->setSubject($subjectTemplate)
	        ->setBodyHtml($bodyHtmlTemplate)
	        ->setBodyText($bodyTextTemplate);
	     		  
	   try{   $this->sendRaw($mail);
	   }catch(Exception $e ){
	   	exit("loi");
		   
	   }	
	   
    }

    return $this;
	}
	protected function _translate($key, $locale = 'en', $noDefault = false){
	    $translate = Zend_Registry::get('Zend_Translate');		
		
	    $value = $translate->translate($key, $locale);
		
		
	    if( $value == $key || '' == trim($value) ) {
	      if( $noDefault ) {
	        return false;
	      } else {
	        $value = $translate->translate($key);
	        if( $value == $key || '' == trim($value) ) {
	          return false;
	        }
	      }
	    }
	    return $value;
  }
  public function sendRaw($mail) {
  	 
	 try {
	 	
	 	$mail->send($this->getTransport());
		
	 	//$this->addLog($mail);			
		} catch(Exception $e) {
			//throw $e;
			$params['success'] = 0;
		}

		
	}

	/**
	 * @param array   $params
	 * @param string  $name [OPTIONAL]
	 * @return NULL
	 * @throws
	 */
	public function addLog($mail) {
		try {			
			
		} catch(Exception $e) {
			
		}
	
	}
	public function sendMailQueue($send_limit =  NULL){

		if($send_limit==0){
			$send_limit =  Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.count', 25);
		}
		
		$table =Engine_Api::_()->getDbTable('emails','yncontest');
		$select =  $table->select()->where('sended=0')->order('priority desc')->limit($send_limit);
		foreach($table->fetchAll($select) as $item){			
			 $mail = $this->create()
	        ->addTo($item->send_to, $item->to_name)
	        ->setFrom($item->send_from, $item->from_name)
	        ->setSubject($item->subject)
	        ->setBodyHtml($item->body_html)
	        ->setBodyText($item->body_text);
	        $this->sendRaw($mail);
			$item->sended = 1;
			$item->save();
		}  
	}

}
