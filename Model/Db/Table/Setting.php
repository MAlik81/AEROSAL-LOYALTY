<?php

class Migachat_Model_Db_Table_Setting extends Core_Model_Db_Table
{
    protected $_name = "migachat_setting"; //Database table name
    protected $_primary = "migachat_setting_id"; //name of primary key column

    /**
     * @return mixed
     */
    public function getCronInfo() {
        return $this->_db->fetchAll("SELECT * FROM cron WHERE name LIKE 'Migachat cron job.'");
    }

    /**
     * @return mixed
     */
    public function getAppIdByValueId($value_id) {
        $data = $this->_db->fetchAll("SELECT `app_id` FROM `application_option_value` WHERE value_id = $value_id");
        return $data[0]['app_id'];
    }
}
