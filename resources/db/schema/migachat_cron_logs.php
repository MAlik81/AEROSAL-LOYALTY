<?php

/**
 *
 * Schema definition for 'migachat_setting'
 *
 * Last update: 2020-09-17
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migachat_cron_logs'] = [
    'id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],
    'value_id' => [
        'type' => 'int(11) unsigned',
        'default' => '0'
    ],
    'chat_log_id' => [
        'type' => 'int(11) unsigned',
        'default' => '0'
    ],
    'cron_type' => [
        'type' => 'varchar(10)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'status' => [
        'type' => 'int(11) unsigned',
        'default' => '0'
    ],
    'created_at' => [
        'type' => 'datetime'
    ],
    'updated_at' => [
        'type' => 'datetime'
    ],
];
