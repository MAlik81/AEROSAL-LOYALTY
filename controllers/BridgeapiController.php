<?php

class Migachat_BridgeapiController extends Application_Controller_Default
{

    public function getbridgeapichatlogsAction()
    {
        if ($received_request = $this->getRequest()->getPost()) {
            try {
                $errors   = '';
                $warnings = '';
                if (! $received_request['customer_id']) {
                    throw new Exception(p__("Migachat", 'Chat Id can not be empty.'));
                }
                $value_id  = $received_request['value_id'];    // Replace with the actual customer ID
                $chat_id   = $received_request['customer_id']; // Replace with the actual customer ID
                $date_from = $received_request['date_from'];   // Replace with the actual start date
                $date_to   = $received_request['date_to'];     // Replace with the actual end date

                $chat_ids = (new Migachat_Model_ModelChatIds())->find(['value_id' => $value_id, 'chat_id' => $chat_id]);

                $chatlogs_obj = new Migachat_Model_BridgeAPI();
                $chatlogs     = $chatlogs_obj->getAllChatLogs($value_id, $chat_id, $date_from, $date_to);

                $formatted_chatlogs = [];

                foreach ($chatlogs as $chatlog) {
                    if ($chatlog['message_content']) {
                        $formatted_chatlogs[] = [
                            "sender"    => ($chatlog['role'] == 'user') ? "user" : "bot",
                            "text"      => $chatlog['message_content'],
                            "date_time" => $chatlog['created_at'],
                            "tokens"    => ($chatlog['role'] == 'user') ? $chatlog['prompt_tokens'] : $chatlog['completion_tokens'],
                        ];
                    }
                }

                $total_messages = count($formatted_chatlogs); // Total filtered messages count
                $chat_id_data   = (new Migachat_Model_BridgeAPI)->getChatIDData($value_id, $chat_id);

                $chat_id_limit_obj = new Migachat_Model_BridgrapiChatLimits();
                $is_ai_turned_off  = $chat_id_limit_obj->find(['value_id' => $value_id, 'chat_id' => $chat_id, 'is_limit' => 0]);
                $temp_blaclist       = new Migachat_Model_TemporaryBlaclist();
                $is_temp_blaclist    = $temp_blaclist->find([
                    'value_id' => $value_id,
                    'chat_id'  => $chat_id,
                ]);

                $payload = [
                    "chatlogs"            => $formatted_chatlogs,
                    "total_messages"      => $total_messages,
                    "chat_id"             => $chat_id,
                    "name"                => $chat_ids->getUserName(),
                    "email"               => $chat_ids->getUserEmail(),
                    "mobile"              => $chat_ids->getUserMobile(),
                    "is_ai_turned_off"    => ($is_ai_turned_off->getId()) ? true : false,
                    "is_temp_blaclist"    => ($is_temp_blaclist->getId()) ? true : false,
                ];

            } catch (\Exception $e) {
                $payload = ['error' => true, 'message' => p__("Migachat", $e->getMessage())];
            }
        } else {
            $payload = ['error' => true, 'message' => p__("Migachat", 'An error occurred during process. Please try again later.')];
        }
        $this->_sendJson($payload);
    }
    public function downloadcsvAction()
    {

        if ($received_request = $this->getRequest()->getPost()) {
            try {

                $formatted_chatlogs = [];
                $errors             = '';
                $warnings           = '';
                if (! $received_request['value_id']) {
                    throw new Exception(p__("Migachat", 'Value id can not be empty.'));
                }
                $value_id = $received_request['value_id'];    // Replace with the actual customer ID
                $chat_id  = $received_request['customer_id']; // Replace with the actual customer ID
                if (empty($received_request['date_from'])) {
                    $date_from = date('Y-m-01 H:i:s');
                } else {
                    $date_from = $received_request['date_from'];
                }
                if (empty($received_request['date_to'])) {
                    $date_to = date('Y-m-31 H:i:s');
                } else {
                    $date_to = $received_request['date_to'];
                }
                // Replace with the actual start date
                // Replace with the actual end date

                $date_from    = date("Y-m-d 00:00:01", strtotime($date_from));
                $date_to      = date("Y-m-d 23:59:01", strtotime($date_to));
                $chatlogs_obj = new Migachat_Model_BridgeAPI();
                $chatlogs     = $chatlogs_obj->getAllChatLogs($value_id, $chat_id, $date_from, $date_to);

                $chat_ids = (new Migachat_Model_ModelChatIds())->find(['value_id' => $value_id, 'chat_id' => $chat_id]);

                $formatted_chatlogs[] = ['Chat Id', 'Name', 'email', 'mobile', 'Role', 'Message', 'Message Date', 'tokens'];
                foreach ($chatlogs as $chatlog) {
                    $formatted_message_content = str_replace(',', ' ', $chatlog['message_content']);
                    $formatted_message_content = str_replace('"', ' ', $formatted_message_content);
                    $formatted_chatlogs[]      = [
                        $chatlog['chat_id'],
                        $chat_ids->getUserName(),
                        $chat_ids->getUserEmail(),
                        $chat_ids->getUserMobile(),
                        ($chatlog['role'] == 'user') ? "USER" : "AGENT",
                        $chatlog['message_content'],
                        $chatlog['created_at'],
                        ($chatlog['role'] == 'user') ? $chatlog['prompt_tokens'] : $chatlog['completion_tokens'],
                    ];
                }

                $this->exportToCSV($formatted_chatlogs, 'customer_chat_logs.csv');

            } catch (\Exception $e) {
                $this->_sendJson($e);
            }
        } else {
            $this->_sendJson($received_request);
        }
        $this->_sendJson($formatted_chatlogs);

    }
    private function exportToCSV($dataArray, $filename)
    {

        // Open a file handle for writing to php://output (output stream).
        $output = fopen('php://output', 'w');

        // Write the headers from the CSV data.
        fputcsv($output, $dataArray[0]);

        // Loop through the array and write each row to the CSV file.
        for ($i = 1; $i < count($dataArray); $i++) {
            fputcsv($output, $dataArray[$i]);
        }
        // Set the Content-Type and Content-Disposition headers to force the download.
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Output the contents of the buffer
        fpassthru($output);

        // Close the file handle
        fclose($output);
        return true;
    }

