<?php
/**
 * Class Migachat_Public_BridgeapiController
 *
 * This class handles API communication for the Migachat platform, particularly focused on
 * managing chat-based interactions, including translation, consent handling, operator requests,
 * and token limits. It extends the `Migachat_Controller_Default` class.
 *
 * Key Features:
 * - Manages chatbot API interactions using OpenAI's GPT models.
 * - Handles user consent for GDPR and commercial communications.
 * - Tracks token limits for overall usage and individual chat sessions.
 * - Manages operator requests and their notifications via email and webhooks.
 * - Supports multiple languages for better user experience.
 */

class Migachat_Public_BridgeapiController extends Migachat_Controller_Default
{
    private $languages_list = [
        "sq"  => "Albanian",
        "am"  => "Amharic",
        "ar"  => "Arabic",
        "an"  => "Aragonese",
        "hy"  => "Armenian",
        "ast" => "Asturian",
        "az"  => "Azerbaijani",
        "eu"  => "Basque",
        "be"  => "Belarusian",
        "bg"  => "Bulgarian",
        "ca"  => "Catalan",
        "ckb" => "Central Kurdish",
        "zh"  => "Chinese",
        "co"  => "Corsican",
        "hr"  => "Croatian",
        "cs"  => "Czech",
        "da"  => "Danish",
        "nl"  => "Dutch",
        "en"  => "English",
        "eo"  => "Esperanto",
        "et"  => "Estonian",
        "fo"  => "Faroese",
        "fil" => "Filipino",
        "fi"  => "Finnish",
        "fr"  => "French",
        "gl"  => "Galician",
        "ka"  => "Georgian",
        "de"  => "German",
        "el"  => "Greek",
        "haw" => "Hawaiian",
        "he"  => "Hebrew",
        "hu"  => "Hungarian",
        "is"  => "Icelandic",
        "id"  => "Indonesian",
        "ia"  => "Interlingua",
        "ga"  => "Irish",
        "it"  => "Italian",
        "ja"  => "Japanese",
        "kn"  => "Kannada",
        "kk"  => "Kazakh",
        "km"  => "Khmer",
        "ko"  => "Korean",
        "ku"  => "Kurdish",
        "ky"  => "Kyrgyz",
        "la"  => "Latin",
        "lv"  => "Latvian",
        "ln"  => "Lingala",
        "lt"  => "Lithuanian",
        "mk"  => "Macedonian",
        "mt"  => "Maltese",
        "no"  => "Norwegian",
        "oc"  => "Occitan",
        "pl"  => "Polish",
        "pt"  => "Portuguese",
        "qu"  => "Quechua",
        "ro"  => "Romanian",
        "ru"  => "Russian",
        "gd"  => "Scottish Gaelic",
        "sr"  => "Serbian",
        "sh"  => "Serbo",
        "sk"  => "Slovak",
        "sl"  => "Slovenian",
        "so"  => "Somali",
        "es"  => "Spanish",
        "sv"  => "Swedish",
        "tr"  => "Turkish",
        "uk"  => "Ukrainian",
    ];
    private $positiveResponses = [
        // One-word positive responses
        'yes',
        'agree',
        'great',
        'sure',
        'absolutely',
        'fantastic',
        'excellent',
        'awesome',
        'perfect',
        'definitely',
        'superb',
        'okay',
        'ok',
        'si',

        // Two-word positive responses
        'I agree',
        'very good',
        'sounds good',
        'excellent idea',
        'fantastic job',
        'great choice',
        'absolutely right',

        // Three-word positive responses
        'I completely agree',
        'fantastic job, indeed',
        'absolutely wonderful idea',
        'sounds like a plan',

        // English
        'yes',
        'agree',
        'great',
        'sure',
        'absolutely',
        'fantastic',
        'excellent',
        'awesome',
        'perfect',
        'definitely',
        'superb',
        'I agree',
        'very good',
        'sounds good',
        'excellent idea',
        'fantastic job',
        'great choice',
        'absolutely right',
        'I completely agree',
        'fantastic job, indeed',
        'absolutely wonderful idea',
        'sounds like a plan',

        // Spanish
        'sí',
        'de acuerdo',
        'genial',
        'seguro',
        'absolutamente',
        'fantástico',
        'excelente',
        'increíble',
        'perfecto',
        'definitivamente',
        'espléndido',
        'estoy de acuerdo',
        'muy bien',
        'suena bien',
        'excelente idea',
        'trabajo fantástico',
        'gran elección',
        'absolutamente correcto',
        'estoy completamente de acuerdo',
        'trabajo fantástico, de hecho',
        'idea absolutamente maravillosa',
        'suena como un plan',

        // French
        'oui',
        'd\'accord',
        'super',
        'certainement',
        'absolument',
        'fantastique',
        'excellent',
        'impressionnant',
        'parfait',
        'certainement',
        'superbe',
        'je suis d\'accord',
        'très bien',
        'ça sonne bien',
        'excellente idée',
        'travail fantastique',
        'excellent choix',
        'absolument juste',
        'je suis tout à fait d\'accord',
        'travail fantastique, en effet',
        'idée absolument merveilleuse',
        'ça semble être un plan',

        // German
        'ja',
        'einverstanden',
        'großartig',
        'sicher',
        'absolut',
        'fantastisch',
        'ausgezeichnet',
        'genial',
        'perfekt',
        'definitiv',
        'hervorragend',
        'ich stimme zu',
        'sehr gut',
        'klingt gut',
        'ausgezeichnete Idee',
        'fantastische Arbeit',
        'große Auswahl',
        'absolut richtig',
        'ich stimme vollkommen zu',
        'fantastische Arbeit, in der Tat',
        'absolut wunderbare Idee',
        'klingt wie ein Plan',

        // Italian
        'sì',
        'd\'accordo',
        'grande',
        'sicuro',
        'assolutamente',
        'fantastico',
        'eccellente',
        'impressionante',
        'perfetto',
        'decisamente',
        'superbo',
        'sono d\'accordo',
        'molto bene',
        'suona bene',
        'ottima idea',
        'lavoro fantastico',
        'ottima scelta',
        'assolutamente giusto',
        'sono completamente d\'accordo',
        'lavoro fantastico, davvero',
        'assolutamente meravigliosa idea',
        'sembra un piano',

        // Portuguese
        'sim',
        'concordo',
        'ótimo',
        'certamente',
        'absolutamente',
        'fantástico',
        'excelente',
        'incrível',
        'perfeito',
        'definitivamente',
        'esplêndido',
        'estou de acordo',
        'muito bom',
        'soa bem',
        'excelente ideia',
        'trabalho fantástico',
        'ótima escolha',
        'absolutamente certo',
        'concordo completamente',
        'trabalho fantástico, de fato',
        'ideia absolutamente maravilhosa',
        'soa como um plano',

        // Dutch
        'ja',
        'mee eens',
        'geweldig',
        'zeker',
        'absoluut',
        'fantastisch',
        'uitstekend',
        'geweldig',
        'perfect',
        'zeker',
        'superb',
        'ik ben het eens',
        'heel goed',
        'klinkt goed',
        'uitstekend idee',
        'fantastische prestatie',
        'geweldige keuze',
        'absoluut juist',
        'ik ben het volledig eens',
        'fantastisch werk, inderdaad',
        'absoluut geweldig idee',
        'klinkt als een plan',

        // Swedish
        'ja',
        'håller med',
        'fantastiskt',
        'säkert',
        'absolut',
        'fantastiskt',
        'utmärkt',
        'underbart',
        'perfekt',
        'absolut',
        'superb',
        'jag håller med',
        'mycket bra',
        'låter bra',
        'utmärkt idé',
        'fantastiskt jobb',
        'utmärkt val',
        'absolut rätt',
        'jag håller helt med',
        'fantastiskt jobb, verkligen',
        'helt underbar idé',
        'låter som en plan',

        // Russian
        'да',
        'согласен',
        'отлично',
        'конечно',
        'абсолютно',
        'фантастически',
        'отлично',
        'прекрасно',
        'перфектно',
        'определенно',
        'великолепно',
        'я согласен',
        'очень хорошо',
        'звучит хорошо',
        'отличная идея',
        'фантастическая работа',
        'отличный выбор',
        'абсолютно правильно',
        'я полностью согласен',
        'фантастическая работа, действительно',
        'абсолютно замечательная идея',
        'звучит как план',
    ];

    private $negativeResponses = [
        // One-word negative responses
        'no',
        'disagree',
        'bad',
        'wrong',
        'nope',
        'never',
        'awful',
        'terrible',
        'incorrect',
        'negative',
        'poor',
        'nah',
        'no way',

        // Two-word negative responses
        'I disagree',
        'not good',
        'bad idea',
        'terrible choice',
        'absolutely not',
        'no chance',
        'wrong move',
        'never agree',

        // Three-word negative responses
        'I completely disagree',
        'terrible choice, indeed',
        'absolutely awful idea',
        'not going to happen',

        // English
        'no',
        'disagree',
        'bad',
        'wrong',
        'nope',
        'never',
        'awful',
        'terrible',
        'incorrect',
        'negative',
        'poor',
        'I disagree',
        'not good',
        'bad idea',
        'terrible choice',
        'absolutely not',
        'no chance',
        'wrong move',
        'I completely disagree',
        'terrible choice, indeed',
        'absolutely awful idea',
        'not going to happen',

        // Spanish
        'no',
        'no estoy de acuerdo',
        'malo',
        'incorrecto',
        'nunca',
        'horrible',
        'terrible',
        'incorrecto',
        'negativo',
        'pobre',
        'nunca estoy de acuerdo',
        'no es bueno',
        'mala idea',
        'elección terrible',
        'absolutamente no',
        'ninguna oportunidad',
        'movimiento incorrecto',
        'discrepo completamente',
        'elección terrible, de hecho',
        'idea absolutamente horrible',
        'no va a suceder',

        // French
        'non',
        'pas d\'accord',
        'mauvais',
        'faux',
        'jamais',
        'horrible',
        'terrible',
        'incorrect',
        'négatif',
        'pauvre',
        'je ne suis pas d\'accord',
        'pas bon',
        'mauvaise idée',
        'choix terrible',
        'absolument pas',
        'aucune chance',
        'mauvaise décision',
        'je ne suis absolument pas d\'accord',
        'choix terrible, en effet',
        'idée absolument horrible',
        'cela n\'arrivera pas',

        // German
        'nein',
        'nicht einverstanden',
        'schlecht',
        'falsch',
        'nie',
        'schrecklich',
        'schlimm',
        'falsch',
        'negativ',
        'arm',
        'ich stimme nicht zu',
        'nicht gut',
        'schlechte Idee',
        'schreckliche Wahl',
        'absolut nicht',
        'keine Chance',
        'falsche Bewegung',
        'ich stimme völlig nicht zu',
        'schreckliche Wahl, in der Tat',
        'absolut schreckliche Idee',
        'wird nicht passieren',

        // Italian
        'no',
        'non sono d\'accordo',
        'male',
        'sbagliato',
        'mai',
        'orribile',
        'terribile',
        'errato',
        'negativo',
        'povero',
        'non sono d\'accordo',
        'non buono',
        'brutta idea',
        'scelta terribile',
        'assolutamente no',
        'nessuna possibilità',
        'mossa sbagliata',
        'non sono assolutamente d\'accordo',
        'scelta terribile, davvero',
        'idea assolutamente orribile',
        'non accadrà',

        // Portuguese
        'não',
        'discordo',
        'ruim',
        'errado',
        'nunca',
        'horrível',
        'terrível',
        'incorreto',
        'negativo',
        'pobre',
        'eu discordo',
        'não é bom',
        'má ideia',
        'escolha terrível',
        'absolutamente não',
        'sem chance',
        'movimento errado',
        'discordo completamente',
        'escolha terrível, de fato',
        'ideia absolutamente horrível',
        'não vai acontecer',

        // Dutch
        'nee',
        'oneens',
        'slecht',
        'verkeerd',
        'nooit',
        'verschrikkelijk',
        'vreselijk',
        'incorrect',
        'negatief',
        'arm',
        'ik ben het niet eens',
        'niet goed',
        'slecht idee',
        'verschrikkelijke keuze',
        'absoluut niet',
        'geen kans',
        'verkeerde zet',
        'ik ben het volledig oneens',
        'vreselijke keuze, inderdaad',
        'absoluut verschrikkelijk idee',
        'gaat niet gebeuren',

        // Swedish
        'nej',
        'håller inte med',
        'dåligt',
        'fel',
        'aldrig',
        'hemskt',
        'förskräckligt',
        'felaktig',
        'negativ',
        'fattig',
        'jag håller inte med',
        'inte bra',
        'dålig idé',
        'hemskt val',
        'absolut inte',
        'ingen chans',
        'fel drag',
        'jag håller helt inte med',
        'hemskt val, verkligen',
        'helt fruktansvärd idé',
        'kommer inte att hända',

        // Russian
        'нет',
        'не согласен',
        'плохо',
        'неправильно',
        'никогда',
        'ужасно',
        'страшно',
        'неверно',
        'негативно',
        'бедный',
        'я не согласен',
        'не хорошо',
        'плохая идея',
        'ужасный выбор',
        'абсолютно нет',
        'нет шансов',
        'неправильный ход',
        'я полностью не согласен',
        'ужасный выбор, действительно',
        'абсолютно ужасная идея',
        'это не произойдет',
    ];
    // // Example usage:
    // $userComment = "Yes, I agree with that idea!";  // Replace with the user's actual comment
    // $userComment = strtolower($userComment);

