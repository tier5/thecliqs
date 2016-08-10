<?php

class Ynmobile_Form_Admin_Product extends Engine_Form
{
	public function init()
	{
		$this -> setTitle('Subscription Product Settings');
    	$this->addElement('Select', 'storekitpurchase_type', array(
            'label' => 'Mobile App',
            'multiOptions' => array(
    			'iphone' => 'iphone',
    			'ipad' => 'ipad',
    			'android' => 'android'
    		),
    		'attribs' => array(
    			'onchange' => 'chageAppType(this);'
    		),
        ));
		
        if (isset($_GET['type']) && in_array($_GET['type'], array('iphone', 'ipad', 'android')))
        {
        	$this->getElement("storekitpurchase_type")->setValue($_GET['type']);
        	$appType = $_GET['type'];
        }
        else 
        {
        	$appType = 'iphone';
        }
        
        
        $storekitTable = Engine_Api::_() -> getDbtable('storekitpurchases', 'ynmobile');
		$packageTable = Engine_Api::_() -> getDbtable('packages', 'payment');
    	$select = $packageTable -> select();
    	$packages = $packageTable -> fetchAll($select);
    	
    	foreach ($packages as $package)
    	{
    		$product = $storekitTable -> getProduct($appType, $package->package_id);
    		$this->addElement('Text', 'package_' . $package->package_id, array(
		      'label' => $package -> getTitle(),
		      'allowEmpty' => true,
		      'required' => false,
    		  'value' => ($product) ? $product->storekitpurchase_key : ''
			));
    	}
        
        // Submit Button
	    $this->addElement('Button', 'submit', array(
	      'label' => 'Save',
	      'type' => 'submit',
	    ));
	}
}