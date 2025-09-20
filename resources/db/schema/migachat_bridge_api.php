<?php

/**
 *
 * Schema definition for 'migachat_setting'
 *
 * Last update: 2020-09-17
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migachat_bridge_api'] = [
    'migachat_bridge_id' => [
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
    'user_mobile' => [
        'type' => 'varchar(255)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'user_email' => [
        'type' => 'varchar(255)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'user_name' => [
        'type' => 'varchar(255)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'chatbot_setting_id' => [
        'type' => 'int(11) unsigned',
        'default' => '0'
    ],
    'role' => [
        'type' => 'varchar(30)',
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
   
    'channel' => [
        'type' => 'varchar(255)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'has_error' => [
        'type' => 'int(1) unsigned',
        'default' => '0'
    ],
    'max_token_exeed' => [
        'type' => 'tinyint(1) unsigned',
        'default' => '0',
    ],
    'max_token_responce' => [
        'type' => 'longtext',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'is_null' => true,
    ],

    'error_description' => [
        'type' => 'varchar(255)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'asked_for_operator' => [
        'type' => 'tinyint(1) unsigned',
        'default' => '0',
        'is_null' => true,
    ],
    'assistant_id' => [
        'type' => 'varchar(255)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'thread_id' => [
        'type' => 'varchar(255)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'thread_message_id' => [
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
