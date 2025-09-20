<?php

class Migachat_Model_Assistants extends Core_Model_Default
{
    public function __construct($datas = array())
    {
        parent::__construct($datas);
        $this->_db_table = 'Migachat_Model_Db_Table_Assistants'; //Db Model Name

    }
    
    public function updateAssistants($assistants,$value_id,$app_id) {
        return $this->getTable()->updateAssistants($assistants,$value_id,$app_id);
    }
   
    
}