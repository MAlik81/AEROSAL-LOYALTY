<?php

class Migastarter_Model_Setting extends Core_Model_Default
{
    public function __construct($datas = array())
    {
        parent::__construct($datas);
        $this->_db_table = 'Migastarter_Model_Db_Table_Setting'; //Db Model Name

    }

    // Deep Link
    /**
    * @param $valueId
    * @return array
    */
    public function getInappStates($valueId)
    {
        $inAppStates = [
            [
                'state' => 'migastarter-view',
                'offline' => false,
                'params' => [
                    'value_id' => $valueId,
                ],
            ],
        ];
        return $inAppStates;
    }
}