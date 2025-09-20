<?php

/**
 *
 * Schema definition for 'migachat_setting'
 *
 * Last update: 2020-09-17
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migachat_setting'] = [
    'migachat_setting_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],
    'help_url' => [
        'type' => 'varchar(255)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'history_duration' => [
        'type' => 'int(2) unsigned',
        'default' => '3'
    ],
    'blacklisted_numbers' => [
        'type' => 'text',
        'default' => '+393517819155,+393336239480,+393663534311,+393516321732',
        'is_null' => true,
    ],
    'created_at' => [
        'type' => 'datetime'
    ],
    'updated_at' => [
        'type' => 'datetime'
    ],
];
