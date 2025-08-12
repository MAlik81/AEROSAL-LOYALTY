<?php
class Migastarter_Mobile_ViewController extends Application_Controller_Mobile_Default
{
    public function loadAction()
    {
        if ($value_id = $this->getRequest()->getParam('value_id')) {
        
            try {
                
                $categories_obj = new Migastarter_Model_Categories();
                $locations_obj = new Migastarter_Model_Locations();
                $adverts_obj = new Migastarter_Model_Adverts();

                $app_id  = $this->getApplication()->getId();
                // get all locations

                $default  = new Core_Model_Default();
                $base_url  = $default->getBaseUrl();
                $pre_image_path = $base_url."/images/application/".$this->getApplication()->getId()."/features/migastarter/";
                $locations = $locations = $locations_obj->findAll([
                    'app_id'   => $app_id,
                    'value_id' => $value_id,
                    'is_deleted' => 0,
                ])->toArray();


            $settings_obj = new Migastarter_Model_Appsettings();
            $settings = $settings_obj->find(['app_id' => $app_id, 'value_id' => $value_id]);
                $payload = [
                    "page_title" => p__("Migastarter", $this->getCurrentOptionValue()->getTabbarName()),
                    "locations" => $locations,
                    'pre_image_path' => $pre_image_path,
                    'general_description' => $settings->getGeneralDescription(),
                    'base_url' => $base_url,
                    'default_image' => $base_url.'/app/local/modules/Migastarter/resources/design/desktop/flat/images/ICONA_DEFAULT.png',
                    
                ];

            } catch (\Exception $e) {
                
                $payload = [
                    
                    'error' => true,
                    
                    'message' => p__("Migastarter", $e->getMessage())
                    
                ];

            }

        } else {
            
            $payload = [
                
                'error' => true,
                
                'message' => p__("Migastarter", 'An error occurred during process. Please try again later.')
                
            ];

        }
        
        $this->_sendJson($payload);

    }
      
}