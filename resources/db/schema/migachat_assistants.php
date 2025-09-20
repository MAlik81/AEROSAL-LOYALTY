<?php
// Database schema for migachat_assistants table

$schemas['migachat_assistants'] = [
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
    'assistant_id' => [
        'type' => 'varchar(64)',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'object' => [
        'type' => 'varchar(32)',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'is_null' => true,
        'comment' => 'OpenAI object type',
    ],
    'name' => [
        'type' => 'varchar(255)',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'is_null' => true,
    ],
    'description' => [
        'type' => 'text',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'is_null' => true,
    ],
    'instructions' => [
        'type' => 'longtext',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'is_null' => true,
    ],
    'model' => [
        'type' => 'varchar(50)',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'is_null' => true,
    ],
    'tools' => [
        'type' => 'text',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'is_null' => true,
        'comment' => 'JSON encoded array of tools',
    ],
    'file_paths' => [
        'type' => 'longtext',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'is_null' => true,
        'comment' => 'JSON encoded array of local file paths',
    ],
    'openai_file_ids' => [
        'type' => 'longtext',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'is_null' => true,
        'comment' => 'JSON encoded array of OpenAI file IDs',
    ],
    'temperature' => [
        'type' => 'float',
        'default' => '0.7',
        'is_null' => true,
    ],
    'top_p' => [
        'type' => 'float',
        'default' => '1.0',
        'is_null' => true,
    ],
    'response_format' => [
        'type' => 'text',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'is_null' => true,
        'comment' => 'JSON encoded response format',
    ],
    'tool_choice' => [
        'type' => 'text',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'is_null' => true,
        'comment' => 'JSON encoded tool choice or null',
    ],
    'metadata' => [
        'type' => 'text',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'is_null' => true,
        'comment' => 'JSON encoded metadata',
    ],
    'created_at' => [
        'type' => 'datetime'
    ],
    'updated_at' => [
        'type' => 'datetime'
    ],
];
