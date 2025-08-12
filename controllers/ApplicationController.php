<?php

class Migastarter_ApplicationController extends Application_Controller_Default
{

    public function viewAction()
    {

        $this->loadPartials();
    }

    public function cropAction()
    {

        if ($datas = $this->getRequest()->getPost()) {

            try {

                $uploader = new Core_Model_Lib_Uploader();

                $file = $uploader->savecrop($datas);

                $datas = [

                    'success'         => true,

                    'file'            => $file,

                    'message'         => p__('Migastarter', 'Info successfully saved.'),

                    'message_button'  => 0,

                    'message_timeout' => 2,

                ];
            } catch (Exception $e) {

                $datas = [

                    'error'   => true,

                    'message' => $e->getMessage(),

                ];
            }

            $this->_sendJson($datas);
        }
    }

    public function importcsvAction()
    {

        if ($received_request = $this->getRequest()->getPost()) {
            try {
                $value_id = $received_request['value_id'];
                $app_id   = $received_request['app_id'];
                // Check if a file was uploaded
                // Retrieve Chatbot settings
                $raw_data_obj = new Migastarter_Model_Rawdata();

                if (isset($_FILES['adverts_csv']) && is_uploaded_file($_FILES['adverts_csv']['tmp_name'])) {
                    $file = $_FILES['adverts_csv']['tmp_name'];

                    // Open the file
                    if (($handle = fopen($file, 'r')) !== false) {
                        $row           = 0; // To track row number for validations
                        $importedCount = 0; // Track the number of successfully imported rows
                        $temp          = [];
                        // get raw csv string
                        $raw_data                                   = file_get_contents($file);
                        $raw_data_for_table                         = [];
                        $raw_data_for_table['value_id']             = $value_id;
                        $raw_data_for_table['app_id']               = $app_id;
                        $raw_data_for_table['raw_datcsv_raw_dataa'] = $raw_data;
                        $raw_data_for_table['created_at']           = date("Y-m-d H:i:s");
                        $raw_data_for_table['updated_at']           = date("Y-m-d H:i:s");
                        $raw_data_obj->addData($raw_data_for_table)->save();
                        // Read the CSV file line by line

                        while (($data = fgetcsv($handle, 5000, ';')) !== false) {

                            $row++;
                            // Skip header row if necessary
                            if ($row == 1) {
                                continue;
                            }

                                                    // Validate the row data
                            if (count($data) < 8) { // Adjust based on the expected number of columns
                                continue;
                            }

                            $category_name = $data[1];
                            $temp[]        = $data;
                            // // Check if the category already exists
                            // $category = $categories_obj->find([
                            //     'app_id' => $app_id,
                            //     'name' => $category_name,
                            // ]);
                            // $temp[] = $category->getData();
                            // if(!$category) {
                            //     // If not, create a new category
                            //     $categories_obj->setData([
                            //         'app_id' => $app_id,
                            //         'value_id' => $value_id,
                            //         'name' => $category_name,
                            //         'is_active' => 1,
                            //         'is_default' => 0,
                            //         'is_deleted' => 0,
                            //         'created_at' => date("Y-m-d H:i:s"),
                            //         'updated_at' => date("Y-m-d H:i:s"),
                            //     ])->save();
                            // }
                            // // get location data
                            // // Check if the location already exists
                            // $address = $data[5];
                            // $address_parts = explode(' ', $address);
                            // // get last part of the address
                            // $last_part = array_pop($address_parts);
                            // $location = $locations_obj->find([
                            //     'app_id' => $app_id,
                            //     'name' => $last_part,
                            // ]);
                            // if(!$location->getId()) {
                            //     // If not, create a new location
                            //     $locations_obj->setData([
                            //         'app_id' => $app_id,
                            //         'value_id' => $value_id,
                            //         'name' => $last_part,
                            //         'image_path' => '',
                            //         'is_active' => 1,
                            //         'is_default' => 0,
                            //         'is_deleted' => 0,
                            //         'created_at' => date("Y-m-d H:i:s"),
                            //         'updated_at' => date("Y-m-d H:i:s"),
                            //     ])->save();
                            // }

                            // // get adverts data
                            // // Check if the advert already exists
                            // $advert_number = $data[0];
                            // $advert = $adverts_obj->find([
                            //     'app_id' => $app_id,
                            //     'advert_number' => $advert_number,
                            // ]);
                            // if(!$advert->getId()) {
                            //     $adverts_data = array();
                            //     $adverts_data['value_id'] = $value_id;
                            //     $adverts_data['app_id'] = $app_id;
                            //     $adverts_data['advert_number'] = $data[0];
                            //     $adverts_data['category_id'] = $categories_obj->getId();
                            //     $adverts_data['location_id'] = $locations_obj->getId();
                            //     $adverts_data['lot_description'] = $data[4];
                            //     $adverts_data['advert_description'] = $data[6];
                            //     $adverts_data['price_description'] = $data[9];
                            //     $adverts_data['address'] = $data[5];
                            //     $adverts_data['sales_date'] = $data[3];
                            //     $adverts_data['publish_date'] = $data[8];
                            //     $adverts_data['price'] = $data[10];
                            //     $adverts_data['status'] = 1;
                            //     $adverts_data['is_active'] = 1;
                            //     $adverts_data['created_at'] = date("Y-m-d H:i:s");
                            //     $adverts_data['updated_at'] = date("Y-m-d H:i:s");
                            //     $adverts_obj->addData($adverts_data)->save();
                            // }

                            $importedCount++;
                        }

                        fclose($handle);

                        // dd($temp);
                        // Process the collected data

                        $temp2 = [];
                        foreach ($temp as $key => $value) {

                            $categories_obj     = new Migastarter_Model_Categories();
                            $locations_obj      = new Migastarter_Model_Locations();
                            $adverts_obj        = new Migastarter_Model_Adverts();
                            $category_name      = $value[1];
                            $address            = $value[5];
                            $advert_number      = $value[0];
                            $lot_description    = $value[4];
                            $advert_description = $value[6];
                            $price_description  = $value[9];
                            $sales_date         = $value[3];
                            $publish_date       = $value[8];
                            $price              = $value[10];

                            // Check if the category already exists
                            $category = $categories_obj->find([
                                'app_id'     => $app_id,
                                'name'       => $category_name,
                                'is_deleted' => 0,
                            ]);
                            if (! $category || ! $category->getId()) {
                                // If not, create a new category
                                (new Migastarter_Model_Categories())->setData([
                                    'app_id'     => $app_id,
                                    'value_id'   => $value_id,
                                    'name'       => $category_name,
                                    'is_active'  => 1,
                                    'is_default' => 0,
                                    'is_deleted' => 0,
                                    'created_at' => date("Y-m-d H:i:s"),
                                    'updated_at' => date("Y-m-d H:i:s"),
                                ])->save();
                            }
                            $category = $categories_obj->find([
                                'app_id'     => $app_id,
                                'name'       => $category_name,
                                'is_deleted' => 0,
                            ]);

                            // get location data
                            // Check if the location already exists
                            $address_parts = explode(' ', $address);
                            // get last part of the address
                            $last_part = array_pop($address_parts);
                            $location  = $locations_obj->find([
                                'app_id'     => $app_id,
                                'name'       => $last_part,
                                'is_deleted' => 0,
                            ]);
                            if (! $location || ! $location->getId()) {
                                // If not, create a new location
                                // dd($last_part);
                                // dd($app_id);
                                // dd($value_id);
                                // If not, create a new location
                                (new Migastarter_Model_Locations())->setData([
                                    'app_id'     => $app_id,
                                    'value_id'   => $value_id,
                                    'name'       => $last_part,
                                    'image_path' => '',
                                    'is_active'  => 1,
                                    'is_default' => 0,
                                    'is_deleted' => 0,
                                    'created_at' => date("Y-m-d H:i:s"),
                                    'updated_at' => date("Y-m-d H:i:s"),
                                ])->save();
                            }
                            $location = $locations_obj->find([
                                'app_id'     => $app_id,
                                'name'       => $last_part,
                                'is_deleted' => 0,
                            ]);
                            // get adverts data
                            // Check if the advert already exists
                            $advert = $adverts_obj->find([
                                'app_id'        => $app_id,
                                'advert_number' => $advert_number,
                                'is_deleted'    => 0,
                            ]);
                            // For sales_date with time
                            if (! $advert || ! $advert->getId()) {
                                $adverts_data                       = [];
                                $adverts_data['value_id']           = $value_id;
                                $adverts_data['app_id']             = $app_id;
                                $adverts_data['advert_number']      = $advert_number;
                                $adverts_data['category_id']        = $category->getId();
                                $adverts_data['location_id']        = $location->getId();
                                $adverts_data['lot_description']    = $lot_description;
                                $adverts_data['advert_description'] = $advert_description;
                                $adverts_data['price_description']  = $price_description;
                                $adverts_data['address']            = $address;
                                $date                               = DateTime::createFromFormat('d/m/y H:i', $sales_date);
                                $adverts_data['sales_date']         = $date->format('Y-m-d H:i:s');

                                // For publish_date without time
                                $date                         = DateTime::createFromFormat('d/m/y', $publish_date);
                                $adverts_data['publish_date'] = $date->format('Y-m-d H:i:s');
                                $adverts_data['price']        = $price;
                                $adverts_data['status']       = 1;
                                $adverts_data['is_active']    = 1;
                                $adverts_data['created_at']   = date("Y-m-d H:i:s");
                                $adverts_data['updated_at']   = date("Y-m-d H:i:s");
                                $temp2[]                      = $adverts_data;
                                // Save the advert
                                (new Migastarter_Model_Adverts())->addData($adverts_data)->save();
                            }
                            $advert   = null;
                            $location = null;
                            $category = null;

                            $categories_obj = null;
                            $locations_obj  = null;
                            $adverts_obj    = null;

                        }

                        // Success payload
                        $payload = [
                            'success' => true,
                            'temp2'   => $temp2,
                            'message' => $importedCount . p__("Migastarter", " records imported successfully."),
                        ];
                    } else {
                        throw new \Exception(p__("Migastarter", "Unable to open the uploaded file."));
                    }
                } else {
                    throw new \Exception(p__("Migastarter", "No file uploaded or invalid file."));
                }
            } catch (\Exception $e) {
                $payload = [
                    'error'   => true,
                    'message' => p__("Migastarter", $e->getMessage()),
                ];
            }
        } else {
            $payload = [
                'error'   => true,
                'message' => p__("Migastarter", "An error occurred during the process. Please try again later."),
            ];
        }

        $this->_sendJson($payload);
    }
    