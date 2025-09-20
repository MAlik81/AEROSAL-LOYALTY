<?php

/**
 *
 * Schema definition for 'migachat_setting'
 *
 * Last update: 2020-09-17
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migachat_bridge_api_settings'] = [
    'migachat_bridge_api_setting_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],

    'value_id' => [
        'type' => 'int(11) unsigned',
        'default' => '0'
    ],
   
    'auth_token' => [
        'type' => 'varchar(255)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    
    'system_prompt' => [
        'type' => 'longtext',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'is_null' => true,
    ],
    'disable_api' => [
        'type' => 'tinyint(1) unsigned',
        'default' => '0',
    ],
    'overall_duration' => [
        'type' => 'int(11) unsigned',
        'default' => '24'
    ],
    'overall_limit' => [
        'type' => 'int(11) unsigned',
        'default' => '2500000'
    ],
    'user_duration' => [
        'type' => 'int(11) unsigned',
        'default' => '24'
    ],
    'user_limit' => [
        'type' => 'int(11) unsigned',
        'default' => '100000'
    ],
    'user_chat_limit_responce' => [
        'type' => 'longtext',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'default' => "I am sorry, but you have reached the maximum number of interactions I can do. This is a free information service, but obviously if you want to find out more I recommend you contact us at our contact details.",
    ],
    
    'ai_answer_token_limit' => [
        'type' => 'int(11) unsigned',
        'default' => '500'
    ],
    
    'ai_answer_token_limit_msg' => [
        'type' => 'longtext',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'default' => "Do you want me to continue the answer? Type YES if you do, type NO if you want to ask another question.",
    ],
    'suspention_duration' => [
        'type' => 'int(11) unsigned',
        'default' => '1'
    ],
    'created_at' => [
        'type' => 'datetime'
    ],
    'updated_at' => [
        'type' => 'datetime'
    ],
];
