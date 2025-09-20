<?php

class Migachat_Model_BridgeAPI extends Core_Model_Default
{
    public function __construct($datas = array())
    {
        parent::__construct($datas);
        $this->_db_table = 'Migachat_Model_Db_Table_BridgeAPI'; //Db Model Name

    }
    
    public function getNewChatId($value_id) {
        return $this->getTable()->getNewChatId($value_id);
    }
    public function getHistoryMessages($value_id,$chat_id,$count)
    {
        return $this->getTable()->getHistoryMessages($value_id,$chat_id,$count);
    }
    public function getAllChatLogs($value_id,$chat_id, $date_from, $date_to)
    {
        return $this->getTable()->getAllChatLogs($value_id,$chat_id, $date_from, $date_to);
    }
    public function getAllChatIds($value_id)
    {
        return $this->getTable()->getAllChatIds($value_id);
    }
    public function deleteChatLogs($value_id,$chat_id,$delete_type)
    {
        return $this->getTable()->deleteChatLogs($value_id,$chat_id,$delete_type);
    }
    public function deleteInstanceChatLogs($value_id,$delete_type)
    {
        return $this->getTable()->deleteInstanceChatLogs($value_id,$delete_type);
    }
    public function getChatTokenStats($data)
    {
        return $this->getTable()->getChatTokenStats($data);
    }
    public function getChatIDName($value_id,$chat_id)
    {
        return $this->getTable()->getChatIDName($value_id,$chat_id);
    }

    public function getChatIDData($value_id, $chat_id)
    {
        return $this->getTable()->getChatIDData($value_id,$chat_id);
    }
    public function lastTwoMessagesCheck($value_id,$chat_id,$message)
    {
        return $this->getTable()->lastTwoMessagesCheck($value_id,$chat_id,$message);
    }
    public function getOverAllTokens($value_id,$over_all_duration)
    {
        return $this->getTable()->getOverAllTokens($value_id,$over_all_duration);
    }

    public function getChatIdTokens($value_id, $chatid_duration,$chat_id)
    {
        return $this->getTable()->getChatIdTokens($value_id, $chatid_duration,$chat_id);
    }
    public function getApiChatStats($data)
    {
        return $this->getTable()->getApiChatStats($data);
    }
    public function getApiChatStatsCsvData($data)
    {
        return $this->getTable()->getApiChatStatsCsvData($data);
    }
    
}