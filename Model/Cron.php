<?php

class Migachat_Model_Cron extends Core_Model_Default
{
    public function __construct($datas = array())
    {
        parent::__construct($datas);
        $this->_db_table = 'Migachat_Model_Db_Table_Cron'; //Db Model Name

    }

    public function newMessagesPushLogs($value_id) {
        return $this->getTable()->newMessagesPushLogs($value_id);
    }
    public function notSentMessagesPushLogs($value_id) {
        return $this->getTable()->notSentMessagesPushLogs($value_id);
    }
}