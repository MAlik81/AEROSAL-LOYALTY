<?php

/**
 *
 * Schema definition for 'migachat_setting'
 *
 * Last update: 2020-09-17
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migachat_bridge_api_chat_ids'] = [
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
    'thread_id' => [
        'type' => 'varchar(50)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
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
    // 2 for no action performed yet,3 for asked,0 for denied and 1 foraccepted
    'gdpr_consent' => [
        'type' => 'tinyint(1) unsigned',
        'default' => '2',
    ],
    'commercial_consent' => [
        'type' => 'tinyint(1) unsigned',
        'default' => '2'
    ],
    'gdpr_consent_external' => [
        'type' => 'tinyint(1) unsigned',
        'default' => '0',
    ],
    'commercial_consent_external' => [
        'type' => 'tinyint(1) unsigned',
        'default' => '0'
    ],
    'gdpr_consent_timestamp' => [
        'type' => 'datetime',
        'is_null' => true,
    ],
    'commercial_consent_timestamp' => [
        'type' => 'datetime',
        'is_null' => true,
    ],
    'asked_for_operator' => [
        'type' => 'tinyint(1) unsigned',
        'default' => '0'
    ],
    'asked_for_operator_st' => [
        'type' => 'datetime',
        'is_null' => true,
    ],
    'last_asked_for_operator_at' => [
        'type' => 'datetime',
        'is_null' => true,
    ],
    'asked_for_operator_count' => [
        'type' => 'tinyint(1) unsigned',
        'default' => '0',
        'is_null' => true,
    ],
    'last_token_limit_reached_at' => [
        'type' => 'datetime',
        'default' => NULL,
        'is_null' => true,
    ],
    'requests_count' => [
        'type' => 'tinyint(1) unsigned',
        'default' => 0,
        'is_null' => true,
    ],
    'current_thread_id' => [
        'type' => 'varchar(255)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'created_at' => [
        'type' => 'datetime'
    ],
    'update_at' => [
        'type' => 'datetime',
        'default' => NULL,
        'is_null' => true,
    ],
];
