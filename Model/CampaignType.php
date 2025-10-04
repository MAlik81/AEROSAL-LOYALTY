<?php
class Aerosalloyalty_Model_CampaignType extends Core_Model_Default
{
    protected $_db_table;

    public function __construct($datas = array()) {
        parent::__construct($datas);
        $this->_db_table = new Aerosalloyalty_Model_Db_Table_CampaignType();
    }

    public function findByCode($value_id, $code) {
        $row = $this->_db_table->fetchRow([
            'value_id = ?' => (int)$value_id,
            'code = ?'     => (string)$code
        ]);
        if ($row) $this->setData($row->toArray());
        return $this;
    }

    public function upsert(array $data) {
        $value_id = (int)$data['value_id'];
        $code     = (string)$data['code'];
        $code_original = array_key_exists('code_original', $data) ? (string)$data['code_original'] : '';
        $id = array_key_exists('id', $data) ? (int)$data['id'] : 0;

        unset($data['code_original'], $data['id']);

        if ($code_original === '') {
            $code_original = $code;
        }

        $row = null;
        if ($id) {
            $row = $this->_db_table->find($id)->current();
            if (!$row || (int)$row->value_id !== $value_id) {
                $row = null;
            }
        }

        if (!$row && $code_original !== '') {
            $row = $this->_db_table->fetchRow([
                'value_id = ?' => $value_id,
                'code = ?'     => $code_original
            ]);
        }

        if (!$row) {
            $row = $this->_db_table->fetchRow([
                'value_id = ?' => $value_id,
                'code = ?'     => $code
            ]);
        }

        $data['updated_at'] = date('Y-m-d H:i:s');

        if ($row) {
            $this->_db_table->update($data, ['aerosalloyalty_campaign_type_id = ?' => $row->aerosalloyalty_campaign_type_id]);
            $this->setData(array_merge($row->toArray(), $data));
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $id = $this->_db_table->insert($data);
            $this->find($id);
        }
        return $this;
    }

    public function allForValue($value_id) {
        return $this->_db_table->fetchAll(['value_id = ?' => (int)$value_id], 'name ASC');
    }
}
