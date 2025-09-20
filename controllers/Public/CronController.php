<?php
/**
 * Class Migachat_Public_CronController
 */

class Migachat_Public_CronController extends Migachat_Controller_Default
{

    public function runAction()
    {

        $responce = Migachat_Model_Db_Table_Cron::__Cron();
        print_r($responce);
        $this->_sendJson($responce);
        exit;
        // return $resp;
        // exit;

    }

    public function croninfoAction()
    {

        $setting = new Migachat_Model_Setting();

        $cron_info = $setting->getCronInfo();

        $default = new Core_Model_Default();

        $url = $default->getBaseUrl() . "/migachat/public_cron/run";

        $html = [

            "last_error" => $cron_info[0]['last_error'],

            "last_error_date" => ($cron_info[0]['last_error_date'] != '0000-00-00 00:00:00') ? str_replace('-', '/', date('d-m-Y H:i:s', strtotime($cron_info[0]['last_error_date']))) : '',

            "last_trigger" => ($cron_info[0]['last_trigger'] != '0000-00-00 00:00:00') ? str_replace('-', '/', date('d-m-Y H:i:s', strtotime($cron_info[0]['last_trigger']))) : '',

            "last_success" => ($cron_info[0]['last_success'] != '0000-00-00 00:00:00') ? str_replace('-', '/', date('d-m-Y H:i:s', strtotime($cron_info[0]['last_success']))) : '',

            "last_fail" => ($cron_info[0]['last_fail'] != '0000-00-00 00:00:00') ? str_replace('-', '/', date('d-m-Y H:i:s', strtotime($cron_info[0]['last_fail']))) : '',

            "created_at" => ($cron_info[0]['created_at'] != '0000-00-00 00:00:00') ? str_replace('-', '/', date('d-m-Y H:i:s', strtotime($cron_info[0]['created_at']))) : '',

            "updated_at" => ($cron_info[0]['updated_at'] != '0000-00-00 00:00:00') ? str_replace('-', '/', date('d-m-Y H:i:s', strtotime($cron_info[0]['updated_at']))) : '',

            "info_message" => p__('Migachat', "The cron is set to run every 1 minute and if you want to run it now just click on the link above. If you want you can add this link to your own server cron."),

            "cron_message" => "",

            "url" => $url,

        ];



        $this->_sendJson($html);

    }

}