<?php

class Ynrestapi_Model_DbTable_OauthClients extends Engine_Db_Table
{
    /**
     * @var string
     */
    protected $_name = 'ynrestapi_oauth_clients';

    /**
     * @var string
     */
    protected $_rowClass = 'Ynrestapi_Model_OauthClient';
}
