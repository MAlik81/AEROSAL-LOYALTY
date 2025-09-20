<?php
class Migachat_Model_ChatGPTAPI
{
    private $apiUrl;
    private $apiKey;
    private $organizationId;
    private $gptModel;
    private $conversation;

    public function __construct($apiUrl, $apiKey, $organizationId, $gpt_model)
    {
        $this->apiUrl = $apiUrl;
        $this->apiKey = $apiKey;
        $this->organizationId = $organizationId;
        $this->gptModel = $gpt_model;
        $this->conversation = array();
    }

    public function generateResponse($prompt, $all_conversation, $customer_name, $max_tokens)
    {
        $maxRetries = 3;
        $retryCount = 0;

        
            $pattern = '/[^a-zA-Z0-9]+/';
            if ($customer_name) {
                $temp_name = preg_replace($pattern, '-', $customer_name);

                $all_conversation[] = array(
                    'name' => substr($temp_name,0,30),
                    'role' => 'user',
                    'content' => $prompt
                );
            } else {

                $all_conversation[] = array(
                    'role' => 'user',
                    'content' => $prompt
                );
            }
            // print_r($all_conversation);
            // die();
            if ($max_tokens && $max_tokens != 'mx') {

                $data = array(
                    'model' => $this->gptModel,
                    'messages' => $all_conversation,
                    'max_tokens' => (int) $max_tokens,
                    "temperature"=> 0.2,
                    "top_p"=> 1,
                );
            } else {
                $data = array(
                    'model' => $this->gptModel,
                    'messages' => $all_conversation,
                    "temperature"=> 0.2,
                    "top_p"=> 1,
                );
            }
            $headers = array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey,
                'OpenAI-Organization: ' . $this->organizationId
            );
            do {
            $ch = curl_init($this->apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

            $response = curl_exec($ch);
            $error = curl_error($ch); // Get error message, if any
            curl_close($ch);

            if ($response === false) {
                // dd($error);
                return [false, 'Not Sent'];
            }
            $responseData = json_decode($response, true);
            if (isset($responseData['choices'][0]['message']['content'])) {
                $prompt_tokens = $responseData['usage']['prompt_tokens'];
                $completion_tokens = $responseData['usage']['completion_tokens'];
                $total_tokens = $responseData['usage']['total_tokens'];
                $messageContent = $responseData['choices'][0]['message']['content'];
                if ($customer_name == 'admin') {
                    return [true, $messageContent, $prompt_tokens, $completion_tokens, $total_tokens];
                } else {
                    $first_image = $this->processFirstImages($messageContent);
                    $messageContent = $this->processCustomUrls($messageContent);
                    $messageContent = $this->processUrls($messageContent);
                    $messageContent = $this->processImages($messageContent);
                    $messageContent = $this->processPhoneNumbers($messageContent);
                    return [true, $first_image . $messageContent, $prompt_tokens, $completion_tokens, $total_tokens];
                }
            } elseif ($responseData['error']) {
                if ($responseData['error']['message'] === 'Service Unavailable') {
                    $retryCount++;
                    usleep(500000); // Sleep for 0.5 seconds before retrying
                } else {
                    return [false, $responseData['error']['message']];
                }

            }
        } while ($retryCount < $maxRetries);
        // Error handling for invalid response
        return [false, 'Not Sent'];
    }


