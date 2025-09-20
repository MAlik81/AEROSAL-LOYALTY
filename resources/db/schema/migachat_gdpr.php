<?php

/**
 *
 * Schema definition for 'migachat_setting'
 *
 * Last update: 2020-09-17
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migachat_gdpr'] = [
    'gdpr_id' => [
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
    'default_language' => [
        'type' => 'varchar(10)',
        'is_null' => true,
        'default' => 'it',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'gdpr_active' => [
        'type' => 'tinyint(1) unsigned',
        'default' => '0',
    ],
    'commercial_active' => [
        'type' => 'tinyint(1) unsigned',
        'default' => '0',
    ],
    
    'gdpr_welcome_text' => [
        'type' => 'longtext',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'default' => ' Dear user, in order to write to us I need you to confirm that you have read our privacy policy which can be found at this link here below. Please therefore simply reply with a “YES” to this message.',
    ],
    'gdpr_link' => [
        'type' => 'text',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'default' => 'https://yourdomain.com/',
    ],
    'gdpr_success_text' => [
        'type' => 'longtext',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'default' => 'Compliments! Now we can start chatting together.',
    ],
    'gdpr_failure_text' => [
        'type' => 'longtext',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'default' => 'I am sorry but to proceed you must write YES.',
    ],
    'gdpr_reset' => [
        'type' => 'int(11) unsigned',
        'default' => '60'
    ],
    
    'commercial_welcome_text' => [
        'type' => 'longtext',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'default' => 'Perfect! Now I need to collect your consent to share commercial information with you on this chat or other communication channels. Answer YES if you give consent or NO if you don’t.',
    ],
    
    'created_at' => [
        'type' => 'datetime'
    ],
    'updated_at' => [
        'type' => 'datetime'
    ],
];
