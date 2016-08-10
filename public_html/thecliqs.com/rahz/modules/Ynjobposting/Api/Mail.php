<?php

class Ynjobposting_Api_Mail {
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
  		
		$table = Engine_Api::_()->getDbtable('MailTemplates', 'groupbuy');
		
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
	public function send($recipient, $type, $params, $bodyHtmlContent, $use_mail_queue = false, $mail_priority =0) {
		// Verify mail template type
	$translate = Zend_Registry::get('Zend_Translate');
    $mailTemplateTable = Engine_Api::_()->getDbtable('MailTemplates', 'Ynjobposting');
    $mailTemplate = $mailTemplateTable->fetchRow($mailTemplateTable->select()->where('type = ?', $type));
	
    if( null === $mailTemplate ) {
      return;
    }
	
	$vars = array();
    $params = Engine_Api::_()->getApi('ConvertMailVars', 'Ynjobposting')->process($params, $vars, $type);
	
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

      } else {
      	// continue running.
      	/*if(APPLICATION_ENV == 'development'){
      		echo "skip this email $recipient";
      	}*/
        continue;
      }

      
	  //TODO set html content
	  $view = Zend_Registry::get('Zend_View');
	  $bodyHtmlTemplate = $bodyHtmlContent;
	  $subjectTemplate = $view -> translate ('Job Alert from ') . $view -> layout() -> siteinfo['title'];
		  // Send
	      $mail = $this->create()
	        ->addTo($recipientEmail, $recipientName)
	        ->setFrom($fromAddress, $fromName)
	        ->setSubject($subjectTemplate)
	        ->setBodyHtml($bodyHtmlTemplate)
	        ->setBodyText($bodyTextTemplate);
	      
	      $this->sendRaw($mail);	
	 // }      
    }

    return $this;
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
}
