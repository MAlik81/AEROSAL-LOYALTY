<?php

class Migachat_Mobile_ViewController extends Application_Controller_Mobile_Default
{

    /**
     * @deprecated in Siberian 5.0
     */

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
    public function findAction()
    {
        try {
            $value_id    = $this->getRequest()->getParam('value_id');
            $customer_id = $this->getRequest()->getParam('customer_id');
            $start       = $this->getRequest()->getParam('start_index'); // Start index for pagination
            $limit       = $this->getRequest()->getParam('limit');       // Number of records to fetch

            if ($value_id && $customer_id && $start !== null && $limit !== null) {
                $payload       = [];
                $migachat_logs = new Migachat_Model_Chatlogs();
                $logs_data     = $migachat_logs->getChatLogs($value_id, $customer_id, $start, $limit);

                $formattedData = [];

                foreach ($logs_data as $entry) {
                    $content         = $entry["message_content"];
                    $sender          = ($entry["message_sent_received"] === "sent") ? "user" : "bot";
                    $formattedData[] = [
                        "content" => $content,
                        "sender"  => $sender,
                    ];
                }

                $default  = new Core_Model_Default();
                $base_url = $default->getBaseUrl();
                $payload  = [
                    'success' => true,
                    'data'    => $formattedData,
                    'logo2'   => $base_url . '/app/local/modules/Migachat/resources/design/desktop/flat/images/chatbot.png',
                ];
            } else {
                throw new Exception(p__("Migachat", 'Hi, sorry for the inconvenience. Please try again later'));
            }
        } catch (Exception $e) {
            $payload = [
                'error'   => true,
                'message' => $e->getMessage(),
            ];
        }

        $this->_sendJson($payload);
    }

    public function checknewmessagesAction()
    {
        try {
            if ($value_id = $this->getRequest()->getParam('value_id') and $customer_id = $this->getRequest()->getParam('customer_id')) {
                $payload       = [];
                $migachat_logs = new Migachat_Model_Chatlogs();
                $logs_data     = $migachat_logs->getUnreadChatLogs($value_id, $customer_id);

                $formattedData = [];

                foreach ($logs_data as $entry) {
                    $content         = $entry["message_content"];
                    $sender          = ($entry["message_sent_received"] === "sent") ? "user" : "bot";
                    $formattedData[] = [
                        "content" => $content,
                        "sender"  => $sender,
                    ];
                }

                $payload = [
                    'success' => true,
                    'data'    => $formattedData,
                ];

            } else {
                throw new Exception(p__("Migachat", 'Hi, sorry for the inconvenience. Please try again later'));
            }
        } catch (Exception $e) {
            $payload = [
                'error'   => true,
                'message' => $e->getMessage(),
            ];
        }

        $this->_sendJson($payload);
    }

