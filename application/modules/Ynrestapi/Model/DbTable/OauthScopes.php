<?php

class Ynrestapi_Model_DbTable_OauthScopes extends Engine_Db_Table
{
    /**
     * @var string
     */
    protected $_name = 'ynrestapi_oauth_scopes';

    /**
     * @var string
     */
    protected $_rowClass = 'Ynrestapi_Model_OauthScope';
}
