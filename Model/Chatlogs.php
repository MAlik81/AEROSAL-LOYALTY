<?php

class Migachat_Model_Chatlogs extends Core_Model_Default
{
    public function __construct($datas = array())
    {
        parent::__construct($datas);
        $this->_db_table = 'Migachat_Model_Db_Table_Chatlogs'; //Db Model Name

    }

    /**
     * @return mixed
     */
    public function getChatLogs($value_id, $customer_id, $start, $limit) {
        return $this->getTable()->getChatLogs($value_id, $customer_id, $start, $limit);
    }
    public function getUserIdsWithChat($value_id) {
        return $this->getTable()->getUserIdsWithChat($value_id);
    }
    public function getUnreadChatLogs($value_id, $customer_id)
    {
        return $this->getTable()->getUnreadChatLogs($value_id,$customer_id);
    }

    public function markAsRead($value_id, $customer_id)
    {
        return $this->getTable()->markAsRead($value_id,$customer_id);
    }
    public function getAllChatLogs($value_id,$customer_id, $date_from, $date_to)
    {
        return $this->getTable()->getAllChatLogs($value_id,$customer_id, $date_from, $date_to);
    }
    public function getLastTenMessages($value_id,$customer_id,$limit)
    {
        return $this->getTable()->getLastTenMessages($value_id,$customer_id,$limit);
    }
    public function getInstanceLogs($value_id,$customer_id)
    {
        return $this->getTable()->getInstanceLogs($value_id,$customer_id);
    }
    public function deleteChatLogs($value_id,$chat_id)
    {
        return $this->getTable()->deleteChatLogs($value_id,$chat_id);
    }
    
    public function deleteInstanceChatLogs($value_id)
    {
        return $this->getTable()->deleteInstanceChatLogs($value_id);
    }
    public function getChatTokenStats($data)
    {
        return $this->getTable()->getChatTokenStats($data);
    }
    public function getAppChatStats($data)
    {
        return $this->getTable()->getAppChatStats($data);
    }
    public function getAppChatStatsCsvData($data)
    {
        return $this->getTable()->getAppChatStatsCsvData($data);
    }
    
}