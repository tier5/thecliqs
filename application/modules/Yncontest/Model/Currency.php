<?php

class Yncontest_Model_Currency extends Core_Model_Item_Abstract {

	public function currencyDisplay() {
		switch($this->display) {
			case 'Use Symbol' :
				return Zend_Currency::USE_SYMBOL;
			case 'No Symbol' :
				return Zend_Currency::NO_SYMBOL;
			case 'Use Name' :
				return Zend_Currency::USE_NAME;
			case 'Use Shortname' :
				return Zend_Currency::USE_SHORTNAME;
		}
		return Zend_Currency::NO_SYMBOL;
	}

	public function currencyPosition() {
		switch($this->position) {
			case 'Standard' :
				return Zend_Currency::STANDARD;
			case 'Left' :
				return Zend_Currency::LEFT;
			case 'Right' :
				return Zend_Currency::RIGHT;
		}
		return Zend_Currency::STANDARD;
	}

}
