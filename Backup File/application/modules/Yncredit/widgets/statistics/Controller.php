<?php
class Yncredit_Widget_StatisticsController extends Engine_Content_Widget_Abstract 
{
    public function indexAction() 
    {
        $this -> view -> statistics = $statistics = Engine_Api::_() -> getDbTable("balances", "yncredit") -> getBalanceStatistics();
    }
}