    public function deletectchathistoryAction()
    {
        if ($received_request = $this->getRequest()->getPost()) {
            try {

                $errors   = '';
                $warnings = '';

                if (empty($received_request['value_id'])) {
                    throw new Exception(p__("Migachat", 'Value id can not be empty.'));
                }
                if (empty($received_request['customer_id'])) {
                    throw new Exception(p__("Migachat", 'Customer can not be empty.'));
                }
                $delete_type  = $received_request['delete_type'];
                $value_id     = $received_request['value_id'];
                $customer_id  = $received_request['customer_id'];
                $chatlogs_obj = new Migachat_Model_BridgeAPI();
                $chatlogs     = $chatlogs_obj->deleteChatLogs($value_id, $customer_id, $delete_type);
                $payload      = [
                    'success'   => true,
                    'message'   => p__("Migachat", 'Chat Deleted Successfully'),
                    'sent_data' => $received_request,
                ];
            } catch (\Exception $e) {
                $payload = ['error' => true, 'message' => p__("Migachat", $e->getMessage())];
            }
        } else {
            $payload = ['error' => true, 'message' => p__("Migachat", 'An error occurred during process. Please try again later.')];
        }
        $this->_sendJson($payload);
    }

    public function deleteinstancechathistoryAction()
    {
        if ($received_request = $this->getRequest()->getPost()) {
            try {

                $errors   = '';
                $warnings = '';

                if (empty($received_request['value_id'])) {
                    throw new Exception(p__("Migachat", 'Value id can not be empty.'));
                }
                $value_id     = $received_request['value_id'];
                $delete_type  = $received_request['delete_type'];
                $chatlogs_obj = new Migachat_Model_BridgeAPI();
                $chatlogs     = $chatlogs_obj->deleteInstanceChatLogs($value_id, $delete_type);
                $payload      = [
                    'success' => true,
                    'message' => p__("Migachat", 'API Chat Deleted Successfully'),
                ];
            } catch (\Exception $e) {
                $payload = ['error' => true, 'message' => p__("Migachat", $e->getMessage())];
            }
        } else {
            $payload = ['error' => true, 'message' => p__("Migachat", 'An error occurred during process. Please try again later.')];
        }
        $this->_sendJson($payload);
    }
    public function gettokensstatsAction()
    {
        if ($received_request = $this->getRequest()->getPost()) {
            try {

                $errors   = '';
                $warnings = '';

                if (empty($received_request['value_id'])) {
                    throw new Exception(p__("Migachat", 'Value id can not be empty.'));
                }
                $chatlogs_obj = new Migachat_Model_BridgeAPI();
                $chatlogs     = $chatlogs_obj->getChatTokenStats($received_request);
                $rep          = '';
                if ($chatlogs) {
                    $rep = $chatlogs[0];
                }
                $payload = [
                    'success'   => true,
                    'sent_data' => $received_request,
                    'chatlogs'  => $rep,
                ];
            } catch (\Exception $e) {
                $payload = ['error' => true, 'message' => p__("Migachat", $e->getMessage())];
            }
        } else {
            $payload = ['error' => true, 'message' => p__("Migachat", 'An error occurred during process. Please try again later.')];
        }
        $this->_sendJson($payload);
    }
    public function disablebridgeapiAction()
    {
        if ($received_request = $this->getRequest()->getPost()) {
            try {

                $errors   = '';
                $warnings = '';

                if (empty($received_request['value_id'])) {
                    throw new Exception(p__("Migachat", 'Value id can not be empty.'));
                }

                $received_request['created_at'] = date("Y-m-d H:i:s");
                $received_request['updated_at'] = date("Y-m-d H:i:s");

                $bridge_obj = new Migachat_Model_BridgeAPISettings();
                $bridge_obj->find([
                    'value_id' => $received_request['value_id'],
                ]);
                if ($bridge_obj->getId()) {
                    $received_request['migachat_app_setting_id'] = $bridge_obj->getId();
                    $received_request['disable_api']             = ($bridge_obj->getDisableApi()) ? 0 : 1;
                }
                $bridge_obj->addData($received_request)->save();

                $payload = [
                    'success' => true,
                    'message' => p__("Migachat", 'Settings saved successfully!'),
                ];
            } catch (\Exception $e) {
                $payload = ['error' => true, 'message' => p__("Migachat", $e->getMessage())];
            }
        } else {
            $payload = ['error' => true, 'message' => p__("Migachat", 'An error occurred during process. Please try again later.')];
        }
        $this->_sendJson($payload);
    }
    public function saveauthtokenAction()
    {
        if ($received_request = $this->getRequest()->getPost()) {
            try {

                $errors   = '';
                $warnings = '';

                if (empty($received_request['value_id'])) {
                    throw new Exception(p__("Migachat", 'Value id can not be empty.'));
                }
                if (empty($received_request['auth_token'])) {
                    throw new Exception(p__("Migachat", 'Authen token can not be empty.'));
                }
                $received_request['created_at'] = date("Y-m-d H:i:s");
                $received_request['updated_at'] = date("Y-m-d H:i:s");

                $bridge_obj = new Migachat_Model_BridgeAPISettings();
                $bridge_obj->find([
                    'value_id' => $received_request['value_id'],
                ]);
                if ($bridge_obj->getId()) {
                    $received_request['migachat_bridge_api_setting_id'] = $bridge_obj->getId();
                }
                $bridge_obj->addData($received_request)->save();

                $payload = [
                    'success' => true,
                    'message' => p__("Migachat", 'Settings saved successfully!'),
                ];
            } catch (\Exception $e) {
                $payload = ['error' => true, 'message' => p__("Migachat", $e->getMessage())];
            }
        } else {
            $payload = ['error' => true, 'message' => p__("Migachat", 'An error occurred during process. Please try again later.')];
        }
        $this->_sendJson($payload);
    }

