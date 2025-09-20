<?php

class Migachat_Model_Db_Table_Chatlogs extends Core_Model_Db_Table
{
    protected $_name = "migachat_chatlogs"; //Database table name
    protected $_primary = "migachat_chatlog_id"; //name of primary key column

    public function getChatLogs($value_id, $customer_id, $offset, $limit)
    {
        return $this->_db->fetchAll("SELECT * FROM (
            SELECT * FROM  $this->_name WHERE value_id = $value_id AND customer_id = $customer_id ORDER BY migachat_chatlog_id DESC LIMIT $offset,$limit
           ) AS $this->_name
             ORDER BY migachat_chatlog_id ASC");
    }

    public function getUserIdsWithChat($value_id)
    {
        return $this->_db->fetchAll("SELECT customer_id FROM $this->_name WHERE value_id = $value_id GROUP BY customer_id ORDER BY customer_id ASC");
    }
    public function getUnreadChatLogs($value_id, $customer_id)
    {
        return $this->_db->fetchAll("SELECT * FROM $this->_name WHERE value_id = $value_id AND customer_id = $customer_id AND is_read = 0 ORDER BY migachat_chatlog_id ASC");
    }

    public function markAsRead($value_id, $customer_id)
    {
        $data = array('is_read' => 1);
        $where = array(
            $this->_db->quoteInto('value_id = ?', $value_id),
            $this->_db->quoteInto('customer_id = ?', $customer_id),
            $this->_db->quoteInto('is_read = ?', 0)
        );
        return $this->_db->update($this->_name, $data, $where);
    }
    public function getAllChatLogs($value_id, $customer_id, $date_from, $date_to)
    {

        $where = "";
        if ($date_from) {
            $where .= " AND $this->_name.created_at >= '$date_from'  ";
        }
        if ($date_to) {
            $where .= " AND $this->_name.created_at <= '$date_to'  ";
        }
        return $this->_db->fetchAll("SELECT $this->_name.*,customer.email,customer.mobile FROM $this->_name 
                    JOIN customer on $this->_name.customer_id = customer.customer_id
                    WHERE $this->_name.value_id = $value_id 
                        AND $this->_name.customer_id = $customer_id 
                        $where
                    ORDER BY $this->_name.migachat_chatlog_id ASC");

    }
    public function getLastTenMessages($value_id, $customer_id,$limit)
    {
        return $this->_db->fetchAll("SELECT * FROM $this->_name WHERE value_id = $value_id AND customer_id = $customer_id ORDER BY created_at DESC LIMIT $limit");

    }

    public function getInstanceLogs($value_id, $customer_id)
    {
        $where = '';
        if ($customer_id) {
            $where .= ' AND customer_id=' . $customer_id . ' ';
        }
        return $this->_db->fetchAll("SELECT migachat_chatlog_id, value_id, customer_id, chatbot_setting_id, message_sent_received,
        CASE 
          WHEN message_sent_received = 'sent' THEN message_content
        END AS question,
          CASE 
          WHEN message_sent_received = 'received' THEN message_content
        END AS answer,
        created_at
      FROM migachat_chatlogs
      WHERE value_id = $value_id
      $where
      ORDER BY migachat_chatlog_id,created_at;");

    }
    public function deleteChatLogs($value_id, $customer_id)
    {
        $first_two_records = $this->_db->fetchAll("SELECT migachat_chatlog_id FROM migachat_chatlogs WHERE value_id = $value_id AND customer_id = $customer_id ORDER BY migachat_chatlog_id ASC limit 2");
        $first_two_ids = array_column($first_two_records, 'migachat_chatlog_id');

        return $this->_db->delete("migachat_chatlogs", ['customer_id = ?' => $customer_id, 'value_id = ?' => $value_id, 'migachat_chatlog_id NOT IN (?)' => $first_two_ids]);
    }
    
    public function deleteInstanceChatLogs($value_id)
    {

        $first_two_records_query = "SELECT `migachat_chatlog_id`
        FROM (
            SELECT `migachat_chatlog_id`, ROW_NUMBER() OVER (PARTITION BY customer_id ORDER BY `migachat_chatlog_id`) AS row_num
            FROM migachat_chatlogs
            WHERE value_id = $value_id
        ) AS ranked
        WHERE row_num <= 2";

        $first_two_records = $this->_db->fetchAll($first_two_records_query);

        $first_two_ids = array_column($first_two_records, 'migachat_chatlog_id');
        return $this->_db->delete("migachat_chatlogs", ['value_id = ?' => $value_id, 'migachat_chatlog_id NOT IN (?)' => $first_two_ids]);
    }
    public function getChatTokenStats($data)
    {
        $where = "";
        if (!empty($data['customer_id'])) {
            $where .= ' AND customer_id=' . $data['customer_id'] . ' ';
        }
        $value_id = $data['value_id'];
        $start_date = $data['from_date'];
        $end_date = $data['to_date'];
        return $this->_db->fetchAll("SELECT
        SUM(prompt_tokens) AS total_prompt_tokens,
        SUM(completion_tokens) AS total_completion_tokens,
        SUM(total_tokens) AS total_total_tokens
        FROM
            migachat_chatlogs
        WHERE
        created_at >= '" . $start_date . "' AND created_at <= '" . $end_date . "' AND value_id = $value_id
        $where");

    }


    public function getAppChatStats($data)
    {
        $value_id = $data['value_id'];
        $start_date = $data['date_from'];
        $end_date = $data['date_to'];
        $customers_count =  $this->_db->fetchAll("SELECT COUNT(DISTINCT customer_id) AS unique_customer_count
                                        FROM migachat_chatlogs
                                        WHERE created_at >= '$start_date'
                                            AND created_at <= '$end_date'
                                            AND value_id = $value_id");


        $chat_stats =  $this->_db->fetchAll("SELECT 
                        SUM(prompt_tokens) AS total_prompt_tokens,
                        SUM(completion_tokens) AS total_completion_tokens,
                        SUM(total_tokens) AS total_total_tokens
                        FROM migachat_chatlogs
                        WHERE customer_id IN (
                            SELECT DISTINCT customer_id
                            FROM migachat_chatlogs
                            WHERE created_at >= '$start_date'
                                AND created_at <= '$end_date'
                                AND value_id = $value_id
                        );");
        return ["unique_customer_count"=>$customers_count[0]['unique_customer_count'],"chat_stats"=>$chat_stats[0]];
    }

    public function getAppChatStatsCsvData($data)
    {
        $value_id = $data['value_id'];
        $start_date = $data['date_from'];
        $end_date = $data['date_to'];
        
        return  $this->_db->fetchAll("SELECT migachat_chatlogs.*,customer.firstname,customer.lastname,customer.email,customer.mobile
                                            FROM migachat_chatlogs
                                            JOIN customer ON customer.customer_id = migachat_chatlogs.customer_id
                                            WHERE migachat_chatlogs.customer_id IN (
                                                SELECT DISTINCT customer_id
                                                FROM migachat_chatlogs
                                                WHERE created_at >= '$start_date'
                                                    AND created_at <= '$end_date'
                                            )
                                            AND migachat_chatlogs.value_id = $value_id
                                            ORDER BY migachat_chatlogs.customer_id ASC, migachat_chatlogs.created_at DESC
                                            ");
    }
}