<?php

class Migachat_ApplicationController extends Application_Controller_Default
{

    public function apisconfigAction()
    {
        if ($received_request = $this->getRequest()->getPost()) {
            try {


                $errors = '';
                $warnings = '';
                if ($received_request['api_type'] == 'chatgpt') {
                    if (!$received_request['organization_id']) {
                        $errors .= p__("Migachat", 'Organization Id can not be empty.') . "<br/>";
                    }
                    if (!$received_request['secret_key']) {
                        $errors .= p__("Migachat", 'Secret key can not be empty.') . "<br/>";
                    }
                    if ($received_request['organization_id'] && $received_request['secret_key']) {
                        $api = new Migachat_Model_ChatGPTAPI(
                            'https://api.openai.com/v1/models',
                            $received_request['secret_key'],
                            $received_request['organization_id'],
                            'gpt-4o-mini'
                        );

                        // Get the available models
                        $models = $api->getModels();
                        // Output the model information
                        if ($models == false) {
                            $errors .= p__("Migachat", 'Chatgpt credentials are not correct. Please make sure to enter correct credentials.') . "<br/>";
                        }

                    }
                } else {
                    if (!$received_request['webhook_url']) {
                        $errors .= p__("Migachat", 'Webhook url can not be empty.') . "<br/>";
                    }
                    if (!$received_request['auth_token']) {
                        $errors .= p__("Migachat", 'Authentication token can not be empty.') . "<br/>";
                    }
                }


                if ($errors) {
                    throw new Exception($errors);
                }
                $received_request['created_at'] = date("Y-m-d H:i:s");
                $received_request['updated_at'] = date("Y-m-d H:i:s");

                $setting_obj = new Migachat_Model_ChatbotSettings();
                $setting_obj->find([
                    'value_id' => $received_request['value_id']
                ]);
                if ($setting_obj->getId()) {
                    $received_request['migachat_chatbot_setting_id'] = $setting_obj->getId();
                }
                $setting_obj->addData($received_request)->save();

                $payload = [
                    'success' => true,
                    'message' => p__("Migachat", 'Settings saved successfully!'),
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
    public function validateopenaicredsAction($api_key, $organizationId)
    {
        // Instantiate the ChatGPTAPI class
        $api = new Migachat_Model_ChatGPTAPI(
            'https://api.openai.com/v1/chat/completions',
            $api_key,
            $organizationId,
            'gpt-4o-mini'
        );

        // Generate a response
        $prompt = 'Hello, ChatGPT!';
        $response = $api->generateResponse($prompt);
        // Output the response
        if ($response[0] === true) {

            return true;
        } else {

            return false;
        }


    }
    public function getchatlogsAction()
    {
        if ($received_request = $this->getRequest()->getPost()) {
            try {


                $errors = '';
                $warnings = '';
                if (!$received_request['customer_id']) {
                    throw new Exception(p__("Migachat", 'Customer Id can not be empty.'));
                }
                $value_id = $received_request['value_id']; // Replace with the actual customer ID
                $customer_id = $received_request['customer_id']; // Replace with the actual customer ID
                $date_from = $received_request['date_from']; // Replace with the actual start date
                $date_to = $received_request['date_to']; // Replace with the actual end date
                $date_from = date("Y-m-d 00:00:01", strtotime($date_from));
                $date_to = date("Y-m-d 23:59:01", strtotime($date_to));

                $chatlogs_obj = new Migachat_Model_Chatlogs();
                $chatlogs = $chatlogs_obj->getAllChatLogs($value_id, $customer_id, $date_from, $date_to);
                // $payload = $chatlogs;
                $formatted_chatlogs = [];

                foreach ($chatlogs as $chatlog) {
                    $formatted_chatlogs[] = [
                        "sender" => ($chatlog['message_sent_received'] == 'sent') ? "user" : "bot",
                        "text" => $chatlog['message_content'],
                        "date_time" => $chatlog['created_at'],
                        "tokens" => ($chatlog['message_sent_received'] == 'sent') ? $chatlog['prompt_tokens'] : $chatlog['completion_tokens'],
                    ];
                }

                $total_messages = count($formatted_chatlogs); // Total filtered messages count

                $customer_obj = new Customer_Model_Customer();
                $customer_data = $customer_obj->find(['customer_id' => $customer_id]);
                $customer_name = $customer_data->getFirstname() . ' ' . $customer_data->getLastname();
                $payload = [
                    "chatlogs" => $formatted_chatlogs,
                    "total_messages" => $total_messages,
                    "customer_name" => $customer_name,
                    "email" => $customer_data->getEmail(),
                    "mobile" => $customer_data->getMobile()
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


                $formatted_chatlogs = array();
                $errors = '';
                $warnings = '';
                if (!$received_request['customer_id']) {
                    throw new Exception(p__("Migachat", 'Customer Id can not be empty.'));
                }
                $value_id = $received_request['value_id']; // Replace with the actual customer ID
                $customer_id = $received_request['customer_id']; // Replace with the actual customer ID
                $date_from = $received_request['date_from']; // Replace with the actual start date
                $date_to = $received_request['date_to']; // Replace with the actual end date

                $date_from = date("Y-m-d 00:00:01", strtotime($date_from));
                $date_to = date("Y-m-d 23:59:01", strtotime($date_to));
                $chatlogs_obj = new Migachat_Model_Chatlogs();
                $chatlogs = $chatlogs_obj->getAllChatLogs($value_id, $customer_id, $date_from, $date_to);

                $formatted_chatlogs[] = ['value_id', 'customer_id', 'Role', 'Message', 'Date', 'Email', 'Mobile'];
                foreach ($chatlogs as $chatlog) {

                    // Escape double quotes within the string and enclose the string in double quotes
                    $formatted_message_content = str_replace(',', ' ', $chatlog['message_content']);
                    $formatted_message_content = str_replace('"', ' ', $formatted_message_content);
                    $formatted_message_content = mb_convert_encoding($formatted_message_content, 'UTF-8', 'UTF-8');

                    $formatted_chatlogs[] = [
                        $chatlog['value_id'],
                        $chatlog['customer_id'],
                        ($chatlog['message_sent_received'] == 'sent') ? "USER" : "AGENT",
                        $formatted_message_content,
                        $chatlog['created_at'],
                        $chatlog['email'],
                        $chatlog['mobile']
                    ];
                }

                // $this->exportToCSV($formatted_chatlogs, 'customer_chat_logs.csv');

            } catch (\Exception $e) {
                $this->_sendJson($e->getMessage());
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

    public function appsettingsAction()
    {
        if ($received_request = $this->getRequest()->getPost()) {
            try {


                $errors = '';
                $warnings = '';
                if (empty($received_request['value_id'])) {
                    throw new Exception(p__("Migachat", 'Value id can not be empty.'));
                }
                // system prompt token limit

                $tokens_string = '';
                $model_Change = '';

                if (!empty($received_request['system_prompt'])) {

                    $value_id = $received_request['value_id'];
                    $system_prompt_token_limit = (new Migachat_Model_ModelTokens())->getSystemPromptTokens($value_id);

                    $system_prompt = $received_request['system_prompt'];
                    $setting_obj = new Migachat_Model_ChatbotSettings();
                    $setting_obj->find([
                        'value_id' => $value_id
                    ]);
                    if ($setting_obj->getId()) {
                        if ($setting_obj->getApiType() == 'chatgpt') {

                            $apiUrl = 'https://api.openai.com/v1/chat/completions';
                            $secret_key = $setting_obj->getSecretKey();
                            $organization_id = $setting_obj->getOrganizationId();

                            $gpt_model = 'gpt-4o-mini';
                            $chatAPI = new Migachat_Model_ChatGPTAPI($apiUrl, $secret_key, $organization_id, $gpt_model);

                            $all_conversation[] = array(
                                'role' => 'system',
                                'content' => 'Count the token of given prompt'
                            );

                            $response = $chatAPI->generateResponse($system_prompt, $all_conversation, 'admin', null);
                            $tokens = 0;
                            $english_translate = '<br> ' . p__("Migachat", ' If the prompt is not already in English you can translate it into English, for same prompt you will have less tokens.');
                            if ($response[0] === true) {
                                $tokens = $response[2] - 20;
                                $tokens_string = $tokens . '/' . $system_prompt_token_limit;
                                if ($tokens > $system_prompt_token_limit) {
                                    throw new Exception(p__("Migachat", 'System prompt exceeded tokens') . ' ' . $tokens_string . $english_translate);
                                }
                            } else {
                                $tokens = $this->countTokens($received_request['system_prompt']);
                                if ($tokens > $system_prompt_token_limit) {
                                    throw new Exception(p__("Migachat", 'System prompt exceeded tokens') . ' ' . $system_prompt_token_limit . $english_translate);
                                }
                            }
                            $received_request['system_prompt_tokens'] = $tokens;
                        }
                    }

                }
                if (isset($received_request['gpt_model']) && !empty($received_request['gpt_model'])) {
                    $model_Change = '<br> ' . p__("Migachat", " For consistant performance delete the chat archives after changing the GPT model.");
                }
                $app = $this->getApplication();
                $app_id = $app->getId();

                $received_request['app_id'] = $app_id;
                $received_request['created_at'] = date("Y-m-d H:i:s");
                $received_request['updated_at'] = date("Y-m-d H:i:s");

                $setting_obj = new Migachat_Model_PromptSettings();
                $setting_obj->find([
                    'value_id' => $received_request['value_id']
                ]);
                if ($setting_obj->getId()) {
                    $received_request['migachat_app_setting_id'] = $setting_obj->getId();
                }
                $setting_obj->addData($received_request)->save();

                $payload = [
                    'success' => true,
                    'message' => p__("Migachat", 'Settings saved successfully!') . ' ' . $tokens_string . ' ' . $model_Change,
                ];
            } catch (\Exception $e) {
                $payload = ['error' => true, 'message' => p__("Migachat", $e->getMessage())];
            }
        } else {
            $payload = ['error' => true, 'message' => p__("Migachat", 'An error occurred during process. Please try again later.')];
        }
        $this->_sendJson($payload);
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

    public function savebridgeapisettingsAction()
    {
        if ($received_request = $this->getRequest()->getPost()) {
            try {


                $errors = '';
                $warnings = '';

                if (empty($received_request['value_id'])) {
                    throw new Exception(p__("Migachat", 'Value id can not be empty.'));
                }
                if (empty($received_request['auth_token'])) {
                    throw new Exception(p__("Migachat", 'Authentication Token can not be empty.'));
                }



                $received_request['created_at'] = date("Y-m-d H:i:s");
                $received_request['updated_at'] = date("Y-m-d H:i:s");

                $bridge_obj = new Migachat_Model_BridgeAPISettings();
                $bridge_obj->find([
                    'value_id' => $received_request['value_id']
                ]);
                if ($bridge_obj->getId()) {
                    $received_request['migachat_bridge_api_setting_id'] = $bridge_obj->getId();
                }
                $bridge_obj->addData($received_request)->save();

                $payload = [
                    'success' => true,
                    'message' => p__("Migachat", 'Settings saved successfully!'),
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
    public function deletectchathistoryAction()
    {
        if ($received_request = $this->getRequest()->getPost()) {
            try {


                $errors = '';
                $warnings = '';
                if (empty($received_request['value_id'])) {
                    throw new Exception(p__("Migachat", 'Value id can not be empty.'));
                }
                if ($received_request['data_type'] == 2) {
                    if (empty($received_request['customer_id'])) {
                        throw new Exception(p__("Migachat", 'Customer can not be empty.'));
                    }
                    $value_id = $received_request['value_id'];
                    $customer_id = $received_request['customer_id'];
                    $chatlogs_obj = new Migachat_Model_Chatlogs();
                    $chatlogs = $chatlogs_obj->deleteChatLogs($value_id, $customer_id);
                }else if($received_request['data_type'] == 1) {
                    $value_id = $received_request['value_id'];
                    $chatlogs_obj = new Migachat_Model_Chatlogs();
                    $chatlogs = $chatlogs_obj->deleteInstanceChatLogs($value_id);
                }
                $payload = [
                    'success' => true,
                    'message' => p__("Migachat", 'App Chat Deleted Successfully'),
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
    public function gettokensstatsAction()
    {
        if ($received_request = $this->getRequest()->getPost()) {
            try {


                $errors = '';
                $warnings = '';

                if (empty($received_request['value_id'])) {
                    throw new Exception(p__("Migachat", 'Value id can not be empty.'));
                }
                $chatlogs_obj = new Migachat_Model_Chatlogs();
                $chatlogs = $chatlogs_obj->getChatTokenStats($received_request);
                $rep = '';
                if ($chatlogs) {
                    $rep = $chatlogs[0];
                }
                $payload = [
                    'success' => true,
                    'sent_data' => $received_request,
                    'chatlogs' => $rep,
                ];
            } catch (\Exception $e) {
                $payload = ['error' => true, 'message' => p__("Migachat", $e->getMessage())];
            }
        } else {
            $payload = ['error' => true, 'message' => p__("Migachat", 'An error occurred during process. Please try again later.')];
        }
        $this->_sendJson($payload);
    }
    public function getappchatstatsAction()
    {
        if ($received_request = $this->getRequest()->getPost()) {
            try {


                $errors = '';
                $warnings = '';

                if (empty($received_request['value_id'])) {
                    throw new Exception(p__("Migachat", 'Value id can not be empty.'));
                }
                if (empty($received_request['date_from'])) {
                    throw new Exception(p__("Migachat", 'Value id can not be empty.'));
                }
                if (empty($received_request['date_to'])) {
                    throw new Exception(p__("Migachat", 'Value id can not be empty.'));
                }
                if (strtotime($received_request['date_from']) > strtotime($received_request['date_to'])) {
                    throw new Exception(p__("Migachat", 'Date from can not be greater than date to'));
                }

                $received_request['date_from'] = date("Y-m-d 00:00:01", strtotime($received_request['date_from']));
                $received_request['date_to'] = date("Y-m-d 23:59:01", strtotime($received_request['date_to']));

                $chatlogs_obj = new Migachat_Model_Chatlogs();
                $chatlogs = $chatlogs_obj->getAppChatStats($received_request);
                $payload = [
                    'success' => true,
                    'data' => $chatlogs,
                ];
            } catch (\Exception $e) {
                $payload = ['error' => true, 'message' => p__("Migachat", $e->getMessage())];
            }
        } else {
            $payload = ['error' => true, 'message' => p__("Migachat", 'An error occurred during process. Please try again later.')];
        }
        $this->_sendJson($payload);
    }
    public function getappchatstatscsvdownloadAction()
    {
        if ($received_request = $this->getRequest()->getPost()) {
            try {


                $errors = '';
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
                $received_request['date_to'] = date("Y-m-d 23:59:01", strtotime($received_request['date_to']));

                $chatlogs_obj = new Migachat_Model_Chatlogs();
                $chatlogs = $chatlogs_obj->getAppChatStatsCsvData($received_request);

                $payload = [
                    'success' => true,
                    'data' => $chatlogs,
                ];
            } catch (\Exception $e) {
                $payload = ['error' => true, 'message' => p__("Migachat", $e->getMessage())];
            }
        } else {
            $payload = ['error' => true, 'message' => p__("Migachat", 'An error occurred during process. Please try again later.')];
        }
        $this->_sendJson($payload);
    }
    public function optimzepromptAction()
    {
        if ($received_request = $this->getRequest()->getPost()) {
            try {


                $errors = '';
                $warnings = '';

                if (empty($received_request['value_id'])) {
                    throw new Exception(p__("Migachat", 'Value id can not be empty.'));
                }
                if (empty($received_request['system_prompt'])) {
                    throw new Exception(p__("Migachat", 'System prompt can not be empty.'));
                }
                $value_id = $received_request['value_id'];
                $system_prompt = $received_request['system_prompt'];
                $setting_obj = new Migachat_Model_ChatbotSettings();
                $setting_obj->find([
                    'value_id' => $value_id
                ]);
                if ($setting_obj->getId()) {

                    if ($setting_obj->getApiType() == 'chatgpt') {


                        $apiUrl = 'https://api.openai.com/v1/chat/completions';
                        $secret_key = $setting_obj->getSecretKey();
                        $organization_id = $setting_obj->getOrganizationId();




                        $app_setting_obj = new Migachat_Model_PromptSettings();
                        $app_setting_obj->find([
                            'value_id' => $value_id
                        ]);
                        $gpt_model = 'gpt-4o-mini';
                        // if ($app_setting_obj->getGptModel()) {
                        //     $gpt_model = $app_setting_obj->getGptModel();
                        // }

                        $chatAPI = new Migachat_Model_ChatGPTAPI($apiUrl, $secret_key, $organization_id, $gpt_model);


                        $all_conversation[] = array(
                            'role' => 'system',
                            'content' => 'You are a helpful assistant. Optimize the given prompt for OpenAi chatGPT SYSTEM PROMPT with out losing the context and semantics. Do not translate, keep the orignal language.'
                        );
                        
                        $system_prompt_token_limit = (new Migachat_Model_ModelTokens())->getSystemPromptTokens($value_id);
                        $response = $chatAPI->generateResponse($system_prompt, $all_conversation, 'admin', $system_prompt_token_limit);
                        if ($response[0] === true) {
                            $response_msg = $response[1];
                            $tokens = $response[3];
                            $payload = [
                                'success' => true,
                                'data' => $response_msg,
                                'tokens' => $tokens,
                            ];
                        } else {
                            $payload = [
                                'success' => true,
                                'data' => $system_prompt,
                                'tokens' => $system_prompt_token_limit,
                            ];
                        }
                    }
                }
            } catch (\Exception $e) {
                $payload = ['error' => true, 'message' => p__("Migachat", $e->getMessage())];
            }
        } else {
            $payload = ['error' => true, 'message' => p__("Migachat", 'An error occurred during process. Please try again later.')];
        }
        $this->_sendJson($payload);
    }
    public function translatepromptAction()
    {
        if ($received_request = $this->getRequest()->getPost()) {
            try {


                $errors = '';
                $warnings = '';

                if (empty($received_request['value_id'])) {
                    throw new Exception(p__("Migachat", 'Value id can not be empty.'));
                }
                if (empty($received_request['system_prompt'])) {
                    throw new Exception(p__("Migachat", 'System prompt can not be empty.'));
                }
                $value_id = $received_request['value_id'];
                $system_prompt = $received_request['system_prompt'];
                $setting_obj = new Migachat_Model_ChatbotSettings();
                $setting_obj->find([
                    'value_id' => $value_id
                ]);
                if ($setting_obj->getId()) {

                    if ($setting_obj->getApiType() == 'chatgpt') {


                        $apiUrl = 'https://api.openai.com/v1/chat/completions';
                        $secret_key = $setting_obj->getSecretKey();
                        $organization_id = $setting_obj->getOrganizationId();




                        $app_setting_obj = new Migachat_Model_PromptSettings();
                        $app_setting_obj->find([
                            'value_id' => $value_id
                        ]);
                        $gpt_model = 'gpt-4o-mini';
                        // if ($app_setting_obj->getGptModel()) {
                        //     $gpt_model = $app_setting_obj->getGptModel();
                        // }

                        $chatAPI = new Migachat_Model_ChatGPTAPI($apiUrl, $secret_key, $organization_id, $gpt_model);


                        $all_conversation[] = array(
                            'role' => 'system',
                            'content' => 'You are a helpful assistant. translate the given text in English without losing the context and semantics.'
                        );
                        $system_prompt_token_limit = (new Migachat_Model_ModelTokens())->getSystemPromptTokens($value_id);
                        $response = $chatAPI->generateResponse($system_prompt, $all_conversation, 'admin', $system_prompt_token_limit);
                        if ($response[0] === true) {
                            $response_msg = $response[1];
                            $tokens = $response[3];
                            $payload = [
                                'success' => true,
                                'data' => $response_msg,
                                'tokens' => $tokens,
                            ];
                        } else {
                            $payload = [
                                'success' => true,
                                'data' => $system_prompt,
                                'tokens' => $system_prompt_token_limit,
                            ];
                        }
                    }
                }
            } catch (\Exception $e) {
                $payload = ['error' => true, 'message' => p__("Migachat", $e->getMessage())];
            }
        } else {
            $payload = ['error' => true, 'message' => p__("Migachat", 'An error occurred during process. Please try again later.')];
        }
        $this->_sendJson($payload);
    }
    public function savesystempromptoptimzedAction()
    {
        if ($received_request = $this->getRequest()->getPost()) {
            try {


                $errors = '';
                $warnings = '';

                if (empty($received_request['value_id'])) {
                    throw new Exception(p__("Migachat", 'Value id can not be empty.'));
                }
                if (empty($received_request['system_prompt'])) {
                    throw new Exception(p__("Migachat", 'System prompt can not be empty.'));
                }
                $value_id = $received_request['value_id'];
                $system_prompt = $received_request['system_prompt'];


                $app_setting_obj = new Migachat_Model_PromptSettings();
                $app_setting_obj->find([
                    'value_id' => $received_request['value_id']
                ]);

                $received_request['created_at'] = date("Y-m-d H:i:s");
                $received_request['updated_at'] = date("Y-m-d H:i:s");

                $setting_obj = new Migachat_Model_PromptSettings();
                $setting_obj->find([
                    'value_id' => $received_request['value_id']
                ]);
                if ($setting_obj->getId()) {
                    $received_request['migachat_app_setting_id'] = $setting_obj->getId();
                }
                $setting_obj->addData($received_request)->save();

                $payload = [
                    'success' => true,
                    'message' => p__("Migachat", 'Settings saved successfully!'),
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
    public function savecustommodeltokensAction()
    {
        if ($received_request = $this->getRequest()->getPost()) {
            try {
                
                $data = $received_request['tokens_limit'];
                foreach ($data as $key => $value) {
                    $model = $key;
                    $tokens = $value;
                    $models_obj = (new Migachat_Model_ModelTokens())->find(['model_name'=>$model]);
                    $model_data = array();
                    $model_data['model_name'] = $model;
                    $model_data['tokens'] = $tokens;
                    $model_data['created_at'] = date('Y-m-d H:i:s');
                    if ($models_obj->getId()) {
                        $model_data["id"] = $models_obj->getId();
                    }
                    $models_obj->setData($model_data)->save();
                }
                $payload = [
                    "success" => 1,
                    "data" => $received_request,
                    "message" => p__("Migachat", "data updated successfully.")
                ];
            } catch (Exception $e) {
                $payload = [
                    "error" => 1,
                    "message" => $e->getMessage()
                ];
            }
        } else {
            $payload = ['error' => true, 'message' => p__("Migachat", 'An error occurred during process. Please try again later.')];
        }
        $this->_sendJson($payload);
    }

    public function savegdprsettingsAction()
    {
        if ($received_request = $this->getRequest()->getPost()) {
            
            try {
                $value_id = $received_request['value_id'];

                if(empty($received_request['gdpr_active'])){
                    $received_request['gdpr_active'] = 'yes';
                }
                
                if(empty($received_request['commercial_active'])){
                    $received_request['commercial_active'] = 'no';
                }
                
                if(empty($received_request['gdpr_welcome_text'])){
                    $received_request['gdpr_welcome_text'] = 'Gentile utente, per poter scriverci ho bisogno che mi confermi di aver letto la nostra informativa sulla privacy che è reperibile al link qui sotto . Ti prego quindi di rispondere semplicemente con un “SI” a questo messaggio . Dear user, in order to write to us I need you to confirm that you have read our privacy policy which can be found at this link here below. Please therefore simply reply with a “YES” to this message. <br> LINK: <a target="blank" href="https://www.migastone.com/privacy">https://www.migastone.com/privacy</a>';
                }
                
                if(empty($received_request['gdpr_link'])){
                    $received_request['gdpr_link'] = 'https://www.migastone.com/privacy';
                }
                
                if(empty($received_request['gdpr_success_text'])){
                    $received_request['gdpr_success_text'] = 'Complimenti! Ora possiamo iniziare a chattare assieme. Compliments! Now we can start chatting together.';
                }
                
                if(empty($received_request['gdpr_failure_text'])){
                    $received_request['gdpr_failure_text'] = 'Mi spiace ma per poter procedere devi scrivere SI. I am sorry but to proceed you must write YES.';
                }
                
                if(empty($received_request['gdpr_reset'])){
                    $received_request['gdpr_reset'] = 60;
                }
                
                if(empty($received_request['commercial_welcome_text'])){
                    $received_request['commercial_welcome_text'] = 'Perfetto! Ora ho bisogno di raccogliere il tuo consenso per condividerti informazioni commerciali su questa chat o altri canali di comunicazione. Rispondi SI se rilasci il consenso oppure NO se non lo rilasci. / Perfect! Now I need to collect your consent to share commercial information with you on this chat or other communication channels. Answer YES if you give consent or NO if you don’t.';
                }
                $gdpr_settings = (new Migachat_Model_GDPR)->find(['value_id' => $value_id]);
               
                if ($gdpr_settings->getId()) {
                    $received_request['gdpr_id'] = $gdpr_settings->getId();
                }
                $gdpr_settings->addData($received_request)->save();
                $payload = [
                    "success" => 1,
                    "data" => $received_request,
                    "message" => p__("Migachat", "data updated successfully.")
                ];
            } catch (Exception $e) {
                $payload = [
                    "error" => 1,
                    "message" => $e->getMessage()
                ];
            }
        } else {
            $payload = ['error' => true, 'message' => p__("Migachat", 'An error occurred during process. Please try again later.')];
        }
        $this->_sendJson($payload);
    }
    public function getlogdetailsAction()
    {
        if ($received_request = $this->getRequest()->getPost()) {
            
            try {
                $value_id = $received_request['value_id'];
                $log_id = $received_request['log_id'];
                $log_data = (new Migachat_Model_Webservicelogs())->find($log_id);
                $question = $log_data->getMessage();
                $responce = $log_data->getResponce();
                $attached_request = unserialize($log_data->getRequest());
                
                $prepaired_string = "<h3>".p__("Migachat", 'User Prompt')."</h3>";
                $prepaired_string .= "<p>$question</p>";
                $prepaired_string .= "<h3>".p__("Migachat", 'AI Responce')."</h3>";
                $prepaired_string .= "<p>$responce</p>";
                $prepaired_string .= "<h3>".p__("Migachat", 'System Prompt')."</h3>";
                $prepaired_string .= "<p>".$attached_request[0]['content']."</p>";

                $prepaired_string .= "<h3>".p__("Migachat", 'History Attached')."</h3>";
                foreach ($attached_request as $key => $value) {
                    if ($key!=0) {
                        $prepaired_string .= "<p><b>".$value['role']." : </b> ".$value['content']."</p>";
                    }
                }
                if ($log_data->getHasError() == 1) {
                    $prepaired_string = $log_data->getErrorDescription();
                }
                $payload = [
                    "success" => 1,
                    "prepaired_string" => $prepaired_string,
                ];
            } catch (Exception $e) {
                $payload = [
                    "error" => 1,
                    "message" => $e->getMessage()
                ];
            }
        } else {
            $payload = ['error' => true, 'message' => p__("Migachat", 'An error occurred during process. Please try again later.')];
        }
        $this->_sendJson($payload);
    }

    public function saveoperatorsettingsAction()
    {
        if ($received_request = $this->getRequest()->getPost()) {
            try {
                $value_id = $received_request['value_id'];
                $operator_settings = (new Migachat_Model_OperatorSettings)->find(['value_id' => $value_id]);
                if ($operator_settings->getId()) {
                    $received_request['operator_id'] = $operator_settings->getId();
                }
                $received_request['created_at'] = date("Y-m-d H:i:s");
                $received_request['updated_at'] = date("Y-m-d H:i:s");
                $operator_settings->addData($received_request)->save();
                $payload = [
                    "success" => 1,
                    "data" => $received_request,
                    "message" => p__("Migachat", "data updated successfully.")
                ];
            } catch (Exception $e) {
                $payload = [
                    "error" => 1,
                    "message" => $e->getMessage()
                ];
            }
        } else {
            $payload = ['error' => true, 'message' => p__("Migachat", 'An error occurred during process. Please try again later.')];
        }
        $this->_sendJson($payload);
    }
    public function acceptedrequestAction()
    {
        if ($received_request = $this->getRequest()->getPost()) {
            try {
                $value_id = $received_request['value_id'];
                $rerquest_id = $received_request['rerquest_id'];
                $operator_settings = (new Migachat_Model_OperatorRequests)->find($rerquest_id)->setStatus('accepted')->save();
                $payload = [
                    "success" => 1,
                    "message" => p__("Migachat", "data updated successfully.")
                ];
            } catch (Exception $e) {
                $payload = [
                    "error" => 1,
                    "message" => $e->getMessage()
                ];
            }
        } else {
            $payload = ['error' => true, 'message' => p__("Migachat", 'An error occurred during process. Please try again later.')];
        }
        $this->_sendJson($payload);
    }
    public function checkwebhookAction()
    {
        if ($received_request = $this->getRequest()->getPost()) {
            try {
                $value_id = $received_request['value_id'];
                $webhook_url_operator = $received_request['webhook_url_operator'];

                $webhook_data = array();
                $webhook_data['app_id'] = 'test_request';
                $webhook_data['instance_id'] = 'test_request';
                $webhook_data['chat_type'] = 'test_request';
                $webhook_data['user_id'] = 'test_request';
                $webhook_data['status'] = 'test_request';
                $webhook_data['date_time'] = 'test_request';


                $webhook_data['app_name'] = 'test_request';
                $webhook_data['operator_request_id'] = 'test_request';
                $webhook_data['user_email'] = 'test_request';
                $webhook_data['user_mobile'] = 'test_request';
                $webhook_data['user_name'] = 'test_request';
                $webhook_data['last_five_history'] = 'test_request';

                // implement curl post request to to send $webhookdata
                $url = $webhook_url_operator;
                $ch = curl_init($url);
                $payload = json_encode($webhook_data);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);
                curl_close($ch);

                if($result === false) {
                    $payload = [
                        "error" => 1,
                        "message" => p__("Migachat", "Failed to send request.")
                    ];
                } else {
                    $payload = [
                        "success" => 1,
                        "message" => p__("Migachat", "Request sent successfully.")
                    ];
                }

                // $payload = [
                //     "success" => 1,
                //     "message" => p__("Migachat", "data updated successfully.")
                // ];
            } catch (Exception $e) {
                $payload = [
                    "error" => 1,
                    "message" => $e->getMessage()
                ];
            }
        } else {
            $payload = ['error' => true, 'message' => p__("Migachat", 'An error occurred during process. Please try again later.')];
        }
        $this->_sendJson($payload);
    }
    // saveblacklistconfig
    public function saveblacklistconfigAction()
    {
        if ($received_request = $this->getRequest()->getPost()) {
            try {
                $value_id = $received_request['value_id'];
                // temporary_blacklist_duration
                // permanent_blacklisted_mobile_numbers
                $received_request['created_at'] = date("Y-m-d H:i:s");
                $app_setting_obj = new Migachat_Model_PromptSettings();
                $app_setting_obj->find([
                    'value_id' => $value_id
                ]);
                if ($app_setting_obj->getId()) {
                    $received_request['migachat_app_setting_id'] = $app_setting_obj->getId();
                } 
                $app_setting_obj->setData($received_request)->save();
               
                $payload = [
                    "success" => 1,
                    "message" => p__("Migachat", "data updated successfully.")
                ];
            } catch (Exception $e) {
                $payload = [
                    "error" => 1,
                    "message" => $e->getMessage()
                ];
            }
        } else {
            $payload = ['error' => true, 'message' => p__("Migachat", 'An error occurred during process. Please try again later.')];
        }
        $this->_sendJson($payload);
    }
}