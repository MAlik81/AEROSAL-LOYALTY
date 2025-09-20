<?php

class Migachat_Model_OperatorRequests extends Core_Model_Default
{
    public function __construct($datas = array())
    {
        parent::__construct($datas);
        $this->_db_table = 'Migachat_Model_Db_Table_OperatorRequests'; //Db Model Name

    }
    
}