    public function getModels()
    {
        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey,
                'OpenAI-Organization: ' . $this->organizationId
            )
        );

        $response = curl_exec($ch);
        $error = curl_error($ch); // Get error message, if any
        curl_close($ch);
        if ($response === false) {

            return false;
        }

        $responseData = json_decode($response, true);

        if (isset($responseData['data'])) {
            return $responseData['data'];
        }

        // Error handling for invalid response
        return false;
    }


    private function processCustomUrls($messageContent)
    {
        $pattern = '/\[([^|\]]+)\s*\|\s*([^|\]]+)\]/i';
        $replacement = "<a class='button button-sm button-calm' style='margin:2px;padding:2px;' href='$1' target='_blank'> $2 <i class='icon ion-android-open'></i></a>";
        $messageContent = preg_replace($pattern, $replacement, $messageContent);
        return $messageContent;
    }

    private function processUrls($messageContent)
    {
        $pattern = '/(?<!href=("|\'|,)|src=("|\'|,))(?!\bhttps?:\/\/\S+\.jpg\b|\bhttps?:\/\/\S+\.png\b|\bhttps?:\/\/\S+\.gif\b|\bhttps?:\/\/\S+\.jpeg\b|\bhttps?:\/\/\S+\.webp\b)\b(https?:\/\/\S+)\b/i';
        $replacement = "<br/><a href='$3' class='button button-sm button-calm' style='margin:2px;padding:2px;'  target='_blank'> Open Link <i class='icon ion-android-open'></i></a><br/>";
        $messageContent = preg_replace($pattern, $replacement, $messageContent);
        return $messageContent;
    }
    private function processImages($messageContent)
    {
        $pattern = '/\[\s*(["\']?)(https?:\/\/\S+\.(?:png|jpe?g|gif|webp))\1\s*\]|\(\s*(["\']?)(https?:\/\/\S+\.(?:png|jpe?g|gif|webp))\3\s*\)(?![^<]*>|[^<>]*<\/)/i';

        $firstImage = true; // Flag to track the first image
        $messageContent = preg_replace_callback($pattern, function ($matches) use (&$firstImage) {

            $matches[0] = str_replace('[', '', $matches[0]);
            $matches[0] = str_replace(']', '', $matches[0]);
            $matches[0] = str_replace(')', '', $matches[0]);
            $matches[0] = str_replace('(', '', $matches[0]);
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

    private function processFirstImages($messageContent)
    {
        $pattern = '/\[\s*(["\']?)(https?:\/\/\S+\.(?:png|jpe?g|gif|webp))\1\s*\]|\(\s*(["\']?)(https?:\/\/\S+\.(?:png|jpe?g|gif|webp))\3\s*\)(?![^<]*>|[^<>]*<\/)/i';

        $firstImageURL = null; // Variable to store the first image URL
        $messageContent = preg_replace_callback($pattern, function ($matches) use (&$firstImageURL) {
            if ($firstImageURL === null) {
                $matches[0] = str_replace('[', '', $matches[0]);
                $matches[0] = str_replace(']', '', $matches[0]);
                $matches[0] = str_replace(')', '', $matches[0]);
                $matches[0] = str_replace('(', '', $matches[0]);
                $firstImageURL = "<a href='{$matches[0]}' class='' target='_blank'><img class='chat_image' src='{$matches[0]}' alt='Image'></a><br> <a href='{$matches[0]}' target='_blank'>{$matches[0]} <i class='icon ion-android-open'></i></a>";
                return "";
            }
        }, $messageContent);

        return $firstImageURL;
    }
    private function processPhoneNumbers($messageContent)
    {
        // Define a regular expression pattern to match phone numbers with or without spaces, dots, and commas
        $pattern = '/\+(\d{1,3}\d{5,9})\b/';

        // Define the valid country codes
        $validCountryCodes = [
            '44',
            '1',
            '213',
            '376',
            '244',
            '1264',
            '1268',
            '54',
            '374',
            '297',
            '61',
            '43',
            '994',
            '1242',
            '973',
            '880',
            '1246',
            '375',
            '32',
            '501',
            '229',
            '1441',
            '975',
            '591',
            '387',
            '267',
            '55',
            '673',
            '359',
            '226',
            '257',
            '855',
            '237',
            '1',
            '238',
            '1345',
            '236',
            '56',
            '86',
            '57',
            '269',
            '242',
            '682',
            '506',
            '385',
            '53',
            '90392',
            '357',
            '42',
            '45',
            '253',
            '1809',
            '593',
            '93',
            '20',
            '503',
            '240',
            '291',
            '372',
            '251',
            '500',
            '298',
            '679',
            '358',
            '33',
            '594',
            '689',
            '241',
            '220',
            '7880',
            '49',
            '233',
            '350',
            '30',
            '299',
            '1473',
            '590',
            '671',
            '502',
            '224',
            '245',
            '592',
            '509',
            '504',
            '852',
            '36',
            '354',
            '91',
            '62',
            '98',
            '964',
            '353',
            '972',
            '39',
            '1876',
            '81',
            '962',
            '7',
            '254',
            '686',
            '850',
            '82',
            '965',
            '996',
            '856',
            '371',
            '961',
            '266',
            '231',
            '218',
            '417',
            '370',
            '352',
            '853',
            '389',
            '261',
            '265',
            '60',
            '960',
            '223',
            '356',
            '692',
            '596',
            '222',
            '269',
            '52',
            '691',
            '373',
            '377',
            '976',
            '1664',
            '212',
            '258',
            '95',
            '264',
            '674',
            '977',
            '31',
            '687',
            '64',
            '505',
            '227',
            '234',
            '683',
            '672',
            '670',
            '47',
            '968',
            '680',
            '507',
            '675',
            '595',
            '51',
            '63',
            '48',
            '351',
            '1787',
            '974',
            '262',
            '40',
            '250',
            '378',
            '239',
            '966',
            '221',
            '381',
            '248',
            '232',
            '65',
            '421',
            '386',
            '677',
            '252',
            '27',
            '34',
            '94',
            '290',
            '1869',
            '1758',
            '249',
            '597',
            '268',
            '46',
            '41',
            '963',
            '886',
            '681',
            '969',
            '967',
            '260',
            '263',
            '92',

        ];

        // Use preg_replace_callback to replace the matched phone numbers
        $messageContent = preg_replace_callback($pattern, function ($matches) use ($validCountryCodes) {
            $phoneNumber = preg_replace('/[., ]/', '', $matches[1]); // Remove dots, commas, and spaces

            // Extract the first 1 to 3 digits as the potential country code
            $potentialCountryCode1 = substr($phoneNumber, 0, 1);
            $potentialCountryCode2 = substr($phoneNumber, 0, 2);
            $potentialCountryCode3 = substr($phoneNumber, 0, 3);
            // Check if the potential country code is valid and the phone number meets the length and criteria
            if ((in_array($potentialCountryCode1, $validCountryCodes) || in_array($potentialCountryCode2, $validCountryCodes) || in_array($potentialCountryCode3, $validCountryCodes)) && strlen($phoneNumber) >= 6 && strlen($phoneNumber) <= 13) {
                // Format the phone number as a link
                $replacement = "<a class='button button-xs button-calm' style='margin:2px;padding:2px;' href='tel:$phoneNumber'>$matches[0] <i class='icon ion-android-call'></i></a>";
                return $replacement;
            } else {
                // Return the original number without modification
                return $matches[0];
            }
        }, $messageContent);

        return $messageContent;
    }



}