    public function getbotreplyAction()
    {
        try {
            if ($value_id = $this->getRequest()->getParam('value_id')) {
                $payload = [];

                $data = Siberian_Json::decode($this->getRequest()->getRawBody());
                if (empty($data['customer_id'])) {
                    throw new Exception(p__("Migachat", 'Hi, sorry for inconvenience. Please try again later.'));
                } elseif (empty($data['message'])) {
                    throw new Exception(p__("Migachat", 'Message cannot b empty!.'));
                } else {
                    $prompt = $data['message'];
                }
                $setting_obj = new Migachat_Model_ChatbotSettings();
                $setting_obj->find([
                    'value_id' => $value_id,
                ]);
                if (! $setting_obj->getId()) {
                    throw new Exception(p__("Migachat", 'Chatbot settings not found.'));
                }

                $chatbot_setting_obj = new Migachat_Model_ChatbotSettings();
                $chatbot_setting_obj->find(['value_id' => $value_id]);
                $secret_key      = $chatbot_setting_obj->getSecretKey();
                $organization_id = $chatbot_setting_obj->getOrganizationId();
                if (empty($secret_key) || empty($organization_id)) {
                    throw new Exception(p__("Migachat", 'OpenAI API key or organization ID is not set.'));
                }

                $openai = new Migachat_Model_AssistantsGPTAPI($secret_key, $organization_id);

                $app    = $this->getApplication();
                $app_id = $app->getId();
                // Migachat_Model_CustomerGdpr
                $customer_id = $data['customer_id'];
                // gdpr and commercial concsent
                $customer_consent_obj = new Migachat_Model_CustomerGdpr();
                $customer_consent     = $customer_consent_obj->find(['value_id' => $value_id, 'customer_id' => $customer_id]);

                if (! $customer_consent->getId()) {
                    $customer_gdpr                       = [];
                    $customer_gdpr['app_id']             = $app_id;
                    $customer_gdpr['value_id']           = $value_id;
                    $customer_gdpr['customer_id']        = $customer_id;
                    $customer_gdpr['gdpr_consent']       = 2;
                    $customer_gdpr['commercial_consent'] = 2;
                    $customer_gdpr['created_at']         = date('Y-m-d H:i:s');
                    $customer_consent->addData($customer_gdpr)->save();
                }
                $thread_id = $customer_consent->getThreadId();
                if (empty($thread_id) && $chatbot_setting_obj->getUseAssistant() == "1") {
                    $meta_data                = [];
                    $meta_data['value_id']    = (string) $value_id;
                    $meta_data['customer_id'] = (string) $customer_id;
                    $meta_data['created_at']  = date('Y-m-d H:i:s');
                    $new_thread               = $openai->createThread($meta_data);
                    // dd($new_thread);
                    // {
                    //     "id": "thread_abc123",
                    //     "object": "thread",
                    //     "created_at": 1629470000,
                    //     "metadata": {"user_id": "12345"},
                    //     // Only if messages were included:
                    //     "messages": [
                    //         {
                    //         "id": "msg_123",
                    //         "role": "user",
                    //         "content": "What's the weather today?"
                    //         }
                    //     ]
                    // }
                    if (isset($new_thread['id']) && ! empty($new_thread['id'])) {
                        $thread_id = $new_thread['id'];
                        $customer_consent->setThreadId($thread_id)->save();
                    } else {
                        throw new Exception(p__("Migachat", 'Failed to create a new thread. Please try again later.'));
                    }

                }

                $app_setting_obj = new Migachat_Model_PromptSettings();
                $app_setting_obj->find([
                    'value_id' => $value_id,
                ]);
                // // getting gdpr settings
                // $gdpr_settings = (new Migachat_Model_GDPR)->find(['value_id' => $value_id]);
                // if ($gdpr_settings->getId() && $gdpr_settings->getGdprActive() == '1') {
                //     if ($customer_consent->getGdprConsent() == 2) {
                //         // save current user message for later responce

                //         $chatlogs_obj = new Migachat_Model_Chatlogs();
                //         $chatlog_data = array();
                //         $chatlog_data['value_id'] = $value_id;
                //         $chatlog_data['customer_id'] = $data['customer_id'];
                //         $chatlog_data['chatbot_setting_id'] = $setting_obj->getId();
                //         $chatlog_data['message_sent_received'] = 'sent';
                //         $chatlog_data['message_content'] = $data['message'];
                //         $chatlog_data['is_sent'] = 0;
                //         $chatlog_data['has_error'] = 0;
                //         $chatlog_data['is_read'] = 1;
                //         $chatlog_data['error_description'] = "";
                //         $chatlog_data['created_at'] = date("Y-m-d H:i:s");
                //         $chatlog_data['updated_at'] = date("Y-m-d H:i:s");
                //         $lastInsertId = $chatlogs_obj->addData($chatlog_data)->save();

                //         // ask for consent and update flag in db
                //         $customer_consent->setGdprConsent(3)->save();
                //         $payload = array();
                //         $payload = [
                //             'success' => true,
                //             'data' => $gdpr_settings->getGdprWelcomeText(),
                //             'type' => 'chatgpt',
                //         ];
                //         return $this->_sendJson($payload);
                //     } elseif ($customer_consent->getGdprConsent() == 3) {
                //         if (strtolower($data['message']) == 'yes' || strtolower($data['message']) == 'si') {
                //             # if comercial is enable than send that otherwise success message
                //             $customer_consent->setGdprConsent(1)->setGdprConsentTimestamp(date('Y-m-d H:i:s'))->save();
                //             if ($gdpr_settings->getCommercialActive() == '1') {
                //                 $customer_consent->setCommercialConsent(3)->save();

                //                 $payload = array();
                //                 $payload = [
                //                     'success' => true,
                //                     'data' => $gdpr_settings->getCommercialWelcomeText(),
                //                     'type' => 'chatgpt',
                //                 ];
                //                 return $this->_sendJson($payload);
                //             } else {
                //                 $payload = array();
                //                 $payload = [
                //                     'success' => true,
                //                     'data' => $gdpr_settings->getGdprSuccessText(),
                //                     'type' => 'chatgpt',
                //                 ];
                //                 return $this->_sendJson($payload);
                //             }
                //         } else {
                //             $payload = array();
                //             $payload = [
                //                 'success' => true,
                //                 'data' => $gdpr_settings->getGdprFailureText(),
                //                 'type' => 'chatgpt',
                //             ];
                //             return $this->_sendJson($payload);
                //         }
                //     } elseif ($customer_consent->getGdprConsent() == 1) {
                //         if ($gdpr_settings->getCommercialActive() == 1 && $customer_consent->getCommercialConsent() == 3) {
                //             if (strtolower($data['message']) == 'yes' || strtolower($data['message']) == 'si') {

                //                 $customer_consent->setCommercialConsent(1)->setCommercialConsentTimestamp(date('Y-m-d H:i:s'))->save();

                //                 $payload = array();
                //                 $payload = [
                //                     'success' => true,
                //                     'data' => $gdpr_settings->getGdprSuccessText(),
                //                     'type' => 'chatgpt',
                //                 ];
                //                 return $this->_sendJson($payload);
                //             } else{
                //                 $customer_consent->setCommercialConsent(0)->save();
                //                 $payload = array();
                //                 $payload = [
                //                     'success' => true,
                //                     'data' => $gdpr_settings->getGdprSuccessText(),
                //                     'type' => 'chatgpt',
                //                 ];
                //                 return $this->_sendJson($payload);
                //             }
                //         }
                //     }

                // }
                // // gdpr and commercial consent endpoint
                //analyze user prompt if he wants to speak to an operator
                $apiUrl          = 'https://api.openai.com/v1/chat/completions';
                $secret_key      = $setting_obj->getSecretKey();
                $organization_id = $setting_obj->getOrganizationId();

                $customer_obj  = new Customer_Model_Customer();
                $customer_data = $customer_obj->find(['customer_id' => $data['customer_id']]);

                $operator_settings         = (new Migachat_Model_OperatorSettings)->find(['value_id' => $value_id]);
                $responseTimeoutRaw        = $operator_settings->getOperatorResponseTimeoutMinutes();
                $responseTimeoutMinutes    = is_numeric($responseTimeoutRaw) ? max(1, (int) $responseTimeoutRaw) : 10;
                $responseTimeoutSeconds    = $responseTimeoutMinutes * 60;
                $current_time              = time();
                if ($operator_settings->getIsEnabledInApp()) {
                    $last_five_chat_history     = (new Migachat_Model_Chatlogs())->getLastTenMessages($value_id, $data['customer_id'], 10);
                    $last_five_conversation     = [];
                    $fivw_conversation_temp     = [];
                    $rev_last_five_conversation = "";

                    $customer_name = $customer_data->getFirstname() . '-' . $customer_data->getLastname();
                    foreach ($last_five_chat_history as $key => $value) {
                        if ($value['message_sent_received'] == 'sent') {
                            $last_five_conversation[] = [
                                'role'    => 'user',
                                'name'    => str_replace(" ", "_", $customer_name),
                                'content' => $value['message_content'],
                            ];
                        } else {

                            $last_five_conversation[] = [
                                'role'    => 'assistant',
                                'content' => $value['message_content'],
                            ];

                        }
                    }
                    $fivw_conversation_temp = array_reverse($last_five_conversation);
                    foreach ($fivw_conversation_temp as $key => $value) {
                        $rev_last_five_conversation .= 'TIMESTAMP = ' . $value['date_time'] . '<br>' . 'ROLE = ' . $value['role'] . '<br>' . 'TEXT = ' . $value['content'] . '<br>----------------<br>';
                    }
                    $canAttemptEscalation = ($customer_consent->getAskedForOperator() != 1);

                    if ($canAttemptEscalation) {
                        $operator       = false;
                        $operatorprompt = "Analyze this text string that a user wrote on our support chat (user prompt), reply with 1 if it is sufficiently probable tha it means that the user wants to speak to an operator. If it is not clear enough and in all other cases reply with a 0";

                        if ($operator_settings->getOperatorSystemPrompt()) {
                            $operatorprompt = $operator_settings->getOperatorSystemPrompt();
                        }

                        $operatorprompt = str_replace('@@last_five_history@@', $rev_last_five_conversation, $operatorprompt);
                        $operator       = $this->checkOperator($operatorprompt, $prompt, $secret_key, $organization_id);
                        if ($operator) {
                            $customer_consent->setAskedForOperator(1)->setAskedForOperatorAt(date('Y-m-d H:i:s'))->setCreatedAt(date('Y-m-d H:i:s'))->save();

                            $response            = [];
                            $response['success'] = true;
                            $response['type']    = 'chatgpt';
                            $response['data']    = $operator_settings->getAskCallFromOperatorMsg();
                            return $this->_sendJson($response);
                        }
                    } elseif ($customer_consent->getAskedForOperator() == 1) {
                        $asked_for_operator_at = $customer_consent->getAskedForOperatorAt();
                        // check if asked for operater at is more than configured minutes old
                        $asked_for_operator_at = strtotime($asked_for_operator_at);
                        $diff                  = $current_time - $asked_for_operator_at;
                        if ($diff < $responseTimeoutSeconds) {
                            // asked user to confirm if he wants a call from opertaor now check user prompt if he wants or don't want a call from opertaor.
                            $operatorprompt = "Analyze this text string that a user wrote on our support chat (user prompt),We asked user to confirm if he wants a call from opertaor now check user prompt if he wants with given prompt \"" . $operator_settings->getAskCallFromOperatorMsg() . "\" reply with 1 if it is sufficiently probable tha it means that the user wants to speak to an operator. If it is not clear enough and in all other cases reply with a 0";
                            $operator       = false;
                            $operator       = $this->checkPositiveResponce($operator_settings->getAskCallFromOperatorMsg(), $prompt, $secret_key, $organization_id);
                            if ($operator) {
                                $app_id                            = (new Migachat_Model_Setting())->getAppIdByValueId($value_id);
                                $operator_reqested                 = [];
                                $operator_reqested['app_id']       = $app_id;
                                $operator_reqested['value_id']     = $value_id;
                                $operator_reqested['bot_type']     = 'in_app';
                                $operator_reqested['user_id']      = $customer_id;
                                $operator_reqested['status']       = 'pending';
                                $operator_reqested['request_data'] = $prompt;
                                $operator_reqested['user_email']   = $customer_data->getEmail();
                                $operator_reqested['user_mobile']  = $customer_data->getMobile();
                                $operator_reqested['user_name']    = $customer_data->getFirstname() . '-' . $customer_data->getLastname();
                                $operator_reqested['created_at']   = date('Y-m-d H:i:s');
                                $operator_reqested['updated_at']   = date('Y-m-d H:i:s');
                                $operator_saved                    = (new Migachat_Model_OperatorRequests())->addData($operator_reqested)->save();
                                $operator_id                       = $operator_saved->getId();
                                // // send email and webhook here later

                                $this->sendOperatorWebhook($operator_reqested, $customer_data, $operator_id, $operator_settings, $rev_last_five_conversation);
                                $this->sendOperatorEmail($operator_reqested, $customer_data, $operator_id, $operator_settings, $rev_last_five_conversation);

                                $customer_consent->setAskedForOperator(0)->setCreatedAt(date('Y-m-d H:i:s'))->save();
                                $response            = [];
                                $response['success'] = true;
                                $response['type']    = 'chatgpt';
                                $response['data']    = $operator_settings->getConfirmCallFromOperatorMsg();
                                return $this->_sendJson($response);
                            } else {
                                // $negativeResponses
                                if (in_array(strtolower($prompt), $this->negativeResponses)) {
                                    $customer_consent->setAskedForOperator(0)->setCreatedAt(date('Y-m-d H:i:s'))->save();
                                    $response            = [];
                                    $response['success'] = true;
                                    $response['type']    = 'chatgpt';
                                    $response['data']    = $operator_settings->getDeclinedCallFromOperatorMsg();

                                    return $this->_sendJson($response);
                                } else {
                                    $response            = [];
                                    $response['success'] = true;
                                    $response['type']    = 'chatgpt';
                                    $response['data']    = $operator_settings->getAskCallFromOperatorMsg();
                                    return $this->_sendJson($response);
                                }
                            }
                        } else {
                            $customer_consent->setAskedForOperator(0)->setCreatedAt(date('Y-m-d H:i:s'))->save();

                            $response            = [];
                            $response['success'] = true;
                            $response['type']    = 'chatgpt';
                            $response['data']    = $operator_settings->getDeclinedCallFromOperatorMsg();

                            return $this->_sendJson($response);
                        }

                    }
                }

                $app_setting_obj = new Migachat_Model_PromptSettings();
                $app_setting_obj->find([
                    'value_id' => $value_id,
                ]);
                $history_tokens_limit = 4000;
                $gpt_model            = 'gpt-4o-mini';
                if ($app_setting_obj->getGptModel()) {
                    $gpt_model = $app_setting_obj->getGptModel();
                }

                $complete_prompt = "You are a helpful assistant. if the answer include a link add always the complete url starting with https://";
                if ($app_setting_obj->getSystemPrompt()) {
                    $complete_prompt = $app_setting_obj->getSystemPrompt() . ' if the answer include a link add always the complete url starting with https://';
                }
                $complete_prompt .= ' ' . $prompt . ' ';

                $token_limit = (new Migachat_Model_ModelTokens())->find(['model_name' => $gpt_model])->getTokens();
                if (! $token_limit) {
                    $token_limit = 4000;
                }
                $system_prompt_token_limit = (new Migachat_Model_ModelTokens())->getSystemPromptTokens($value_id);

                $history_tokens       = (new Migachat_Model_ModelTokens())->getHistoryTokens($value_id);
                $history_tokens_limit = $history_tokens[0] + $system_prompt_token_limit;

                $history_messages_limit = $history_tokens[1];

                $customer_name = $customer_data->getFirstname() . '-' . $customer_data->getLastname();
                if ($setting_obj->getId()) {
                    $message_sent = 0;
                    $chat_history = (new Migachat_Model_Chatlogs())->getLastTenMessages($value_id, $data['customer_id'], $history_messages_limit);

                    $all_conversation    = [];
                    $bu_all_conversation = [];

                    foreach ($chat_history as $key => $value) {
                        $complete_prompt .= $value['message_content'];
                        if ((new Migachat_Model_Setting)->countTokens($complete_prompt) > $token_limit) {
                            break;
                        }
                        if ($value['message_sent_received'] == 'sent') {
                            $all_conversation[] = [
                                'role'    => 'user',
                                'name'    => str_replace(" ", "_", $customer_name),
                                'content' => $value['message_content'],
                            ];
                        } else {

                            $all_conversation[] = [
                                'role'    => 'assistant',
                                'content' => $value['message_content'],
                            ];

                        }
                    }
                    $bu_all_conversation = $all_conversation;

                    if ($app_setting_obj->getId()) {
                        if ($app_setting_obj->getPromptChatgptActive()) {
                            if ((new Migachat_Model_Setting)->countTokens($app_setting_obj->getSystemPrompt()) < $system_prompt_token_limit) {
                                $all_conversation[] = [
                                    'role'    => 'system',
                                    'content' => $app_setting_obj->getSystemPrompt() . ' if the answer include a link add always the complete url starting with https://',
                                ];
                            } else {
                                $all_conversation[] = [
                                    'role'    => 'system',
                                    'content' => 'You are a helpful assistant. if the answer include a link add always the complete url starting with https://',
                                ];
                            }
                        } else {
                            $all_conversation[] = [
                                'role'    => 'system',
                                'content' => 'You are a helpful assistant. if the answer include a link add always the complete url starting with https://',
                            ];
                        }
                    } else {
                        $all_conversation[] = [
                            'role'    => 'system',
                            'content' => 'You are a helpful assistant. if the answer include a link add always the complete url starting with https://',
                        ];
                    }

                    $all_conversation_r = array_reverse($all_conversation);
                    // dd($all_conversation_r);
                    $chatlogs_obj                          = new Migachat_Model_Chatlogs();
                    $chatlog_data                          = [];
                    $chatlog_data['value_id']              = $value_id;
                    $chatlog_data['customer_id']           = $data['customer_id'];
                    $chatlog_data['chatbot_setting_id']    = $setting_obj->getId();
                    $chatlog_data['message_sent_received'] = 'sent';
                    $chatlog_data['message_content']       = $this->removeEmojis($data['message']);
                    $chatlog_data['is_sent']               = 0;
                    $chatlog_data['has_error']             = 0;
                    $chatlog_data['is_read']               = 1;
                    $chatlog_data['error_description']     = "";
                    $chatlog_data['created_at']            = date("Y-m-d H:i:s");
                    $chatlog_data['updated_at']            = date("Y-m-d H:i:s");
                    $lastInsertId                          = $chatlogs_obj->addData($chatlog_data)->save();

                    if ($setting_obj->getApiType() == 'chatgpt') {

                        if ($chatbot_setting_obj->getId() && $chatbot_setting_obj->getUseAssistant() != "1") {
                            $apiUrl          = 'https://api.openai.com/v1/chat/completions';
                            $secret_key      = $setting_obj->getSecretKey();
                            $organization_id = $setting_obj->getOrganizationId();

                            $chatAPI = new Migachat_Model_ChatGPTAPI($apiUrl, $secret_key, $organization_id, $gpt_model);

                            $response = $chatAPI->generateResponse($prompt, $all_conversation_r, $customer_name, null);
                            if ($response[0] === true) {
                                $response_msg = str_ireplace("\n", '<br>', $response[1]);
                                $response_msg = $this->removeEmojis($response_msg);
                                $payload      = [
                                    'success'      => true,
                                    'data'         => $response_msg,
                                    'type'         => 'chatgpt',
                                    'chatlog_data' => $chatlog_data,
                                ];

                                $error_array                      = [];
                                $error_array['value_id']          = $value_id;
                                $error_array['has_error']         = 0;
                                $error_array['error_description'] = p__("Migachat", 'Message Successfully sent & received response');
                                $error_array['platform']          = 'app_chatgpt';
                                $error_array['customer_id']       = $data['customer_id'];
                                $error_array['message']           = $data['message'];
                                $error_array['request']           = serialize($all_conversation_r);
                                $error_array['responce']          = $response_msg;
                                $error_array['message_id']        = $lastInsertId->getId();
                                $error_array['created_at']        = date("Y-m-d");
                                (new Migachat_Model_Webservicelogs())->addData($error_array)->save();

                                if ($lastInsertId->getId()) {
                                    $chatlog_data                        = [];
                                    $chatlog_data['migachat_chatlog_id'] = $lastInsertId->getId();
                                    $chatlog_data['is_sent']             = 1;
                                    $chatlog_data['prompt_tokens']       = $response[2];
                                    $chatlog_data['updated_at']          = date("Y-m-d H:i:s");
                                    $chatlogs_obj->addData($chatlog_data)->save();
                                }

                                $chatlogs_obj                          = new Migachat_Model_Chatlogs();
                                $chatlog_data                          = [];
                                $chatlog_data['value_id']              = $value_id;
                                $chatlog_data['customer_id']           = $data['customer_id'];
                                $chatlog_data['chatbot_setting_id']    = $setting_obj->getId();
                                $chatlog_data['message_sent_received'] = 'received';
                                $chatlog_data['message_content']       = $response_msg;
                                $chatlog_data['completion_tokens']     = $response[3];
                                $chatlog_data['total_tokens']          = $response[4];
                                $chatlog_data['is_sent']               = 1;
                                $chatlog_data['has_error']             = 0;
                                $chatlog_data['is_read']               = 1;
                                $chatlog_data['error_description']     = "";
                                $chatlog_data['created_at']            = date("Y-m-d H:i:s");
                                $chatlog_data['updated_at']            = date("Y-m-d H:i:s");
                                $chatlogs_obj->addData($chatlog_data)->save();
                            } else {

                                if ($lastInsertId->getId()) {
                                    $received_request['migachat_chatlog_id'] = $lastInsertId->getId();
                                    $chatlog_data['is_sent']                 = 0;
                                    $chatlog_data['has_error']               = 1;
                                    $chatlog_data['error_description']       = p__("Migachat", "Message not sent");
                                }
                                $chatlogs_obj->addData($chatlog_data)->save();
                                $payload = [
                                    'success' => false,
                                    'message' => $response[1],
                                ];
                                $error_array                      = [];
                                $error_array['value_id']          = $value_id;
                                $error_array['has_error']         = 1;
                                $error_array['error_description'] = $response[1];
                                $error_array['platform']          = 'app_chatgpt';
                                $error_array['customer_id']       = $data['customer_id'];
                                $error_array['message']           = $data['message'] . "<br><br><br>" . $response[1];
                                $error_array['message_id']        = $lastInsertId->getId();
                                $error_array['created_at']        = date("Y-m-d");
                                (new Migachat_Model_Webservicelogs())->addData($error_array)->save();
                            }
                        } else {
                            // add message to thread
                            try {
                                $message_to_thread = $openai->addMessageToThread($thread_id, 'user', $prompt);
                            } catch (Exception $exception) {
                                $this->logMobileAssistantException($exception, 'addMessageToThread');
                                $payload = $this->finalizeMobileAssistantFailure(
                                    $this->mapAssistantApiErrorCodeToMessage(null),
                                    $value_id,
                                    $data,
                                    $chatlogs_obj,
                                    $chatlog_data,
                                    $lastInsertId
                                );

                                return $this->_sendJson($payload);
                            }

                            if ($failurePayload = $this->handleMobileAssistantErrorResponse(
                                $message_to_thread,
                                'addMessageToThread',
                                $value_id,
                                $data,
                                $chatlogs_obj,
                                $chatlog_data,
                                $lastInsertId
                            )) {
                                return $this->_sendJson($failurePayload);
                            }

                            if (! isset($message_to_thread['id'])) {
                                $this->logMobileAssistantWarning('addMessageToThread', 'Missing message identifier in response', $message_to_thread);
                                $payload = $this->finalizeMobileAssistantFailure(
                                    $this->mapAssistantApiErrorCodeToMessage(null),
                                    $value_id,
                                    $data,
                                    $chatlogs_obj,
                                    $chatlog_data,
                                    $lastInsertId
                                );

                                return $this->_sendJson($payload);
                            }

                            $assistant_id = $app_setting_obj->getAssistantId();
                            if (empty($assistant_id)) {
                                $this->logMobileAssistantWarning('runThread', 'Assistant ID is not set in chatbot settings', []);
                                $payload = $this->finalizeMobileAssistantFailure(
                                    $this->mapAssistantApiErrorCodeToMessage(null),
                                    $value_id,
                                    $data,
                                    $chatlogs_obj,
                                    $chatlog_data,
                                    $lastInsertId
                                );

                                return $this->_sendJson($payload);
                            }

                            // Migachat_Model_Assistants
                            $assistant = (new Migachat_Model_Assistants())->find(['assistant_id' => $assistant_id]);
                            if (! $assistant->getId()) {
                                $this->logMobileAssistantWarning('runThread', 'Assistant not found for provided ID', ['assistant_id' => $assistant_id]);
                                $payload = $this->finalizeMobileAssistantFailure(
                                    $this->mapAssistantApiErrorCodeToMessage(null),
                                    $value_id,
                                    $data,
                                    $chatlogs_obj,
                                    $chatlog_data,
                                    $lastInsertId
                                );

                                return $this->_sendJson($payload);
                            }
                            $file_ids = $assistant->getOpenaiFileIds();
                            // 3. Run the assistant (with vector store if needed)
                            $options = is_string($file_ids) ? json_decode($file_ids, true) : ($file_ids ? $file_ids : false);

                            $opts = [
                                'truncation_strategy'   => [
                                    'type'          => 'last_messages', // or 'auto'
                                    'last_messages' => 10,              // keep only the last 8–10 thread messages
                                ]
                            ];

                            // If you’re attaching a vector store, include it as you already do:
                            if ($options) {
                                $opts['tool_resources'] = [
                                    'file_search' => [
                                        'vector_store_ids' => is_string($file_ids) ? json_decode($file_ids, true) : ($file_ids ? $file_ids : [])
                                    ],
                                ];
                            }

                            try {
                                $run = $openai->runThread($thread_id, $assistant_id, $opts);
                            } catch (Exception $exception) {
                                $this->logMobileAssistantException($exception, 'runThread');
                                $payload = $this->finalizeMobileAssistantFailure(
                                    $this->mapAssistantApiErrorCodeToMessage(null),
                                    $value_id,
                                    $data,
                                    $chatlogs_obj,
                                    $chatlog_data,
                                    $lastInsertId
                                );

                                return $this->_sendJson($payload);
                            }

                            if ($failurePayload = $this->handleMobileAssistantErrorResponse(
                                $run,
                                'runThread',
                                $value_id,
                                $data,
                                $chatlogs_obj,
                                $chatlog_data,
                                $lastInsertId
                            )) {
                                return $this->_sendJson($failurePayload);
                            }

                            if (! isset($run['id'])) {
                                $this->logMobileAssistantWarning('runThread', 'Missing run identifier in response', $run);
                                $payload = $this->finalizeMobileAssistantFailure(
                                    $this->mapAssistantApiErrorCodeToMessage(null),
                                    $value_id,
                                    $data,
                                    $chatlogs_obj,
                                    $chatlog_data,
                                    $lastInsertId
                                );

                                return $this->_sendJson($payload);
                            }
                            $run_id = $run['id'];

                            // 4. Poll for run completion
                            $maxTries   = 10;
                            $tries      = 0;
                            $run_status = null;

                            do {
                                sleep(2);

                                try {
                                    $status = $openai->getRunStatus($thread_id, $run_id);
                                } catch (Exception $exception) {
                                    $this->logMobileAssistantException($exception, 'getRunStatus');
                                    $payload = $this->finalizeMobileAssistantFailure(
                                        $this->mapAssistantApiErrorCodeToMessage(null),
                                        $value_id,
                                        $data,
                                        $chatlogs_obj,
                                        $chatlog_data,
                                        $lastInsertId
                                    );

                                    return $this->_sendJson($payload);
                                }

                                if ($failurePayload = $this->handleMobileAssistantErrorResponse(
                                    $status,
                                    'getRunStatus',
                                    $value_id,
                                    $data,
                                    $chatlogs_obj,
                                    $chatlog_data,
                                    $lastInsertId
                                )) {
                                    return $this->_sendJson($failurePayload);
                                }

                                if (! isset($status['status'])) {
                                    $this->logMobileAssistantWarning('getRunStatus', 'Missing status value in response', $status);
                                    $payload = $this->finalizeMobileAssistantFailure(
                                        $this->mapAssistantApiErrorCodeToMessage(null),
                                        $value_id,
                                        $data,
                                        $chatlogs_obj,
                                        $chatlog_data,
                                        $lastInsertId
                                    );

                                    return $this->_sendJson($payload);
                                }

                                $run_status = $status['status'];
                                $tries++;
                            } while ($run_status !== 'completed' && $run_status !== 'failed' && $tries < $maxTries);

                            if ($run_status !== 'completed') {
                                $lastError = $status['last_error'] ?? [];

                                if (is_array($lastError) && ! empty($lastError)) {
                                    $this->logMobileAssistantApiErrorDetails('runThreadStatus', $lastError, $status);
                                    $friendlyMessage = $this->mapAssistantApiErrorCodeToMessage($lastError['code'] ?? null);
                                } else {
                                    $this->logMobileAssistantWarning('runThreadStatus', 'Run ended without completion', ['status' => $run_status]);
                                    $friendlyMessage = $this->mapAssistantApiErrorCodeToMessage(null);
                                }

                                $payload = $this->finalizeMobileAssistantFailure(
                                    $friendlyMessage,
                                    $value_id,
                                    $data,
                                    $chatlogs_obj,
                                    $chatlog_data,
                                    $lastInsertId
                                );

                                return $this->_sendJson($payload);
                            }

                            $promptTokens     = $status['usage']['prompt_tokens'] ?? 0;
                            $completionTokens = $status['usage']['completion_tokens'] ?? 0;
                            $totalTokens      = $status['usage']['total_tokens'] ?? 0;
                            // 5. Retrieve final messages

                            try {
                                $messages = $openai->getThreadMessages($thread_id, ['order' => 'desc', 'limit' => 1]);
                            } catch (Exception $exception) {
                                $this->logMobileAssistantException($exception, 'getThreadMessages');
                                $payload = $this->finalizeMobileAssistantFailure(
                                    $this->mapAssistantApiErrorCodeToMessage(null),
                                    $value_id,
                                    $data,
                                    $chatlogs_obj,
                                    $chatlog_data,
                                    $lastInsertId
                                );

                                return $this->_sendJson($payload);
                            }

                            if ($failurePayload = $this->handleMobileAssistantErrorResponse(
                                $messages,
                                'getThreadMessages',
                                $value_id,
                                $data,
                                $chatlogs_obj,
                                $chatlog_data,
                                $lastInsertId
                            )) {
                                return $this->_sendJson($failurePayload);
                            }

                            if (! isset($messages['data'][0])) {
                                $this->logMobileAssistantWarning('getThreadMessages', 'No messages found in thread response', $messages);
                                $payload = $this->finalizeMobileAssistantFailure(
                                    $this->mapAssistantApiErrorCodeToMessage(null),
                                    $value_id,
                                    $data,
                                    $chatlogs_obj,
                                    $chatlog_data,
                                    $lastInsertId
                                );

                                return $this->_sendJson($payload);
                            }

                            $assistant_response = $messages['data'][0]['content'][0]['text']['value'] ?? '[No response content]';

                            $response_msg = '';
                            $response_msg = str_ireplace("\n", '<br>', $assistant_response);
                            $response_msg = $this->removeEmojis($response_msg);
                            $payload      = [
                                'success'      => true,
                                'data'         => $response_msg,
                                'type'         => 'chatgpt',
                                'chatlog_data' => $chatlog_data,
                            ];

                            $error_array                      = [];
                            $error_array['value_id']          = $value_id;
                            $error_array['has_error']         = 0;
                            $error_array['error_description'] = p__("Migachat", 'Message Successfully sent & received response');
                            $error_array['platform']          = 'app_chatgpt';
                            $error_array['customer_id']       = $data['customer_id'];
                            $error_array['message']           = $data['message'];
                            $error_array['request']           = serialize($all_conversation_r);
                            $error_array['responce']          = $response_msg;
                            $error_array['message_id']        = $lastInsertId->getId();
                            $error_array['created_at']        = date("Y-m-d");
                            (new Migachat_Model_Webservicelogs())->addData($error_array)->save();

                            if ($lastInsertId->getId()) {
                                $chatlog_data                        = [];
                                $chatlog_data['migachat_chatlog_id'] = $lastInsertId->getId();
                                $chatlog_data['is_sent']             = 1;
                                $chatlog_data['prompt_tokens']       = $promptTokens;
                                $chatlog_data['updated_at']          = date("Y-m-d H:i:s");
                                $chatlogs_obj->addData($chatlog_data)->save();
                            }

                            $chatlogs_obj                          = new Migachat_Model_Chatlogs();
                            $chatlog_data                          = [];
                            $chatlog_data['value_id']              = $value_id;
                            $chatlog_data['customer_id']           = $data['customer_id'];
                            $chatlog_data['chatbot_setting_id']    = $setting_obj->getId();
                            $chatlog_data['message_sent_received'] = 'received';
                            $chatlog_data['message_content']       = $response_msg;
                            $chatlog_data['completion_tokens']     = $completionTokens;
                            $chatlog_data['total_tokens']          = $totalTokens;
                            $chatlog_data['is_sent']               = 1;
                            $chatlog_data['has_error']             = 0;
                            $chatlog_data['is_read']               = 1;
                            $chatlog_data['error_description']     = "";
                            $chatlog_data['created_at']            = date("Y-m-d H:i:s");
                            $chatlog_data['updated_at']            = date("Y-m-d H:i:s");
                            $chatlogs_obj->addData($chatlog_data)->save();
                        }

                    } else {
                        $app         = $this->getApplication();
                        $app_id      = $app->getId();
                        $app_name    = $app->getName();
                        $webhook_url = $setting_obj->getWebhookUrl();

                        $customer_id     = $data['customer_id'];
                        $customer_obj    = new Customer_Model_Customer();
                        $customer_data   = $customer_obj->find(['customer_id' => $customer_id]);
                        $customer_name   = $customer_data->getFirstname() . ' ' . $customer_data->getLastname();
                        $customer_email  = $customer_data->getEmail();
                        $customer_mobile = $customer_data->getMobile();

                        $webhook_url .= "?customer_id=@@customer_id@@&instance_id=@@instance_id@@&app_id=@@app_id@@&app_name=@@app_name@@&customer_name=@@customer_name@@&customer_mobile=@@customer_mobile@@&customer_email=@@customer_email@@";
                        // https://webhook.site/7970f21c-2a7d-4099-89de-82c5b323fab3?message=@@message@@&
                        // Replace the placeholders with the corresponding values
                        $placeholders = [
                            "@@customer_id@@"     => (! empty($customer_id)) ? urlencode($customer_id) : '_',
                            "@@instance_id@@"     => (! empty($value_id)) ? urlencode($value_id) : '_',
                            "@@app_id@@"          => (! empty($app_id)) ? urlencode($app_id) : '_',
                            "@@app_name@@"        => (! empty($app_name)) ? urlencode($app_name) : '_',
                            "@@customer_name@@"   => (! empty($customer_name)) ? urlencode($customer_name) : '_',
                            "@@customer_mobile@@" => (! empty($customer_mobile)) ? urlencode($customer_mobile) : '_',
                            "@@customer_email@@"  => (! empty($customer_email)) ? urlencode($customer_email) : '_',
                        ];

                        $webhook_url   = str_replace(array_keys($placeholders), array_values($placeholders), $webhook_url);
                        $system_prompt = [];
                        if ($app_setting_obj->getId()) {
                            if ($app_setting_obj->getPromptWebhookActive()) {
                                if ($this->countTokens($prompt) < 2000) {
                                    $bu_all_conversation[] = [
                                        'role'    => 'system',
                                        'content' => $app_setting_obj->getSystemPrompt(),
                                    ];
                                    $system_prompt[] = [
                                        'role'    => 'system',
                                        'content' => $app_setting_obj->getSystemPrompt(),
                                    ];
                                }
                            }
                        }
                        $prompt_history = [];
                        if ($app_setting_obj->getId()) {
                            if ($app_setting_obj->getWebhookHistory()) {
                                $history   = array_reverse($bu_all_conversation);
                                $history[] = [
                                    'role'    => 'user',
                                    'name'    => $customer_name,
                                    'content' => $prompt,
                                ];
                                $history_temp['message'] = $history;
                                $prompt_history          = json_encode($history_temp);
                            } elseif ($app_setting_obj->getPromptWebhookActive()) {

                                $system_prompt[] = [
                                    'role'    => 'user',
                                    'name'    => $customer_name,
                                    'content' => $prompt,
                                ];
                                $history_temp['message'] = $system_prompt;
                                $prompt_history          = json_encode($history_temp);
                            } else {
                                $prompt_history['message'] = $prompt;
                            }
                        }
                        $webhook  = new Migachat_Model_Webhook($webhook_url);
                        $response = $webhook->generateResponse($prompt_history, null);
                        // dd($response);
                        if ($response) {
                            $payload = [
                                'success'     => true,
                                'data'        => true,
                                'type'        => 'webhook',
                                'webhook_url' => $webhook_url,
                            ];

                            $error_array                      = [];
                            $error_array['value_id']          = $value_id;
                            $error_array['has_error']         = 0;
                            $error_array['error_description'] = 'webhook success';
                            $error_array['platform']          = 'app_webhook';
                            $error_array['created_at']        = date("Y-m-d");
                            (new Migachat_Model_Webservicelogs())->addData($error_array)->save();

                            if ($lastInsertId->getId()) {
                                $received_request['migachat_chatlog_id'] = $lastInsertId->getId();
                                $chatlog_data['is_sent']                 = 1;
                                $chatlog_data['updated_at']              = date("Y-m-d H:i:s");
                            }
                            $chatlogs_obj->addData($chatlog_data)->save();
                        } else {
                            $payload = [
                                'success'     => false,
                                'message'     => 'not_sent',
                                'webhook_url' => $webhook_url,
                                'response'    => $response,
                            ];

                            $error_array                      = [];
                            $error_array['value_id']          = $value_id;
                            $error_array['has_error']         = 1;
                            $error_array['error_description'] = 'webhook failure';
                            $error_array['platform']          = 'app_webhook';
                            $error_array['created_at']        = date("Y-m-d");
                            (new Migachat_Model_Webservicelogs())->addData($error_array)->save();

                            if ($lastInsertId->getId()) {
                                $received_request['migachat_chatlog_id'] = $lastInsertId->getId();
                                $chatlog_data['is_sent']                 = 0;
                                $chatlog_data['has_error']               = 1;
                                $chatlog_data['error_description']       = p__("Migachat", "Message not sent");
                            }
                            $chatlogs_obj->addData($chatlog_data)->save();
                        }
                    }
                } else {
                    throw new Exception(p__("Migachat", 'Hi, sorry for inconvenience. Please try again later'));
                }
            }

        } catch (Exception $e) {
            $payload = [
                'error'   => true,
                'message' => $e->getMessage(),
            ];
        }

        $this->_sendJson($payload);
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

    private function finalizeMobileAssistantFailure($friendlyMessage, $valueId, $data, $chatlogs_obj, array $chatlogData, $lastInsertId)
    {
        if ($lastInsertId && method_exists($lastInsertId, 'getId') && $lastInsertId->getId()) {
            $chatlogData['migachat_chatlog_id'] = $lastInsertId->getId();
        }

        $chatlogData['is_sent']           = 0;
        $chatlogData['has_error']         = 1;
        $chatlogData['error_description'] = $friendlyMessage;
        $chatlogData['updated_at']        = date('Y-m-d H:i:s');

        $chatlogs_obj->addData($chatlogData)->save();

        $payload = [
            'success' => false,
            'message' => $friendlyMessage,
        ];

        $errorArray = [
            'value_id'          => $valueId,
            'has_error'         => 1,
            'error_description' => $friendlyMessage,
            'platform'          => 'app_chatgpt',
            'customer_id'       => isset($data['customer_id']) ? $data['customer_id'] : null,
            'message'           => (isset($data['message']) ? $data['message'] : '') . '<br><br><br>' . $friendlyMessage,
            'message_id'        => ($lastInsertId && method_exists($lastInsertId, 'getId')) ? $lastInsertId->getId() : null,
            'created_at'        => date('Y-m-d'),
        ];

        (new Migachat_Model_Webservicelogs())->addData($errorArray)->save();

        return $payload;
    }

    private function handleMobileAssistantErrorResponse($response, $contextDescription, $valueId, $data, $chatlogs_obj, array $chatlogData, $lastInsertId)
    {
        if (! is_array($response)) {
            $this->logMobileAssistantWarning($contextDescription, 'Response was not an array', ['response' => $response]);

            return $this->finalizeMobileAssistantFailure(
                $this->mapAssistantApiErrorCodeToMessage(null),
                $valueId,
                $data,
                $chatlogs_obj,
                $chatlogData,
                $lastInsertId
            );
        }

        if (isset($response['error']) && $response['error']) {
            $error = is_array($response['error']) ? $response['error'] : ['message' => (string) $response['error']];

            $this->logMobileAssistantApiErrorDetails($contextDescription, $error, $response);

            $code = isset($error['code']) ? (string) $error['code'] : null;

            return $this->finalizeMobileAssistantFailure(
                $this->mapAssistantApiErrorCodeToMessage($code),
                $valueId,
                $data,
                $chatlogs_obj,
                $chatlogData,
                $lastInsertId
            );
        }

        return null;
    }

    private function logMobileAssistantApiErrorDetails($contextDescription, array $error, $fullResponse = [])
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

        error_log('[Migachat Mobile Assistants API Error] ' . json_encode($logContext));

        if (! in_array($code, ['insufficient_quota', 'rate_limit_exceeded'], true)) {
            error_log('[Migachat Mobile Assistants API Error] Full response: ' . json_encode($fullResponse));
        }
    }

