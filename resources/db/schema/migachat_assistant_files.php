<?php
// Database schema for migachat_assistant_files table

$schemas['migachat_assistant_files'] = [
    'id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],
    'assistant_id' => [
        'type' => 'varchar(64)',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'comment' => 'OpenAI assistant identifier',
    ],
    'vector_store_id' => [
        'type' => 'varchar(64)',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'is_null' => true,
        'comment' => 'OpenAI vector store identifier',
    ],
    'openai_file_id' => [
        'type' => 'varchar(128)',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'comment' => 'Identifier of the uploaded OpenAI file',
    ],
    'original_name' => [
        'type' => 'varchar(255)',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'is_null' => true,
        'comment' => 'Original filename supplied by the user',
    ],
    'created_at' => [
        'type' => 'datetime',
    ],
    'updated_at' => [
        'type' => 'datetime',
    ],
];