    public function getapichatstatsAction()
    {
        if ($received_request = $this->getRequest()->getPost()) {
            try {

                $errors   = '';
                $warnings = '';

                if (empty($received_request['value_id'])) {
                    throw new Exception(p__("Migachat", 'Value id can not be empty.'));
                }
                if (empty($received_request['date_from'])) {
                    throw new Exception(p__("Migachat", 'Date from can not be empty.'));
                }
                if (empty($received_request['date_to'])) {
                    throw new Exception(p__("Migachat", 'Date to can not be empty.'));
                }
                if (strtotime($received_request['date_from']) > strtotime($received_request['date_to'])) {
                    throw new Exception(p__("Migachat", 'Date from can not be greater than date to'));
                }

                $chatlogs_obj = new Migachat_Model_BridgeAPI();
                $chatlogs     = $chatlogs_obj->getApiChatStats($received_request);
                $payload      = [
                    'success' => true,
                    'data'    => $chatlogs,
                ];
            } catch (\Exception $e) {
                $payload = ['error' => true, 'message' => p__("Migachat", $e->getMessage())];
            }
        } else {
            $payload = ['error' => true, 'message' => p__("Migachat", 'An error occurred during process. Please try again later.')];
        }
        $this->_sendJson($payload);
    }
    public function getapichatstatscsvdownloadAction()
    {
        if ($received_request = $this->getRequest()->getPost()) {
            try {

                $errors   = '';
                $warnings = '';

                if (empty($received_request['value_id'])) {
                    throw new Exception(p__("Migachat", 'Value id can not be empty.'));
                }
                if (empty($received_request['date_from'])) {
                    throw new Exception(p__("Migachat", 'Date from can not be empty.'));
                }
                if (empty($received_request['date_to'])) {
                    throw new Exception(p__("Migachat", 'Date to can not be empty.'));
                }
                if (strtotime($received_request['date_from']) > strtotime($received_request['date_to'])) {
                    throw new Exception(p__("Migachat", 'Date from can not be greater than date to'));
                }

                $received_request['date_from'] = date("Y-m-d 00:00:01", strtotime($received_request['date_from']));
                $received_request['date_to']   = date("Y-m-d 23:59:01", strtotime($received_request['date_to']));
                $chatlogs_obj                  = new Migachat_Model_BridgeAPI();
                $chatlogs                      = $chatlogs_obj->getApiChatStatsCsvData($received_request);

                $payload = [
                    'success' => true,
                    'data'    => $chatlogs,
                ];
            } catch (\Exception $e) {
                $payload = ['error' => true, 'message' => p__("Migachat", $e->getMessage())];
            }
        } else {
            $payload = ['error' => true, 'message' => p__("Migachat", 'An error occurred during process. Please try again later.')];
        }
        $this->_sendJson($payload);
    }

