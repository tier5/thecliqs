<?php 
    if (session_id() == "") 
        session_start();
	include_once 'logging.php';
    interface Payment_Interface
    {    
        /**
        * set setting for payment 
        * 
        * @param mixed $aSetting
        */
         public function set($aSetting = array());
         /**
         * Verify the transaction of site.
         * 
         * @param mixed $params
         */
         public function verify($params = array());
         /**
         * Check to send money to the gateway.
         * 
         * @param mixed $params
         */
         public function checkOut($params = array());
         /**
         * get Callback Url .
         * 
         */
         public function getCallBackUrl();
         /**
         * Log 
         * 
         * @param mixed $log
         */
         public function logging($log = "");
         /**
         * Callback URL
         * 
         * @param mixed $cmd
         */
         public function Redirect ( $cmd );
    }
?>