    /**
     * Handles incoming chat messages, processes responses via OpenAI's GPT API,
     * checks for token limits, and manages GDPR/commercial consent.
     */
    /**
     * Handles incoming chat messages via the Bridge API.
     *
     * This function processes user or agent messages, manages authentication, checks and enforces
     * token and usage limits, handles GDPR/commercial consent, operator requests, and logs all actions.
     * It supports multi-channel input, language detection, translation, and integrates with OpenAI's GPT models.
     *
     * Main Steps:
     * 1. Parse and validate incoming request parameters.
     * 2. Authenticate the request using the provided auth_token.
     * 3. Enforce global and per-chat token limits, including temporary/permanent blacklists.
     * 4. Detect language and translate messages as needed.
     * 5. Validate and store user information (name, mobile, email).
     * 6. Handle GDPR and commercial consent flows, including translation of consent texts.
     * 7. Prevent repeated messages and log all actions.
     * 8. Detect operator requests and manage operator notification (email/webhook).
     * 9. Prepare conversation history and system prompts for GPT.
     * 10. Generate AI responses using OpenAI GPT, handle token cut-off, and log responses.
     * 11. Return structured JSON responses for all outcomes.
     *
     * @return void Outputs JSON response and exits.
     */
    public function sendmessageAction()
    {
        try {

            $ai_awnser_prepend = '';
            // Extract incoming request details and bootstrap controller state.
            $requestContext = $this->initializeRequestContext();
            $params         = $requestContext['params'];
            $ws_log_data    = $requestContext['ws_log_data'];
            $chat_id_data   = $requestContext['chat_id_data'];

            $missingParamsResponse = $this->validateRequiredParams($params, $ws_log_data);
            if ($missingParamsResponse) {
                return $this->_sendJson($missingParamsResponse);
            } else {
                // Extract parameters
                $value_id = $params['instance_id'];

                // Load Bridge API settings and authenticate the request
                $instanceContext = $this->resolveInstanceContext($params, $ws_log_data);
                $bridge_obj      = $instanceContext['settings'];
                $message         = $instanceContext['message'];

                // bridge api tokens setup
                $chatid_duration = ($bridge_obj->getUserDuration()) ? $bridge_obj->getUserDuration() : 60;
                $chatid_tokens   = ($bridge_obj->getUserLimit()) ? $bridge_obj->getUserLimit() : 200000;

                $chatControlPayload = $this->handleChatControlCommands($params, $message, $value_id);
                if (null !== $chatControlPayload) {
                    return $this->_sendJson($chatControlPayload);
                }
                // check ovelall tokens limit here
                $this->enforceGlobalTokenLimit($value_id, $bridge_obj, null);

                // Retrieve Chatbot settings
                $setting_obj = new Migachat_Model_ChatbotSettings();
                $setting_obj->find(['value_id' => $value_id]);

                if ($setting_obj->getApiType() == 'chatgpt') { //chat GPT chatbot
                    $secret_key      = $setting_obj->getSecretKey();
                    $organization_id = $setting_obj->getOrganizationId();
                    if (empty($secret_key) || empty($organization_id)) {
                        throw new Exception(p__("Migachat", 'OpenAI API key or organization ID is not set.'));
                    }

                    $openai = new Migachat_Model_AssistantsGPTAPI($secret_key, $organization_id);

                    // Retrieve app settings
                    $app_setting_obj = new Migachat_Model_PromptSettings();
                    $app_setting_obj->find([
                        'value_id' => $value_id,
                    ]);
                    $history_tokens_limit = 128000;
                    $gpt_model            = 'gpt-4o-mini';
                    if ($app_setting_obj->getGptModel()) {
                        $gpt_model = $app_setting_obj->getGptModel();
                    }
                    $apiUrl          = 'https://api.openai.com/v1/chat/completions';
                    $secret_key      = $setting_obj->getSecretKey();
                    $organization_id = $setting_obj->getOrganizationId();

                    $name = "";
                    if (isset($params['name'])) {
                        $name                      = $params['name']; // Replace with your mobile number
                        $chat_id_data['user_name'] = $name;
                    }
                    if (isset($params['chat_id'])) {
                        $chat_id = $params['chat_id'];
                        if (! $name) {
                            $bridge_chat_obj = new Migachat_Model_BridgeAPI();
                            $name            = $bridge_chat_obj->getChatIDName($value_id, $chat_id); // already updated to chat_ids table
                        }
                    } else {
                        // get chat id by fetcheching the bigest existing chat_id +1
                        $bridge_chat_obj = new Migachat_Model_BridgeAPI();
                        $chat_id         = $bridge_chat_obj->getNewChatId($value_id); // already updated to chat_ids table
                    }
                    $chat_id_data['chat_id']  = $chat_id;
                    $chat_id_data['value_id'] = $value_id;

                    $chatIdentityContext = $this->prepareChatIdentity(
                        $params,
                        $app_setting_obj,
                        $setting_obj,
                        $chat_id_data,
                        $openai
                    );

                    $contactInfo = $chatIdentityContext['contact'];
                    $channel     = $chatIdentityContext['channel'];
                    $email       = $contactInfo['email'] ?? '';
                    $mobile      = $contactInfo['mobile'] ?? '';
                    if (! empty($contactInfo['name'])) {
                        $name = $contactInfo['name'];
                    }
                    $thread_id = $chatIdentityContext['thread_id'];

                    $check_ai_limit_duration = $this->checkAILimmitDuration($value_id, $chat_id);
                    $bridge_chatid_tokens    = (new Migachat_Model_BridgeAPI())->getChatIdTokens($value_id, $chatid_duration, $chat_id);

                    // getting last 4 conversations to translate the custome message accordingly
                    $two_chat_history              = (new Migachat_Model_BridgeAPI())->getHistoryMessages($value_id, $chat_id, 8);
                    $two_chat_history_conversation = [];
                    $chat_history_string           = "";
                    foreach ($two_chat_history as $key => $value) {
                        $chat_history_string .= ' ' . $value['message_content'];
                    }
                    $chat_history_string .= ' ' . $message;
                    $two_chat_history_conversation[] = [
                        'role'    => 'system',
                        'content' => "Detect the language of the given text with a focus on accurate language identification. Return the 2-letter language code for the detected language. If the language cannot be identified with reasonable certainty or if the text is ambiguous, default to returning 'it'. Ensure that the response is always a 2-letter language code, reflecting the detected or defaulted language.",
                    ];

                    $global_lang = 'it';
                    $chatAPI     = new Migachat_Model_ChatGPTAPI($apiUrl, $secret_key, $organization_id, $gpt_model);
                    $response    = $chatAPI->generateResponse($chat_history_string, $two_chat_history_conversation, 'admin', null);

                    // $user_chat_limit_responce = $bridge_obj->getUserChatLimitResponce();
                    if ($response[0] === true) {
                        // Log the response and update chat logs
                        $global_lang = $response[1];
                        if (strlen($global_lang) > 2) {
                            $global_lang = 'it';
                        }
                    }
                    $global_lang = strtoupper($global_lang);

                    $translate_system_prompt[] = [
                        'role'    => 'system',
                        'content' => "Translate the user-provided text into `$global_lang`. If the detected language of the text is already `$global_lang`, return the original text exactly as it is, without translating. Ensure that the translation preserves the original meaning, tone, and context as closely as possible. The output should consist only of the translated (or unchanged) text, with no additional content, explanations, or responses included. If the language cannot be detected, default to returning the original text without any modifications.",
                    ];

                    $chatLimitResult = $this->handleChatLimits([
                        'value_id'                        => $value_id,
                        'chat_id'                         => $chat_id,
                        'chatid_duration'                 => $chatid_duration,
                        'chatid_tokens'                   => $chatid_tokens,
                        'bridge_chatid_tokens'            => $bridge_chatid_tokens,
                        'bridge_obj'                      => $bridge_obj,
                        'chat_history_string'             => $chat_history_string,
                        'two_chat_history_conversation'   => $two_chat_history_conversation,
                        'global_lang'                     => $global_lang,
                        'translate_system_prompt'         => $translate_system_prompt,
                        'apiUrl'                          => $apiUrl,
                        'secret_key'                      => $secret_key,
                        'organization_id'                 => $organization_id,
                        'gpt_model'                       => $gpt_model,
                        'mobile'                          => $mobile,
                    ]);

                    if (! empty($chatLimitResult['payload'])) {
                        return $this->_sendJson($chatLimitResult['payload']);
                    }

                    $chat_id_data_exists = $chatLimitResult['limit_state']['chat_id_record'];
                    if (! $chat_id_data_exists->getId()) {
                        $chat_id_data_exists = $chatIdentityContext['chat_id_entity'];
                    }
                    $thread_id = $chat_id_data_exists->getThreadId() ?: $thread_id;

                    $system_channel_prompt = "The last message sent by the user was received from $channel";

                    if (isset($params['role']) && $params['role'] == 'agent') {
                        // Process agent role
                        $role                                         = $params['role'];
                        $consent_data                                 = [];
                        $consent_data['gdpr_consent']                 = 1;
                        $consent_data['commercial_consent']           = 1;
                        $consent_data['gdpr_consent_external']        = 1;
                        $consent_data['commercial_consent_external']  = 1;
                        $consent_data['gdpr_consent_timestamp']       = date("Y-m-d H:i:s");
                        $consent_data['commercial_consent_timestamp'] = date("Y-m-d H:i:s");
                        $consent_data['id']                           = $chat_id_data_exists->getId();
                        $chat_id_data_exists->addData($consent_data)->save();
                        $chatlogs_obj = new Migachat_Model_BridgeAPI();
                        $chatlog_data = [
                            'value_id'           => $value_id,
                            'chat_id'            => $chat_id,
                            'chatbot_setting_id' => $setting_obj->getId(),
                            'role'               => 'assistant',
                            'message_content'    => $this->removeEmojis($message),
                            'user_email'         => $email,
                            'user_name'          => $name,
                            'user_mobile'        => $mobile,
                            'is_sent'            => 1,
                            'channel'            => $channel,
                            'has_error'          => 0,
                            'is_read'            => 1,
                            'error_description'  => "",
                            'created_at'         => date("Y-m-d H:i:s"),
                            'updated_at'         => date("Y-m-d H:i:s"),
                        ];
                        $lastInsertId = $chatlogs_obj->addData($chatlog_data)->save();

                        $response                         = ['success' => true, 'chat_id' => $chat_id];
                        $message_to_thread                = $openai->addMessageToThread($thread_id, 'assistant', $message);
                        $error_array                      = [];
                        $error_array['value_id']          = $value_id;
                        $error_array['has_error']         = 0;
                        $error_array['error_description'] = p__("Migachat", 'Message Successfully Added');
                        $error_array['platform']          = 'Bridge API';
                        $error_array['message']           = $message;
                        $error_array['message_id']        = $lastInsertId->getId();
                        $error_array['customer_id']       = $chat_id;
                        $error_array['created_at']        = date("Y-m-d");
                        (new Migachat_Model_Webservicelogs())->addData($error_array)->save();

                        return $this->_sendJson($response);

                    } else {
                        // Process user role
                        $role = 'user';
                        // =============================
                        // gdpr and commercial concsent
                        $chat_id_consent     = [];
                        $chat_id_consent_obj = new Migachat_Model_ModelChatIds();
                        $chat_id_consent     = $chat_id_consent_obj->find(['value_id' => $value_id, 'chat_id' => $chat_id]);

                        // getting gdpr settings
                        $gdpr_settings = (new Migachat_Model_GDPR)->find(['value_id' => $value_id]);

                        $consentResult = $this->handleConsentFlow(
                            $chat_id_consent,
                            $gdpr_settings,
                            $translate_system_prompt,
                            $message,
                            [
                                'chat_api'                     => $chatAPI,
                                'chat_history_string'          => $chat_history_string,
                                'two_chat_history_conversation'=> $two_chat_history_conversation,
                                'value_id'                     => $value_id,
                                'chat_id'                      => $chat_id,
                                'setting_obj'                  => $setting_obj,
                                'email'                        => $email,
                                'name'                         => $name,
                                'mobile'                       => $mobile,
                                'channel'                      => $channel,
                                'gdpr_link'                    => $gdpr_settings->getGdprLink(),
                                'secret_key'                   => $secret_key,
                                'organization_id'              => $organization_id,
                            ]
                        );

                        if (! empty($consentResult['payload'])) {
                            return $this->_sendJson($consentResult['payload']);
                        }

                        if (! empty($consentResult['consent_satisfied']) && ! empty($consentResult['prepend'])) {
                            $ai_awnser_prepend = $consentResult['prepend'];
                        }
                        // gdpr and commercial consent endpoint
                        // =============================
                        // chackpoint for last two same messages
                        $last_two_messages_check_obj = new Migachat_Model_BridgeAPI();
                        $last_two_messages_check     = $last_two_messages_check_obj->lastTwoMessagesCheck($value_id, $chat_id, $message);
                        if ($last_two_messages_check) {
                            $this->logBridgeApiError(
                                [
                                    'value_id'    => $value_id,
                                    'customer_id' => $chat_id,
                                    'message'     => $message,
                                ],
                                p__("Migachat", 'Repeating messages more than 2 times is not allowed!')
                            );
                            throw new Exception(p__("Migachat", 'Repeating messages more than 2 times is not allowed!'));
                        }
                        // chackpoint for last two same messages ends

                        // =============================

                        // saving user message in DB
                        $chatlogs_obj = new Migachat_Model_BridgeAPI();
                        if (empty($ai_awnser_prepend)) {
                            $chatlog_data = [
                                'value_id'           => $value_id,
                                'chat_id'            => $chat_id,
                                'chatbot_setting_id' => $setting_obj->getId(),
                                'role'               => 'user',
                                'message_content'    => $this->removeEmojis($message),
                                'user_email'         => $email,
                                'user_mobile'        => $mobile,
                                'user_name'          => $name,
                                'is_sent'            => 1,
                                'channel'            => $channel,
                                'has_error'          => 0,
                                'is_read'            => 1,
                                'error_description'  => "",
                                'created_at'         => date("Y-m-d H:i:s"),
                                'updated_at'         => date("Y-m-d H:i:s"),
                            ];
                            $lastInsertId = $chatlogs_obj->addData($chatlog_data)->save();
                        } else {
                            // for AI auto responce after GDPR Consent
                            $lastInsertId = (new Migachat_Model_BridgeAPI())->find(['value_id' => $value_id, 'chat_id' => $chat_id, 'is_sent' => 3]);
                            $message      = $lastInsertId->getMessageContent();
                        }
                        // ====================================================
                        //analyze user prompt if he wants to speak to an operator
                        $operator_settings = (new Migachat_Model_OperatorSettings)->find(['value_id' => $value_id]);

                        $operatorResponse = $this->handleOperatorEscalation([
                            'operator_settings'        => $operator_settings,
                            'chat_id_consent'          => $chat_id_consent,
                            'global_lang'              => $global_lang,
                            'translator'               => function ($raw, $targetLang) use ($chatAPI, $translate_system_prompt) {
                                $prompt = "Just give the translation no other text and if the language of text is same than don't translate.Tranlate the text in {$targetLang}: " . $raw;
                                $resp   = $chatAPI->generateResponse($prompt, $translate_system_prompt, 'admin', null);
                                return ($resp[0] ?? false) ? (string) $resp[1] : $raw;
                            },
                            'message'                  => $message,
                            'value_id'                 => $value_id,
                            'chat_id'                  => $chat_id,
                            'secret_key'               => $secret_key,
                            'organization_id'          => $organization_id,
                            'last_insert_id'           => $lastInsertId,
                            'chatlogs_obj'             => $chatlogs_obj,
                        ]);

                        if ($operatorResponse !== null) {
                            return $this->_sendJson($operatorResponse);
                        }

                        // bridge api chat limit overall + chat id
                        $chat_id_limit_obj = new Migachat_Model_BridgrapiChatLimits();
                        $is_ai_turned_off  = $chat_id_limit_obj->find(['value_id' => $value_id, 'chat_id' => $chat_id, 'is_limit' => 0]);

                        if ($is_ai_turned_off->getId()) {
                            throw new Exception(p__("Migachat", 'AI is turned off for this chat id.'), 1);
                        }

                        $conversationContext = $this->buildConversationContext([
                            'value_id'                      => $value_id,
                            'chat_id'                       => $chat_id,
                            'message'                       => $message,
                            'name'                          => $name,
                            'channel'                       => $channel,
                            'bridge_settings'               => $bridge_obj,
                            'prompt_settings'               => $app_setting_obj,
                            'chatbot_settings'              => $setting_obj,
                            'chat_api'                      => $chatAPI,
                            'thread_id'                     => $thread_id,
                            'chat_history_string'           => $chat_history_string,
                            'two_chat_history_conversation' => $two_chat_history_conversation,
                            'system_channel_prompt'         => $system_channel_prompt,
                            'translate_system_prompt'       => $translate_system_prompt,
                            'gpt_model'                     => $gpt_model,
                            'api_url'                       => $apiUrl,
                            'secret_key'                    => $secret_key,
                            'organization_id'               => $organization_id,
                        ]);

                        $payload = $this->generateAndLogAiResponse(
                            $conversationContext,
                            [
                                'openai'            => $openai,
                                'chatlogs_obj'      => $chatlogs_obj,
                                'last_insert_id'    => $lastInsertId,
                                'ai_answer_prepend' => $ai_awnser_prepend,
                                'value_id'          => $value_id,
                                'chat_id'           => $chat_id,
                                'setting_obj'       => $setting_obj,
                                'email'             => $email,
                                'name'              => $name,
                                'mobile'            => $mobile,
                                'channel'           => $channel,
                            ]
                        );

                        return $this->_sendJson($payload);
                    }
                } else {
                    $this->logBridgeApiError(
                        ['value_id' => $value_id],
                        p__("Migachat", 'Application settings mismatch')
                    );
                    throw new Exception(p__("Migachat", 'Application settings mismatch'), 1);
                }
            }
        } catch (Exception $e) {
            // Handle exceptions and send error response
            $error_message  = $e->getMessage();
            $error_response = [
                'success' => false,
                'message' => $error_message,
            ];
            return $this->_sendJson($error_response);
            // return $error_response;
        }
        $this->_sendJson([]);
        exit;
    }

