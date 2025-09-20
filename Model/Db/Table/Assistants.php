<?php

class Migachat_Model_Db_Table_Assistants extends Core_Model_Db_Table
{
    protected $_name = "migachat_assistants"; //Database table name
    protected $_primary = "id"; //name of primary key column


   public function updateAssistants($assistants, $value_id, $app_id) {
    if (empty($assistants) || !is_array($assistants)) {
        return false;
    }

    foreach ($assistants['data'] as $assistant) {
        // Extract vector_store_ids if present
        $vector_store_ids = null;
        if (isset($assistant['tool_resources']['file_search']['vector_store_ids'])) {
            $vector_store_ids = $assistant['tool_resources']['file_search']['vector_store_ids'];
        }

        $data = [
            'app_id'          => $app_id,
            'value_id'        => $value_id,
            'assistant_id'    => $assistant['id'] ?? null,
            'object'          => $assistant['object'] ?? null,
            'name'            => $assistant['name'] ?? null,
            'description'     => $assistant['description'] ?? null,
            'model'           => $assistant['model'] ?? null,
            'instructions'    => $assistant['instructions'] ?? null,
            'tools' => isset($assistant['tools']) 
                    ? json_encode(array_column($assistant['tools'], 'type')) 
                    : null,
            'openai_file_ids' => $vector_store_ids ? json_encode($vector_store_ids) : null, // updated to hold vector_store_ids
            'metadata'        => isset($assistant['metadata']) ? json_encode($assistant['metadata']) : null,
            'temperature'     => $assistant['temperature'] ?? null,
            'top_p'           => $assistant['top_p'] ?? null,
            'response_format' => $assistant['response_format'] ?? null,
            'tool_choice'     => isset($assistant['tool_choice']) ? (is_array($assistant['tool_choice']) ? json_encode($assistant['tool_choice']) : $assistant['tool_choice']) : null,
            'created_at'      => isset($assistant['created_at']) ? date('Y-m-d H:i:s', $assistant['created_at']) : null
        ];

        // Check if the assistant already exists
        $existingAssistant = (new Migachat_Model_Assistants)->find([
            'value_id'     => $value_id,
            'assistant_id' => $assistant['id'] ?? null,
        ]);

        if (count($existingAssistant->getData()) > 0) {
            $this->_db->update($this->_name, $data, [
                'value_id = ?'     => $value_id,
                'assistant_id = ?' => $assistant['id'] ?? null
            ]);
        } else {
            $this->_db->insert($this->_name, $data);
        }
    }

    return true;
}

    
}