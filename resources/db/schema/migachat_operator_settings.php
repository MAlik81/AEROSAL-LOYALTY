
<?php

/**
 *
 * Schema definition for 'migachat_setting'
 *
 * Last update: 2020-09-17
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migachat_operator_settings'] = [
    'migachat_operator_setting_id' => [
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
    'is_enabled_in_app' => [
        'type' => 'tinyint(1) unsigned',
        'default' => '0',
    ],
    'is_enabled_bridge_api' => [
        'type' => 'tinyint(1) unsigned',
        'default' => '1',
    ],
    'webhook_url' => [
        'type' => 'varchar(255)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'ask_call_from_operator_msg' => [
        'type' => 'text',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'default' => 'Do you want me to have one of our operators call you? (Type Yes or No)',
    ],
    'confirm_call_from_operator_msg' => [
        'type' => 'longtext',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'default' => 'Perfect, I warned my colleagues, they will be in touch very soon. Now you can continue to ask me for information if you want.',
    ],
    'invalid_ask_call_from_operator_msg' => [
        'type' => 'text',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'default' => 'I did not understand, do you want me to have one of our operators call you? (Type Yes or No)',
    ],
    'declined_call_from_operator_msg' => [
        'type' => 'text',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'default' => 'Ok, than you can continue to ask me for information if you want.',
    ],
    'default_language' => [
        'type' => 'varchar(10)',
        'default' => 'it',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'email_subject' => [
        'type' => 'longtext',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'default' => '@@user_name@@ asks to be contacted on our AI Chatbot of APP @@app_name@@',
    ],
    'email_template' => [
        'type' => 'longtext',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'default' => 'Ciao Admin,

abbiamo ricevuto una richiesta di contatto da un utente durante una conversazione sulla nostra chat AI.
Qui i dettagli di riferimento:

APP ID: @@app_id@@
NOME APP: @@app_name@@
ID Istanza: @@instance_id@@
Nome Utente: @@user_name@@
Mail Utente: @@user_email@@
Telefono Utente: @@user_mobile@@
Chat ID: @@chat_id@@
Tipo Chat: @@chat_type@@
ID Richiesta: @@operator_request_id@@
Data Richiesta: @@date_time@@
Ecco le ultime 5 interazioni sulla chat prima della richiesta: @@last_five_history@@

Procedi con il contattare il prospect quanto prima.
Grazie ADMIN',
    ],
    'additional_emails' => [
        'type' => 'varchar(255)',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'default' => '0',
    ],
    'operator_system_prompt' => [
        'type' => 'longtext',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'default' => 'Analyze this text string that a user wrote on our support chat (user prompt), reply with 1 if it is sufficiently probable tha it means that the user wants to speak to an operator. If it is not clear enough and in all other cases reply with a 0',
    ],
    'created_at' => [
        'type' => 'datetime'
    ],
    'updated_at' => [
        'type' => 'datetime'
    ],
];
