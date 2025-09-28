<?php

class Migachat_Model_AssistantFiles extends Core_Model_Default
{
    public function __construct($datas = [])
    {
        parent::__construct($datas);
        $this->_db_table = 'Migachat_Model_Db_Table_AssistantFiles';
    }

    public function saveFileMetadata($assistantId, $vectorStoreId, $fileId, $originalName)
    {
        if (empty($assistantId) || empty($fileId)) {
            return null;
        }

        $fileModel = new self();
        $fileModel->find([
            'assistant_id'  => $assistantId,
            'openai_file_id' => $fileId,
        ]);

        $timestamp = date('Y-m-d H:i:s');

        if ($fileModel->getId()) {
            $fileModel
                ->setOriginalName($originalName)
                ->setVectorStoreId($vectorStoreId)
                ->setUpdatedAt($timestamp)
                ->save();

            return $fileModel;
        }

        $fileModel
            ->setAssistantId($assistantId)
            ->setVectorStoreId($vectorStoreId)
            ->setOpenaiFileId($fileId)
            ->setOriginalName($originalName)
            ->setCreatedAt($timestamp)
            ->setUpdatedAt($timestamp)
            ->save();

        return $fileModel;
    }

    public function deleteByFileId($fileId, $assistantId = null)
    {
        if (empty($fileId)) {
            return false;
        }

        $fileModel = new self();
        $conditions = ['openai_file_id' => $fileId];
        if (! empty($assistantId)) {
            $conditions['assistant_id'] = $assistantId;
        }

        $fileModel->find($conditions);
        if (! $fileModel->getId()) {
            return false;
        }

        $fileModel->delete();
        return true;
    }
}
