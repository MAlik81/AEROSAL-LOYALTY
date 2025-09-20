<?php

class Migachat_Model_ModelTokens extends Core_Model_Default
{
    public function __construct($datas = array())
    {
        parent::__construct($datas);
        $this->_db_table = 'Migachat_Model_Db_Table_ModelTokens'; //Db Model Name

    }
    public function getSystemPromptTokens($value_id){
        return $this->getTable()->getSystemPromptTokens($value_id);
    }
    public function getHistoryTokens($value_id){
        return $this->getTable()->getHistoryTokens($value_id);
    }
}