    public function resetprivacyconsentforallAction()
    {
        if ($received_request = $this->getRequest()->getPost()) {
            try {

                $errors   = '';
                $warnings = '';

                if (empty($received_request['value_id'])) {
                    throw new Exception(p__("Migachat", 'Value id can not be empty.'));
                }
                (new Migachat_Model_GDPR)->resetAll($received_request['value_id']);

                $payload = [
                    'success' => true,
                    'data'    => p__("Migachat", 'privacy collection process for all users restarted.'),
                ];
            } catch (\Exception $e) {
                $payload = ['error' => true, 'message' => p__("Migachat", $e->getMessage())];
            }
        } else {
            $payload = ['error' => true, 'message' => p__("Migachat", 'An error occurred during process. Please try again later.')];
        }
        $this->_sendJson($payload);
    }
    public function resetprivacyconsentforoneAction()
    {
        if ($received_request = $this->getRequest()->getPost()) {
            try {

                $errors   = '';
                $warnings = '';

                if (empty($received_request['value_id'])) {
                    throw new Exception(p__("Migachat", 'Value id can not be empty.'));
                }
                if (empty($received_request['chat_id'])) {
                    throw new Exception(p__("Migachat", 'Chat Id can not be empty.'));
                }
                (new Migachat_Model_GDPR)->resetOne($received_request['value_id'], $received_request['chat_id']);

                $payload = [
                    'success' => true,
                    'data'    => p__("Migachat", 'privacy collection process restarted for this user.'),
                ];
            } catch (\Exception $e) {
                $payload = ['error' => true, 'message' => p__("Migachat", $e->getMessage())];
            }
        } else {
            $payload = ['error' => true, 'message' => p__("Migachat", 'An error occurred during process. Please try again later.')];
        }
        $this->_sendJson($payload);
    }

