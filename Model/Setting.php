<?php

class Migachat_Model_Setting extends Core_Model_Default
{
    public function __construct($datas = array())
    {
        parent::__construct($datas);
        $this->_db_table = 'Migachat_Model_Db_Table_Setting'; //Db Model Name

    }

    /**
     * @return mixed
     */
    public function getCronInfo() {
        return $this->getTable()->getCronInfo();
    }
    /**
     * @return mixed
     */
    public function getAppIdByValueId($value_id) {
        return $this->getTable()->getAppIdByValueId($value_id);
    }
    
    public function countTokens($text)
    {
        $byteSize = strlen(utf8_encode($text));
        return $this->bytesToTokens($byteSize);
    }
    private function bytesToTokens($bytes)
    {
        $tokensPerByte = 1.0 / 4.0; // Assume 4 bytes per token
        return intval(ceil($bytes * $tokensPerByte));
    }
}
