<?php
session_start();
?>
<?php
class Ynbusinesspages_Widget_BusinessProfileContactController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		
	 	 // Don't render this if not authorized
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!Engine_Api::_() -> core() -> hasSubject()) {
			return $this -> setNoRender();
		}
		// Get subject and check auth
		$subject = Engine_Api::_() -> core() -> getSubject('ynbusinesspages_business');
        if (!$subject -> isViewable()) {
            return $this -> setNoRender();
        }
	    if($subject -> is_claimed)
		{
			return $this -> setNoRender();
		}
        $contactForm = $subject->getContactForm();
        if (!$contactForm) {
            return $this -> setNoRender();
        }
        $this->view->form = $form = new Ynbusinesspages_Form_Business_Contact(array('business' => $subject));
        if($viewer -> getIdentity())
		{
			$form -> name -> setValue($viewer -> displayname);
			$form -> email -> setValue($viewer -> email);
		}
	    if(isset($_SESSION['$message']))
		{
			$form -> addNotice($_SESSION['$message']);
			unset($_SESSION['$message']);
		}
		$params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
		
        if (!isset($params['submit_btn'])) {
            return;
        }
        $posts = Zend_Controller_Front::getInstance()->getRequest() -> getPost();
        if (!$form -> isValid($posts)) {
            return;
        }
		
		$values = $form -> getValues();
		
		//check email
		$regexp = "/^[A-z0-9_]+([.][A-z0-9_]+)*[@][A-z0-9_]+([.][A-z0-9_]+)*[.][A-z]{2,4}$/";
		if (!preg_match($regexp, $values['email'])) {
			$form -> addError('Please enter valid email!');
			return;
		}
		
		$name = $values['name'];
		$email = $values['email'];
		
		//send contact
		$recipient = $subject -> getOwner() -> email;
		if(!empty($values['department']))
		{
			$recipient = $values['department'];
		}
		$subjectStr = $values['subject'];
		$message = $values['message'];
		$bodyHtmlContent = $this -> view -> translate("Full Name: %s", $name)."<br />";
		$bodyHtmlContent .= $this -> view -> translate("Email: %s", $email)."<br />";
		$bodyHtmlContent .= $this -> view -> translate("Message: %s", $message)."<br />";
		
		$metaTbl = Engine_Api::_()->getDbTable('meta', 'ynbusinesspages');
		$optionTbl = Engine_Api::_()->getDbTable('options', 'ynbusinesspages');
		
		//save custom field info
        foreach ($values as $key => $value) {
            if (strpos($key, 'field_') !== false && $value) {
				$field_id = substr($key, 6);
				$field =  $metaTbl -> getField($field_id);
				if(isset($field))
				{
					$label = $field -> label;
					switch ($field -> type) {
						case 'text':
						case 'textarea':
							$bodyHtmlContent .= $label.": ".$value."<br />";
							break;
						case 'checkbox':
							$strValue = "";
							$iCheck = 1;
		                    foreach($value as $eachValue)
							{
								$strValue .= $optionTbl -> getLabel($eachValue, $field_id);
								if($iCheck < count($value))
								{
									$strValue .= ", ";
									$iCheck++;
								}
							}
							$bodyHtmlContent .= $label.": ".$strValue."<br />";
							break;
						case 'radio':
							$strValue = $optionTbl -> getLabel($value, $field_id);
							$bodyHtmlContent .= $label.": ".$strValue."<br />";
							break;
						default:
							
							break;
					}
				}
            }
        }
		
		try
		{
			Engine_Api::_()->getApi('mail','ynbusinesspages')->sendContact($recipient, $subjectStr, $bodyHtmlContent);
			$message = $this -> view -> translate('Send email contact succesfully!');
		}
		catch(exception $e)
		{
			$message = $this -> view -> translate('Send email contact failure!');
		}

		$_SESSION['$message'] = $message;
		Zend_Controller_Action_HelperBroker::getStaticHelper('redirector')-> gotoRoute(array('id' => $subject->getIdentity(), 'slug' => $subject -> getSlug(), 'tab' => $this->view->identity, 'page' => $this->view->page), 'ynbusinesspages_profile', true);
		
	}
}
	