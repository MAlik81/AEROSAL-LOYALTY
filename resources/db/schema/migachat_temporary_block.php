<?php

/**
 *
 * Schema definition for 'migachat_setting'
 *
 * Last update: 2020-09-17
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migachat_temporary_block'] = [
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
    'user_mobile' => [
        'type' => 'varchar(255)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'blocked_at' => [
        'type' => 'datetime',
        'is_null' => true,
    ],
    'unblock_at' => [
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
