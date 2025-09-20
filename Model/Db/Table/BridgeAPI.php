<?php

class Migachat_Model_Db_Table_BridgeAPI extends Core_Model_Db_Table
{
    protected $_name = "migachat_bridge_api"; //Database table name
    protected $_primary = "migachat_bridge_id"; //name of primary key column


    public function getNewChatId($value_id)
    {
        $data = $this->_db->fetchAll("SELECT MAX(chat_id) AS new_chat_id FROM migachat_bridge_api_chat_ids WHERE value_id = $value_id");
        if (!count($data)) {
            return 1;
        } else {
            return $data[0]['new_chat_id'] + 1;
        }

    }
    public function getHistoryMessages($value_id, $chat_id, $count)
    {
       
        $data = $this->_db->fetchAll("SELECT * FROM $this->_name WHERE value_id = $value_id AND chat_id = $chat_id ORDER BY created_at DESC LIMIT $count");
        // dd($data);
        return $data;
    }

    public function getAllChatLogs($value_id, $chat_id, $date_from, $date_to)
    {
        $where = "";
        if ($chat_id) {
            $where .= " AND chat_id = $chat_id ";
        }
        // date from (start of the day) and date to (end of the day)
        $date_from = $date_from . " 00:00:00";
        $date_to = $date_to . " 23:59:59";

        if ($date_from) {
            $where .= " AND $this->_name.created_at >= '$date_from'  ";
        }
        if ($date_to) {
            $where .= " AND $this->_name.created_at <= '$date_to'  ";
        }
        return $this->_db->fetchAll("SELECT * FROM $this->_name WHERE value_id = $value_id $where ORDER BY migachat_bridge_id,created_at ASC");

    }
    public function getAllChatIds($value_id)
    {
        return $this->_db->fetchAll("SELECT chat_id FROM $this->_name WHERE value_id = $value_id GROUP BY chat_id ORDER BY chat_id ASC");

    }
    public function deleteChatLogs($value_id, $chat_id, $delete_type)
    {
        if ($delete_type == 1) {
            $this->_db->delete("migachat_bridge_api", ['chat_id = ?' => $chat_id, 'value_id = ?' => $value_id]);
            $this->_db->delete("migachat_bridge_api_chat_ids", ['chat_id = ?' => $chat_id, 'value_id = ?' => $value_id]);
            return true;
        } elseif ($delete_type == 2) {
            $first_two_records = $this->_db->fetchAll("SELECT migachat_bridge_id FROM migachat_bridge_api WHERE value_id = $value_id AND chat_id = $chat_id ORDER BY chat_id ASC limit 2");
            
            $first_two_ids = array_column($first_two_records, 'migachat_bridge_id');
            
            $this->_db->delete("migachat_bridge_api", ['chat_id = ?' => $chat_id, 'value_id = ?' => $value_id, 'migachat_bridge_id NOT IN (?)' => $first_two_ids]);
            return true;
        }
    }
    public function deleteInstanceChatLogs($value_id, $delete_type)
    {

        if ($delete_type == 1 && $value_id) {
            $this->_db->delete("migachat_bridge_api", ['value_id = ?' => $value_id]);
            $this->_db->delete("migachat_bridge_api_chat_ids", ['value_id = ?' => $value_id]);

            return true;
        } elseif ($delete_type == 2 && $value_id) {

            $first_two_records_query = "SELECT `migachat_bridge_id`
            FROM (
                SELECT `migachat_bridge_id`, ROW_NUMBER() OVER (PARTITION BY chat_id ORDER BY `migachat_bridge_id`) AS row_num
                FROM migachat_bridge_api
                WHERE value_id = $value_id
            ) AS ranked
            WHERE row_num <= 2";

            $first_two_records = $this->_db->fetchAll($first_two_records_query);

            $first_two_ids = array_column($first_two_records, 'migachat_bridge_id');

            $this->_db->delete("migachat_bridge_api", ['value_id = ?' => $value_id,'migachat_bridge_id NOT IN (?)' => $first_two_ids]);

        }

    }
    public function getChatTokenStats($data)
    {
        $where = "";
        if (!empty($data['chat_id'])) {
            $where .= ' AND chat_id=' . $data['chat_id'] . ' ';
        }
        $value_id = $data['value_id'];
        $start_date = $data['from_date'];
        $end_date = $data['to_date'];
        return $this->_db->fetchAll("SELECT
        SUM(prompt_tokens) AS total_prompt_tokens,
        SUM(completion_tokens) AS total_completion_tokens,
        SUM(total_tokens) AS total_total_tokens
        FROM
        migachat_bridge_api
        WHERE
        created_at >= '" . $start_date . "' AND created_at <= '" . $end_date . "' AND value_id = $value_id
        $where");

    }
    public function getChatIDName($value_id, $chat_id)
    {
        $name_data = $this->_db->fetchAll("SELECT user_name FROM migachat_bridge_api_chat_ids WHERE value_id = $value_id AND chat_id = $chat_id AND user_name IS NOT NULL ORDER BY chat_id ASC");
        if ($name_data) {
            return $name_data[0]['user_name'];
        } else {
            return "";
        }
    }
    public function getChatIDData($value_id, $chat_id)
    {
        return $this->_db->fetchAll("SELECT user_name,user_email,user_mobile FROM $this->_name WHERE value_id = $value_id AND chat_id = $chat_id ORDER BY user_name DESC LIMIT 1");
    }


    public function lastTwoMessagesCheck($value_id, $chat_id, $message)
    {
        // Retrieve the last two messages from the database
        $query = "SELECT * FROM $this->_name WHERE value_id = $value_id AND chat_id = $chat_id AND `role` = 'user'  ORDER BY created_at DESC LIMIT 2";
        $result = $this->_db->fetchAll($query);

        // Check if there are at least two messages
        if (count($result) >= 2) {
            // Get the content of the last two messages
            $lastMessage1 = $result[0]['message_content']; // Replace 'message_column_name' with the actual column name in your table.
            $lastMessage2 = $result[1]['message_content']; // Replace 'message_column_name' with the actual column name in your table.

            // Compare the last two messages with $message
            if ($lastMessage1 == $message && $lastMessage2 == $message) {
                // Both last messages are the same as $message
                return true;
            }
        }

        // The last two messages are not the same as $message
        return false;
    }

    public function getOverAllTokens($value_id, $over_all_duration)
    {
        $query = "SELECT 
                    value_id,
                    SUM(total_tokens) AS total_tokens_sum
                FROM 
                    migachat_bridge_api
                WHERE 
                    has_error = 0
                    AND created_at >= NOW() - INTERVAL $over_all_duration HOUR
                    AND value_id = $value_id
                    ";
        return $this->_db->fetchAll($query);
    }
    public function getChatIdTokens($value_id, $chatid_duration, $chat_id)
    {
        $query = "SELECT 
                    value_id,
                    SUM(total_tokens) AS total_tokens_sum
                FROM 
                    migachat_bridge_api
                WHERE 
                    has_error = 0
                    AND created_at >= NOW() - INTERVAL $chatid_duration MINUTE
                    AND value_id = $value_id
                    AND chat_id = $chat_id
                ";
        return $this->_db->fetchAll($query);
    }

    public function getApiChatStats($data)
    {
        $value_id = $data['value_id'];
        $start_date = $data['date_from'];
        $end_date = $data['date_to'];
        $customers_count = $this->_db->fetchAll("SELECT COUNT(DISTINCT migachat_bridge_api.chat_id) AS unique_customer_count
                                        FROM migachat_bridge_api
                                        JOIN migachat_bridge_api_chat_ids on migachat_bridge_api_chat_ids.chat_id = migachat_bridge_api.chat_id
                                        WHERE migachat_bridge_api.created_at >= '$start_date'
                                            AND migachat_bridge_api.created_at <= '$end_date'
                                            AND migachat_bridge_api.value_id = $value_id
                                            AND (migachat_bridge_api_chat_ids.user_name <> '' OR migachat_bridge_api_chat_ids.user_email <> '' OR  migachat_bridge_api_chat_ids.user_mobile  <> '')");


        $chat_stats = $this->_db->fetchAll("SELECT 
                        SUM(prompt_tokens) AS total_prompt_tokens,
                        SUM(completion_tokens) AS total_completion_tokens,
                        SUM(total_tokens) AS total_total_tokens
                        FROM migachat_bridge_api
                        WHERE chat_id IN (
                            SELECT DISTINCT migachat_bridge_api.chat_id
                            FROM migachat_bridge_api
                            JOIN migachat_bridge_api_chat_ids on migachat_bridge_api_chat_ids.chat_id = migachat_bridge_api.chat_id
                            WHERE migachat_bridge_api.created_at >= '$start_date'
                                AND migachat_bridge_api.created_at <= '$end_date'
                                AND migachat_bridge_api.value_id = $value_id
                                AND (migachat_bridge_api_chat_ids.user_name <> '' OR migachat_bridge_api_chat_ids.user_email <> '' OR  migachat_bridge_api_chat_ids.user_mobile  <> '')
                        );");
        return ["unique_customer_count" => $customers_count[0]['unique_customer_count'], "chat_stats" => $chat_stats[0]];
    }

    public function getApiChatStatsCsvData($data)
    {
        $value_id = $data['value_id'];
        $start_date = $data['date_from'];
        $end_date = $data['date_to'];

        return $this->_db->fetchAll("SELECT migachat_bridge_api.*
                                            FROM migachat_bridge_api
                                            WHERE migachat_bridge_api.chat_id IN (
                                                SELECT DISTINCT migachat_bridge_api.chat_id
                                                FROM migachat_bridge_api
                                                JOIN migachat_bridge_api_chat_ids on migachat_bridge_api_chat_ids.chat_id = migachat_bridge_api.chat_id
                                                WHERE migachat_bridge_api.created_at >= '$start_date'
                                                    AND migachat_bridge_api.created_at <= '$end_date'
                                                    AND (migachat_bridge_api_chat_ids.user_name <> '' OR migachat_bridge_api_chat_ids.user_email <> '' OR  migachat_bridge_api_chat_ids.user_mobile  <> '')
                                            )
                                            AND migachat_bridge_api.value_id = $value_id
                                            ORDER BY migachat_bridge_api.chat_id ASC, migachat_bridge_api.created_at DESC
                                            ");
    }
}