<?php
/**
 *  * Class Migastarter_Public_LicenseController
 *  */
class Migastarter_Public_LicenseController extends Migastarter_Controller_Default {

    public $module = 'Migastarter';
    
    public function validateAction() {
        $this->_sendHtml($this->_validatePlatformLicense());
    }

    private function _validatePlatformLicense() {
        $base_url = "";
        $activated = "";
        $expiry = "";
        $days = 0;
        $info_message = "";
        $success_message = "";
        $is_platform = false;
        $base = Core_Model_Directory::getBasePathTo("app/local/modules/" . $this->module);
        if(file_exists($base ."/license.json")) {
            $is_platform = true;
            $license = $this->_readLicense();
            $base_url = $license['base_url'];
            $activated = $license['activated'];
            $expiry = $license['expiry'];
            
            if($this->_isExpired($expiry)) {
                $info_message = "Your license has been expired on " . $expiry . "(READ ONLY ACTIVATED).";
            } else if (Zend_Controller_Front::getInstance()->getBaseUrl() != $base_url) { //this else if is added in 2.0
				$info_message = "The license is invalid and cannot be used on this domain.";
			} else {
                $days = $this->_dateDays($activated, $expiry);
                $success_message = 'Your license is active on the domain ' . $base_url . ' for ' . $days . ' days from ' . $activated . ' to ' . $expiry . '.';
            }
        } else {
            $info_message = "No license found for this module.";
        }

        return [
            "title" => p__('Migastarter',"Migastarter"),
            "icon" => "fa fa-certificate",
            "base_url" => $base_url,
            "activated" => $activated,
            "expiry" => $expiry,
            "days" => $days,
            "is_platform" => $is_platform,
            "info_message" => p__('Migastarter',$info_message),
            "success_message" => p__('Migastarter',$success_message),
        ];
    }

    private function _readLicense() {
        $base = Core_Model_Directory::getBasePathTo("app/local/modules/" . $this->module);
        
        $myfile = fopen($base . "/license.json", "r") or die("Unable to open file!");
        $license = json_decode(fread($myfile,filesize($base . "/license.json")),true);
        fclose($myfile);

        return [
            'base_url' => base64_decode(base64_decode($license['base_url'])),
            'activated' => base64_decode(base64_decode($license['activated'])),
            'expiry' => base64_decode(base64_decode($license['expiry'])),
            'key' => base64_decode(base64_decode($license['license']))
        ];
    }

    private function _isExpired($expiry) {
        $today = date('Y-m-d');
        $expiry = date('Y-m-d', strtotime($expiry));
        $today_time = strtotime($today);
        $expiry_time = strtotime($expiry);
        return ($today_time > $expiry_time) ? true : false;
    }

    private function _dateDays($start, $end) {
        $start = date_create($start);
        $end = date_create($end);
        $diff = date_diff($start, $end);
        return $diff->format("%a");
    }

