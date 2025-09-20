<?php

/**
 *
 * Schema definition for 'migachat_customer_gdpr'
 *
 * Last update: 2020-09-17
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migachat_customer_gdpr'] = [
    'id' => [
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


    'customer_id' => [
        'type' => 'int(11) unsigned',
        'default' => '0'
    ],
    'thread_id' => [
        'type' => 'varchar(50)',
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
        'is_null' => true
    ],
    'commercial_consent_timestamp' => [
        'type' => 'datetime',
        'is_null' => true
    ],
    'asked_for_operator' => [
        'type' => 'tinyint(1) unsigned',
        'default' => '0'
    ],
    'asked_for_operator_at' => [
        'type' => 'datetime',
        'is_null' => true
    ],
    'created_at' => [
        'type' => 'datetime',
        'is_null' => true
    ],
];
