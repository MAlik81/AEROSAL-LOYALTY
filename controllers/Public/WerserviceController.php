<?php
/**
 * Class Migachat_Public_CronController
 */

class Migachat_Public_WerserviceController extends Migachat_Controller_Default
{

    public function getchatlosAction()
    {

        $request = $this->getRequest();
        $params = $request->getParams();

        // Process the request
        // You can perform specific actions based on the received parameters

        // Check if required parameters exist
        $chat_logs = array();
        if (isset($params['instance_id'])) {
            $value_id = $params['instance_id'];
            $customer_id = false;
            if (isset($params['customer_id'])) {
                $customer_id = $params['customer_id'];
            }
            $chat_logs = (new Migachat_Model_Chatlogs())->getInstanceLogs($value_id, $customer_id);
            $response = [
                'status' => 'success',
                'data' => $chat_logs
            ];

            $this->_sendJson($response);
            exit;
        } else {
            $response = [
                'status' => 'fail',
                'message' => p__("Migachat", 'Instance id cannot be empty')
            ];

            $this->_sendJson($response);
            exit;
        }

    }
    public function webserviceAction()
    {

        try {
            $request = $this->getRequest();
            $params = $request->getParams();
           
            // Process the request
            // You can perform specific actions based on the received parameters

            // Check if required parameters exist
            $requiredParams = ['instance_id', 'message', 'auth_token'];
            $missing_params = "";
            $ws_log_data = array();
            foreach ($requiredParams as $param) {
                if (!isset($params[$param])) {
                    $missing_params .= " Missing required parameter: $param <br>";
                } else if (isset($params[$param])) {
                    $ws_log_data[$param] = $params[$param];
                }
            }
            
            if (!isset($params['customer_id']) && !isset($params['customer_email'])) {
                $missing_params .= " Missing required parameter: customer_id <br>";
                $missing_params .= " Missing required parameter: customer_email <br>";
            }
           
            if (!isset($params['customer_id']) && isset($params['customer_email'])) {
                $email = $params['customer_email'];

                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    echo "Valid email address";
                } else {
                    echo "Invalid email address";
                }

                $customer_obj = new Customer_Model_Customer();
                $customer_data = $customer_obj->find(['email' => $email]);
                if ($customer_data->getId()) {
                    $params['customer_id'] = $customer_data->getId();
                }else{
                    $response = [
                        'status' => 'failure',
                        'message' => p__("Migachat", 'Email does not belong to any customer.')
                    ];

                    $this->_sendJson($response);
                    exit;
                }
            }
            // Check the source of the request
           
            if ($missing_params !== "") {
                $ws_logs = new Migachat_Model_Webservicelogs();
                $ws_log_data['has_error'] = 1;
                $ws_log_data['error_description'] = $missing_params;
                $ws_log_data['platform'] = 'make.com';
                $ws_log_data['created_at'] = date("Y-m-d");
                $ws_logs->addData($ws_log_data)->save();
                $response = [
                    'status' => 'failure',
                    'message' => $missing_params
                ];
                // dd($response);
                return $this->_sendJson($response);
                exit;
            } else {
                // save data in chat logs 
                $value_id = $params['instance_id'];
                $message = $params['message'];
                $auth_token = $params['auth_token'];
                $setting_obj = new Migachat_Model_ChatbotSettings();
                $setting_obj->find([
                    'value_id' => $value_id
                ]);
                if ($setting_obj && $setting_obj->getAuthToken() == $auth_token) {

                    $chatlogs_obj = new Migachat_Model_Chatlogs();
                    $chatlog_data = array();
                    $chatlog_data['value_id'] = $value_id;
                    $chatlog_data['customer_id'] = $params['customer_id'];
                    $chatlog_data['chatbot_setting_id'] = $setting_obj->getId();
                    $chatlog_data['message_sent_received'] = 'received';
                    $messageContent = $message;
                    $first_image = $this->processFirstImages($messageContent);
                    $messageContent = $this->processCustomUrls($messageContent);
                    $messageContent = $this->processUrls($messageContent);
                    $messageContent = $this->processImages($messageContent);
                    $messageContent = $this->processPhoneNumbers($messageContent);
                    $chatlog_data['message_content'] = $first_image . ' <br> ' . $messageContent;
                    $chatlog_data['is_sent'] = 1;
                    $chatlog_data['is_read'] = 0;
                    $chatlog_data['has_error'] = 0;
                    $chatlog_data['error_description'] = "";
                    $chatlog_data['created_at'] = date("Y-m-d H:i:s");
                    $chatlog_data['updated_at'] = date("Y-m-d H:i:s");

                    $resp = $chatlogs_obj->addData($chatlog_data)->save();
                    // Return a response if necessary
                    // You can use JSON, XML, or any other format depending on your requirements
                    $response = [
                        'status' => 'success',
                        'customer_id' => $params['customer_id'],
                        'message' => p__("Migachat", 'Request processed successfully')
                    ];

                    $error_array = array();
                    $error_array['value_id'] = $value_id;
                    $error_array['has_error'] = 0;
                    $error_array['error_description'] = p__("Migachat", 'Request processed successfully');
                    $error_array['platform'] = 'Webservice';
                    $error_array['created_at'] = date("Y-m-d");
                    (new Migachat_Model_Webservicelogs())->addData($error_array)->save();

                    return $this->_sendJson($response);
                    exit;
                } else {
                    $response = [
                        'status' => 'failure',
                        'message' => p__("Migachat", 'Authentication failed')
                    ];

                    $error_array = array();
                    $error_array['value_id'] = $value_id;
                    $error_array['has_error'] = 1;
                    $error_array['error_description'] = p__("Migachat", 'Authentication failed');
                    $error_array['platform'] = 'Webservice';
                    $error_array['created_at'] = date("Y-m-d");
                    (new Migachat_Model_Webservicelogs())->addData($error_array)->save();

                    return $this->_sendJson($response);
                    exit;
                }

            }
        } catch (Exception $e) {
            return  $this->_sendJson($e->getMessage());
            // return $e->getMessage();
            exit;
        }
    }

    private function processCustomUrls($messageContent)
    {
        $pattern = '/\[([^|\]]+)\s*\|\s*([^|\]]+)\]/i';
        $replacement = "<a class='badge badge-calm' style='margin:2px;padding:2px;'  href='$1' target='_blank'> $2 <i class='icon ion-android-open'></i></a>";
        $messageContent = preg_replace($pattern, $replacement, $messageContent);
        return $messageContent;
    }
    function processUrls($messageContent)
    {
        $pattern = '/(?<!href=("|\'|,)|src=("|\'|,))(?!\bhttps?:\/\/\S+\.jpg\b|\bhttps?:\/\/\S+\.png\b|\bhttps?:\/\/\S+\.gif\b|\bhttps?:\/\/\S+\.jpeg\b|\bhttps?:\/\/\S+\.webp\b)\b(https?:\/\/\S+)\b/i';
        $replacement = "<br/><a href='$3' class='button button-sm button-calm' style='margin:2px;padding:2px;'  target='_blank'> Open Link <i class='icon ion-android-open'></i></a><br/>";
        $messageContent = preg_replace($pattern, $replacement, $messageContent);
        return $messageContent;
    }
    function processImages($messageContent)
    {
        $pattern = '/\b(https?:\/\/\S+\.(?:png|jpe?g|gif|webp))\b(?![\'"])/i';

        $firstImage = true; // Flag to track the first image
        $messageContent = preg_replace_callback($pattern, function ($matches) use (&$firstImage) {
            if ($firstImage) {
                $firstImage = false;
                // return "<a href='{$matches[0]}' class='' target='_blank'><img class='chat_image' src='{$matches[0]}' alt='Image'></a> <br> <a href='{$matches[0]}' target='_blank'>{$matches[0]}</a>";
                return "<a href='{$matches[0]}' target='_blank'>{$matches[0]} <i class='icon ion-android-open'></i></a>";
            } else {
                return "<a href='{$matches[0]}' target='_blank'>{$matches[0]} <i class='icon ion-android-open'></i></a>";
            }
        }, $messageContent);

        return $messageContent;
    }

    function processFirstImages($messageContent)
    {
        $pattern = '/\b(https?:\/\/\S+\.(?:png|jpe?g|gif|webp))\b(?![\'"])/i';

        $firstImageURL = null; // Variable to store the first image URL
        $messageContent = preg_replace_callback($pattern, function ($matches) use (&$firstImageURL) {
            if ($firstImageURL === null) {
                $firstImageURL = "<a href='{$matches[0]}' class='' target='_blank'><img class='chat_image' src='{$matches[0]}' alt='Image'></a><br> <a href='{$matches[0]}' target='_blank'>{$matches[0]} <i class='icon ion-android-open'></i></a>";
                return "<a href='{$matches[0]}' class='' target='_blank'><img class='chat_image' src='{$matches[0]}' alt='Image'></a>";
            }
        }, $messageContent);

        return $firstImageURL;
    }


    private function processPhoneNumbers($messageContent)
    {
        $pattern = '/\b\d{6,}\b/';
        $replacement = "<a class='button button-sm button-calm' style='margin:2px;padding:2px;'  href='tel:$0'>$0 <i class='icon ion-android-call'></i></a>";
        $messageContent = preg_replace($pattern, $replacement, $messageContent);
        return $messageContent;
    }

}