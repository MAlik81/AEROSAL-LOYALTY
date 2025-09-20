<?php

class Migachat_Model_GDPR extends Core_Model_Default
{

    public function __construct($datas = array()) {
        parent::__construct($datas);
        $this->_db_table = 'Migachat_Model_Db_Table_GDPR';
    }

    public function resetAll($value_id)
    {
        return $this->getTable()->resetAll($value_id);
    }
    public function resetOne($value_id,$chat_id)
    {
        return $this->getTable()->resetOne($value_id,$chat_id);
    }
}
