<?php

/**
 *
 * Schema definition for 'migachat_setting'
 *
 * Last update: 2020-09-17
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migachat_model_limmits'] = [
    'limit_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],

    'value_id' => [
        'type' => 'int(11) unsigned',
        'default' => '0',
        'is_null' => true,
    ],
   
    'model_name' => [
        'type' => 'varchar(100)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],

    'system_prompt_limit' => [
        'type' => 'int(11) unsigned',
        'default' => '0',
        'is_null' => true,
    ],
   
    'total_prompt_limit' => [
        'type' => 'int(11) unsigned',
        'default' => '0',
        'is_null' => true,
    ],
   
    'created_at' => [
        'type' => 'datetime'
    ],
];
