<?php
class Migachat_Model_Webhook
{
    private $webHookUrl;

    public function __construct($webHookUrl)
    {

        // Parse the URL components
        $parsedUrl = parse_url($webHookUrl);

        if (!$parsedUrl) {
            throw new Exception("Failed to parse URL: " . $webHookUrl);
            // Handle the error accordingly
        }

        // Rebuild the URL
        $rebuildUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
        if (isset($parsedUrl['port'])) {
            $rebuildUrl .= ':' . $parsedUrl['port'];
        }
        if (isset($parsedUrl['path'])) {
            $rebuildUrl .= $parsedUrl['path'];
        }
        if (isset($parsedUrl['query'])) {
            $rebuildUrl .= '?' . $parsedUrl['query'];
        }

        $this->webHookUrl = $rebuildUrl;
    }

    public function generateResponse($post_data)
    {
        try {
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => $this->webHookUrl,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS => $post_data,
            ));
            
            $response = curl_exec($curl);
            $httpStatusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            curl_close($curl);

            if ($httpStatusCode !== 200) {
                throw new Exception(
                    "Request failed with HTTP status code: " . $httpStatusCode .
                    "\n Response: " . $response . "\n".$this->webHookUrl
                );
            }

            return $response;
        } catch (Exception $e) {
            // Handle the exception
            return $e->getMessage();
        }
    }

    public function getWebHookURL()
    {
        return $this->webHookUrl;
    }
}



?>