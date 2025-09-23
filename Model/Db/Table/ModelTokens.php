<?php

class Migachat_Model_Db_Table_ModelTokens extends Core_Model_Db_Table
{
    protected $_name = "migachat_model_tokens"; //Database table name
    protected $_primary = "id"; //name of primary key column

    public function getSystemPromptTokens($value_id){
        $k8 = array('gpt-4', 'gpt-4-0613', 'gpt-4-0314', 'code-davinci-002');
        $k16 = array('gpt-3.5-turbo-16k', 'gpt-3.5-turbo-16k-0613');
        $k32 = array('gpt-4-32k', 'gpt-4-32k-0613', 'gpt-4-32k-0314', 'code-davinci-002');
        $k128 = array('gpt-4o-mini', 'gpt-4-vision-preview','chatgpt-4o-latest','gpt-4o-mini-2024-07-18','gpt-4o-2024-08-06','gpt-4o-2024-05-13','gpt-4o');
        $token_limit_percent = 20;
        $complete_tokens = 4000;

        // get model name
        $gpt_model = 'gpt-4o-mini';
        $app_setting_obj = new Migachat_Model_PromptSettings();
        $app_setting_obj->find([
            'value_id' => $value_id
        ]);

        if ($app_setting_obj->getGptModel()) {
            $gpt_model = $app_setting_obj->getGptModel();
        }
        if (in_array($gpt_model,$k8)) {
            $complete_tokens = 8000;
        }elseif (in_array($gpt_model,$k16)) {
            $complete_tokens = 16000;
        }elseif(in_array($gpt_model,$k32)) {
            $complete_tokens = 32000;
        }elseif(in_array($gpt_model,$k128)) {
            $complete_tokens = 128000;
        }else {
            $complete_tokens = 4000;
        }
        // model tokens
        $models_obj = (new Migachat_Model_ModelTokens())->find(['model_name'=>$gpt_model]);
        if($models_obj->getId()){
            $complete_tokens = $models_obj->getTokens();
        }

        // percent Limit
        $chat_bot_obj = (new Migachat_Model_ChatbotSettings())->find(['value_id'=> $value_id]);
        if($chat_bot_obj->getId()){
            $token_limit_percent = $chat_bot_obj->getSystemPromptTokens();
        }
        $system_prompt_tokens = (($token_limit_percent)/100)*($complete_tokens-500);
        return $system_prompt_tokens;
    }
    public function getHistoryTokens($value_id){
        $k8 = array('gpt-4', 'gpt-4-0613', 'gpt-4-0314', 'code-davinci-002');
        $k16 = array('gpt-3.5-turbo-16k', 'gpt-3.5-turbo-16k-0613');
        $k32 = array('gpt-4-32k', 'gpt-4-32k-0613', 'gpt-4-32k-0314', 'code-davinci-002');
        $k128 = array('gpt-4o-mini', 'gpt-4-vision-preview','chatgpt-4o-latest','gpt-4o-mini-2024-07-18','gpt-4o-2024-08-06','gpt-4o-2024-05-13','gpt-4o');
        $token_limit_percent = 75;
        $complete_tokens = 4000;
        $history_messages=15;
        // get model name
        $gpt_model = 'gpt-4o-mini';
        $app_setting_obj = new Migachat_Model_PromptSettings();
        $app_setting_obj->find([
            'value_id' => $value_id
        ]);
        if ($app_setting_obj->getGptModel()) {
            $gpt_model = $app_setting_obj->getGptModel();
        }
        if (in_array($gpt_model,$k8)) {
            $complete_tokens = 8000;
        }elseif (in_array($gpt_model,$k16)) {
            $complete_tokens = 16000;
        }elseif(in_array($gpt_model,$k32)) {
            $complete_tokens = 32000;
        }elseif(in_array($gpt_model,$k128)) {
            $complete_tokens = 128000;
        }else {
            $complete_tokens = 4000;
        }

        // model tokens
        $models_obj = (new Migachat_Model_ModelTokens())->find(['model_name'=>$gpt_model]);
        if($models_obj->getId()){
            $complete_tokens = $models_obj->getTokens();
        }

        // percent Limit
        $chat_bot_obj = (new Migachat_Model_ChatbotSettings())->find(['value_id'=> $value_id]);
        if($chat_bot_obj->getId()){
            $token_limit_percent = $chat_bot_obj->getHistoryTokens();
            $history_messages= $chat_bot_obj->getHistoryMessages();
        }
        $history_tokens = (($token_limit_percent)/100)*($complete_tokens-500);
        return [$history_tokens,$history_messages];
    }

}