    private function logMobileAssistantWarning($contextDescription, $message, $extra = [])
    {
        $payload = [
            'context' => $contextDescription,
            'message' => $message,
            'extra'   => $extra,
        ];

        error_log('[Migachat Mobile Assistants API Warning] ' . json_encode($payload));
    }

    private function logMobileAssistantException(Exception $exception, $contextDescription)
    {
        $logContext = [
            'context' => $contextDescription,
            'type'    => get_class($exception),
            'message' => $exception->getMessage(),
        ];

        error_log('[Migachat Mobile Assistants API Error] Exception: ' . json_encode($logContext));
    }

    public function removeEmojis($string)
    {
        return preg_replace('/\p{So}+/u', '', $string);
    }

    public function checkPositiveResponce($question, $text, $secret_key, $organization_id)
    {
        $isPositive = false;

        foreach ($this->positiveResponses as $positiveWord) {
            if (strpos($text, $positiveWord) !== false) {
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
    public function updatemsgsstatusAction()
    {

        try {
            if ($value_id = $this->getRequest()->getParam('value_id') and $customer_id = $this->getRequest()->getParam('customer_id')) {
                $payload       = [];
                $migachat_logs = new Migachat_Model_Chatlogs();
                $logs_data     = $migachat_logs->markAsRead($value_id, $customer_id);

                $payload = [
                    'success' => true,
                ];

            } else {
                throw new Exception(p__("Migachat", 'Hi, sorry for inconvenience. Please try again later'));
            }

        } catch (Exception $e) {
            $payload = [
                'error'   => true,
                'message' => $e->getMessage(),
            ];
        }

        $this->_sendJson($payload);
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
    public function clearhistoryAction()
    {
        try {
            if ($value_id = $this->getRequest()->getParam('value_id') and $customer_id = $this->getRequest()->getParam('customer_id')) {
                $chatlogs_obj = new Migachat_Model_Chatlogs();
                $chatlogs     = $chatlogs_obj->deleteChatLogs($value_id, $customer_id);
                $payload      = [
                    'success' => true,
                    'message' => p__("Migachat", 'Chat Deleted Successfully'),
                ];

            } else {
                throw new Exception(p__("Migachat", 'Hi, sorry for inconvenience. Please try again later'));
            }

        } catch (Exception $e) {
            $payload = [
                'error'   => true,
                'message' => $e->getMessage(),
            ];
        }

        $this->_sendJson($payload);

    }

    public function loadtitleplaceholderAction()
    {
        $request     = $this->getRequest();
        $application = $this->getApplication();
        $appId       = $application->getId();
        $session     = $this->getSession();

        try {
            if ($value_id = $this->getRequest()->getParam('value_id')) {

                // licenses settings start
                $main_domain = __get('main_domain');
                if (! preg_match("~^(?:f|ht)tps?://~i", $main_domain)) {
                    // If not exist then add http
                    $main_domain = "https://" . $main_domain;
                }
                $curlSession = curl_init();
                curl_setopt($curlSession, CURLOPT_URL, $main_domain . '/migachat/public_license/validateapplicense?app_id=' . $appId . '&instance_id=' . $value_id);
                curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
                curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
                $jsonDataString = curl_exec($curlSession);
                curl_close($curlSession);
                $license = Siberian_Json::decode($jsonDataString);
                // licenses settings end
                if (! $license['success_message']) {
                    throw new Exception($license['info_message']);
                }
                if ($this->getCurrentOptionValue()->getId()) {

                    $setting_obj = new Migachat_Model_ChatbotSettings();
                    $setting_obj->find([
                        'value_id' => $value_id,
                    ]);
                    $payload = [
                        'success'      => true,
                        'page_title'   => p__("Migachat", $this->getCurrentOptionValue()->getTabbarName()),
                        'place_holder' => p__("Migachat", $setting_obj->getPlaceHolder()),
                    ];
                } else {
                    throw new Siberian_Exception(__('Unknown feature!'));
                }

            } else {
                throw new Exception(p__("Migachat", 'Hi, sorry for inconvenience. Please try again later'));
            }

        } catch (Exception $e) {
            $payload = [
                'error'   => true,
                'message' => $e->getMessage(),
            ];
        }

        $this->_sendJson($payload);

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
    private function sendOperatorWebhook($operator_reqested, $customer_data, $operator_id, $operator_settings, $rev_last_five_conversation)
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
        $webhook_data['user_email']          = $customer_data->getEmail();
        $webhook_data['user_mobile']         = $customer_data->getMobile();
        $webhook_data['user_name']           = $customer_data->getFirstname() . '-' . $customer_data->getLastname();
        $webhook_data['last_five_history']   = $rev_last_five_conversation;

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
    private function sendOperatorEmail($operator_reqested, $customer_data, $operator_id, $operator_settings, $rev_last_five_conversation)
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
        $webhook_data['user_email']          = $customer_data->getEmail();
        $webhook_data['user_mobile']         = $customer_data->getMobile();
        $webhook_data['user_name']           = $customer_data->getFirstname() . '-' . $customer_data->getLastname();

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
            $webhook_data['user_name'],
            $webhook_data['user_email'],
            $webhook_data['user_mobile'],
            $webhook_data['user_id'],
            $webhook_data['chat_type'],
            $webhook_data['operator_request_id'],
            $webhook_data['date_time'],
            $rev_last_five_conversation,
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

}

// "responce": "Request failed with HTTP status code: 400\n Response: <html><body><h1>400 Bad request</h1>\nYour browser sent an invalid request.\n</body></html>\n\n\n",
// "webhook_url": "https://webhook.site/7970f21c-2a7d-4099-89de-82c5b323fab3?message=hi&user_id=817&value_id=817&app_id=5&app_name=M.BETA&customer_name=Shahzar Younas&customer_mobile=+923361427740&customer_email=malikshahzar2222@gmail.com",