    public function removeEmojis($string)
    {
        return preg_replace('/\p{So}+/u', '', $string);
    }
    public function defaultSMTPEmail($data, $value_id)
    {

        $app_id = (new Migachat_Model_Setting())->getAppIdByValueId($value_id);
        $admin  = new Admin_Model_Admin();
        if (Siberian_Version::is('SAE')) {
            $admins      = $admin->findAll()->toArray();
            $admin_owner = $admin;
            $admin_owner->setData(current($admins));
        } else {
            $admins = $admin->getAllApplicationAdmins($app_id);
        }
        try {
            # @version 4.8.7 - SMTP
            $mail = new Siberian_Mail();
            $mail->setBodyHtml($data['body']);
            $mail->addTo($admins[0]['email']);
            foreach ($admins as $key => $value) {
                $mail->addCc($value['email']);
            }

            $mail->setSubject($data['subject']);
            if (! $mail->send()) {
                throw new Exception("Error Processing Request", 1);
            } else {
                $responce['success'] = true;
                $responce['message'] = 'success';
            }
        } catch (Exception $e) {
            $responce['success'] = false;
            $responce['message'] = $e->getMessage();
        }
        return $responce;
    }
    public function countTokens($text)
    {
        $byteSize = strlen(utf8_encode($text));
        return $this->bytesToTokens($byteSize);
    }
    private function bytesToTokens($bytes)
    {
        $tokensPerByte = 1.0 / 4.0; // Assume 4 bytes per token
        return intval(ceil($bytes * $tokensPerByte));
    }

    /**
     * Ensures the global token cap is respected for the provided instance.
     *
     * @param mixed                              $value_id
     * @param Migachat_Model_BridgeAPISettings   $bridgeSettings
     * @param array<int, array<string, mixed>>|null $conversationTotals
     *
     * @throws Exception When the global token limit is exceeded.
     */
    private function enforceGlobalTokenLimit($value_id, Migachat_Model_BridgeAPISettings $bridgeSettings, $conversationTotals = null)
    {
        $overallDuration = $bridgeSettings->getOverallDuration() ? $bridgeSettings->getOverallDuration() : 24;
        $overallLimit    = $bridgeSettings->getOverallLimit() ? $bridgeSettings->getOverallLimit() : 5000000;

        if (null === $conversationTotals) {
            $conversationTotals = (new Migachat_Model_BridgeAPI())->getOverAllTokens($value_id, $overallDuration);
        }

        $totalTokens = 0;
        if (isset($conversationTotals[0]['total_tokens_sum'])) {
            $totalTokens = $conversationTotals[0]['total_tokens_sum'];
        }

        if ($totalTokens > $overallLimit) {
            $this->logBridgeApiError(
                ['value_id' => $value_id],
                p__("Migachat", 'Overall token limit reached!')
            );

            $app_id      = (new Migachat_Model_Setting())->getAppIdByValueId($value_id);
            $application = (new Application_Model_Application())->find($app_id);
            $main_domain = __get('main_domain');

            $email_data            = [];
            $email_data['subject'] = p__("Migachat", 'Warning global API limit reached');
            $email_data['body']    = "In the past $overallDuration hours we reached more than $overallLimit tokens allowed, please check your system. APP_ID:$app_id , APP_NAME:" . $application->getName() . " , MAIN DOMAIN:$main_domain ";

            $this->defaultSMTPEmail($email_data, $value_id);

            throw new Exception(p__("Migachat", 'Overall token limit reached!'), 1);
        }

        return $conversationTotals;
    }

    /**
     * Handle GDPR/commercial consent translations, state transitions, and early responses.
     *
     * @param Migachat_Model_ModelChatIds $chatIdConsent
     * @param Migachat_Model_GDPR         $gdprSettings
     * @param array                       $translateSystemPrompt
     * @param string                      $message
     * @param array                       $context
     *
     * @return array{payload: array|null, prepend: string, consent_satisfied: bool}
     */
    private function handleConsentFlow($chatIdConsent, $gdprSettings, array $translateSystemPrompt, $message, array $context)
    {
        $result = [
            'payload'           => null,
            'prepend'           => '',
            'consent_satisfied' => false,
        ];

        if (! $gdprSettings || ! $gdprSettings->getId()) {
            $result['consent_satisfied'] = true;
            return $result;
        }

        if ($gdprSettings->getGdprActive() != '1') {
            $result['consent_satisfied'] = true;
            return $result;
        }

        $chatAPI = $context['chat_api'] ?? null;
        if (! $chatAPI instanceof Migachat_Model_ChatGPTAPI) {
            $result['consent_satisfied'] = true;
            return $result;
        }

        $chatHistoryString          = $context['chat_history_string'] ?? '';
        $twoChatHistoryConversation = $context['two_chat_history_conversation'] ?? [];

        $defaultLanguageKey  = $gdprSettings->getDefaultLanguage();
        $defaultLanguageName = $this->languages_list[$defaultLanguageKey] ?? ($this->languages_list['it'] ?? 'Italian');
        $language            = $defaultLanguageName;
        $prependTranslate    = null;

        $response = $chatAPI->generateResponse($chatHistoryString, $twoChatHistoryConversation, 'admin', null);
        if ($response[0] === true && ! empty($response[1])) {
            $language         = str_ireplace("\n", ' ', $response[1]);
            $prependTranslate = "Just give the translation of given text.No explainations, no other text. if the language of text is same than don't translate.Tranlate the text in $language: ";
        }

        $gdprWelcomeText       = $gdprSettings->getGdprWelcomeText();
        $commercialWelcomeText = $gdprSettings->getCommercialWelcomeText();
        $gdprSuccessText       = $gdprSettings->getGdprSuccessText();
        $gdprFailureText       = $gdprSettings->getGdprFailureText();

        $shouldTranslate = $prependTranslate && $language != $defaultLanguageName;

        $translateText = function ($text) use ($chatAPI, $translateSystemPrompt, $prependTranslate, $shouldTranslate) {
            if (! $shouldTranslate) {
                return $text;
            }

            $translationResponse = $chatAPI->generateResponse($prependTranslate . $text, $translateSystemPrompt, 'admin', null);
            if ($translationResponse[0] === true && ! empty($translationResponse[1])) {
                return $translationResponse[1];
            }

            return $text;
        };

        $chatId          = $context['chat_id'] ?? null;
        $valueId         = $context['value_id'] ?? null;
        $settingObj      = $context['setting_obj'] ?? null;
        $email           = $context['email'] ?? null;
        $name            = $context['name'] ?? null;
        $mobile          = $context['mobile'] ?? null;
        $channel         = $context['channel'] ?? null;
        $gdprLink        = $context['gdpr_link'] ?? $gdprSettings->getGdprLink();
        $secretKey       = $context['secret_key'] ?? null;
        $organizationId  = $context['organization_id'] ?? null;

        if ($chatIdConsent->getGdprConsent() == 2) {
            $chatlogs_obj = new Migachat_Model_BridgeAPI();
            $chatlog_data = [
                'value_id'           => $valueId,
                'chat_id'            => $chatId,
                'chatbot_setting_id' => ($settingObj) ? $settingObj->getId() : null,
                'role'               => 'user',
                'message_content'    => $this->removeEmojis($message),
                'user_email'         => $email,
                'user_mobile'        => $mobile,
                'user_name'          => $name,
                'is_sent'            => 3,
                'channel'            => $channel,
                'has_error'          => 0,
                'is_read'            => 1,
                'error_description'  => "",
                'created_at'         => date("Y-m-d H:i:s"),
                'updated_at'         => date("Y-m-d H:i:s"),
            ];
            $chatlogs_obj->addData($chatlog_data)->save();

            $gdprWelcomeText = $translateText($gdprWelcomeText);
            $chatIdConsent->setGdprConsent(3)->setCreatedAt(date('Y-m-d H:i:s'))->save();

            $result['payload'] = [
                'success' => true,
                'chat_id' => $chatId,
                'message' => $gdprWelcomeText . '<a href="' . $gdprLink . '">' . $gdprLink . '</a>',
            ];

            return $result;
        }

        if ($chatIdConsent->getGdprConsent() == 3) {
            $isPositive = $this->checkPositiveResponce($gdprSettings->getGdprWelcomeText(), $message, $secretKey, $organizationId);
            if ($isPositive) {
                $chatIdConsent->setGdprConsent(1)->setGdprConsentTimestamp(date('Y-m-d H:i:s'))->save();
                if ($gdprSettings->getCommercialActive() == '1') {
                    $commercialWelcomeText = $translateText($commercialWelcomeText);
                    $chatIdConsent->setCommercialConsent(3)->save();

                    $result['payload'] = [
                        'success' => true,
                        'chat_id' => $chatId,
                        'message' => $commercialWelcomeText,
                    ];

                    return $result;
                }

                $gdprSuccessText             = $translateText($gdprSuccessText);
                $result['prepend']           = $gdprSuccessText . ' ';
                $result['consent_satisfied'] = true;

                return $result;
            }

            $gdprFailureText      = $translateText($gdprFailureText);
            $result['payload']    = [
                'success' => true,
                'chat_id' => $chatId,
                'message' => $gdprFailureText,
            ];
            $result['prepend']           = '';
            $result['consent_satisfied'] = false;

            return $result;
        }

        if ($chatIdConsent->getGdprConsent() == 1) {
            if ($gdprSettings->getCommercialActive() == '1' && $chatIdConsent->getCommercialConsent() == 3) {
                $isPositive = $this->checkPositiveResponce($gdprSettings->getCommercialWelcomeText(), $message, $secretKey, $organizationId);
                if ($isPositive) {
                    $chatIdConsent->setCommercialConsent(1)->setCommercialConsentTimestamp(date('Y-m-d H:i:s'))->save();
                } else {
                    $chatIdConsent->setCommercialConsent(0)->save();
                }

                $gdprSuccessText             = $translateText($gdprSuccessText);
                $result['prepend']           = $gdprSuccessText . ' ';
                $result['consent_satisfied'] = true;

                return $result;
            }
        }

        $result['consent_satisfied'] = true;

        return $result;
    }