    public function downloadcsvgdprAction()
    {

        if ($received_request = $this->getRequest()->getPost()) {
            try {

                $formatted_chatlogs = [];
                $errors             = '';
                $warnings           = '';
                if (! $received_request['value_id']) {
                    throw new Exception(p__("Migachat", 'Value id can not be empty.'));
                }
                $value_id = $received_request['value_id'];

                $chat_ids = (new Migachat_Model_ModelChatIds())->findAll(['value_id' => $value_id]);

                $formatted_chatlogs[] = ['ChatId', 'name', 'mobile', 'email', 'GdprConsent', 'GdprConsentExternal', 'GdprConsentDate', 'CommercialConsent', 'CommercialConsentExternal', 'CommercialConsentDate'];
                foreach ($chat_ids as $chat_id) {
                    $formatted_chatlogs[] = [
                        $chat_id['chat_id'],
                        $chat_id['user_name'],
                        $chat_id['user_mobile'],
                        $chat_id['user_email'],
                        ($chat_id['gdpr_consent'] == 1) ? true : false,
                        ($chat_id['gdpr_consent_external'] == 1) ? true : false,
                        $chat_id['gdpr_consent_timestamp'],
                        ($chat_id['commercial_consent'] == 1) ? true : false,
                        ($chat_id['commercial_consent_external'] == 1) ? true : false,
                        $chat_id['commercial_consent_timestamp'],
                    ];
                }

                $this->exportToCSV($formatted_chatlogs, 'customer_gdpr_consents.csv');

            } catch (\Exception $e) {
                $this->_sendJson($e);
            }
        } else {
            $this->_sendJson($received_request);

        }
        $this->_sendJson($formatted_chatlogs);

    }
    public function importapicsvchatsAction()
    {

        if ($received_request = $this->getRequest()->getPost()) {
            try {
                $value_id = $received_request['value_id'];
                // Check if a file was uploaded
                // Retrieve Chatbot settings
                $setting_obj = new Migachat_Model_ChatbotSettings();
                $setting_obj->find(['value_id' => $value_id]);

                if (isset($_FILES['api_chat_csv_file']) && is_uploaded_file($_FILES['api_chat_csv_file']['tmp_name'])) {
                    $file = $_FILES['api_chat_csv_file']['tmp_name'];

                    // Open the file
                    if (($handle = fopen($file, 'r')) !== false) {
                        $row           = 0; // To track row number for validations
                        $importedCount = 0; // Track the number of successfully imported rows
                        $temp          = [];
                        while (($data = fgetcsv($handle, 5000, ',')) !== false) {
                            $row++;
                            // Skip header row if necessary
                            if ($row == 1) {
                                continue;
                            }

                                                    // Validate the row data
                            if (count($data) < 2) { // Adjust based on the expected number of columns
                                throw new \Exception(p__("Migachat", "Invalid data format on row {$row}."));
                            }

                            // Example: Map columns and process data
                            $temp['chat_id']      = trim($data[0]);
                            $temp['name']         = trim($data[1]);
                            $temp['email']        = trim($data[2]);
                            $temp['mobile']       = trim($data[3]);
                            $temp['role']         = trim($data[4]);
                            $temp['message']      = trim($data[5]);
                            $temp['message_date'] = trim($data[6]);
                            $temp['token_used']   = 0;
                            $chat_id              = $temp['chat_id'];

                            $chat_id_obj         = new Migachat_Model_ModelChatIds();
                            $chat_id_data_exists = $chat_id_obj->find(['value_id' => $value_id, 'chat_id' => $chat_id]);
                            if (! $chat_id_data_exists->getId()) {
                                $temp['chat_id'] = str_replace("+", "", $temp['mobile']);
                                $temp['chat_id'] = str_replace(" ", "", $temp['mobile']);
                                $temp['chat_id'] = str_replace("-", "", $temp['mobile']);
                                $chat_id_data    = [
                                    'value_id'                     => $value_id,
                                    'chat_id'                      => $temp['chat_id'],
                                    'user_name'                    => $temp['name'],
                                    'user_email'                   => $temp['email'],
                                    'user_mobile'                  => $temp['mobile'],
                                    'gdpr_consent'                 => 0,
                                    'gdpr_consent_external'        => 0,
                                    'gdpr_consent_timestamp'       => null,
                                    'commercial_consent'           => 0,
                                    'commercial_consent_external'  => 0,
                                    'commercial_consent_timestamp' => null,
                                    'created_at'                   => date("Y-m-d H:i:s"),
                                    'updated_at'                   => date("Y-m-d H:i:s"),
                                ];
                                $chat_id_data['created_at'] = date('Y-m-d H:i:s');
                                $chat_id_data_exists        = null;
                                $chat_id_data_exists        = (new Migachat_Model_ModelChatIds())->addData($chat_id_data)->save();

                            }
                            $secret_key      = $setting_obj->getSecretKey();
                            $organization_id = $setting_obj->getOrganizationId();
                            if (empty($secret_key) || empty($organization_id)) {
                                throw new Exception(p__("Migachat", 'OpenAI API key or organization ID is not set.'));
                            }

                            $openai = new Migachat_Model_AssistantsGPTAPI($secret_key, $organization_id);

                            $thread_id = $chat_id_data_exists->getThreadId();
                            if (empty($thread_id) && $setting_obj->getUseAssistant() == "1") {
                                $meta_data               = [];
                                $meta_data['value_id']   = (string) $value_id;
                                $meta_data['chat_id']    = (string) $chat_id_data_exists->getChatId();
                                $meta_data['created_at'] = date('Y-m-d H:i:s');
                                $new_thread              = $openai->createThread($meta_data);

                                if (isset($new_thread['id']) && ! empty($new_thread['id'])) {
                                    $thread_id = $new_thread['id'];
                                    $chat_id_data_exists->setThreadId($thread_id)->save();
                                } else {
                                    throw new Exception(p__("Migachat", 'Failed to create a new thread. Please try again later.'));
                                }

                            }
                            $thread_role       = (strtolower($temp['role']) == 'user') ? 'user' : 'assistant';
                            $message           = $temp['message'];
                            $message_to_thread = $openai->addMessageToThread($thread_id, $thread_role, $message);
                            // dd($message_to_thread);
                            $chatlog_data = [
                                'value_id'           => $value_id,
                                'chat_id'            => $temp['chat_id'],
                                'chatbot_setting_id' => $setting_obj->getId(),
                                'role'               => strtolower($temp['role']),
                                'message_content'    => $temp['message'],
                                'completion_tokens'  => 0,
                                'total_tokens'       => 0,
                                'user_email'         => $temp['email'],
                                'user_name'          => $temp['name'],
                                'user_mobile'        => $temp['mobile'],
                                'is_sent'            => 1,
                                'channel'            => "CSV",
                                'has_error'          => 0,
                                'is_read'            => 1,
                                'error_description'  => "",
                                'max_token_exeed'    => 0,
                                'max_token_responce' => null,
                                'created_at'         => date("Y-m-d H:i:s"),
                                'updated_at'         => date("Y-m-d H:i:s"),
                            ];

                            $chatlogs_obj = new Migachat_Model_BridgeAPI();
                            $chatlogs_obj->addData($chatlog_data)->save();

                            $importedCount++;
                        }

                        fclose($handle);
                        // Success payload
                        $payload = [
                            'success' => true,
                            'message' => p__("Migachat", "{$importedCount} chats imported successfully."),
                        ];
                    } else {
                        throw new \Exception(p__("Migachat", "Unable to open the uploaded file."));
                    }
                } else {
                    throw new \Exception(p__("Migachat", "No file uploaded or invalid file."));
                }
            } catch (\Exception $e) {
                dd($e);
                $payload = [
                    'error'   => true,
                    'message' => p__("Migachat", $e->getMessage()),
                ];
            }
        } else {
            $payload = [
                'error'   => true,
                'message' => p__("Migachat", "An error occurred during the process. Please try again later."),
            ];
        }

        $this->_sendJson($payload);
    }
    // temproraryblacklist
    public function temproraryblacklistAction()
    {
        // type: "post",
        // data: {
        //   value_id: value_id,
        //   chat_id: chat_id
        // },

        if ($received_request = $this->getRequest()->getPost()) {
            try {
                $value_id = $received_request['value_id'];
                $chat_id  = $received_request['chat_id'];

                $setting_obj = new Migachat_Model_PromptSettings();
                $setting_obj->find([
                    'value_id' => $value_id,
                ]);
                $duration_in_hours = $setting_obj->getTemporaryBlacklistDuration();
                $chat_ids_obj      = new Migachat_Model_ModelChatIds();
                $chat_ids_obj->find([
                    'value_id' => $value_id,
                    'chat_id'  => $chat_id,
                ]);
                $temporary_blaclist = new Migachat_Model_TemporaryBlaclist();
                $temporary_blaclist->addData([
                    'value_id'    => $value_id,
                    'chat_id'     => $chat_id,
                    'user_mobile' => $chat_ids_obj->getUserMobile(),
                    'blocked_at'  => date("Y-m-d H:i:s"),
                    'unblock_at'  => date("Y-m-d H:i:s", strtotime("+$duration_in_hours hours")),
                    'created_at'  => date("Y-m-d H:i:s"),
                    'updated_at'  => date("Y-m-d H:i:s"),
                ])->save();
                $payload = [
                    'success' => true,
                    'message' => p__("Migachat", "Chat added to temporary blacklist successfully."),
                ];
            } catch (\Exception $e) {
                $payload = [
                    'error'   => true,
                    'message' => p__("Migachat", $e->getMessage()),
                ];
            }
        } else {
            $payload = [
                'error'   => true,
                'message' => p__("Migachat", "An error occurred during the process. Please try again later."),
            ];
        }

        $this->_sendJson($payload);

    }
    // getassistantinfo
    public function getassistantinfoAction()
    {
        if ($received_request = $this->getRequest()->getPost()) {
            try {
                $value_id     = $received_request['value_id'];
                $assistant_id = $received_request['assistant_id'];

                $assistants_obj = new Migachat_Model_Assistants();
                $assistant_info = $assistants_obj->find([
                    'value_id'     => $value_id,
                    'assistant_id' => $assistant_id,
                ]);

                if (! $assistant_info->getId()) {
                    throw new \Exception(p__("Migachat", "Assistant not found."));
                }
                $assistant_data = $assistant_info->getData();
                // dd($assistant_data, $assistant_info->getOpenaiFileIds(),json_decode($assistant_info->getOpenaiFileIds(), true));
                // get vector store IDs
                $vector_store_ids = [];
                if (! empty($assistant_data['openai_file_ids'])) {
                    $decodedIds = json_decode($assistant_data['openai_file_ids'], true);
                    if (is_array($decodedIds)) {
                        foreach ($decodedIds as $id) {
                            if (! empty($id) && is_string($id)) {
                                $vector_store_ids[] = $id;
                            }
                        }
                    } elseif (is_string($assistant_data['openai_file_ids'])) {
                        $vector_store_ids[] = $assistant_data['openai_file_ids'];
                    }
                }
                $chatbot_setting_obj = new Migachat_Model_ChatbotSettings();
                $chatbot_setting_obj->find(['value_id' => $value_id]);
                $secret_key      = $chatbot_setting_obj->getSecretKey();
                $organization_id = $chatbot_setting_obj->getOrganizationId();

                $openai = new Migachat_Model_AssistantsGPTAPI($secret_key, $organization_id);
                // Prepare file metadata
                $file_ids   = [];
                $file_items = [];
                $assistant_files_model = new Migachat_Model_AssistantFiles();
                $stored_files = $assistant_files_model->findAll(['assistant_id' => $assistant_id]);
                $stored_file_map = [];
                if ($stored_files) {
                    foreach ($stored_files as $stored_file) {
                        $stored_file_map[$stored_file->getOpenaiFileId()] = $stored_file->getOriginalName();
                    }
                }

                foreach ($vector_store_ids as $vector_store_id) {
                    if (empty($vector_store_id)) {
                        continue;
                    }

                    $files = $openai->listFilesInVectorStore($vector_store_id);
                    if (! isset($files['data']) || ! is_array($files['data'])) {
                        throw new \Exception(p__("Migachat", "Failed to retrieve files from OpenAI."));
                    }

                    foreach ($files['data'] as $file) {
                        $openai_file_id = $file['file_id'] ?? ($file['id'] ?? null);
                        if (empty($openai_file_id)) {
                            continue;
                        }

                        $file_ids[] = $openai_file_id;

                        $original_name = $stored_file_map[$openai_file_id] ?? null;

                        if (! $original_name) {
                            try {
                                $file_details = $openai->retrieveFile($openai_file_id);
                                if (isset($file_details['filename']) && $file_details['filename']) {
                                    $original_name = $file_details['filename'];
                                    $assistant_files_model->saveFileMetadata(
                                        $assistant_id,
                                        $vector_store_id,
                                        $openai_file_id,
                                        $original_name
                                    );
                                    $stored_file_map[$openai_file_id] = $original_name;
                                }
                            } catch (\Exception $e) {
                                $original_name = null;
                            }
                        }

                        $file_items[] = [
                            'file_id'              => $openai_file_id,
                            'vector_store_file_id' => $file['id'] ?? null,
                            'vector_store_id'      => $vector_store_id,
                            'original_name'        => $original_name ?: $openai_file_id,
                        ];
                    }
                }

                $vector_store_ids = array_values(array_unique($vector_store_ids));

                $payload = [
                    'success'              => true,
                    'data'                 => $assistant_data,
                    'file_ids'             => array_values(array_unique($file_ids)),
                    'files'                => $file_items,
                    'vector_store_ids'     => $vector_store_ids[0] ?? null,
                    'vector_store_id_list' => $vector_store_ids,
                ];
            } catch (\Exception $e) {
                $payload = [
                    'error'   => true,
                    'message' => p__("Migachat", $e->getMessage()),
                ];
            }
        } else {
            $payload = [
                'error'   => true,
                'message' => p__("Migachat", "An error occurred during the process. Please try again later."),
            ];
        }

        $this->_sendJson($payload);
    }
    // saveassistantsettings
    public function saveassistantsettingsAction()
    {
        if ($received_request = $this->getRequest()->getPost()) {
            try {
                // Step 1: Validate fields
                if (empty($received_request['value_id'])) {
                    throw new Exception(p__("Migachat", 'Value id cannot be empty.'));
                }

                if (empty($received_request['app_id'])) {
                    throw new Exception(p__("Migachat", 'Application id cannot be empty.'));
                }

                if (empty(trim($received_request['assistant_name']))) {
                    throw new Exception(p__("Migachat", 'Assistant name cannot be empty.'));
                }

                if (empty(trim($received_request['description']))) {
                    throw new Exception(p__("Migachat", 'Description cannot be empty.'));
                }

                if (empty(trim($received_request['instructions']))) {
                    throw new Exception(p__("Migachat", 'Instructions cannot be empty.'));
                }

                if (empty(trim($received_request['model']))) {
                    throw new Exception(p__("Migachat", 'Model cannot be empty.'));
                }

                if (! isset($received_request['temperature']) || ! is_numeric($received_request['temperature'])) {
                    $received_request['temperature'] = 0.7; // Default value for temperature
                }

                if (! isset($received_request['top_p']) || ! is_numeric($received_request['top_p'])) {
                    $received_request['top_p'] = 1.0; // Default value for top_p
                }
                // dd($received_request);
                // Step 2: Extract data
                $value_id     = $received_request['value_id'];
                $app_id       = $received_request['app_id'];
                $assistant_id = $received_request['assistant_id'];
                $name         = trim($received_request['assistant_name']);
                $description  = trim($received_request['description']);
                $instructions = trim($received_request['instructions']);
                $model        = trim($received_request['model']);
                $temperature  = floatval($received_request['temperature']);
                $top_p        = floatval($received_request['top_p']);
                
                $chatbot_setting_obj = new Migachat_Model_ChatbotSettings();
                $chatbot_setting_obj->find(['value_id' => $value_id]);
                $secret_key      = $chatbot_setting_obj->getSecretKey();
                $organization_id = $chatbot_setting_obj->getOrganizationId();

                $openai = new Migachat_Model_AssistantsGPTAPI($secret_key, $organization_id);

                $vector_store_ids = [];
                $existing_vector_store = $received_request['vector_store_ids'] ?? null;
                if (! empty($existing_vector_store) && is_string($existing_vector_store)) {
                    $vector_store_ids[] = $existing_vector_store;
                }
                $file_id = null;
                $uploadedFilesMetadata = [];

                // Step 3: Handle file upload to server & then to OpenAI
                if (isset($_FILES["attached_file"]["name"]) && ! empty($_FILES["attached_file"]["name"])) {
                    $file_name = $_FILES["attached_file"]["name"];
                    $ext       = pathinfo($file_name, PATHINFO_EXTENSION);
                    $filename  = uniqid() . time() . "." . $ext;
                    $source    = $_FILES["attached_file"]["tmp_name"];

                    $basePath = Core_Model_Directory::getBasePathTo("/images/application/" . $app_id);
                    @mkdir($basePath . "/features/migachat/files", 0775, true);
                    $targetPath = $basePath . "/features/migachat/files/" . $filename;

                    if (! move_uploaded_file($source, $targetPath)) {
                        throw new Exception(p__("Migachat", "Failed to upload file."));
                    }
                    // Validate file type
                    $finfo    = finfo_open(FILEINFO_MIME_TYPE);
                    $mimeType = finfo_file($finfo, $targetPath);
                    finfo_close($finfo);

                    $allowedTypes = ['application/pdf', 'text/plain'];
                    if (! in_array($mimeType, $allowedTypes)) {
                        throw new Exception(p__("Migachat", "Only PDF and text files are allowed. Detected: $mimeType"));
                    }

                    // Upload to OpenAI
                    $upload = $openai->uploadFile($targetPath, 'assistants');
                    // dd($upload);
                    if (! isset($upload['id'])) {
                        throw new Exception(p__("Migachat", "Failed to upload file to GPT."));
                    }

                    $file_id = $upload['id'];

                    // Create vector store
                    if (! empty($assistant_id)) {
                        // Fetch existing assistant to get vector store ID
                        $existingAssistant = $openai->getAssistant($assistant_id);

                        if (isset($existingAssistant['tool_resources']['file_search']['vector_store_ids'][0])) {
                            $existing_vector_store_id = $existingAssistant['tool_resources']['file_search']['vector_store_ids'][0];

                            // Append file to existing vector store
                            $addFile = $openai->addFileToVectorStore($existing_vector_store_id, $file_id);
                            if (! isset($addFile['id'])) {
                                throw new Exception(p__("Migachat", "Failed to add file to existing vector store."));
                            }

                            $vector_store_ids[] = $existing_vector_store_id;
                            $uploadedFilesMetadata[] = [
                                'vector_store_id' => $existing_vector_store_id,
                                'file_id'         => $file_id,
                                'original_name'   => $file_name,
                            ];
                        } else {
                            // No existing vector store found, create new
                            $vectorStore = $openai->createVectorStore(['file_ids' => [$file_id]]);
                            if (! isset($vectorStore['id'])) {
                                throw new Exception(p__("Migachat", "Failed to create vector store."));
                            }

                            $vector_store_ids[] = $vectorStore['id'];
                            $uploadedFilesMetadata[] = [
                                'vector_store_id' => $vectorStore['id'],
                                'file_id'         => $file_id,
                                'original_name'   => $file_name,
                            ];
                        }
                    } else {
                        // Creating new assistant, create new vector store
                        $vectorStore = $openai->createVectorStore(['file_ids' => [$file_id]]);
                        if (! isset($vectorStore['id'])) {
                            throw new Exception(p__("Migachat", "Failed to create vector store."));
                        }

                        $vector_store_ids[] = $vectorStore['id'];
                        $uploadedFilesMetadata[] = [
                            'vector_store_id' => $vectorStore['id'],
                            'file_id'         => $file_id,
                            'original_name'   => $file_name,
                        ];
                    }

                }
                // remove duplicate vector store IDs
                $vector_store_ids = array_values(array_unique($vector_store_ids));
                // Step 4: Assistant payload
                if (empty($vector_store_ids)) {
                  $assistantPayload = [
                    'name'           => $name,
                    'description'    => $description,
                    'instructions'   => $instructions,
                    'model'          => $model,
                    'temperature'    => $temperature,
                    'top_p'          => $top_p,
                  ];
                } else {
                    $assistantPayload = [
                        'name'           => $name,
                        'description'    => $description,
                        'instructions'   => $instructions,
                        'model'          => $model,
                        'temperature'    => $temperature,
                        'top_p'          => $top_p,
                        'tools'          => [["type" => "file_search"]],
                        'tool_resources' => [
                            'file_search' => ['vector_store_ids' => $vector_store_ids],
                        ],
                    ];
                }

                // Step 5: Create or update assistant
                if (empty($assistant_id) || $assistant_id === '0') {
                    $response     = $openai->createAssistant($assistantPayload);
                    $assistant_id = $response['id'] ?? null;
                } else {
                    $response = $openai->patchAssistant($assistant_id, $assistantPayload);

                }

                if ($assistant_id && ! empty($uploadedFilesMetadata)) {
                    $assistantFilesModel = new Migachat_Model_AssistantFiles();
                    foreach ($uploadedFilesMetadata as $metadata) {
                        $assistantFilesModel->saveFileMetadata(
                            $assistant_id,
                            $metadata['vector_store_id'] ?? null,
                            $metadata['file_id'] ?? null,
                            $metadata['original_name'] ?? null
                        );
                    }
                }
                // Migachat_Model_PromptSettings
                $migachat_setting_obj = new Migachat_Model_PromptSettings();
                $migachat_setting_obj->find(['value_id' => $value_id]);
                if (! $migachat_setting_obj->getId()) {
                    $migachat_setting_obj->setValueId($value_id);
                    $migachat_setting_obj->setAppId($app_id);

                }
                $migachat_setting_obj->setAssistantId($assistant_id);
                $migachat_setting_obj->setTemperature($temperature);
                $migachat_setting_obj->setTopP($top_p);
                $migachat_setting_obj->save();
                $payload = [
                    'success'         => true,
                    'message'         => p__("Migachat", "Assistant settings saved successfully."),
                    'assistant_id'    => $assistant_id,
                    'file_ids'        => $file_id,
                    'openai_response' => $response,
                    'assistantPayload' => $assistantPayload,
                    'vector_store_ids' => $vector_store_ids,
                ];
            } catch (Exception $e) {
                $payload = [
                    'error'   => true,
                    'message' => p__("Migachat", $e->getMessage()),
                ];
            }
        } else {
            $payload = [
                'error'   => true,
                'message' => p__("Migachat", "No data received."),
            ];
        }

        $this->_sendJson($payload);
    }
    // removefilefromvectorstore
    public function removefilefromvectorstoreAction()
    {
        if ($received_request = $this->getRequest()->getPost()) {
            try {
                $value_id = $received_request['value_id'];
                $assistant_id = $received_request['assistant_id'] ?? null;
                $file_id  = $received_request['file_id'];
                $vector_store_id = $received_request['vector_store_id'] ?? null;

                if (empty($value_id) || empty($file_id)) {
                    throw new Exception(p__("Migachat", 'Value ID and File ID cannot be empty.'));
                }

                $chatbot_setting_obj = new Migachat_Model_ChatbotSettings();
                $chatbot_setting_obj->find(['value_id' => $value_id]);
                $secret_key      = $chatbot_setting_obj->getSecretKey();
                $organization_id = $chatbot_setting_obj->getOrganizationId();

                $openai = new Migachat_Model_AssistantsGPTAPI($secret_key, $organization_id);
                // Remove file from vector store
                $response = $openai->deleteFileFromVectorStore($vector_store_id, $file_id);
                // dd($response);
                if (! isset($response['id'])) {
                    throw new Exception(p__("Migachat", "Failed to remove file from vector store."));
                }

                (new Migachat_Model_AssistantFiles())->deleteByFileId($file_id, $assistant_id);

                $payload = [
                    'success' => true,
                    'message' => p__("Migachat", "File removed from vector store successfully."),
                    'data'    => $response,
                ];
            } catch (\Exception $e) {
                $payload = [
                    'error'   => true,
                    'message' => p__("Migachat", $e->getMessage()),
                ];
            }
        } else {
            $payload = [
                'error'   => true,
                'message' => p__("Migachat", "No data received."),
            ];
        }

        $this->_sendJson($payload);
    }

}
