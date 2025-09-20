<?php

use onesignal\client\ApiException;
use Push2\Model\Onesignal\Scheduler;
use Siberian\Json;

class Migachat_Model_Db_Table_Cron extends Core_Model_Db_Table
{

    protected $_name = "migachat_cron_logs"; //Database table name
    protected $_primary = "id"; //name of primary key column

    public static function __Cron()
    {
        $ChatbotSettings = new Migachat_Model_Db_Table_Cron();
        $ChatbotSettings->handleTempBlacklisted();
        
        // $ChatbotSettings->notSentMessagesCron();
        // $ChatbotSettings->unreadMessagesCron();
        // $ChatbotSettings->updateCronStatus();
        
        $ChatbotSettings->deleteChatHistory();
        $ChatbotSettings->deleteForGDPR();
        $ChatbotSettings->createAssistants();
        return true;
        // this cron runs every minute i want a method in it that only initiates once every hour
       
    }
    public function createAssistants(){
        try {
            //get all migachat_app_settings where prompt_chatgpt_active is 1 and assistant_id is null
            $assistants = $this->_db->fetchAll("SELECT * FROM migachat_app_settings WHERE prompt_chatgpt_active = 1 AND assistant_id IS NULL");
            if ($assistants) {
                echo "<pre>";
                foreach ($assistants as $assistant) {
                    $app_id = $assistant['app_id'];
                    $value_id = $assistant['value_id'];
                    $system_prompt = ($assistant['system_prompt'])? $assistant['system_prompt'] : 'You are a helpful assistant. Answer the user\'s questions to the best of your ability.';
                    $gpt_model = $assistant['gpt_model'];
                    $chatbot_setting_obj = new Migachat_Model_ChatbotSettings();
                    $chatbot_setting_obj->find(['value_id' => $value_id]);
                    
                    if (!$chatbot_setting_obj->getId()) {
                        continue; // Skip if no chatbot settings found
                    }
                    if($chatbot_setting_obj->getUseAssistant() || $chatbot_setting_obj->getApiType() !== 'chatgpt') {
                        continue; // Skip if use_assistant is already set
                    }
                    
                    $secret_key      = $chatbot_setting_obj->getSecretKey();
                    $organization_id = $chatbot_setting_obj->getOrganizationId();
                    echo "<br>App ID: $app_id, Value ID: $value_id,  GPT Model: $gpt_model, Secret Key: $secret_key, Organization ID: $organization_id";
                    
                    $openai = new Migachat_Model_AssistantsGPTAPI($secret_key, $organization_id);
                    $vector_store_ids = [];
                    $assistantPayload = [
                        'name'           => __get('main_domain').'-App ID:'.$app_id.'-Value ID: '.$value_id,
                        'description'    => __get('main_domain').'-App ID:'.$app_id.'-Value ID: '.$value_id,
                        'instructions'   => $system_prompt,
                        'model'          => $gpt_model,
                        'temperature'    => 0.7,
                        'top_p'          => 1,
                        'tools'          => [["type" => "file_search"]],
                        'tool_resources' => [
                            'file_search' => ['vector_store_ids' => $vector_store_ids],
                        ],
                    ];
                    $response     = $openai->createAssistant($assistantPayload);
                    echo "<br>----------------------<br>";
                    $assistant_id = $response['id'] ?? null;
                    if (!$assistant_id) {
                        continue; // Skip if no assistant ID returned
                    }
                    // Update the migachat_app_settings with the assistant_id

                    $migachat_setting_obj = new Migachat_Model_PromptSettings();
                    $migachat_setting_obj->find(['value_id' => $value_id]);
                    $migachat_setting_obj->setAssistantId($assistant_id);
                    $migachat_setting_obj->setTemperature($temperature);
                    $migachat_setting_obj->setTopP($top_p);
                    $migachat_setting_obj->save();

                    $chatbot_setting_obj = new Migachat_Model_ChatbotSettings();
                    $chatbot_setting_obj->find(['value_id' => $value_id]);
                    if (!$chatbot_setting_obj->getId()) {
                        continue; // Skip if no chatbot settings found
                    }
                    // use_assistant
                    $chatbot_setting_obj->setUseAssistant(1);
                    $chatbot_setting_obj->setAssistantId($assistant_id);
                    $chatbot_setting_obj->save();
                }
            }
            return true;

        } catch (\Exception $e) {
            // dd($e);
           return $e->getMessage();
        }
    }
    public function deleteForGDPR()
    {

        $resp = array();
        $migachat_value_ids = $this->_db->fetchAll("SELECT value_id 
        FROM `application_option_value` 
        JOIN application_option ON application_option.option_id = application_option_value.option_id
        WHERE application_option.code = 'migachat';");
        foreach ($migachat_value_ids as $key => $value) {
            $value_id = $value['value_id'];
            $gdpr_settings = (new Migachat_Model_GDPR)->find(['value_id' => $value_id]);
            $reset_minutes = 60;
            if ($gdpr_settings->getId()){
                $reset_minutes = $gdpr_settings->getGdprReset();
                $timestamp = strtotime("-$reset_minutes minutes");
                $newDate = date('Y-m-d H:i:s', $timestamp);

                $data = array(
                    'user_name' => "",
                    'user_email' => "",
                    'user_mobile' => "",
                    'gdpr_consent' => 2,
                    'commercial_consent' => 2,
                );
                $where = ['created_at < ?' => $newDate,'gdpr_consent = ?'=>3,'value_id = ?'=>$value_id];
                return $this->_db->update('migachat_bridge_api_chat_ids', $data, $where);

            }else{
                $timestamp = strtotime("-$reset_minutes minutes");
                $newDate = date('Y-m-d H:i:s', $timestamp);

                $data = array(
                    'user_name' => "",
                    'user_email' => "",
                    'user_mobile' => "",
                );
                $where = ['created_at < ?' => $newDate,'gdpr_consent = ?'=>3,'value_id = ?'=>$value_id];
                return $this->_db->update('migachat_bridge_api_chat_ids', $data, $where);
            }
        }
        
        return $resp;
    }
    public function deleteChatHistory()
    {


        $setting = new Migachat_Model_Setting();
        $setting->find(1);
        $history_duration = $setting->getHistoryDuration();
        if (empty($history_duration) && $history_duration !== 0) {
            $history_duration = 3;
        }
        if ($history_duration !== 0) {
            $currentDate = date('Y-m-d'); // Get the current date in the 'YYYY-MM-DD' format

            // Define the number of months to subtract
            $monthsToSubtract = 3; // Change this to the desired number of months

            // Subtract the months from the current date
            $newDate = date('Y-m-d H:i:s', strtotime("-$monthsToSubtract months", strtotime($currentDate)));
            $resp[] = $this->_db->delete("migachat_chatlogs", ['created_at < ?' => $newDate]);
            $resp[] = $this->_db->delete("migachat_bridge_api", ['created_at < ?' => $newDate]);
        }

        return $resp;
    }
    public function updateCronStatus()
    {

        $data = array('cron_status' => 1);
        $where = array(
            $this->_db->quoteInto('cron_status = ?', 0)
        );
        return $this->_db->update('migachat_chatlogs', $data, $where);
    }
    public function notSentMessagesCron()
    {

        $not_sent_messages = $this->_db->fetchAll("SELECT *
        FROM migachat_chatlogs AS c1
        WHERE is_sent = 0  AND message_sent_received = 'sent' AND cron_status = 0
        AND NOT EXISTS (
          SELECT 1
          FROM migachat_chatlogs AS c2
          WHERE c2.customer_id = c1.customer_id
          AND c2.is_sent = 1
          AND c2.created_at > c1.created_at
        );");
        foreach ($not_sent_messages as $key => $value) {
            $value_id = $value['value_id'];
            $application = $this->_db->fetchAll("SELECT `app_id` FROM `application_option_value` WHERE `value_id`=$value_id");
            if ($application) {
                $app_id = $application[0]['app_id'];
                $message_responce = $this->sendPush([
                    'start_date' => date("Y-m-d H:i:s"),
                    'notification_title' => p__("Migachat", 'Message Not Sent!'),
                    'notification_text' => $value['message_content'],
                    'base_url' => 'https://' . __get('main_domain'),
                    'app_id' => $app_id,
                    'customer_id' => $value['customer_id'],
                    'value_id' => $value_id,
                ]);

                if ($message_responce['success']) {
                    $save_data = (new Migachat_Model_Cron())->setData([
                        'value_id' => $value_id,
                        'chat_log_id' => $value['migachat_chatlog_id'],
                        'cron_type' => 'Not Sent',
                        'status' => 1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'update_at' => date('Y-m-d H:i:s'),
                    ])->save();
                } else {
                    $save_data = (new Migachat_Model_Cron())->setData([
                        'value_id' => $value_id,
                        'chat_log_id' => $value['migachat_chatlog_id'],
                        'cron_type' => 'Not Sent',
                        'status' => 0,
                        'created_at' => date('Y-m-d H:i:s'),
                        'update_at' => date('Y-m-d H:i:s'),
                    ])->save();
                }
            }
        }
        return true;
    }
    public function unreadMessagesCron()
    {

        $new_unread_messages = $this->_db->fetchAll("SELECT l.*
        FROM `migachat_chatlogs` l
        JOIN (
            SELECT `customer_id`, MAX(`migachat_chatlog_id`) AS max_chatlog_id
            FROM `migachat_chatlogs`
            WHERE `is_read` = 0
              AND `message_sent_received` = 'received'
            GROUP BY `customer_id`
        ) m
        ON l.`customer_id` = m.`customer_id` AND l.`migachat_chatlog_id` = m.`max_chatlog_id`
        WHERE l.`is_read` = 0 AND l.`message_sent_received` = 'received' AND cron_status = 0
        ORDER BY l.`migachat_chatlog_id` DESC");
        foreach ($new_unread_messages as $key => $value) {
            $value_id = $value['value_id'];
            $application = $this->_db->fetchAll("SELECT `app_id` FROM `application_option_value` WHERE `value_id`=$value_id");
            if ($application) {
                $app_id = $application[0]['app_id'];
                $message_responce = $this->sendPush([
                    'start_date' => date("Y-m-d H:i:s"),
                    'notification_title' => p__("Migachat", 'Incomming Message'),
                    'notification_text' => $value['message_content'],
                    'base_url' => 'https://' . __get('main_domain'),
                    'app_id' => $app_id,
                    'customer_id' => $value['customer_id'],
                    'value_id' => $value_id,
                ]);

                if ($message_responce['success']) {
                    $save_data = (new Migachat_Model_Cron())->setData([
                        'value_id' => $value_id,
                        'chat_log_id' => $value['migachat_chatlog_id'],
                        'cron_type' => 'New Message',
                        'status' => 1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'update_at' => date('Y-m-d H:i:s'),
                    ])->save();
                } else {
                    $save_data = (new Migachat_Model_Cron())->setData([
                        'value_id' => $value_id,
                        'chat_log_id' => $value['migachat_chatlog_id'],
                        'cron_type' => 'New Message',
                        'status' => 0,
                        'created_at' => date('Y-m-d H:i:s'),
                        'update_at' => date('Y-m-d H:i:s'),
                    ])->save();
                }
            }
        }
        return true;
    }

    public function sendPush($data)
    {
        $responce = false;
        try {
            $push_message = $data['notification_text'];
            $push_title = $data['notification_title'];

            $version = Siberian_Version::VERSION;
            if ($version >= '5.0.0') {
                $new_push_reponse = $this->newPushSendingMethod($push_title, $push_message, $data);
                return $new_push_reponse;
            } else {
                // Old Method
                $message = new Push_Model_Message();
                $message->setData([
                    'type_id' => $message->getMessageType(),
                    'app_id' => $data['app_id'],
                    'send_to_all' => 0,
                    'send_to_specific_customer' => 1,
                    'base_url' => $data['base_url'],
                    'target_devices' => 'all',
                ]);
                $message->setTitle($push_title)->setText($push_message);
                $message->save();
                $message_id = $message->getId();

                $customer_message_data = array();
                $customer_message = new Push_Model_Customer_Message();
                $customer_message_data = [
                    "customer_id" => $data['customer_id'],
                    "message_id" => $message_id,
                ];
                $customer_message->setData($customer_message_data);
                $customer_message->save();
                if ($message_id) { //update the device and push status
                    $responce = ['success' => true, 'message_id' => $message_id];
                }
                if ($message->getMessageType() != Push_Model_Message::TYPE_PUSH) {
                    $message->updateStatus('delivered');
                }
            }

        } catch (Exception $e) {
            $responce = [
                'success' => false,
                'msg' => $e->getMessage(),
            ];
        }
        return $responce;
    }
    public function newPushSendingMethod($push_title, $push_message, $data)
    {
        try {

            $application = (new \Application_Model_Application())->find($data['app_id']);
            // Required for the Message
            $values = [
                'app_id' => $data['app_id'],
                // The application ID, required
                'value_id' => $data['value_id'],
                // The value ID, optional
                'title' => $push_title,
                // Required
                'body' => $push_message,
                // Required
                'send_after' => null,
                'delayed_option' => null,
                'delivery_time_of_day' => null,
                'is_for_module' => true,
                // If true, the message is linked to a module, push will not be listed in the admin
                'is_test' => false,
                // If true, the message is a test push, it will not be listed in the admin
                'open_feature' => false,
                // If true, the message will open a feature, it works with feature_id
                'feature_id' => null,
                // The feature ID, required if open_feature is true
                // 'big_picture'=>empty($data['cover']) ? null : '/' . $data['app_id'] . '/features/migaprontelevator/' . $data['cover']
            ];
            $scheduler = new Scheduler($application);
            $scheduler->buildMessageFromValues($values);
            $scheduler->sendToCustomer($data['customer_id']); // This part will automatically sets the player_id and is_individual to true
            $payload = [
                'success' => true,
                'message' => p__('push2', 'Push sent'),
            ];
            $responce = ['success' => true, 'message_id' => $scheduler->message->getId()];
            // $responce = ['success'=>true,'message_id'=>0];
        } catch (ApiException $e) {
            $body = Json::decode($e->getResponseBody());
            $payload = [
                'error' => true,
                'message' => "<b>[OneSignal]</b><br/>" . $body["errors"][0],
            ];
            $responce = ['success' => false];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
            $responce = ['success' => false];
        }
        // New Method;
        return $responce;
    }

    public function newMessagesPushLogs($value_id)
    {
        try {
            return $this->_db->fetchAll("SELECT  migachat_chatlogs.*,migachat_cron_logs.created_at,migachat_cron_logs.status,customer.firstname,customer.lastname
            FROM migachat_cron_logs
            JOIN migachat_chatlogs ON  migachat_chatlogs.migachat_chatlog_id = migachat_cron_logs.chat_log_id
            JOIN customer ON  customer.customer_id = migachat_chatlogs.customer_id
            WHERE cron_type = 'New Message' 
            AND migachat_cron_logs.`value_id`=$value_id");
        } catch (Exception $th) {
            return $th;
        }
    }
    public function notSentMessagesPushLogs($value_id)
    {
        try {
            return $this->_db->fetchAll("SELECT  migachat_chatlogs.*,migachat_cron_logs.created_at,migachat_cron_logs.status,customer.firstname,customer.lastname,migachat_chatbot_settings.api_type
            FROM migachat_cron_logs
            JOIN migachat_chatlogs ON  migachat_chatlogs.migachat_chatlog_id = migachat_cron_logs.chat_log_id
            JOIN customer ON  customer.customer_id = migachat_chatlogs.customer_id
            JOIN migachat_chatbot_settings ON  migachat_chatbot_settings.migachat_chatbot_setting_id = migachat_chatlogs.chatbot_setting_id
            WHERE cron_type = 'Not Sent' AND  migachat_chatlogs.`value_id`=$value_id");
        } catch (Exception $th) {
            return $th;
        }

    }

    // handle temp blacklisted
    public function handleTempBlacklisted(){
        try {
            // Migachat_Model_TemporaryBlaclist
            // $temp_blaclist = $this->_db->fetchAll("SELECT * FROM migachat_temporary_block WHERE unblock_at < NOW()");
            return $this->_db->delete('migachat_temporary_block',['unblock_at < ?' => date('Y-m-d H:i:s')]);
        } catch (Exception $th) {
            // dd($th);
            return $th;
        }

    }
}