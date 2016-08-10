<?php

class Socialgames_Plugin_Core
{
    public function onStatistics($event)
    {
        $table = Engine_Api::_()->getDbTable('games', 'socialgames');
        $select = new Zend_Db_Select($table->getAdapter());
        $select->from($table->info('name'), 'COUNT(*) AS count')->where('is_active =?', 1);
        $event->addResponse($select->query()->fetchColumn(0), 'games');
    }
}