<?php

class Migachat_Backoffice_MigachatController extends Backoffice_Controller_Default
{

    public function loadAction()
    {
        $this->_sendJson([
            'title' => p__("Migachat", "Migachat"),
            'icon' => 'fa fa-certificate'
        ]);
    }

    public function helpAction()
    {
        $help_url = "";
        $info_message = "";
        $success_message = "";
        $setting = new Migachat_Model_Setting();
        $setting->find(1);
        $stored_help_url = $setting->getHelpUrl();
        if (!empty($stored_help_url)) {
            $help_url = $stored_help_url;
            $success_message = p__("Migachat", "You are using your custom help URL.");
        } else {
            $info_message = p__("Migachat", "You are using default help URL (https://www.migastone.com/migachat).");
        }
        $this->_sendHtml([
            "help_url" => $help_url,
            "info_message" => p__("Migachat", $info_message),
            "success_message" => p__("Migachat", $success_message),
        ]);
    }

    public function savehelpAction()
    {
        if ($data = Siberian_Json::decode($this->getRequest()->getRawBody())) {
            try {
                if (empty($data["help_url"])) {
                    throw new Exception(p__("Migachat", "Help URL cannot be empty."));
                }
                if (filter_var($data["help_url"], FILTER_VALIDATE_URL) === FALSE) {
                    throw new Exception(p__("Migachat", "Help URL is not valid."));
                }
                $setting = new Migachat_Model_Setting();
                $setting->find(1);
                if ($setting->getMigachatSettingId()) {
                    $data["migachat_setting_id"] = $setting->getMigachatSettingId();
                }
                $setting->setData($data)->save();
                $data = [
                    "success" => 1,
                    "message" => p__("Migachat", "Help URL updated successfully.")
                ];
            } catch (Exception $e) {
                $data = [
                    "error" => 1,
                    "message" => $e->getMessage()
                ];
            }
            $this->_sendHtml($data);
        }
    }
    public function loadhistorylimitAction()
    {
        $history_duration = "";
        $setting = new Migachat_Model_Setting();
        $setting->find(1);
        $history_duration = $setting->getHistoryDuration();
        if (empty($history_duration) || !$history_duration) {
            $history_duration = 3;
        }
        $this->_sendHtml([
            "history_duration" => $history_duration,
        ]);
    }
    public function savehistorylimitAction()
    {
        $data = array();
        if ($data = Siberian_Json::decode($this->getRequest()->getRawBody())) {
            try {
                if (empty($data["history_duration"])) {
                    throw new Exception(p__("Migachat", "History duration cannot be empty."));
                }
                $setting = new Migachat_Model_Setting();
                $setting->find(1);
                if ($setting->getMigachatSettingId()) {
                    $data["migachat_setting_id"] = $setting->getMigachatSettingId();
                }
                $setting->setData($data)->save();
                $data = [
                    "success" => 1,
                    "data" => $data,
                    "message" => p__("Migachat", "History duration updated successfully.")
                ];
            } catch (Exception $e) {
                $data = [
                    "error" => 1,
                    "message" => $e->getMessage()
                ];
            }
            $this->_sendHtml($data);
        }
        $this->_sendHtml($data);
    }
    public function loadblacklistednumbersAction()
    {
        $blacklisted_numbers = "";
        $setting = new Migachat_Model_Setting();
        $setting->find(1);
        $blacklisted_numbers = $setting->getBlacklistedNumbers();
        
        $this->_sendHtml([
            "blacklisted_numbers" => $blacklisted_numbers,
        ]);
    }
    public function saveblacklistednumbersAction()
    {
        $data = array();
        if ($data = Siberian_Json::decode($this->getRequest()->getRawBody())) {
            try {
                if (empty($data["blacklisted_numbers"])) {
                    $data["blacklisted_numbers"] = NULL;
                }
                $setting = new Migachat_Model_Setting();
                $setting->find(1);
                if ($setting->getMigachatSettingId()) {
                    $data["migachat_setting_id"] = $setting->getMigachatSettingId();
                }
                $setting->setData($data)->save();
                $data = [
                    "success" => 1,
                    "data" => $data,
                    "message" => p__("Migachat", "Blacklisted numbers updated successfully.")
                ];
            } catch (Exception $e) {
                $data = [
                    "error" => 1,
                    "message" => $e->getMessage()
                ];
            }
            $this->_sendHtml($data);
        }
        $this->_sendHtml($data);
    }
    public function loadgptmodelsAction()
    {
        $apiUrl = 'https://api.openai.com/v1/models';
        $vRbH = 'dummy';
        $ani = 'dummy';
        $chatAPI = new Migachat_Model_ChatGPTAPI($apiUrl, $vRbH, $ani, 'gpt-4o-mini');
        $gpt_models = $chatAPI->getModels();

        
        $k8 = array('gpt-4', 'gpt-4-0613', 'gpt-4-0314', 'code-davinci-002');
        $k16 = array('gpt-3.5-turbo-16k', 'gpt-3.5-turbo-16k-0613');
        $k32 = array('gpt-4-32k', 'gpt-4-32k-0613', 'gpt-4-32k-0314', 'code-davinci-002');
        $k128 = array('gpt-4-1106-preview', 'gpt-4-vision-preview','chatgpt-4o-latest','gpt-4o-mini-2024-07-18','gpt-4o-mini','gpt-4o-2024-08-06','gpt-4o-2024-05-13','gpt-4o');

        $model_limit = array();
        $open_ai_gpt_models = array();
        $openai = array('openai-dev','openai','openai-internal','system');
        foreach ($gpt_models as $key => $value) {
            $tokens_obj = new Migachat_Model_ModelTokens();
            $tokens = $tokens_obj->find([
                'model_name' => $value['id']
            ]);
            $limit = 0;
            if ($tokens->getId()) {
                $limit = intval($tokens->getTokens());
            }else{
                if (in_array($value['id'],$k8)) {
                    $limit = 8000;
                }elseif (in_array($value['id'],$k16)) {
                    $limit = 16000;
                }elseif(in_array($value['id'],$k32)) {
                    $limit = 32000;
                }elseif(in_array($value['id'],$k128)) {
                    $limit = 128000;
                }else {
                    $limit = 4000;
                }
            }
           
            $model_limit[$value['id']] = $limit;

            if (in_array($value['owned_by'],$openai)) {
                $open_ai_gpt_models[]['id']=$value['id'];
            }
        }
        $payload = array(
            'gpt_models' => $open_ai_gpt_models,
            'model_limit' => $model_limit);
        $this->_sendHtml($payload);
    }

    public function savegptmodelstokensAction()
    {
        $data = array();
        if ($data = Siberian_Json::decode($this->getRequest()->getRawBody())) {
            try {
                
                
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
                $data = [
                    "success" => 1,
                    "data" => $data,
                    "message" => p__("Migachat", "data updated successfully.")
                ];
            } catch (Exception $e) {
                $data = [
                    "error" => 1,
                    "message" => $e->getMessage()
                ];
            }
            $this->_sendHtml($data);
        }
        $this->_sendHtml($data);
    }
}
