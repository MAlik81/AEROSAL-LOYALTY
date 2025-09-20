<?php

/**
 *
 * Schema definition for 'migachat_setting'
 *
 * Last update: 2020-09-17
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migachat_chatlogs'] = [
    'migachat_chatlog_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],

    'value_id' => [
        'type' => 'int(11) unsigned',
        'default' => '0'
    ],
   
    'customer_id' => [
        'type' => 'int(11) unsigned',
        'default' => '0'
    ],
    'chatbot_setting_id' => [
        'type' => 'int(11) unsigned',
        'default' => '0'
    ],
    'message_sent_received' => [
        'type' => 'varchar(10)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'message_content' => [
        'type' => 'longtext',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'is_sent' => [
        'type' => 'int(1) unsigned',
        'default' => '1'
    ],
    'is_read' => [
        'type' => 'int(1) unsigned',
        'default' => '0'
    ],
    'cron_status' => [
        'type' => 'int(1) unsigned',
        'default' => '0',
        'is_null' => true,
    ],

    'source' => [
        'type' => 'varchar(255)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'has_error' => [
        'type' => 'int(1) unsigned',
        'default' => '0'
    ],
    'prompt_tokens' => [
        'type' => 'int(11) unsigned',
        'default' => '0'
    ],
    'completion_tokens' => [
        'type' => 'int(11) unsigned',
        'default' => '0'
    ],
    'total_tokens' => [
        'type' => 'int(11) unsigned',
        'default' => '0'
    ],

    'error_description' => [
        'type' => 'varchar(255)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'created_at' => [
        'type' => 'datetime'
    ],
    'updated_at' => [
        'type' => 'datetime'
    ],
];
