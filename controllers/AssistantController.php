<?php

class Migachat_AssistantController extends Application_Controller_Default
{

    public function getAssistantsAction()
    {
        $value_id = $this->getRequest()->getParam('value_id');
        $assistants = new Migachat_Model_Assistants();
        $assistants_gpt_api = new Migachat_Model_AssistantsGPTAPI();
        $chatbot_setting_obj = new Migachat_Model_ChatbotSettings();
        $chatbot_setting_obj->find([
            'value_id' => $value_id,
        ]);

    }

    
    
}