    public function saveAction() {

        $this->_sendHtml([
                'license_type' => 'platform'
            ]);

        if($data = Siberian_Json::decode($this->getRequest()->getRawBody())) {
            try {
                if(empty($data["key"])) {
                    throw new Exception("License key cannot be empty.");
                }
                if(strlen($data["key"]) < 36 || substr_count ($data["key"], '-') < 1) {
                    throw new Exception("License key is invalid.");
                }
                $message = "";
                $base = Core_Model_Directory::getBasePathTo("app/local/modules/" . $this->module);
                if(file_exists($base . "/package.json")) {
                    
                    $myfile = fopen($base . "/package.json", "r") or die("Unable to open file!");
                    $package = json_decode(fread($myfile,filesize($base . "/package.json")),true);
                    fclose($myfile);


                    $main_domain =  __get('main_domain');
                    if (!preg_match("~^(?:f|ht)tps?://~i", $main_domain)) {
        
                        // If not exist then add http
                        $main_domain = "https://" . $main_domain;
                    }
                    $content = [
                        'license' => $data["key"],
                        'base_url' => $main_domain,
                        'module' => $package['name'],
                        'version' => $package['version'],
                        'license_type' => 'platform'
                    ];
                    
                $curl = curl_init();

                curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://licenses.migastone.com/validate-license',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $content,
                CURLOPT_SSL_VERIFYPEER => FALSE,
                // CURLOPT_HTTPHEADER     => [
                //     'Content-Type: application/json; charset=utf-8' , 
                //     'X-Requested-With:XMLHttpRequest' ,  
                // ]
                CURLOPT_HTTPHEADER => array(
                    'Cookie: ci_session=a%3A5%3A%7Bs%3A10%3A%22session_id%22%3Bs%3A32%3A%224a1668506c81790b96e6b66e4367703c%22%3Bs%3A10%3A%22ip_address%22%3Bs%3A13%3A%22206.84.150.47%22%3Bs%3A10%3A%22user_agent%22%3Bb%3A0%3Bs%3A13%3A%22last_activity%22%3Bi%3A1638383477%3Bs%3A9%3A%22user_data%22%3Bs%3A0%3A%22%22%3B%7Da45436d5e65e096561f0551c5df2b186'
                ),
                ));

                $curl_response = curl_exec( $curl );
                $curl_error = curl_error( $curl );
                curl_close( $curl );
                $response = json_decode($curl_response);
                $curl_error = json_decode($curl_error);

                if ($curl_error) {
                    throw new Exception($curl_error);
                } else {
                    switch($response->success) {
                        case 1:
                        case 2:
                                $message = $response->message;
                                unset($response->success);
                                unset($response->message);
                                $fp = fopen($base . "/license.json", 'w+');
                                fwrite($fp, json_encode($response, JSON_PRETTY_PRINT));
                                fclose($fp);
                                if(file_exists($base . "/app_licenses.json")) {
                                    unlink($base ."/app_licenses.json");
                                }
                            break;
                        default:
                            throw new Exception($response->message);
                    }
                }
            }
            $data = [
                "success" => 1,
                "message" => p__('Migastarter',$message)
            ];
        } catch(Exception $e) {
            $data = [
                "error" => 1,
                "message" => p__('Migastarter',$e->getMessage()),
            ];
        }
        $this->_sendHtml($data);
        }
    }

    public function disableAction() {
        $message = "";
        $success = 0;
        if($token = $this->getRequest()->getParam('token')) {
            $license_type = $this->getRequest()->getParam('license_type');
            $base = Core_Model_Directory::getBasePathTo("app/local/modules/" . $this->module);
            if($license_type == 'platform') {
                if(file_exists($base ."/license.json")) {
                    $license = $this->_readLicense();
                    $base_url = base64_encode(base64_encode($license['base_url']));
                    $expiry = $license['expiry'];
                    if($base_url == $token) {
                        if($this->_isExpired($expiry)) {
                            unlink($base ."/license.json");
                            $message = "Module is expired and license is deleted.";
                            $success = 1;
                        } else {
                            $message = "Module is active.";
                        }
                    } else {
                        $message = "Invalid token.";
                    }
                } else {
                    $message = "No license found for this module.";
                }
            } else {
                if(file_exists($base . "/app_licenses.json")) {
                    
                    $myfile = fopen($base . "/app_licenses.json", "r") or die("Unable to open file!");
                    $app_licenses = json_decode(fread($myfile,filesize($base . "/app_licenses.json")),true);
                    fclose($myfile);

                    if(count($app_licenses)) {
                        $app_id = $this->getRequest()->getParam('app_id');
                        foreach($app_licenses as $app_license_key => $app_license) {
                            if($app_id == base64_decode(base64_decode($app_license['app_id']))) {
                                $base_url = base64_decode(base64_decode($app_license['base_url']));
                                $expiry = base64_decode(base64_decode($app_license['expiry']));
                                if($base_url == $token) {
                                    if($this->_isExpired($expiry)) {
                                        $info_message = "Your license has been expired on " . $expiry . "(READ ONLY ACTIVATED).";
                                    } else if (Zend_Controller_Front::getInstance()->getBaseUrl() != $base_url) { //this else if is added in 2.0
                                        $info_message = "The license is invalid and cannot be used on this domain.";
                                    } else {
                                        $days = $this->_dateDays($activated, $expiry);
                                        $success_message = 'Your license is active on the domain ' . $base_url . ' for ' . $days . ' days from ' . $activated . ' to ' . $expiry . '.';
                                        $info_message = '';
                                    }
                                } else {
                                    $message = "Invalid app token.";
                                }
                            }
                        }
                        if($success) {
                            $json_data = json_encode($app_licenses, JSON_PRETTY_PRINT);
                            file_put_contents($base . "/app_licenses.json", $json_data);
                        }
                    }
                } else {
                    $message = "No app license found for this module.";
                }
            }
        } else {
            $message = "No token sent.";
        }

        $this->_sendHtml([
            "success" => $success,
            "message" => p__('Migastarter',$message),
        ]);
    }

    public function validateapplicenseAction() {
        if ($app_id = $this->getRequest()->getParam('app_id')) {
            $license = $this->_validatePlatformLicense();
            if(!$license['is_platform']) {
                
                $info_message = 'No license found for this module.';
                $success_message = '';
                $base = Core_Model_Directory::getBasePathTo("app/local/modules/" . $this->module);
                if(file_exists($base . "/app_licenses.json")) {
                    $myfile = fopen($base . "/app_licenses.json", "r") or die("Unable to open file!");
                    $app_licenses = json_decode(fread($myfile,filesize($base . "/app_licenses.json")),true);
                    fclose($myfile);


                    if(count($app_licenses)) {
                        foreach($app_licenses as $app_license_key => $app_license) {
                            $license_app_id = base64_decode(base64_decode($app_license['app_id']));
                            if($app_id == $license_app_id) {
                                $expiry = base64_decode(base64_decode($app_license['expiry']));
                                $base_url = base64_decode(base64_decode($app_license['base_url']));
                                $activated = base64_decode(base64_decode($app_license['activated']));
                                if($this->_isExpired($expiry)) {
                                    $info_message = "Your license has been expired on " . $expiry . "(READ ONLY ACTIVATED).";
                                } else {
                                    $days = $this->_dateDays($activated, $expiry);
                                    $success_message = 'Your license is active on the domain ' . $base_url . ' for ' . $days . ' days from ' . $activated . ' to ' . $expiry . '.';
                                    $info_message = '';
                                }
                                break;
                            }
                        }
                    }
                }
                $license = [
                    "is_platform" => false,
                    "info_message" => p__('Migastarter',$info_message),
                    "success_message" => p__('Migastarter',$success_message),
                ];
            }
        }
        $this->_sendHtml($license);
    }

    public function saveapplicenseAction() {
        
        if ($data = $this->getRequest()->getPost()) {
            try {
                if(empty($data["key"])) {
                    throw new Exception("License key cannot be empty.");
                }
                if(strlen($data["key"]) < 36 || substr_count ($data["key"], '-') < 1) {
                    throw new Exception("License key is invalid.");
                }
                

                $message = "";
                $base = Core_Model_Directory::getBasePathTo("app/local/modules/" . $this->module);
                if(file_exists($base . "/package.json")) {
                    // $package_file = file_get_contents($base . "/package.json");
                    // $package = json_decode($package_file, true);
                    
                    $myfile = fopen($base . "/package.json", "r") or die("Unable to open file!");
                    $package = json_decode(fread($myfile,filesize($base . "/package.json")),true);
                    fclose($myfile);
                    
   
                    $main_domain =  __get('main_domain');
                    if (!preg_match("~^(?:f|ht)tps?://~i", $main_domain)) {
        
                        // If not exist then add http
                        $main_domain = "https://" . $main_domain;
                    }

                    $content =  [
                        'license' => $data["key"],
                        'base_url' => $main_domain,
                        'module' => $package['name'],
                        'version' => $package['version'],
                        'license_type' => 'per-app',
                        'app_id' => $data['app_id']
                    ];
                
                $curl = curl_init();

                curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://licenses.migastone.com/validate-license',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $content,
                CURLOPT_SSL_VERIFYPEER => FALSE,
                ));

                $curl_response = curl_exec( $curl );
                $curl_error = curl_error( $curl );
                curl_close( $curl );
                $response = json_decode($curl_response);
                $curl_error = json_decode($curl_error);

                if ($curl_error) {
                     
                    throw new Exception($curl_error);
                } else {
                    
                        switch($response->success) {
                            case 1:
                            case 2:
                                    $message = $response->message;
                                    unset($response->success);
                                    unset($response->message);
                                    if(file_exists($base . "/app_licenses.json")) {
                                        // $app_licenses = json_decode(file_get_contents($base . "/app_licenses.json"));
                                        
                                        $myfile = fopen($base . "/app_licenses.json", "r") or die("Unable to open file!");
                                        $app_licenses = json_decode(fread($myfile,filesize($base . "/app_licenses.json")),true);
                                        fclose($myfile);

                                        if(count($app_licenses)) {
                                            foreach($app_licenses as $app_license_key => $app_license) {
                                                if($data['app_id'] == base64_decode(base64_decode($app_license->app_id))) {  
                                                    unset($app_licenses[$app_license_key]);
                                                }
                                            }
                                        }
                                        array_push($app_licenses, $response);
                                        $json_data = json_encode($app_licenses, JSON_PRETTY_PRINT);
                                        file_put_contents($base . "/app_licenses.json", $json_data);
                                    } else {
                                        $data_array[] = $response;
                                        $fp = fopen($base . "/app_licenses.json", 'w+');
                                        fwrite($fp, json_encode($data_array, JSON_PRETTY_PRINT));
                                        fclose($fp);
                                    }
                                break;
                            default:
                                throw new Exception($response->message);
                        }
                    }
                }
                $data = [
                    'success' => true,
                    "message" => p__('Migastarter',$message),
                    'message_timeout' => 0,
                    'message_button' => 0,
                    'message_loader' => 0
                ];
            } catch(Exception $e) {
                $data = [
                    'error' => true,
                    'message' => p__('Migastarter',$e->getMessage()),
                    'message_button' => 1,
                    'message_loader' => 1
                ];
            }
            $this->_sendJson($data);
        }
    }

    public function applicensesAction() {
        $info_message = '';
        $success_message = '';
        $base = Core_Model_Directory::getBasePathTo("app/local/modules/" . $this->module);
        if(file_exists($base . "/app_licenses.json")) {
            // $app_licenses = json_decode(file_get_contents($base . "/app_licenses.json"), true);

            $myfile = fopen($base . "/app_licenses.json", "r") or die("Unable to open file!");
            $app_licenses = json_decode(fread($myfile,filesize($base . "/app_licenses.json")),true);
            fclose($myfile);


            $data = [];
            if(count($app_licenses)) {
                foreach($app_licenses as $app_license_key => $app_license) {
                    $application = new Application_Model_Application();
                    $application->find(base64_decode(base64_decode($app_license['app_id'])));
                    $data[] = [
                        'app_id' => base64_decode(base64_decode($app_license['app_id'])),
                        'app_name' => $application->getName(),
                        'activated' => str_replace('-', '/', date('d-m-Y', strtotime(base64_decode(base64_decode($app_license['activated']))))),
                        'expiry' => str_replace('-', '/', date('d-m-Y', strtotime(base64_decode(base64_decode($app_license['expiry']))))),
                        'status' => $this->_isExpired(base64_decode(base64_decode($app_license['expiry']))) ? p__('Migastarter','Expired') : p__('Migastarter','Active'),
                    ];
                }
            }
        }
        $licenses = [
            "info_message" => p__('Migastarter',$info_message),
            "success_message" => p__('Migastarter',$success_message),
            "app_licenses" => $data,
        ];
        $this->_sendHtml($licenses);
    }
    
}