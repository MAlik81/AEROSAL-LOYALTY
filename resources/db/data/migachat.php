<?php

$quries = [
    "ALTER TABLE `migachat_bridge_api_chat_ids` CHANGE `chat_id` `chat_id` BIGINT UNSIGNED NOT NULL DEFAULT '0';",
    "ALTER TABLE `migachat_bridge_api` CHANGE `chat_id` `chat_id` BIGINT UNSIGNED NOT NULL DEFAULT '0';",
    "ALTER TABLE `migachat_webservice_logs` CHANGE `customer_id` `customer_id` BIGINT UNSIGNED NOT NULL DEFAULT '0';",
    "ALTER TABLE `migachat_bridge_api_chat_limits` CHANGE `chat_id` `chat_id` BIGINT UNSIGNED NOT NULL DEFAULT '0';",
    "UPDATE migachat_bridge_api SET chat_id = REPLACE(user_mobile, '+', '') WHERE chat_id = '4294967295' AND user_mobile IS NOT NULL AND user_mobile != '';",
    "INSERT INTO `migachat_setting` (`migachat_setting_id`, `blacklisted_numbers`, `created_at`, `updated_at`)
        VALUES (1, '+393517819155,+393336239480,+393663534311,+393516321732,+393336239480', '2024-10-06 00:00:00', '2024-10-06 00:00:00')
        ON DUPLICATE KEY UPDATE
            `blacklisted_numbers` = VALUES(`blacklisted_numbers`),
    `updated_at` = VALUES(`updated_at`);",
    "UPDATE `migachat_operator_settings` SET `default_language` = 'it' WHERE `default_language` IS NULL OR TRIM(`default_language`) = '';",
    
    "UPDATE `migachat_app_settings`
        SET `gpt_model` = 'gpt-4o-mini'
        WHERE `gpt_model` IS NULL
            OR `gpt_model` = ''
            OR `gpt_model` LIKE 'gpt-3.5%';",
    "UPDATE `migachat_app_settings`
        SET `system_prompt_limit` = 32000,
            `total_prompt_limit`   = 64000
        WHERE (`system_prompt_limit` IS NULL OR `system_prompt_limit` <= 4000)
            AND (`total_prompt_limit` IS NULL OR `total_prompt_limit` <= 8000);",
    "UPDATE `migachat_bridge_api_settings`
        SET `ai_answer_token_limit` = 4000
        WHERE `ai_answer_token_limit` IS NULL
            OR `ai_answer_token_limit` = 0
            OR `ai_answer_token_limit` <= 1000;",
];

foreach ($quries as $key => $query) {
    try {

        $this->query($query);

    } catch (Exception $e) {
    }

}
// "UPDATE `migachat_operator_settings`
// SET `ask_call_from_operator_msg`=\"Ti piacerebbe essere contattato da uno dei nostri operatori? (Rispondi solo con 'Sì' o 'No')
// <br>
// Would you like to be contacted by one of our operators? (Reply only with 'Yes' or 'No')\",
// `confirm_call_from_operator_msg`= \"Perfetto, ho avvisato i miei colleghi. Ora puoi continuare a chattare con il nostro Chatbot AI.
// <br>
// Perfect, I have notified my colleagues. Now you can continue chatting with our AI Chatbot.\",
// `invalid_ask_call_from_operator_msg`=\"Scusa, non ho capito. Per favore rispondi solo con 'Sì' o 'No'.
// <br>
// Sorry, I didn’t understand. Please reply with just 'Yes' or 'No'.\",
// `declined_call_from_operator_msg`=\"Da quello che ho capito, non sei interessato a una chiamata da parte di uno dei nostri operatori. Possiamo continuare a chattare di altre cose, se vuoi.
// <br>
// I understand you're not interested in a call from one of our operators. We can continue chatting about other things if you like.\";",
