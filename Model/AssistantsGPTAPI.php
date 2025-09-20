<?php

class Migachat_Model_AssistantsGPTAPI
{
    private $apiKey;
    private $organizationId;
    private $apiBase = "https://api.openai.com/v1";
    private $headers;

    public function __construct($apiKey, $organizationId = null)
    {
        $this->apiKey         = $apiKey;
        $this->organizationId = $organizationId;

        $this->headers = [
            "Authorization: Bearer {$this->apiKey}",
            "Content-Type: application/json",
        ];

        if ($this->organizationId) {
            $this->headers[] = "OpenAI-Organization: {$this->organizationId}";
        }
    }

    private function request($method, $endpoint, $data = null, $isFile = false)
    {
        $curl = curl_init();
        $url  = $this->apiBase . $endpoint;

        // Always include the OpenAI-Beta header for Assistants API v2
        $defaultHeaders = [
            "Authorization: Bearer {$this->apiKey}",
            "Content-Type: application/json",
            "OpenAI-Beta: assistants=v2",
        ];
        if ($this->organizationId) {
            $defaultHeaders[] = "OpenAI-Organization: {$this->organizationId}";
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        if ($method === "POST") {
            curl_setopt($curl, CURLOPT_POST, true);
        } elseif ($method === "PATCH") {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PATCH");
        } elseif ($method === "GET") {
            curl_setopt($curl, CURLOPT_HTTPGET, true);
        } elseif ($method === "DELETE") {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
        }

        if ($data) {
            if ($isFile) {
                // For file upload, do not set Content-Type to application/json
                $headers = [
                    "Authorization: Bearer {$this->apiKey}",
                    "OpenAI-Beta: assistants=v2",
                ];
                if ($this->organizationId) {
                    $headers[] = "OpenAI-Organization: {$this->organizationId}";
                }
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            } else {
                curl_setopt($curl, CURLOPT_HTTPHEADER, $defaultHeaders);
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            }
        } else {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $defaultHeaders);
        }

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            throw new Exception(curl_error($curl));
        }

        curl_close($curl);
        return json_decode($response, true);
    }

    // Remaining methods unchanged...
    public function getAllAssistants()
    {return $this->request("GET", "/assistants");}

    public function createAssistant($payload)
    {return $this->request("POST", "/assistants", $payload);}
    public function patchAssistant($assistantId, $payload)
    {
        return $this->request("POST", "/assistants/{$assistantId}", $payload);
    }
    public function uploadFile($filePath, $purpose = 'assistants')
    {
        $curlFile = curl_file_create($filePath, mime_content_type($filePath), basename($filePath));

        $postData = [
            "file"    => $curlFile,
            "purpose" => $purpose,
        ];

        return $this->request("POST", "/files", $postData, true);
    }

    public function assignFilesToAssistant($assistantId, $fileIds = [])
    {
        return $this->patchAssistant($assistantId, ['file_ids' => $fileIds]);
    }

    public function createThread($metadata = [])
    {
        return $this->request("POST", "/threads", ['metadata' => $metadata]);
    }

    public function addMessageToThread($threadId, $role, $content)
    {
        return $this->request("POST", "/threads/{$threadId}/messages", [
            'role'    => $role,
            'content' => $content,
        ]);
    }

    public function runThread($threadId, $assistantId, $options = [])
    {
        $payload = array_merge(['assistant_id' => $assistantId], $options);
        return $this->request("POST", "/threads/{$threadId}/runs", $payload);
    }

    public function getRunStatus($threadId, $runId)
    {
        return $this->request("GET", "/threads/{$threadId}/runs/{$runId}");
    }

    public function getThreadMessages($threadId, $params = [])
    {
        $query = http_build_query($params);
        return $this->request("GET", "/threads/{$threadId}/messages?" . $query);
    }
    public function createVectorStore($payload)
    {
        return $this->request("POST", "/vector_stores", $payload);
    }
    public function addFileToVectorStore($vectorStoreId, $fileId)
    {
        return $this->request("POST", "/vector_stores/{$vectorStoreId}/files", [
            'file_id' => $fileId,
        ]);
    }
    public function getAssistant($assistantId)
    {
        return $this->request("GET", "/assistants/{$assistantId}");
    }
    public function deleteFileFromVectorStore($vectorStoreId, $fileId)
    {
        return $this->request('DELETE', "/vector_stores/{$vectorStoreId}/files/{$fileId}");
    }
    public function listFilesInVectorStore($vectorStoreId)
    {
        return $this->request("GET", "/vector_stores/{$vectorStoreId}/files");
    }




}
