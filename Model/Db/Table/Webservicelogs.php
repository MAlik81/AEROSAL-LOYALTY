<?php

class Migachat_Model_Db_Table_Webservicelogs extends Core_Model_Db_Table
{
    protected $_name = "migachat_webservice_logs"; //Database table name
    protected $_primary = "log_id"; //name of primary key column

    
    /**
     * @return mixed
     */
    public function getLastNRecords($value_id,$limit) {
        return $this->_db->fetchAll("SELECT * FROM $this->_name WHERE value_id = $value_id ORDER BY created_at DESC LIMIT $limit");

    }
    
}
