<?php

class Migachat_Model_Webservicelogs extends Core_Model_Default
{
    public function __construct($datas = array())
    {
        parent::__construct($datas);
        $this->_db_table = 'Migachat_Model_Db_Table_Webservicelogs'; //Db Model Name

    }
    /**
     * @return mixed
     */
    public function getLastNRecords($value_id,$limit) {
        return $this->getTable()->getLastNRecords($value_id,$limit);
    }
    
}