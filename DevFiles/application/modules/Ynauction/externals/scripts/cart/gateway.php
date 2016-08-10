<?php

include_once 'payment_interface.php';
//echo dirname(__FILE__);
final class gateway
{
    private $object;
    
    /**
     * Class constructor
     *
     */
    public function __construct()
    {
    }
    /**
    * load class api payment
    * 
    * @param mixed $gateway_name .gateway_name must the same of folder and file.
    * For example : class paypal must have paypal.php in folder paypal with class paypal in content 
    * @param mixed $settings
    * @param mixed $ds
    */
    public function load($gateway_name, $settings = null,$ds = '/')
    {
        if (!isset($this->object[$gateway_name]))
        {
            $path = dirname(__FILE__).$ds.$gateway_name.$ds.$gateway_name.'.php';
            if (file_exists($path))
            {            
                require($path);
                $this->object[$gateway_name] = new $gateway_name;
                
            }
            else
            {
                return false;
            }        
            //$this->object[$gateway_name] = (file_exists($path) ? Phpfox::getLib('gateway.api.' . $sGateway) : false);
            
            if ($settings !== null && $this->_aObject[$gateway_name] != null)
            {
                $this->object[$gateway_name]->set($settings);
            }
        }        
        
        return $this->object[$gateway_name];
    }
    
    
}

?>