<?php

class Migastarter_Backoffice_MigastarterController extends Backoffice_Controller_Default
{

    public function loadAction()
    {
        $this->_sendJson([
            'title' => p__('Migastarter','Migastarter'),
            'icon' => 'fa fa-certificate'
        ]);
    }

    public function helpAction()
    {
        $help_url = "";
        $info_message = "";
        $success_message = "";
        $setting = new Migastarter_Model_Setting();
        $setting->find(1);
        $stored_help_url = $setting->getHelpUrl();
        if (!empty($stored_help_url)) {
            $help_url = $stored_help_url;
            $success_message = "You are using your custom help URL.";
        } else {
            $info_message = "You are using default help URL (https://www.migastone.com/migastarter).";
        }
        $this->_sendHtml([
            "help_url" => $help_url,
            "info_message" => p__('Migastarter',$info_message),
            "success_message" => p__('Migastarter',$success_message),
        ]);
    }

    public function savehelpAction()
    {
        if ($data = Siberian_Json::decode($this->getRequest()->getRawBody())) {
            try {
                if (empty($data["help_url"])) {
                    throw new Exception("Help URL cannot be empty.");
                }
                if (filter_var($data["help_url"], FILTER_VALIDATE_URL) === FALSE) {
                    throw new Exception("Help URL is not valid.");
                }
                $setting = new Migastarter_Model_Setting();
                $setting->find(1);
                if ($setting->getSettingId()) {
                    $data["setting_id"] = $setting->getSettingId();
                }
                $setting->setData($data)->save();
                $data = [
                    "success" => 1,
                    "message" => p__('Migastarter','Help URL updated successfully.')
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
}
