<?php

/**
 *
 * Schema definition for 'migachat_setting'
 *
 * Last update: 2020-09-17
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migachat_app_settings'] = [
    'migachat_app_setting_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],

    'app_id' => [
        'type' => 'int(11) unsigned',
        'default' => '0'
    ],
    'value_id' => [
        'type' => 'int(11) unsigned',
        'default' => '0'
    ],
    'webhook_history' => [
        'type' => 'tinyint(1) unsigned',
        'default' => '0',
    ],
    'prompt_webhook_active' => [
        'type' => 'tinyint(1) unsigned',
        'default' => '0',
    ],
    'prompt_chatgpt_active' => [
        'type' => 'tinyint(1) unsigned',
        'default' => '0',
    ],
    'system_prompt' => [
        'type' => 'longtext',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'system_prompt_tokens' => [
        'type' => 'int(11) unsigned',
        'default' => '0'
    ],
    'gpt_model' => [
        'type' => 'varchar(255)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    
    'system_prompt_limit' => [
        'type' => 'int(11) unsigned',
        'default' => '2000',
        'is_null' => true,
    ],
   
    'total_prompt_limit' => [
        'type' => 'int(11) unsigned',
        'default' => '4000',
        'is_null' => true,
    ],
    'temporary_blacklist_duration' => [
        'type' => 'int(4) unsigned',
        'default' => '1',
        'is_null' => true,
    ],
   
    'permanent_blacklisted_mobile_numbers' => [
        'type' => 'varchar(255)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'assistant_id' => [
        'type' => 'varchar(255)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'temperature' => [
        'type' => 'varchar(20)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'top_p' => [
        'type' => 'varchar(20)',
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
