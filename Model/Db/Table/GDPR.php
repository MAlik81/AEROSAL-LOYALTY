<?php

class Migachat_Model_Db_Table_GDPR extends Core_Model_Db_Table
{

    protected $_name = "migachat_gdpr";
    protected $_primary = "gdpr_id";

    public function resetAll($value_id)
    {
        $data = array(
            'gdpr_consent' => 2,
            'commercial_consent' => 2,
            'gdpr_consent_external' => 0,
            'commercial_consent_external' => 0,
            'gdpr_consent_timestamp' => null,
            'commercial_consent_timestamp' => null,
            'created_at' => date('Y-m-d H:i:s'),

        );
        $where = array(
            $this->_db->quoteInto('value_id = ?', $value_id)
        );
        return $this->_db->update('migachat_bridge_api_chat_ids', $data, $where);
    }
    public function resetOne($value_id,$chat_id)
    {
        $data = array(
            'gdpr_consent' => 2,
            'commercial_consent' => 2,
            'gdpr_consent_external' => 0,
            'commercial_consent_external' => 0,
            'gdpr_consent_timestamp' => null,
            'commercial_consent_timestamp' => null,
            'created_at' => date('Y-m-d H:i:s'),

        );
        $where = array(
            $this->_db->quoteInto('value_id = ?', $value_id),
            $this->_db->quoteInto('chat_id = ?', $chat_id)
        );
        return $this->_db->update('migachat_bridge_api_chat_ids', $data, $where);
    }
}
