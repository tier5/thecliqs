<?php
class Ynsocialads_Model_DbTable_Adtargets extends Engine_Db_Table {
    protected $_rowClass = 'Ynsocialads_Model_Adtarget';
    
    protected $_serializedColumns = array('countries', 'networks');
}