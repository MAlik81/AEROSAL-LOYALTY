<?php

/**
 *
 * Schema definition for 'migachat_setting'
 *
 * Last update: 2020-09-17
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migachat_bridge_api_chat_limits'] = [
    'id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],

    'value_id' => [
        'type' => 'int(11) unsigned',
        'default' => '0'
    ],
   
    'chat_id' => [
        'type' => 'bigint(20) unsigned',
        'default' => '0'
    ],
    
    'is_limit' => [
        'type' => 'tinyint(1) unsigned',
        'default' => '0',
    ],
    
    'ai_off_at' => [
        'type' => 'datetime',
        'is_null' => true,
    ],
    'limit_off_at' => [
        'type' => 'datetime',
        'is_null' => true,
    ],
    'created_at' => [
        'type' => 'datetime'
    ],
    'updated_at' => [
        'type' => 'datetime'
    ],
];