    public function checkPositiveResponce($question, $text, $secret_key, $organization_id)
    {
        $isPositive = false;
        foreach ($this->positiveResponses as $positiveWord) {
            if (strpos(strtolower($text), $positiveWord) !== false) {
                $isPositive = true;
                break;
            }
        }
        // echo $isPositive ? 'Positive' : 'Not Positive';
        if (! $isPositive) {
            $prompt             = "I asked to the user this: $question. The answer is this one : $text. Please analyze if the answer means Positive or Negitive, and give me the result with simple  positive (1) or negative (0).If unsure not sure about anything return 0. Provide only 1 or 0.do not add any explainations, just return 1 or 0.";
            $apiUrl             = 'https://api.openai.com/v1/chat/completions';
            $gpt_model          = 'gpt-4o-mini';
            $chatAPI            = new Migachat_Model_ChatGPTAPI($apiUrl, $secret_key, $organization_id, $gpt_model);
            $all_conversation[] = [
                'role'    => 'system',
                'content' => "You are a helpful assistant.",
            ];
            $response = $chatAPI->generateResponse($prompt, $all_conversation, 'admin', 100);
            if ($response[0] === true) {
                return $response[1];
            } else {
                return 0;
            }
        } else {
            return 1;
        }
    }

    /**
     * Handle temporary blacklist checks and chat control commands for a chat request.
     *
     * @param array  $params   The full request parameters.
     * @param string $message  The decoded incoming message payload.
     * @param int    $value_id The current instance value identifier.
     *
     * @return array|null Returns a JSON-ready payload to send immediately, or null to continue.
     */
    private function handleChatControlCommands(array $params, $message, $value_id)
    {
        if (! isset($params['chat_id']) || ! $params['chat_id']) {
            return null;
        }

        $chat_id_for_limit = $params['chat_id'];

        $temp_blacklist_obj   = new Migachat_Model_TemporaryBlaclist();
        $check_temp_blacklist = $temp_blacklist_obj->find(['value_id' => $value_id, 'chat_id' => $chat_id_for_limit]);
        if ($check_temp_blacklist->getId()) {
            return [
                'status'  => 'failure',
                'message' => p__("Migachat", 'You are in temporary blacklist, please try again later.'),
            ];
        }

        $normalizedMessage = is_string($message) ? strtolower($message) : '';

        if ($normalizedMessage === '##off##') {
            $chat_id_limit_obj = new Migachat_Model_BridgrapiChatLimits();
            $is_ai_turned_off  = $chat_id_limit_obj->find(['value_id' => $value_id, 'chat_id' => $chat_id_for_limit, 'is_limit' => 0]);
            if (! $is_ai_turned_off->getId()) {
                $chat_id_limit_data = [
                    'value_id'   => $value_id,
                    'chat_id'    => $chat_id_for_limit,
                    'is_limit'   => 0,
                    'ai_off_at'  => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];

                if ((new Migachat_Model_BridgrapiChatLimits())->addData($chat_id_limit_data)->save()) {
                    return [
                        'success' => true,
                        'message' => p__("Migachat", 'AI turned OFF for this chat id, untill it is turned back ON.'),
                        'chat_id' => $chat_id_for_limit,
                    ];
                }

                return [
                    'success' => true,
                    'message' => p__("Migachat", 'Error while turning OFF the AI for this chat id.'),
                    'chat_id' => $chat_id_for_limit,
                ];
            }

            return [
                'success' => true,
                'message' => p__("Migachat", 'AI turned OFF for this chat id, untill it is turned back ON.'),
                'chat_id' => $chat_id_for_limit,
            ];
        }

        if ($normalizedMessage === '##on##') {
            // remove chatid in limits table if exists and not permanently
            $chat_id_limit_obj = new Migachat_Model_BridgrapiChatLimits();
            $is_ai_turned_off  = $chat_id_limit_obj->find(['value_id' => $value_id, 'chat_id' => $chat_id_for_limit, 'is_limit' => 0]);
            if ($is_ai_turned_off->getId() && ! $is_ai_turned_off->getIsLimit()) {
                $del_resp = $chat_id_limit_obj->delete();
                if ($del_resp) {
                    return [
                        'success'    => true,
                        'message'    => p__("Migachat", 'AI turned ON for this chat id, untill it is turned back OFF.'),
                        'chat_id'    => $chat_id_for_limit,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                }

                return [
                    'success' => true,
                    'message' => p__("Migachat", 'Error while turning ON the AI for this chat id.'),
                    'chat_id' => $chat_id_for_limit,
                ];
            }

            return [
                'success'    => true,
                'message'    => p__("Migachat", 'AI turned ON for this chat id, untill it is turned back OFF.'),
                'chat_id'    => $chat_id_for_limit,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }

        if ($normalizedMessage === '##limitoff##' && false) {
            $chat_id_limit_obj   = new Migachat_Model_BridgrapiChatLimits();
            $is_limit_turned_off = $chat_id_limit_obj->find(['value_id' => $value_id, 'chat_id' => $chat_id_for_limit, 'is_limit' => 1]);
            if (! $is_limit_turned_off->getId()) {
                $chat_id_limit_data = [
                    'value_id'     => $value_id,
                    'chat_id'      => $chat_id_for_limit,
                    'is_limit'     => 1,
                    'limit_off_at' => date('Y-m-d H:i:s'),
                    'created_at'   => date('Y-m-d H:i:s'),
                    'updated_at'   => date('Y-m-d H:i:s'),
                ];

                $blacklisted_numbers = "";
                $setting             = new Migachat_Model_Setting();
                $setting->find(1);
                $blacklisted_numbers = $setting->getBlacklistedNumbers();
                $blacklisted_numbers = ',' . trim($blacklisted_numbers, ',') . ',';                 // normalize with commas around
                $blacklisted_numbers = str_replace(',' . $mobile . ',', ',', $blacklisted_numbers); // remove safely
                $blacklisted_numbers = trim($blacklisted_numbers, ',');                             // clean up again
                $setting->setBlacklistedNumbers($blacklisted_numbers)->save();

                if ((new Migachat_Model_BridgrapiChatLimits())->addData($chat_id_limit_data)->save()) {
                    return [
                        'success' => true,
                        'message' => p__("Migachat", 'Token limit turned OFF permanantly for this chat id.'),
                        'chat_id' => $chat_id_for_limit,
                    ];
                }

                return [
                    'success' => true,
                    'message' => p__("Migachat", 'Error while turning OFF the limit for this chat id.'),
                    'chat_id' => $chat_id_for_limit,
                ];
            }

            return [
                'success' => true,
                'message' => p__("Migachat", 'Token limit turned OFF permanantly for this chat id.'),
                'chat_id' => $chat_id_for_limit,
            ];
        }

        return null;
    }

    private function checkAILimmitDuration($value_id, $chat_id)
    {

        // Retrieve Bridge API settings
        $bridge_obj = new Migachat_Model_BridgeAPISettings();
        $bridge_obj->find(['value_id' => $value_id]);
                                                   // Assuming $x is the number of hours
        $x = $bridge_obj->getSuspentionDuration(); // Replace with your desired value

        // Calculate the timestamp X hours ago
        $timestampXHoursAgo = date('Y-m-d H:i:s', strtotime("-$x hours"));

        $chat_id_limit_obj = new Migachat_Model_BridgrapiChatLimits();
        // Query to retrieve records more than X hours old
        $is_ai_turned_off = $chat_id_limit_obj->find([
            'value_id' => $value_id,
            'chat_id'  => $chat_id,
            'is_limit' => 0,
        ]);
        // $this->_db->delete("migachat_bridge_api", ['value_id' => $value_id,'migachat_bridge_id NOT IN (?)' => $first_two_ids]);

        // Check if any records are found
        if ($is_ai_turned_off) {
            if ($is_ai_turned_off->getId() && ! $is_ai_turned_off->getIsLimit() && strtotime($timestampXHoursAgo) > strtotime($is_ai_turned_off->getAiOffAt())) {
                $del_resp = $chat_id_limit_obj->delete();
            }
        }

        $chat_id_limit_obj2 = new Migachat_Model_BridgrapiChatLimits();
        // Query to retrieve records more than X hours old
        $is_limit_turned_off = $chat_id_limit_obj2->find([
            'value_id' => $value_id,
            'chat_id'  => $chat_id,
            'is_limit' => 1,
        ]);

        // Check if any records are found
        if ($is_limit_turned_off) {
            if ($is_limit_turned_off->getId() && $is_limit_turned_off->getIsLimit() && strtotime($timestampXHoursAgo) > strtotime($is_limit_turned_off->getLimitOffAt())) {
                $del_resp = $chat_id_limit_obj2->delete();
            }
        }
        return true;

    }
    private function checkOperator($prompt, $message, $secret_key, $organization_id)
    {
        $isOperator         = false;
        $apiUrl             = 'https://api.openai.com/v1/chat/completions';
        $gpt_model          = 'gpt-4o-mini';
        $chatAPI            = new Migachat_Model_ChatGPTAPI($apiUrl, $secret_key, $organization_id, $gpt_model);
        $all_conversation[] = [
            'role'    => 'system',
            'content' => $prompt,
        ];
        $response = $chatAPI->generateResponse($message, $all_conversation, 'admin', 100);
        if ($response[0] === true) {
            return $response[1];
        } else {
            return 0;
        }
    }
    private function sendOperatorWebhook($operator_reqested, $chat_id_consent, $operator_id, $operator_settings, $rev_five_conversation)
    {
        $webhook_data                = [];
        $webhook_data['app_id']      = $operator_reqested['app_id'];
        $webhook_data['instance_id'] = $operator_reqested['value_id'];
        $webhook_data['chat_type']   = $operator_reqested['bot_type'];
        $webhook_data['user_id']     = $operator_reqested['user_id'];
        $webhook_data['status']      = $operator_reqested['status'];
        $webhook_data['date_time']   = $operator_reqested['created_at'];

        $application = (new Application_Model_Application())->find($webhook_data['app_id']);
        $app_name    = $application->getName();

        $webhook_data['app_name']            = $app_name;
        $webhook_data['operator_request_id'] = $operator_id;
        $webhook_data['user_email']          = ($chat_id_consent->getUserEmail()) ?? '--';
        $webhook_data['user_mobile']         = ($chat_id_consent->getUserMobile()) ?? '--';
        $webhook_data['user_name']           = ($chat_id_consent->getUserName()) ?? '--';
        $webhook_data['last_five_history']   = $rev_five_conversation;
        // implement curl post request to to send $webhookdata
        $url     = $operator_settings->getWebhookUrl();
        $ch      = curl_init($url);
        $payload = json_encode($webhook_data);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;

    }
    private function sendOperatorEmail($operator_reqested, $chat_id_consent, $operator_id, $operator_settings, $rev_five_conversation)
    {
        $webhook_data                = [];
        $webhook_data['app_id']      = $operator_reqested['app_id'];
        $webhook_data['instance_id'] = $operator_reqested['value_id'];
        $webhook_data['chat_type']   = $operator_reqested['bot_type'];
        $webhook_data['user_id']     = $operator_reqested['user_id'];
        $webhook_data['status']      = $operator_reqested['status'];
        $webhook_data['date_time']   = $operator_reqested['created_at'];

        $application = (new Application_Model_Application())->find($webhook_data['app_id']);
        $app_name    = $application->getName();

        $webhook_data['app_name']            = $app_name;
        $webhook_data['operator_request_id'] = $operator_id;
        $webhook_data['user_email']          = $chat_id_consent->getUserEmail();
        $webhook_data['user_mobile']         = $chat_id_consent->getUserMobile();
        $webhook_data['user_name']           = $chat_id_consent->getUserName();

        $subject           = $operator_settings->getEmailSubject();
        $body              = $operator_settings->getEmailTemplate();
        $body              = str_replace(PHP_EOL, "<br>", $body);
        $body              = str_replace('/n', "<br>", $body);
        $additional_emails = $operator_settings->getAdditionalEmails();

        $placeholders = [
            '@@app_id@@',
            '@@app_name@@',
            '@@instance_id@@',
            '@@user_name@@',
            '@@user_email@@',
            '@@user_mobile@@',
            '@@chat_id@@',
            '@@chat_type@@',
            '@@operator_request_id@@',
            '@@date_time@@',
            '@@last_five_history@@',
        ];
        $values = [
            $webhook_data['app_id'],
            $webhook_data['app_name'],
            $webhook_data['instance_id'],
            ($webhook_data['user_name']) ?? '--',
            ($webhook_data['user_email']) ?? '--',
            ($webhook_data['user_mobile']) ?? '--',
            $webhook_data['user_id'],
            $webhook_data['chat_type'],
            $webhook_data['operator_request_id'],
            $webhook_data['date_time'],
            $rev_five_conversation,
        ];
        $subject = str_replace($placeholders, $values, $subject);
        $body    = str_replace($placeholders, $values, $body);
        // implement curl post request to to send $webhookdata
        $url   = $operator_settings->getWebhookUrl();
        $admin = new Admin_Model_Admin();

        if (Siberian_Version::is('SAE')) {
            $admins      = $admin->findAll()->toArray();
            $admin_owner = $admin;
            $admin_owner->setData(current($admins));
        } else {
            $admins = $admin->getAllApplicationAdmins($webhook_data['app_id']);
        }
        try {
            # @version 4.8.7 - SMTP
            $mail = new Siberian_Mail();
            $mail->setBodyHtml($body);
            if (! $additional_emails) {
                $mail->addTo($admins[0]['email']);
                foreach ($admins as $key => $value) {
                    $mail->addCc($value['email']);
                }
            }
            if ($additional_emails) {
                $exploded_emails = explode(',', $additional_emails);
                $email_to        = $admins[0]['email'];
                foreach ($exploded_emails as $key => $value) {
                    $mail->addCc($value);
                    $email_to = $value;
                }
                $mail->addTo($email_to);
            }
            $mail->setSubject($subject);
            if (! $mail->send()) {
                throw new Exception("Error Processing Request", 1);
            } else {
                $responce['success'] = true;
                $responce['message'] = 'success';
            }
        } catch (Exception $e) {
            $responce['success'] = false;
            $responce['message'] = $e->getMessage();
        }
        return $responce;
    }

    /**
     * Prepares request, logging, and chat state containers used throughout the controller.
     */
    private function initializeRequestContext()
    {
        $params = $this->extractRequestParams();

        $ws_log_data = [];
        foreach (['instance_id', 'message', 'auth_token'] as $field) {
            if (isset($params[$field])) {
                $ws_log_data[$field] = $params[$field];
            }
        }

        return [
            'params'       => $params,
            'ws_log_data'  => $ws_log_data,
            'chat_id_data' => [],
        ];
    }

    /**
     * Handles chat-specific limit enforcement and returns either a translated
     * limit payload or the refreshed chat limit state when processing may
     * continue.
     *
     * @param array $context
     *
     * @return array{payload: array|null, limit_state: array}
     */
    private function prepareChatIdentity(
        array $params,
        Migachat_Model_PromptSettings $promptSettings,
        Migachat_Model_ChatbotSettings $chatbotSettings,
        array $chatIdData,
        $assistantsApi = null
    ) {
        $valueId = $chatIdData['value_id'] ?? null;
        $chatId  = $chatIdData['chat_id'] ?? null;

        if (! $valueId || ! $chatId) {
            throw new Exception(p__("Migachat", 'Missing chat identity context.'));
        }

        $allowedChannels = ['APP', 'WHATSAPP', 'TELEGRAM', 'MESSENGER', 'WEB', 'EMAIL', 'FB', 'INSTAGRAM', 'LINKEDIN', 'OTHER'];
        $channel         = 'WEB';
        if (! empty($params['channel'])) {
            $normalizedChannel = strtoupper(trim($params['channel']));
            if (! in_array($normalizedChannel, $allowedChannels, true)) {
                $error_message = p__("Migachat", 'Channel not allowed') . $normalizedChannel;
                $this->logBridgeApiError(
                    [
                        'value_id'    => $valueId,
                        'customer_id' => $chatId,
                    ],
                    $error_message
                );
                throw new Exception(p__("Migachat", 'Channel not allowed'), 1);
            }
            $channel = $normalizedChannel;
        }

        $email = '';
        if (! empty($params['email'])) {
            $email   = trim($params['email']);
            $pattern = '/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/';
            if (! preg_match($pattern, $email)) {
                $this->logBridgeApiError(
                    [
                        'value_id'    => $valueId,
                        'customer_id' => $chatId,
                    ],
                    p__("Migachat", 'Invalide email format')
                );
                throw new Exception(p__("Migachat", 'Invalide Email format'), 1);
            }
        }

        $mobile = '';
        if (! empty($params['mobile'])) {
            $mobile  = trim($params['mobile']);
            $pattern = '/^\+[0-9]{9,}$/';
            if (! preg_match($pattern, $mobile)) {
                $error_message = p__("Migachat", 'Invalide mobile number format') . ' ' . $mobile;
                $this->logBridgeApiError(
                    [
                        'value_id'    => $valueId,
                        'customer_id' => $chatId,
                    ],
                    $error_message
                );
                throw new Exception(p__("Migachat", 'Invalide mobile number format'), 1);
            }

            $setting = new Migachat_Model_Setting();
            $setting->find(1);

            $global_blacklisted_numbers = $this->normalizeNumberList($setting->getBlacklistedNumbers());
            if (! empty($global_blacklisted_numbers) && in_array($mobile, $global_blacklisted_numbers, true)) {
                $error_message = p__("Migachat", 'Mobile number is blacklisted') . ' ' . $mobile;
                $this->logBridgeApiError(
                    [
                        'value_id'    => $valueId,
                        'customer_id' => $chatId,
                    ],
                    $error_message
                );
                throw new Exception(p__("Migachat", 'Mobile number is blacklisted'), 1);
            }

            $permanent_blacklisted_numbers = $this->normalizeNumberList($promptSettings->getPermanentBlacklistedMobileNumbers());
            if (! empty($permanent_blacklisted_numbers) && in_array($mobile, $permanent_blacklisted_numbers, true)) {
                $error_message = p__("Migachat", 'Mobile number is blacklisted') . ' ' . $mobile;
                $this->logBridgeApiError(
                    [
                        'value_id'    => $valueId,
                        'customer_id' => $chatId,
                    ],
                    $error_message
                );
                throw new Exception(p__("Migachat", 'Mobile number is blacklisted'), 1);
            }
        }

        $existingChatIdentity = (new Migachat_Model_ModelChatIds())->find([
            'value_id' => $valueId,
            'chat_id'  => $chatId,
        ]);

        $name = isset($chatIdData['user_name']) ? trim((string) $chatIdData['user_name']) : '';

        $upsertData = [
            'value_id' => $valueId,
            'chat_id'  => $chatId,
        ];

        if ($name !== '') {
            $upsertData['user_name'] = $name;
        }

        if ($email !== '') {
            $upsertData['user_email'] = $email;
        }

        if ($mobile !== '') {
            $upsertData['user_mobile'] = $mobile;
        }

        if ($existingChatIdentity->getId()) {
            $upsertData['id'] = $existingChatIdentity->getId();
        } else {
            $upsertData['created_at'] = date('Y-m-d H:i:s');
        }

        $chatIdEntity = (new Migachat_Model_ModelChatIds())->addData($upsertData)->save();

        $threadId = $chatIdEntity->getThreadId();
        if (empty($threadId) && $chatbotSettings->getUseAssistant() == "1" && $assistantsApi) {
            $meta_data = [
                'value_id'   => (string) $valueId,
                'chat_id'    => (string) $chatId,
                'created_at' => date('Y-m-d H:i:s'),
            ];

            $new_thread = $assistantsApi->createThread($meta_data);
            if (isset($new_thread['id']) && ! empty($new_thread['id'])) {
                $threadId = $new_thread['id'];
                $chatIdEntity->setThreadId($threadId)->save();
            } else {
                throw new Exception(p__("Migachat", 'Failed to create a new thread. Please try again later.'));
            }
        }

        $resolvedEmail  = $email !== '' ? $email : $chatIdEntity->getUserEmail();
        $resolvedMobile = $mobile !== '' ? $mobile : $chatIdEntity->getUserMobile();
        $resolvedName   = $name !== '' ? $name : trim((string) $chatIdEntity->getUserName());

        return [
            'channel'        => $channel,
            'contact'        => [
                'email'  => $resolvedEmail,
                'mobile' => $resolvedMobile,
                'name'   => $resolvedName,
            ],
            'chat_id_entity' => $chatIdEntity,
            'thread_id'      => $threadId,
        ];
    }

    /**
     * Build the conversation payload and metadata required for OpenAI calls.
     */
    private function buildConversationContext(array $context)
    {
        $valueId        = $context['value_id'] ?? null;
        $chatId         = $context['chat_id'] ?? null;
        $originalMsg    = (string) ($context['message'] ?? '');
        $name           = isset($context['name']) ? (string) $context['name'] : '';
        $channel        = isset($context['channel']) ? (string) $context['channel'] : '';
        $bridgeSettings = $context['bridge_settings'] ?? null;
        $promptSettings = $context['prompt_settings'] ?? null;
        $chatbotSettings = $context['chatbot_settings'] ?? null;
        $chatApi        = $context['chat_api'] ?? null;
        $threadId       = $context['thread_id'] ?? null;
        $systemChannelPrompt = (string) ($context['system_channel_prompt'] ?? '');
        $gptModel            = $context['gpt_model'] ?? 'gpt-4o-mini';
        $apiUrl              = $context['api_url'] ?? '';
        $secretKey           = $context['secret_key'] ?? '';
        $organizationId      = $context['organization_id'] ?? '';

        $chatHistoryString           = $context['chat_history_string'] ?? '';
        $twoChatHistoryConversation  = $context['two_chat_history_conversation'] ?? [];
        $translateSystemPrompt       = $context['translate_system_prompt'] ?? [];

        if (! $chatApi instanceof Migachat_Model_ChatGPTAPI) {
            $chatApi = new Migachat_Model_ChatGPTAPI($apiUrl, $secretKey, $organizationId, $gptModel);
        }

        $assistantContext = [
            'use_assistant' => ($chatbotSettings && $chatbotSettings->getUseAssistant() == "1"),
            'thread_id'     => $threadId,
        ];

        if ($assistantContext['use_assistant']) {
            $assistantId = $promptSettings ? $promptSettings->getAssistantId() : null;
            if (empty($assistantId)) {
                throw new Exception("Assistant ID is not set in chatbot settings");
            }

            $assistant = (new Migachat_Model_Assistants())->find(['assistant_id' => $assistantId]);
            if (! $assistant->getId()) {
                throw new Exception("Assistant not found with ID: " . $assistantId);
            }

            $fileIds = $assistant->getOpenaiFileIds();
            $options = is_string($fileIds) ? json_decode($fileIds, true) : ($fileIds ? $fileIds : false);
            $opts    = [
                'truncation_strategy' => [
                    'type'          => 'last_messages',
                    'last_messages' => 10,
                ],
            ];

            if ($options) {
                $opts['tool_resources'] = [
                    'file_search' => [
                        'vector_store_ids' => is_string($fileIds) ? json_decode($fileIds, true) : ($fileIds ? $fileIds : []),
                    ],
                ];
            }

            $assistantContext['assistant_id']       = $assistantId;
            $assistantContext['assistant_options']  = $options;
            $assistantContext['assistant_run_opts'] = $opts;

            $preparedMessage = $originalMsg;

            $context['chat_api']                      = $chatApi;
            $context['conversation']                  = [];
            $context['prepared_message']              = $preparedMessage;
            $context['message']                       = $preparedMessage;
            $context['assistant_context']             = $assistantContext;
            $context['max_tokens']                    = $bridgeSettings ? $bridgeSettings->getAiAnswerTokenLimit() : null;
            $context['user_max_tokens_responce']      = $bridgeSettings ? $bridgeSettings->getAiAnswerTokenLimitMsg() : 'vuoi che continui?';
            $context['last_message_max_token_exeed']  = false;
            $context['translate_system_prompt']       = $translateSystemPrompt;
            $context['chat_history_string']           = $chatHistoryString;
            $context['two_chat_history_conversation'] = $twoChatHistoryConversation;
            $context['name']                          = $name;
            $context['channel']                       = $channel;

            return $context;
        }

        $completePrompt = "You are a helpful assistant. if the answer include a link add always the complete url starting with https://. Use the name of customer sending in prompt of role user.";
        if ($promptSettings && $promptSettings->getSystemPrompt()) {
            $completePrompt = $promptSettings->getSystemPrompt() . ' if the answer include a link add always the complete url starting with https://. Use the name of customer sending in prompt of role user.';
        }
        $completePrompt .= ' ' . $originalMsg . ' ';

        $systemPromptTokenLimit = round((new Migachat_Model_ModelTokens())->getSystemPromptTokens($valueId), 0);
        $historyTokens          = (new Migachat_Model_ModelTokens())->getHistoryTokens($valueId);
        $historyTokensLimit     = $historyTokens[0] + $systemPromptTokenLimit;
        $historyMessagesLimit   = $historyTokens[1];

        $chatHistory = (new Migachat_Model_BridgeAPI())->getHistoryMessages($valueId, $chatId, $historyMessagesLimit);

        $allConversation            = [];
        $lastMessageMaxTokenExceeded = false;
        foreach ($chatHistory as $key => $entry) {
            $completePrompt .= $entry['message_content'];
            if ((new Migachat_Model_Setting())->countTokens($completePrompt) > ($historyTokensLimit - ((5 * $historyTokensLimit) / 100))) {
                break;
            }

            $pattern = '/[^a-zA-Z0-9]+/';
            if ($entry['role'] == 'user') {
                if ($name !== '') {
                    $allConversation[] = [
                        'role'    => 'user',
                        'name'    => preg_replace($pattern, '-', $name),
                        'content' => urldecode($entry['message_content']),
                    ];
                } else {
                    $allConversation[] = [
                        'role'    => 'user',
                        'content' => urldecode($entry['message_content']),
                    ];
                }
            } else {
                $messageContent = $entry['message_content'];
                if ($key == 0 && $entry['max_token_exeed']) {
                    $messageContent             = str_replace($entry['max_token_responce'], ' ', $messageContent);
                    $lastMessageMaxTokenExceeded = true;
                }
                $allConversation[] = [
                    'role'    => 'assistant',
                    'content' => $messageContent,
                ];
            }
        }

        if ($promptSettings && $promptSettings->getId()) {
            if ($promptSettings->getPromptChatgptActive()) {
                $tokenLimit = (new Migachat_Model_ModelTokens())->find(['model_name' => $gptModel])->getTokens();
                if (! $tokenLimit) {
                    $k8   = ['gpt-4', 'gpt-4-0613', 'gpt-4-0314', 'code-davinci-002'];
                    $k16  = ['gpt-3.5-turbo-16k', 'gpt-3.5-turbo-16k-0613'];
                    $k32  = ['gpt-4-32k', 'gpt-4-32k-0613', 'gpt-4-32k-0314', 'code-davinci-002'];
                    $k128 = ['gpt-4o-mini', 'gpt-4-vision-preview', 'chatgpt-4o-latest', 'gpt-4o-mini-2024-07-18', 'gpt-4o-2024-08-06', 'gpt-4o-2024-05-13', 'gpt-4o'];
                    if (in_array($gptModel, $k8)) {
                        $tokenLimit = 8000;
                    } elseif (in_array($gptModel, $k16)) {
                        $tokenLimit = 16000;
                    } elseif (in_array($gptModel, $k32)) {
                        $tokenLimit = 32000;
                    } elseif (in_array($gptModel, $k128)) {
                        $tokenLimit = 128000;
                    } else {
                        $tokenLimit = 4000;
                    }
                }

                $systemPrompt = $promptSettings->getSystemPrompt();
                if ((new Migachat_Model_Setting())->countTokens($systemPrompt) < $systemPromptTokenLimit) {
                    $allConversation[] = [
                        'role'    => 'system',
                        'content' => $systemPrompt . '. ' . $systemChannelPrompt,
                    ];
                } else {
                    $allConversation[] = [
                        'role'    => 'system',
                        'content' => 'You are a helpful assistant. if the answer include a link add always the complete url starting with https://. Use the name of customer sending in prompt of role user.' . $systemChannelPrompt,
                    ];
                }
            } else {
                $allConversation[] = [
                    'role'    => 'system',
                    'content' => 'You are a helpful assistant. if the answer include a link add always the complete url starting with https://. Use the name of customer sending in prompt of role user.' . $systemChannelPrompt,
                ];
            }
        } else {
            $allConversation[] = [
                'role'    => 'system',
                'content' => 'You are a helpful assistant. if the answer include a link add always the complete url starting with https://. Use the name of customer sending in prompt of role user.' . $systemChannelPrompt,
            ];
        }

        $allConversationReversed = array_reverse($allConversation);

        $maxTokens             = $bridgeSettings ? $bridgeSettings->getAiAnswerTokenLimit() : null;
        $userMaxTokensResponse = $bridgeSettings ? $bridgeSettings->getAiAnswerTokenLimitMsg() : 'vuoi che continui?';
        $preparedMessage       = $originalMsg;

        if ($this->checkPositiveResponce($userMaxTokensResponse, $originalMsg, $secretKey, $organizationId) && $lastMessageMaxTokenExceeded) {
            $preparedMessage = "continue the responce from where it stoped.";
        }

        $context['chat_api']                      = $chatApi;
        $context['conversation']                  = $allConversationReversed;
        $context['prepared_message']              = $preparedMessage;
        $context['message']                       = $preparedMessage;
        $context['max_tokens']                    = $maxTokens;
        $context['user_max_tokens_responce']      = $userMaxTokensResponse;
        $context['last_message_max_token_exeed']  = $lastMessageMaxTokenExceeded;
        $context['assistant_context']             = $assistantContext;
        $context['translate_system_prompt']       = $translateSystemPrompt;
        $context['chat_history_string']           = $chatHistoryString;
        $context['two_chat_history_conversation'] = $twoChatHistoryConversation;
        $context['name']                          = $name;
        $context['channel']                       = $channel;

        return $context;
    }

    /**
     * Execute the OpenAI request, update chat logs, and build the final payload.
     */
    private function generateAndLogAiResponse(array $conversationContext, array $executionContext)
    {
        $chatApi      = $conversationContext['chat_api'] ?? null;
        $preparedMsg  = (string) ($conversationContext['prepared_message'] ?? $conversationContext['message'] ?? '');
        $name         = isset($conversationContext['name']) ? (string) $conversationContext['name'] : '';
        $maxTokens    = $conversationContext['max_tokens'] ?? null;
        $conversation = $conversationContext['conversation'] ?? [];

        if (! $chatApi instanceof Migachat_Model_ChatGPTAPI) {
            throw new Exception('Chat API client is not available');
        }

        $response          = [];
        $assistantContext  = $conversationContext['assistant_context'] ?? [];
        $useAssistant      = ! empty($assistantContext['use_assistant']);
        $openai            = $executionContext['openai'] ?? null;

        if ($useAssistant) {
            $response       = $this->runAssistantConversation($assistantContext, $preparedMsg, $openai);
            $isAssistantRun = true;
        } else {
            $response       = $chatApi->generateResponse($preparedMsg, $conversation, $name, $maxTokens);
            $isAssistantRun = false;
        }

        $valueId       = $executionContext['value_id'] ?? ($conversationContext['value_id'] ?? null);
        $chatId        = $executionContext['chat_id'] ?? ($conversationContext['chat_id'] ?? null);
        $settingObj    = $executionContext['setting_obj'] ?? ($conversationContext['chatbot_settings'] ?? null);
        $email         = isset($executionContext['email']) ? $executionContext['email'] : '';
        $contactName   = isset($executionContext['name']) ? $executionContext['name'] : $name;
        $mobile        = isset($executionContext['mobile']) ? $executionContext['mobile'] : '';
        $channel       = isset($executionContext['channel']) ? $executionContext['channel'] : ($conversationContext['channel'] ?? '');
        $aiPrepend     = $executionContext['ai_answer_prepend'] ?? '';
        $lastInsertId  = $executionContext['last_insert_id'] ?? null;
        $chatlogsObj   = $executionContext['chatlogs_obj'] ?? null;

        if ($response[0] !== true) {
            $payload = [
                'success' => false,
                'message' => $this->removeEmojis($response[1]),
                'chat_id' => $chatId,
            ];

            $this->logBridgeApiError(
                [
                    'value_id'    => $valueId,
                    'message'     => $preparedMsg,
                    'customer_id' => $chatId,
                    'message_id'  => ($lastInsertId && method_exists($lastInsertId, 'getId')) ? $lastInsertId->getId() : null,
                ],
                $response[1]
            );

            return $payload;
        }

        $responseMsg = str_ireplace("\n", '<br>', $response[1]);
        if ($lastInsertId && method_exists($lastInsertId, 'getId') && $lastInsertId->getId() && $chatlogsObj) {
            $chatlogsObj->addData([
                'migachat_bridge_id' => $lastInsertId->getId(),
                'is_sent'            => 1,
                'prompt_tokens'      => $response[2],
                'updated_at'         => date('Y-m-d H:i:s'),
            ])->save();
        }

        $maxTokenExceeded   = 0;
        $maxTokenResponse   = null;
        $userMaxTokensReply = $conversationContext['user_max_tokens_responce'] ?? '';

        if (! $isAssistantRun && $maxTokens == $response[3]) {
            $chatHistoryString          = $conversationContext['chat_history_string'] ?? '';
            $twoChatHistoryConversation = $conversationContext['two_chat_history_conversation'] ?? [];
            $chatHistoryString         .= $preparedMsg . ' ' . $responseMsg;

            $trlResponse = $chatApi->generateResponse($chatHistoryString, $twoChatHistoryConversation, 'admin', null);

            if ($trlResponse[0] === true) {
                $language = str_ireplace("\n", '<br>', $trlResponse[1]);

                $userMaxTokensReply = "Just give the translation no other text and if the language of text is same than don't translate.Tranlate the text in $language: " . ' ' . $userMaxTokensReply;
                $translateResponse  = $chatApi->generateResponse($userMaxTokensReply, $conversationContext['translate_system_prompt'] ?? [], 'admin', null);

                $userMaxTokensReply = str_ireplace("\n", '<br>', $translateResponse[1]);
            }
            $responseMsg       .= " <br> <br>" . $userMaxTokensReply;
            $maxTokenExceeded   = 1;
            $maxTokenResponse   = $userMaxTokensReply;
        }

        $chatlogData = [
            'value_id'           => $valueId,
            'chat_id'            => $chatId,
            'chatbot_setting_id' => ($settingObj) ? $settingObj->getId() : null,
            'role'               => 'agent',
            'message_content'    => $this->removeEmojis($responseMsg),
            'completion_tokens'  => $response[3],
            'total_tokens'       => $response[4],
            'user_email'         => $email,
            'user_name'          => $contactName,
            'user_mobile'        => $mobile,
            'is_sent'            => 1,
            'channel'            => $channel,
            'has_error'          => 0,
            'is_read'            => 1,
            'error_description'  => '',
            'max_token_exeed'    => $maxTokenExceeded,
            'max_token_responce' => $maxTokenResponse,
            'created_at'         => date('Y-m-d H:i:s'),
            'updated_at'         => date('Y-m-d H:i:s'),
        ];

        (new Migachat_Model_BridgeAPI())->addData($chatlogData)->save();

        $payload = [
            'success' => true,
            'message' => ($aiPrepend) ? $aiPrepend . '<br><br><br><br>' . $this->removeEmojis($responseMsg) : $this->removeEmojis($responseMsg),
            'chat_id' => $chatId,
        ];

        $errorArray                      = [];
        $errorArray['value_id']          = $valueId;
        $errorArray['has_error']         = 0;
        $errorArray['error_description'] = p__("Migachat", 'Get reply successfully.');
        $errorArray['platform']          = 'Bridge API';
        $errorArray['message']           = $preparedMsg;
        $errorArray['customer_id']       = $chatId;
        $errorArray['request']           = serialize($conversation);
        $errorArray['responce']          = $responseMsg;
        $errorArray['message_id']        = ($lastInsertId && method_exists($lastInsertId, 'getId')) ? $lastInsertId->getId() : null;
        $errorArray['created_at']        = date('Y-m-d');
        (new Migachat_Model_Webservicelogs())->addData($errorArray)->save();

        return $payload;
    }

    private function runAssistantConversation(array $assistantContext, $preparedMsg, $openai)
    {
        if (! $openai) {
            throw new Exception('Assistants API client is not available');
        }

        $threadId = $assistantContext['thread_id'] ?? null;

        try {
            $messageToThread = $openai->addMessageToThread($threadId, 'user', $preparedMsg);
        } catch (Exception $exception) {
            $this->logAssistantException($exception, 'addMessageToThread');
            return $this->assistantFailurePayload($this->mapAssistantApiErrorCodeToMessage(null));
        }

        if ($errorPayload = $this->handleAssistantApiErrorResponse($messageToThread, 'addMessageToThread')) {
            return $errorPayload;
        }

        if (! isset($messageToThread['id'])) {
            $this->logAssistantWarning('addMessageToThread', 'Missing message identifier in response', $messageToThread);
            return $this->assistantFailurePayload($this->mapAssistantApiErrorCodeToMessage(null));
        }

        $assistantId = $assistantContext['assistant_id'] ?? null;
        if (empty($assistantId)) {
            $this->logAssistantWarning('runAssistantConversation', 'Assistant ID is missing in chatbot settings', $assistantContext);

            return $this->assistantFailurePayload($this->mapAssistantApiErrorCodeToMessage(null));
        }

        $opts = $assistantContext['assistant_run_opts'] ?? [];

        try {
            $run = $openai->runThread($threadId, $assistantId, $opts);
        } catch (Exception $exception) {
            $this->logAssistantException($exception, 'runThread');
            return $this->assistantFailurePayload($this->mapAssistantApiErrorCodeToMessage(null));
        }

        if ($errorPayload = $this->handleAssistantApiErrorResponse($run, 'runThread')) {
            return $errorPayload;
        }

        if (! isset($run['id'])) {
            $this->logAssistantWarning('runThread', 'Missing run identifier in response', $run);
            return $this->assistantFailurePayload($this->mapAssistantApiErrorCodeToMessage(null));
        }

        $runId     = $run['id'];
        $deadline  = time() + 120;
        $runStatus = null;
        $status    = null;

        while (true) {
            usleep(600000);

            try {
                $status = $openai->getRunStatus($threadId, $runId);
            } catch (Exception $exception) {
                $this->logAssistantException($exception, 'getRunStatus');
                return $this->assistantFailurePayload($this->mapAssistantApiErrorCodeToMessage(null));
            }

            if ($errorPayload = $this->handleAssistantApiErrorResponse($status, 'getRunStatus')) {
                return $errorPayload;
            }

            if (! isset($status['status'])) {
                $this->logAssistantWarning('getRunStatus', 'Missing status value in response', $status);
                return $this->assistantFailurePayload($this->mapAssistantApiErrorCodeToMessage(null));
            }

            $runStatus = $status['status'];

            if ($runStatus === 'requires_action' && ! empty($status['required_action']['submit_tool_outputs'])) {
                $this->logAssistantWarning('getRunStatus', 'Run requires tool outputs without an implemented handler', $status);
                return $this->assistantFailurePayload($this->mapAssistantApiErrorCodeToMessage(null));
            }

            if (in_array($runStatus, ['completed', 'failed', 'cancelled', 'expired'], true)) {
                break;
            }

            if (time() > $deadline) {
                $this->logAssistantWarning('getRunStatus', 'Run did not complete before deadline', ['last_status' => $runStatus]);
                return $this->assistantFailurePayload($this->mapAssistantApiErrorCodeToMessage(null));
            }
        }

        if ($runStatus !== 'completed') {
            $lastError = $status['last_error'] ?? [];

            if (is_array($lastError) && ! empty($lastError)) {
                $this->logAssistantApiErrorDetails('runThreadStatus', $lastError, $status);
                $friendlyMessage = $this->mapAssistantApiErrorCodeToMessage($lastError['code'] ?? null);
            } else {
                $this->logAssistantWarning('runThreadStatus', 'Run ended without completion', ['status' => $runStatus]);
                $friendlyMessage = $this->mapAssistantApiErrorCodeToMessage(null);
            }

            return $this->assistantFailurePayload($friendlyMessage);
        }

        $promptTokens     = $status['usage']['prompt_tokens'] ?? 0;
        $completionTokens = $status['usage']['completion_tokens'] ?? 0;
        $totalTokens      = $status['usage']['total_tokens'] ?? 0;

        try {
            $messages = $openai->getThreadMessages($threadId, ['order' => 'desc', 'limit' => 1]);
        } catch (Exception $exception) {
            $this->logAssistantException($exception, 'getThreadMessages');
            return $this->assistantFailurePayload($this->mapAssistantApiErrorCodeToMessage(null));
        }

        if ($errorPayload = $this->handleAssistantApiErrorResponse($messages, 'getThreadMessages')) {
            return $errorPayload;
        }

        if (! isset($messages['data'][0])) {
            $this->logAssistantWarning('getThreadMessages', 'No messages found in thread response', $messages);
            return $this->assistantFailurePayload($this->mapAssistantApiErrorCodeToMessage(null));
        }

        $assistantResponse = $messages['data'][0]['content'][0]['text']['value'] ?? '[No response content]';

        $responseMsg = $this->removeEmojis(str_ireplace("\n", '<br>', $assistantResponse));

        return [true, $responseMsg, $promptTokens, $completionTokens, $totalTokens];
    }

    private function handleOperatorEscalation(array $context)
    {
        $operatorSettings = $context['operator_settings'] ?? null;
        if (! $operatorSettings || ! $operatorSettings->getIsEnabledBridgeApi()) {
            return null;
        }

        $chatIdConsent = $context['chat_id_consent'] ?? null;
        if (! $chatIdConsent) {
            return null;
        }

        $valueId        = $context['value_id'] ?? null;
        $chatId         = $context['chat_id'] ?? null;
        $message        = (string) ($context['message'] ?? '');
        $secretKey      = $context['secret_key'] ?? '';
        $organizationId = $context['organization_id'] ?? '';
        $lastInsertId   = $context['last_insert_id'] ?? null;
        $chatlogsObj    = $context['chatlogs_obj'] ?? null;
        $globalLang     = strtolower((string) ($context['global_lang'] ?? ''));
        $translator     = $context['translator'] ?? null;

        $opDefaultLang  = strtolower($operatorSettings->getDefaultLanguage() ?: 'it');
        $opDetectedLang = $globalLang ?: $opDefaultLang;

        $translate = function ($text) use ($translator, $opDetectedLang, $opDefaultLang) {
            $text = (string) $text;

            if ($opDetectedLang && $opDetectedLang !== $opDefaultLang && is_callable($translator)) {
                return $translator($text, strtoupper($opDetectedLang));
            }

            return $text;
        };

        $now           = time();
        $lastAskedRaw  = $chatIdConsent->getLastAskedForOperatorAt();
        $lastAskedDiff = PHP_INT_MAX;
        if (! empty($lastAskedRaw)) {
            $lastAskedDiff = $now - strtotime($lastAskedRaw);
        }

        $historyRecords = (new Migachat_Model_BridgeAPI())->getHistoryMessages($valueId, $chatId, 10);
        $conversation   = [];
        foreach ($historyRecords as $record) {
            $conversation[] = [
                'role'      => $record['role'] === 'user' ? 'user' : 'assistant',
                'content'   => $record['role'] === 'user' ? urldecode($record['message_content']) : $record['message_content'],
                'date_time' => $record['created_at'],
            ];
        }

        $conversation        = array_reverse($conversation);
        $revFiveConversation = '';
        foreach ($conversation as $entry) {
            $revFiveConversation .= 'TIMESTAMP = ' . $entry['date_time'] . '<br>' .
                'ROLE = ' . $entry['role'] . '<br>' .
                'TEXT = ' . $entry['content'] . '<br>----------------<br>';
        }

        if ($chatIdConsent->getAskedForOperator() != 1 && $lastAskedDiff > 3600) {
            $operatorPrompt = $operatorSettings->getOperatorSystemPrompt()
                ?: "Analyze this text string that a user wrote on our support chat (user prompt), reply with 1 if it is sufficiently probable tha it means that the user wants to speak to an operator. If it is not clear enough and in all other cases reply with a 0";

            $operatorPrompt = str_replace(
                '@@last_five_history@@',
                'last five interactions with user : ' . $revFiveConversation,
                $operatorPrompt
            );

            $shouldEscalate = $this->checkOperator($operatorPrompt, $message, $secretKey, $organizationId);

            if ($shouldEscalate) {
                $chatIdConsent
                    ->setAskedForOperator(1)
                    ->setAskedForOperatorSt(date('Y-m-d H:i:s'))
                    ->setAskedForOperatorCount(1)
                    ->setCreatedAt(date('Y-m-d H:i:s'))
                    ->setLastAskedForOperatorAt(date('Y-m-d H:i:s'))
                    ->save();

                if ($lastInsertId && method_exists($lastInsertId, 'getId') && $lastInsertId->getId() && $chatlogsObj) {
                    $chatlogsObj->addData([
                        'asked_for_operator' => 1,
                        'updated_at'         => date('Y-m-d H:i:s'),
                    ])->save();
                }

                return [
                    'success' => true,
                    'chat_id' => $chatId,
                    'message' => $translate($operatorSettings->getAskCallFromOperatorMsg()),
                ];
            }
        } elseif ($chatIdConsent->getAskedForOperator() == 1) {
            $askedStRaw = $chatIdConsent->getAskedForOperatorSt();
            $diff       = PHP_INT_MAX;
            if (! empty($askedStRaw)) {
                $diff = $now - strtotime($askedStRaw);
            }

            if ($diff < 600) {
                $lowerMessage  = strtolower($message);
                $isPositiveHit = false;

                foreach ($this->positiveResponses as $positiveWord) {
                    if (strpos($lowerMessage, $positiveWord) !== false) {
                        $isPositiveHit = true;
                        break;
                    }
                }

                if ($isPositiveHit) {
                    $appId             = (new Migachat_Model_Setting())->getAppIdByValueId($valueId);
                    $operatorRequested = [
                        'app_id'       => $appId,
                        'value_id'     => $valueId,
                        'bot_type'     => 'bridge_api',
                        'user_id'      => $chatId,
                        'status'       => 'pending',
                        'request_data' => $message,
                        'user_email'   => $chatIdConsent->getUserEmail(),
                        'user_mobile'  => $chatIdConsent->getUserMobile(),
                        'user_name'    => $chatIdConsent->getUserName(),
                        'created_at'   => date('Y-m-d H:i:s'),
                        'updated_at'   => date('Y-m-d H:i:s'),
                    ];

                    $operatorSaved = (new Migachat_Model_OperatorRequests())->addData($operatorRequested)->save();
                    $operatorId    = $operatorSaved->getId();

                    $this->sendOperatorWebhook($operatorRequested, $chatIdConsent, $operatorId, $operatorSettings, $revFiveConversation);
                    $this->sendOperatorEmail($operatorRequested, $chatIdConsent, $operatorId, $operatorSettings, $revFiveConversation);

                    $chatIdConsent->setAskedForOperator(0)->setAskedForOperatorCount(0)->setCreatedAt(date('Y-m-d H:i:s'))->save();

                    return [
                        'success' => true,
                        'chat_id' => $chatId,
                        'message' => $translate($operatorSettings->getConfirmCallFromOperatorMsg()),
                    ];
                }

                if (in_array(strtolower($message), $this->negativeResponses)) {
                    $chatIdConsent->setAskedForOperator(0)->setAskedForOperatorCount(0)->setCreatedAt(date('Y-m-d H:i:s'))->save();

                    return [
                        'success' => true,
                        'chat_id' => $chatId,
                        'message' => $translate($operatorSettings->getDeclinedCallFromOperatorMsg()),
                    ];
                }

                if ($chatIdConsent->getAskedForOperatorCount() < 2) {
                    $chatIdConsent->setAskedForOperator(1)->setAskedForOperatorCount(2)->setCreatedAt(date('Y-m-d H:i:s'))->save();

                    return [
                        'success' => true,
                        'chat_id' => $chatId,
                        'message' => $translate($operatorSettings->getInvalidAskCallFromOperatorMsg()),
                    ];
                }

                $chatIdConsent->setAskedForOperator(0)->setAskedForOperatorCount(0)->setCreatedAt(date('Y-m-d H:i:s'))->save();

                return [
                    'success' => true,
                    'chat_id' => $chatId,
                    'message' => $translate($operatorSettings->getDeclinedCallFromOperatorMsg()),
                ];
            }

            $chatIdConsent->setAskedForOperator(0)->setAskedForOperatorCount(0)->setCreatedAt(date('Y-m-d H:i:s'))->save();
        }

        return null;
    }

    private function handleChatLimits(array $context)
    {
        $valueId          = $context['value_id'];
        $chatId           = $context['chat_id'];
        $chatIdDuration   = $context['chatid_duration'];
        $chatIdTokens     = $context['chatid_tokens'];
        $bridgeTokens     = $context['bridge_chatid_tokens'];
        $mobile           = $context['mobile'] ?? '';

        $chatIdRecord = (new Migachat_Model_ModelChatIds())->find([
            'value_id' => $valueId,
            'chat_id'  => $chatId,
        ]);

        $result = [
            'payload'     => null,
            'limit_state' => [
                'chat_id_record'              => $chatIdRecord,
                'requests_count'              => $chatIdRecord->getRequestsCount(),
                'last_token_limit_reached_at' => $chatIdRecord->getLastTokenLimitReachedAt(),
            ],
        ];

        if (! $chatIdRecord->getId()) {
            return $result;
        }

        $lastLimitReachedAt = $chatIdRecord->getLastTokenLimitReachedAt();
        if ($lastLimitReachedAt) {
            $diffInSeconds = time() - strtotime($lastLimitReachedAt);

            if ($diffInSeconds < 3600) {
                $newCount     = (int) $chatIdRecord->getRequestsCount() + 1;
                $chatIdRecord = (new Migachat_Model_ModelChatIds())->addData([
                    'id'             => $chatIdRecord->getId(),
                    'requests_count' => $newCount,
                    'updated_at'     => date('Y-m-d H:i:s'),
                ])->save();

                $result['limit_state']['chat_id_record'] = $chatIdRecord;
                $result['limit_state']['requests_count'] = $newCount;

                $result['payload'] = $this->buildTranslatedLimitResponse($context);

                return $result;
            }

            $chatIdRecord = (new Migachat_Model_ModelChatIds())->addData([
                'id'                          => $chatIdRecord->getId(),
                'requests_count'              => 0,
                'last_token_limit_reached_at' => null,
                'updated_at'                  => date('Y-m-d H:i:s'),
            ])->save();

            $chatLimitModel = new Migachat_Model_BridgrapiChatLimits();
            $aiOffRecord    = $chatLimitModel->find([
                'value_id' => $valueId,
                'chat_id'  => $chatId,
                'is_limit' => 0,
            ]);
            if ($aiOffRecord->getId() && ! $aiOffRecord->getIsLimit()) {
                $chatLimitModel->delete();
            }

            $result['limit_state']['chat_id_record']              = $chatIdRecord;
            $result['limit_state']['requests_count']              = 0;
            $result['limit_state']['last_token_limit_reached_at'] = null;
        }

        $chatLimitModel   = new Migachat_Model_BridgrapiChatLimits();
        $isLimitTurnedOff = $chatLimitModel->find([
            'value_id' => $valueId,
            'chat_id'  => $chatId,
            'is_limit' => 1,
        ]);

        $totalTokens = $bridgeTokens[0]['total_tokens_sum'] ?? 0;
        if ($totalTokens > $chatIdTokens && ! $isLimitTurnedOff->getId()) {
            $this->logBridgeApiError(
                [
                    'value_id'    => $valueId,
                    'customer_id' => $chatId,
                    'mobile'      => $mobile,
                ],
                p__("Migachat", 'Chat id tokens limit reached!')
            );

            $chatIdRecord = (new Migachat_Model_ModelChatIds())->addData([
                'id'                          => $chatIdRecord->getId(),
                'requests_count'              => 1,
                'last_token_limit_reached_at' => date('Y-m-d H:i:s'),
                'updated_at'                  => date('Y-m-d H:i:s'),
            ])->save();

            $aiOffRecord = (new Migachat_Model_BridgrapiChatLimits())->find([
                'value_id' => $valueId,
                'chat_id'  => $chatId,
                'is_limit' => 0,
            ]);
            if (! $aiOffRecord->getId()) {
                (new Migachat_Model_BridgrapiChatLimits())->addData([
                    'value_id'   => $valueId,
                    'chat_id'    => $chatId,
                    'is_limit'   => 0,
                    'ai_off_at'  => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ])->save();
            }

            $result['limit_state']['chat_id_record']              = $chatIdRecord;
            $result['limit_state']['requests_count']              = $chatIdRecord->getRequestsCount();
            $result['limit_state']['last_token_limit_reached_at'] = $chatIdRecord->getLastTokenLimitReachedAt();

            $result['payload'] = $this->buildTranslatedLimitResponse($context);

            return $result;
        }

        return $result;
    }

    /**
     * Sends alert emails and generates the translated response displayed when
     * chat limits are triggered.
     */
    private function buildTranslatedLimitResponse(array $context)
    {
        $valueId        = $context['value_id'];
        $chatId         = $context['chat_id'];
        $chatIdDuration = $context['chatid_duration'];
        $chatIdTokens   = $context['chatid_tokens'];
        $bridgeSettings = $context['bridge_obj'];

        $chatHistoryString           = $context['chat_history_string'];
        $twoChatHistoryConversation  = $context['two_chat_history_conversation'];
        $globalLang                  = $context['global_lang'];
        $translateSystemPrompt       = $context['translate_system_prompt'];
        $apiUrl                      = $context['apiUrl'];
        $secretKey                   = $context['secret_key'];
        $organizationId              = $context['organization_id'];
        $gptModel                    = $context['gpt_model'];

        $appId      = (new Migachat_Model_Setting())->getAppIdByValueId($valueId);
        $application = (new Application_Model_Application())->find($appId);
        $mainDomain  = __get('main_domain');

        $emailBody = "In the past $chatIdDuration minutes we reached more than $chatIdTokens tokens allowed for a single Chatid, the Chatid affected is ID:$chatId , APP_ID:$appId , APP_NAME:" . $application->getName() . ", MAIN DOMAIN:$mainDomain . Please check your system";

        $emailData            = [];
        $emailData['subject'] = "Warning CHATID $chatId API limit reached";
        $emailData['body']    = $emailBody;

        $this->defaultSMTPEmail($emailData, $valueId);

        $chatAPI = new Migachat_Model_ChatGPTAPI($apiUrl, $secretKey, $organizationId, $gptModel);
        $chatAPI->generateResponse($chatHistoryString, $twoChatHistoryConversation, 'admin', null);

        $userChatLimitResponse = $bridgeSettings->getUserChatLimitResponce();
        $prompt                 = "Just give the translation no other text and if the language of text is same than don't translate.Tranlate the text in $globalLang: " . $userChatLimitResponse;
        $translateResponse      = $chatAPI->generateResponse($prompt, $translateSystemPrompt, 'admin', null);

        return [
            'success' => true,
            'message' => $translateResponse[1],
            'chat_id' => $chatId,
        ];
    }

    /**
     * Logs an error entry to the Bridge API webservice log and returns the payload.
     */
    private function logBridgeApiError(array $data, $description)
    {
        $data['has_error']         = 1;
        $data['error_description'] = $description;
        $data['platform']          = 'Bridge API';
        $data['created_at']        = date('Y-m-d');

        (new Migachat_Model_Webservicelogs())->addData($data)->save();

        return $data;
    }

    private function mapAssistantApiErrorCodeToMessage($code)
    {
        if ($code === 'insufficient_quota') {
            return '⚠️ Quota exceeded. Please check your billing plan or wait until it resets.';
        }

        if ($code === 'rate_limit_exceeded') {
            return '⚠️ Too many requests. Please slow down and try again.';
        }

        return '⚠️ An unexpected error occurred. Please try again later.';
    }

    private function assistantFailurePayload($message)
    {
        return [false, $message, 0, 0, 0];
    }

    private function logAssistantApiErrorDetails($contextDescription, array $error, $fullResponse = [])
    {
        $type    = isset($error['type']) ? (string) $error['type'] : 'unknown';
        $code    = isset($error['code']) ? (string) $error['code'] : 'unknown';
        $message = isset($error['message']) ? (string) $error['message'] : 'No error message provided';

        $logContext = [
            'context' => $contextDescription,
            'type'    => $type,
            'code'    => $code,
            'message' => $message,
        ];

        error_log('[Migachat Assistants API Error] ' . json_encode($logContext));

        if (! in_array($code, ['insufficient_quota', 'rate_limit_exceeded'], true)) {
            error_log('[Migachat Assistants API Error] Full response: ' . json_encode($fullResponse));
        }
    }

    private function handleAssistantApiErrorResponse($response, $contextDescription)
    {
        if (! is_array($response)) {
            $this->logAssistantWarning($contextDescription, 'Response was not an array', ['response' => $response]);

            return $this->assistantFailurePayload($this->mapAssistantApiErrorCodeToMessage(null));
        }

        if (isset($response['error']) && $response['error']) {
            $error = is_array($response['error']) ? $response['error'] : ['message' => (string) $response['error']];

            $this->logAssistantApiErrorDetails($contextDescription, $error, $response);

            $code = isset($error['code']) ? (string) $error['code'] : null;

            return $this->assistantFailurePayload($this->mapAssistantApiErrorCodeToMessage($code));
        }

        return null;
    }

    private function logAssistantException(Exception $exception, $contextDescription)
    {
        $logContext = [
            'context' => $contextDescription,
            'type'    => get_class($exception),
            'message' => $exception->getMessage(),
        ];

        error_log('[Migachat Assistants API Error] Exception: ' . json_encode($logContext));
    }

    private function logAssistantWarning($contextDescription, $message, $extra = [])
    {
        $payload = [
            'context' => $contextDescription,
            'message' => $message,
            'extra'   => $extra,
        ];

        error_log('[Migachat Assistants API Warning] ' . json_encode($payload));
    }

    /**
     * Validates the presence of mandatory request parameters.
     *
     * @param array $params
     * @param array $ws_log_data
     *
     * @return array|null
     */
    private function validateRequiredParams(array $params, array &$ws_log_data)
    {
        $requiredParams   = ['instance_id', 'message', 'auth_token'];
        $missing_messages = [];

        foreach ($requiredParams as $param) {
            if (! isset($params[$param]) || $params[$param] === '') {
                $missing_messages[] = " Missing required parameter: $param <br>";
                continue;
            }

            $ws_log_data[$param] = $params[$param];
        }

        if (empty($missing_messages)) {
            return null;
        }

        $missing_params = implode('', $missing_messages);
        $this->logBridgeApiError($ws_log_data, $missing_params);

        return [
            'status'  => 'failure',
            'message' => $missing_params,
        ];

    }

    /**
     * Loads Bridge API settings, decodes the message, and authenticates the request.
     *
     * @param array $params
     * @param array $ws_log_data
     *
     * @return array{settings: Migachat_Model_BridgeAPISettings, message: string}
     *
     * @throws Exception When the auth token mismatches or the Bridge API is disabled.
     */
    private function resolveInstanceContext(array $params, array $ws_log_data)
    {
        $value_id   = $params['instance_id'];
        $auth_token = $params['auth_token'];

        $bridge_obj = $this->loadBridgeApiSettings($value_id);
        $message    = $this->decodeIncomingMessage($params);
        $logContext = $this->buildInstanceLogContext($ws_log_data, $params, $value_id);

        $this->assertValidAuthToken($bridge_obj, $auth_token, $logContext);
        $this->assertBridgeApiEnabled($bridge_obj, $logContext);

        return [
            'settings' => $bridge_obj,
            'message'  => $message,
        ];
    }

    /**
     * Retrieves Bridge API settings for the provided instance.
     */
    private function loadBridgeApiSettings($value_id)
    {
        $bridge_obj = new Migachat_Model_BridgeAPISettings();
        $bridge_obj->find(['value_id' => $value_id]);

        return $bridge_obj;
    }

    /**
     * Returns the incoming user message decoded from URL encoding.
     */
    private function decodeIncomingMessage(array $params)
    {
        if (! isset($params['message'])) {
            return '';
        }

        return urldecode((string) $params['message']);
    }

    /**
     * Ensures the provided authentication token matches the stored secret.
     */
    private function assertValidAuthToken(Migachat_Model_BridgeAPISettings $settings, $auth_token, array $logContext)
    {
        $expectedToken = $settings->getAuthToken();
        if ($auth_token !== $expectedToken || trim((string) $auth_token) === '') {
            $this->logBridgeApiError($logContext, p__("Migachat", 'Authentication token mismatch'));
            throw new Exception(p__("Migachat", 'Authentication token mismatch'), 1);
        }
    }

    /**
     * Guards access when the Bridge API has been disabled for the instance.
     */
    private function assertBridgeApiEnabled(Migachat_Model_BridgeAPISettings $settings, array $logContext)
    {
        if ($settings->getDisableApi()) {
            $this->logBridgeApiError($logContext, p__("Migachat", 'The service is disabled! Please try again later.'));
            throw new Exception(p__("Migachat", 'The service is disabled! Please try again later.'));
        }
    }

    /**
     * Augments log context with instance-specific details without mutating the source array.
     */
    private function buildInstanceLogContext(array $ws_log_data, array $params, $value_id)
    {
        if (! isset($ws_log_data['message']) && isset($params['message'])) {
            $ws_log_data['message'] = $params['message'];
        }

        $ws_log_data['value_id'] = $value_id;

        return $ws_log_data;
    }

    /**
     * Extracts and validates the incoming request payload.
     *
     * @throws Exception
     */
    private function extractRequestParams()
    {
        $request = $this->getRequest();

        $rawBody = trim((string) $request->getRawBody());
        if ($rawBody !== '') {
            $post_params = json_decode($rawBody, true);

            if (! is_array($post_params)) {
                throw new Exception(p__("Migachat", 'Invalid request format'), 1);
            }

            return $post_params;

        }

        return $request->getParams();
    }

    /**
     * Normalizes a list of comma/semicolon/newline separated phone numbers into a clean array.
     *
     * @param string|array|null $numbers
     *
     * @return array
     */
    private function normalizeNumberList($numbers)
    {
        if (! $numbers) {
            return [];
        }

        if (! is_array($numbers)) {
            $numbers = str_replace([';', "\r\n", "\n", "\r"], ',', $numbers);
            $numbers = preg_split('/[,\s]+/', $numbers);
        }

        if (! is_array($numbers)) {
            return [];
        }

        $numbers = array_map(static function ($item) {
            return trim($item);
        }, $numbers);

        return array_values(array_filter($numbers, static function ($item) {
            return $item !== '';
        }));
    }

}
