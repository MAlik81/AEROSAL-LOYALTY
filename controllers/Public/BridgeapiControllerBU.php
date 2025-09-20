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

class Migachat_Public_BridgeapiControllerBU extends Migachat_Controller_Default
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
            // Extract incoming request and validate parameters.
            $request     = $this->getRequest();
            $params      = $request->getParams();
            $post_params = $request->getRawBody();
            $post_params = json_decode($post_params, true);

            if (! is_array($post_params)) {
                throw new Exception(p__("Migachat", 'Invalid request format'), 1);
            }
            if (count($post_params) && isset($post_params['instance_id'])) {
                $params = $post_params;
            } else {
                $params = $request->getParams();
            }

            // Initialize an array to store log data for webservice
            $ws_log_data  = [];
            $chat_id_data = [];

            $normalizeNumberList = static function ($numbers) {
                if (! $numbers) {
                    return [];
                }

                if (is_array($numbers)) {
                    $items = $numbers;
                } else {
                    $numbers = str_replace([';', "\r\n", "\n", "\r"], ',', $numbers);
                    $items   = preg_split('/[,\s]+/', $numbers);
                }

                if (! is_array($items)) {
                    return [];
                }

                $items = array_map(static function ($item) {
                    return trim($item);
                }, $items);

                return array_values(array_filter($items, static function ($item) {
                    return $item !== '';
                }));
            };

            // Check if required parameters exist
            $requiredParams = ['instance_id', 'message', 'auth_token'];
            $missing_params = "";
            foreach ($requiredParams as $param) {
                if (! isset($params[$param])) {
                    $missing_params .= " Missing required parameter: $param <br>";
                } else {
                    $ws_log_data[$param] = $params[$param];
                }
            }
            if ($missing_params !== "") {
                // Log errors for missing parameters or mismatched authentication.
                $ws_log_data['has_error']         = 1;
                $ws_log_data['error_description'] = $missing_params;
                $ws_log_data['platform']          = 'Bridge API';
                $ws_log_data['created_at']        = date("Y-m-d");
                (new Migachat_Model_Webservicelogs())->addData($ws_log_data)->save();
                $response = [
                    'status'  => 'failure',
                    'message' => $missing_params,
                ];
                // return $response;
                return $this->_sendJson($response);

                throw new Exception(json_encode($response), 1);
            } else {
                // Extract parameters
                $value_id   = $params['instance_id'];
                $auth_token = $params['auth_token'];

                // Retrieve Bridge API settings
                $bridge_obj = new Migachat_Model_BridgeAPISettings();
                $bridge_obj->find(['value_id' => $value_id]);

                $message = urldecode($params['message']);
                // Authenticate using auth token
                if ($auth_token !== $bridge_obj->getAuthToken() || trim($auth_token) == '') {
                    $error_array                      = [];
                    $error_array['value_id']          = $value_id;
                    $error_array['has_error']         = 1;
                    $error_array['error_description'] = p__("Migachat", 'Authentication token mismatch');
                    $error_array['platform']          = 'Bridge API';
                    $error_array['created_at']        = date("Y-m-d");
                    (new Migachat_Model_Webservicelogs())->addData($error_array)->save();
                    throw new Exception(p__("Migachat", 'Authentication token mismatch'), 1);
                }
                // bridge api tokens setup
                $over_all_duration = ($bridge_obj->getOverallDuration()) ? $bridge_obj->getOverallDuration() : 24;
                $over_tokens       = ($bridge_obj->getOverallLimit()) ? $bridge_obj->getOverallLimit() : 2500000;
                $chatid_duration   = ($bridge_obj->getUserDuration()) ? $bridge_obj->getUserDuration() : 60;
                $chatid_tokens     = ($bridge_obj->getUserLimit()) ? $bridge_obj->getUserLimit() : 100000;

                // check if bridge api is disabled
                if ($bridge_obj->getDisableApi()) {
                    throw new Exception(p__("Migachat", 'The service is disabled! Please try again later.'));
                }

                // if chat id exists than the process of limit ans AI toggle
                if (isset($params['chat_id']) && $params['chat_id']) {

                    $chat_id_for_limit = $params['chat_id'];
                    // check if temporary blacklist is enabled
                    // Migachat_Model_TemporaryBlaclist
                    $temp_blacklist_obj   = new Migachat_Model_TemporaryBlaclist();
                    $check_temp_blacklist = $temp_blacklist_obj->find(['value_id' => $value_id, 'chat_id' => $chat_id_for_limit]);
                    if ($check_temp_blacklist->getId()) {
                        $response = [
                            'status'  => 'failure',
                            'message' => p__("Migachat", 'You are in temporary blacklist, please try again later.'),
                        ];
                        return $this->_sendJson($response);
                    }

                    // =======
                    if (strtolower($message) == '##off##') {
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
                                $payload = [
                                    'success' => true,
                                    'message' => p__("Migachat", 'AI turned OFF for this chat id, untill it is turned back ON.'),
                                    'chat_id' => $chat_id_for_limit,
                                ];
                            } else {
                                $payload = [
                                    'success' => true,
                                    'message' => p__("Migachat", 'Error while turning OFF the AI for this chat id.'),
                                    'chat_id' => $chat_id_for_limit,
                                ];
                            }
                            return $this->_sendJson($payload);
                            exit;
                        } else {
                            $payload = [
                                'success' => true,
                                'message' => p__("Migachat", 'AI turned OFF for this chat id, untill it is turned back ON.'),
                                'chat_id' => $chat_id_for_limit,
                            ];
                            return $this->_sendJson($payload);
                            exit;
                        }
                    }
                    if (strtolower($message) == '##on##') {
                        # remove chatid in limits table if exists and not permanently
                        $chat_id_limit_obj = new Migachat_Model_BridgrapiChatLimits();
                        $is_ai_turned_off  = $chat_id_limit_obj->find(['value_id' => $value_id, 'chat_id' => $chat_id_for_limit, 'is_limit' => 0]);
                        if ($is_ai_turned_off->getId() && ! $is_ai_turned_off->getIsLimit()) {
                            $del_resp = $chat_id_limit_obj->delete();
                            if ($del_resp) {
                                $payload = [
                                    'success'    => true,
                                    'message'    => p__("Migachat", 'AI turned ON for this chat id, untill it is turned back OFF.'),
                                    'chat_id'    => $chat_id_for_limit,
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ];
                            } else {
                                $payload = [
                                    'success' => true,
                                    'message' => p__("Migachat", 'Error while turning ON the AI for this chat id.'),
                                    'chat_id' => $chat_id_for_limit,
                                ];
                            }
                            return $this->_sendJson($payload);
                            exit;
                        } else {
                            $payload = [
                                'success'    => true,
                                'message'    => p__("Migachat", 'AI turned ON for this chat id, untill it is turned back OFF.'),
                                'chat_id'    => $chat_id_for_limit,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ];
                            return $this->_sendJson($payload);
                            exit;
                        }
                    }
                    if (strtolower($message) == '##limitoff##' && false) {
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
                                $payload = [
                                    'success' => true,
                                    'message' => p__("Migachat", 'Token limit turned OFF permanantly for this chat id.'),
                                    'chat_id' => $chat_id_for_limit,
                                ];
                            } else {
                                $payload = [
                                    'success' => true,
                                    'message' => p__("Migachat", 'Error while turning OFF the limit for this chat id.'),
                                    'chat_id' => $chat_id_for_limit,
                                ];
                            }
                            return $this->_sendJson($payload);
                            exit;
                        } else {
                            $payload = [
                                'success' => true,
                                'message' => p__("Migachat", 'Token limit turned OFF permanantly for this chat id.'),
                                'chat_id' => $chat_id_for_limit,
                            ];
                            return $this->_sendJson($payload);
                            exit;
                        }
                    }

                }
                // check ovelall tokens limit here

                $bridge_overall_tokens = (new Migachat_Model_BridgeAPI())->getOverAllTokens($value_id, $over_all_duration);
                if ($bridge_overall_tokens[0]['total_tokens_sum'] > $over_tokens) {
                    $error_array                      = [];
                    $error_array['value_id']          = $value_id;
                    $error_array['has_error']         = 1;
                    $error_array['error_description'] = p__("Migachat", 'Overall token limit reached!');
                    $error_array['platform']          = 'Bridge API';
                    $error_array['created_at']        = date("Y-m-d");
                    (new Migachat_Model_Webservicelogs())->addData($error_array)->save();

                    $app_id      = (new Migachat_Model_Setting())->getAppIdByValueId($value_id);
                    $application = (new Application_Model_Application())->find($app_id);
                    $main_domain = __get('main_domain');

                    $email_data            = [];
                    $email_data['subject'] = p__("Migachat", 'Warning global API limit reached');
                    $email_data['body']    = "In the past $over_all_duration hours we reached more than $over_tokens tokens allowed, please check your system. APP_ID:$app_id , APP_NAME:$application->getName() , MAIN DOMAIN:$main_domain ";

                    $this->defaultSMTPEmail($email_data, $value_id);

                    throw new Exception(p__("Migachat", 'Overall token limit reached!'), 1);
                }

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
                    $chat_id_data['chat_id'] = $chat_id;

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

                    $mobile = '';
                    if (isset($params['mobile'])) {
                        $mobile = trim($params['mobile']);

                        // Define the regular expression pattern for validation
                        $pattern = '/^\+[0-9]{9,}$/';

                        // Use the preg_match function to check if the mobile number matches the pattern
                        if (! preg_match($pattern, $mobile)) {
                            $error_array                      = [];
                            $error_array['value_id']          = $value_id;
                            $error_array['has_error']         = 1;
                            $error_array['error_description'] = p__("Migachat", 'Invalide mobile number format') . ' ' . $mobile;
                            $error_array['platform']          = 'Bridge API';
                            $error_array['customer_id']       = $chat_id;
                            $error_array['created_at']        = date("Y-m-d");
                            (new Migachat_Model_Webservicelogs())->addData($error_array)->save();
                            throw new Exception(p__("Migachat", 'Invalide mobile number format'), 1);
                        }

                        $chat_id_data['user_mobile'] = $mobile;

                        $setting = new Migachat_Model_Setting();
                        $setting->find(1);

                        $global_blacklisted_numbers = $normalizeNumberList($setting->getBlacklistedNumbers());
                        if (! empty($global_blacklisted_numbers) && in_array($mobile, $global_blacklisted_numbers, true)) {
                            $error_array                      = [];
                            $error_array['value_id']          = $value_id;
                            $error_array['has_error']         = 1;
                            $error_array['error_description'] = p__("Migachat", 'Mobile number is blacklisted') . ' ' . $mobile;
                            $error_array['platform']          = 'Bridge API';
                            $error_array['customer_id']       = $chat_id;
                            $error_array['created_at']        = date("Y-m-d");
                            (new Migachat_Model_Webservicelogs())->addData($error_array)->save();
                            throw new Exception(p__("Migachat", 'Mobile number is blacklisted'), 1);
                        }

                        $permanent_blacklisted_numbers = $normalizeNumberList($app_setting_obj->getPermanentBlacklistedMobileNumbers());
                        if (! empty($permanent_blacklisted_numbers) && in_array($mobile, $permanent_blacklisted_numbers, true)) {
                            $error_array                      = [];
                            $error_array['value_id']          = $value_id;
                            $error_array['has_error']         = 1;
                            $error_array['error_description'] = p__("Migachat", 'Mobile number is blacklisted') . ' ' . $mobile;
                            $error_array['platform']          = 'Bridge API';
                            $error_array['customer_id']       = $chat_id;
                            $error_array['created_at']        = date("Y-m-d");
                            (new Migachat_Model_Webservicelogs())->addData($error_array)->save();
                            throw new Exception(p__("Migachat", 'Mobile number is blacklisted'), 1);
                        }
                    }
                    // ================================================
                    // check if limit is already reached

                    $chat_id_obj_for_limit  = new Migachat_Model_ModelChatIds();
                    $chat_id_data_for_limit = $chat_id_obj_for_limit->find(['value_id' => $value_id, 'chat_id' => $chat_id]);
                    if ($chat_id_data_for_limit->getId()) {
                        // if set last_token_limit_reached_at is not null than check if the duration is passed or not
                        if ($chat_id_data_for_limit->getLastTokenLimitReachedAt()) {
                            $last_token_limit_reached_at = $chat_id_data_for_limit->getLastTokenLimitReachedAt();
                            $last_token_limit_reached_at = strtotime($last_token_limit_reached_at);
                            $current_time                = time();
                            $diff                        = $current_time - $last_token_limit_reached_at;
                            if ($diff < 3600) {
                                $chat_id_data_0                   = [];
                                $chat_id_data_0['requests_count'] = $chat_id_data_for_limit->getRequestsCount() + 1;
                                $chat_id_data_0['id']             = $chat_id_data_for_limit->getId();
                                $chat_id_obj_0_for_limit          = (new Migachat_Model_ModelChatIds())->addData($chat_id_data_0)->save();
                                $total_count                      = $chat_id_obj_0_for_limit->getRequestsCount();
                                if ($total_count > 3 && false) {

                                    if ($mobile) {
                                        $setting = new Migachat_Model_Setting();
                                        $setting->find(1);

                                        $existing_blacklisted_numbers = $normalizeNumberList($setting->getBlacklistedNumbers());
                                        if (! in_array($mobile, $existing_blacklisted_numbers, true)) {
                                            $existing_blacklisted_numbers[] = $mobile;
                                            $setting->setBlacklistedNumbers(implode(',', $existing_blacklisted_numbers))->save();
                                        }
                                    }

                                    $error_array                      = [];
                                    $error_array['value_id']          = $value_id;
                                    $error_array['has_error']         = 1;
                                    $error_array['customer_id']       = $chat_id;
                                    $error_array['error_description'] = p__("Migachat", 'Chat id requests limit reached and after 3 requests in 1 hour, blacklisted permanently!');
                                    $error_array['platform']          = 'Bridge API';
                                    $error_array['created_at']        = date("Y-m-d");
                                    (new Migachat_Model_Webservicelogs())->addData($error_array)->save();

                                    $app_id                = (new Migachat_Model_Setting())->getAppIdByValueId($value_id);
                                    $application           = (new Application_Model_Application())->find($app_id);
                                    $main_domain           = __get('main_domain');
                                    $email_data            = [];
                                    $email_data['subject'] = "Warning CHATID $chat_id API limit reached";
                                    $email_data['body']    = "In the past $chatid_duration minutes we reached more than $chatid_tokens tokens allowed for a single Chatid <br> The Chat-Id affected is ID:$chat_id <br> APP_ID:$app_id <br> APP_NAME:" . $application->getName() . " <br> MAIN DOMAIN:$main_domain.<br>";
                                    $email_data['body'] .= "<br> <br> <strong> The Chat-Id is blacklisted permanently! </strong>";

                                    $this->defaultSMTPEmail($email_data, $value_id);

                                    $payload = [
                                        'success' => true,
                                        'message' => p__("Migachat", 'Chat id requests limit reached and after 3 more messages in 1 hour, blacklisted permanently!'),
                                        'chat_id' => $chat_id,
                                    ];
                                    return $this->_sendJson($payload);
                                    exit;
                                } else {
                                    $chat_id_data_1                   = [];
                                    $chat_id_data_1['id']             = $chat_id_data_for_limit->getId();
                                    $chat_id_data_1['requests_count'] = $chat_id_data_for_limit->getRequestsCount() + 1;
                                    $chat_id_data_1['updated_at']     = date('Y-m-d H:i:s');
                                    (new Migachat_Model_ModelChatIds())->addData($chat_id_data_1)->save();

                                    $app_id                = (new Migachat_Model_Setting())->getAppIdByValueId($value_id);
                                    $application           = (new Application_Model_Application())->find($app_id);
                                    $main_domain           = __get('main_domain');
                                    $email_data            = [];
                                    $email_data['subject'] = "Warning CHATID $chat_id API limit reached";
                                    $email_data['body']    = "In the past $chatid_duration minutes we reached more than $chatid_tokens tokens allowed for a single Chatid, the Chatid affected is ID:$chat_id , APP_ID:$app_id , APP_NAME:" . $application->getName() . ", MAIN DOMAIN:$main_domain . Please check your system";

                                    $this->defaultSMTPEmail($email_data, $value_id);

                                    // Initialize and use the ChatGPT API

                                    $chatAPI  = new Migachat_Model_ChatGPTAPI($apiUrl, $secret_key, $organization_id, $gpt_model);
                                    $response = $chatAPI->generateResponse($chat_history_string, $two_chat_history_conversation, 'admin', null);

                                    $user_chat_limit_responce = $bridge_obj->getUserChatLimitResponce();
                                    $translate_response       = $chatAPI->generateResponse("Just give the translation no other text and if the language of text is same than don't translate.Tranlate the text in $global_lang: " . $user_chat_limit_responce, $translate_system_prompt, 'admin', null);
                                    $payload                  = [
                                        'success' => true,
                                        'message' => $translate_response[1],
                                        'chat_id' => $chat_id,
                                    ];
                                    return $this->_sendJson($payload);
                                    exit;
                                }
                            } else {
                                $chat_id_data_2                                = [];
                                $chat_id_data_2['id']                          = $chat_id_data_for_limit->getId();
                                $chat_id_data_2['requests_count']              = 0;
                                $chat_id_data_2['last_token_limit_reached_at'] = null;
                                $chat_id_data_2['updated_at']                  = date('Y-m-d H:i:s');
                                (new Migachat_Model_ModelChatIds())->addData($chat_id_data_2)->save();

                                $chat_id_limit_obj = new Migachat_Model_BridgrapiChatLimits();
                                $is_ai_turned_off  = $chat_id_limit_obj->find(['value_id' => $value_id, 'chat_id' => $chat_id_for_limit, 'is_limit' => 0]);
                                if ($is_ai_turned_off->getId() && ! $is_ai_turned_off->getIsLimit()) {
                                    $del_resp = $chat_id_limit_obj->delete();
                                }
                            }
                        }
                    }

                    // ================================================

                    $chat_id_limit_obj   = new Migachat_Model_BridgrapiChatLimits();
                    $is_limit_turned_off = $chat_id_limit_obj->find(['value_id' => $value_id, 'chat_id' => $chat_id, 'is_limit' => 1]);
                    // dd($is_limit_turned_off->getData(),$bridge_chatid_tokens[0]['total_tokens_sum'],$chatid_tokens,$chat_id_data_for_limit->getData());
                    // check token limit for chat_id here
                    if ($bridge_chatid_tokens[0]['total_tokens_sum'] > $chatid_tokens && ! $is_limit_turned_off->getId()) {
                        $error_array                      = [];
                        $error_array['value_id']          = $value_id;
                        $error_array['has_error']         = 1;
                        $error_array['customer_id']       = $chat_id;
                        $error_array['error_description'] = p__("Migachat", 'Chat id tokens limit reached!');
                        $error_array['platform']          = 'Bridge API';
                        $error_array['created_at']        = date("Y-m-d");

                        (new Migachat_Model_Webservicelogs())->addData($error_array)->save();

                        $chat_id_data_3                                = [];
                        $chat_id_data_3['id']                          = $chat_id_data_for_limit->getId();
                        $chat_id_data_3['requests_count']              = 1;
                        $chat_id_data_3['last_token_limit_reached_at'] = date('Y-m-d H:i:s');
                        $chat_id_data_3['updated_at']                  = date('Y-m-d H:i:s');
                        (new Migachat_Model_ModelChatIds())->addData($chat_id_data_3)->save();

                        $chat_id_limit_obj = new Migachat_Model_BridgrapiChatLimits();
                        $is_ai_turned_off  = $chat_id_limit_obj->find(['value_id' => $value_id, 'chat_id' => $chat_id, 'is_limit' => 0]);
                        if (! $is_ai_turned_off->getId()) {
                            $chat_id_limit_data = [];
                            $chat_id_limit_data = [
                                'value_id'   => $value_id,
                                'chat_id'    => $chat_id,
                                'is_limit'   => 0,
                                'ai_off_at'  => date('Y-m-d H:i:s'),
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ];

                            (new Migachat_Model_BridgrapiChatLimits())->addData($chat_id_limit_data)->save();
                        }

                        $app_id                = (new Migachat_Model_Setting())->getAppIdByValueId($value_id);
                        $application           = (new Application_Model_Application())->find($app_id);
                        $main_domain           = __get('main_domain');
                        $email_data            = [];
                        $email_data['subject'] = "Warning CHATID $chat_id API limit reached";
                        $email_data['body']    = "In the past $chatid_duration minutes we reached more than $chatid_tokens tokens allowed for a single Chatid, the Chatid affected is ID:$chat_id , APP_ID:$app_id , APP_NAME:" . $application->getName() . ", MAIN DOMAIN:$main_domain . Please check your system";
                        $this->defaultSMTPEmail($email_data, $value_id);

                        // Initialize and use the ChatGPT API
                        $chatAPI  = new Migachat_Model_ChatGPTAPI($apiUrl, $secret_key, $organization_id, $gpt_model);
                        $response = $chatAPI->generateResponse($chat_history_string, $two_chat_history_conversation, 'admin', null);

                        $user_chat_limit_responce = $bridge_obj->getUserChatLimitResponce();
                        $translate_response       = $chatAPI->generateResponse("Just give the translation no other text and if the language of text is same than don't translate.Tranlate the text in $global_lang: " . $user_chat_limit_responce, $translate_system_prompt, 'admin', null);
                        $payload                  = [
                            'success' => true,
                            'message' => $translate_response[1],
                            'chat_id' => $chat_id,
                        ];
                        return $this->_sendJson($payload);
                        exit;
                    }

                    $channel          = '';
                    $allowed_channels = ['APP', 'WHATSAPP', 'TELEGRAM', 'MESSENGER', 'WEB', 'EMAIL', 'FB', 'INSTAGRAM', 'LINKEDIN', 'OTHER'];
                    if (isset($params['channel'])) {
                        $channel = strtoupper($params['channel']);
                        if (! in_array($channel, $allowed_channels)) {
                            $error_array                      = [];
                            $error_array['value_id']          = $value_id;
                            $error_array['has_error']         = 1;
                            $error_array['error_description'] = p__("Migachat", 'Channel not allowed') . $channel;
                            $error_array['platform']          = 'Bridge API';
                            $error_array['customer_id']       = $chat_id;
                            $error_array['created_at']        = date("Y-m-d");
                            (new Migachat_Model_Webservicelogs())->addData($error_array)->save();
                            throw new Exception(p__("Migachat", 'Channel not allowed'), 1);
                        }

                    } else {
                        $channel           = 'WEB';
                        $params['channel'] = $channel;
                    }

                    $email = '';
                    if (isset($params['email'])) {
                        $email = $params['email']; // Replace with the email address you want to validate

                        // Define the regular expression pattern for email validation
                        $pattern = '/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/';

                        // Use the preg_match function to check if the email matches the pattern
                        if (! preg_match($pattern, $email)) {
                            $error_array                      = [];
                            $error_array['value_id']          = $value_id;
                            $error_array['has_error']         = 1;
                            $error_array['error_description'] = p__("Migachat", 'Invalide email format');
                            $error_array['platform']          = 'Bridge API';
                            $error_array['customer_id']       = $chat_id;
                            $error_array['created_at']        = date("Y-m-d");
                            (new Migachat_Model_Webservicelogs())->addData($error_array)->save();
                            throw new Exception(p__("Migachat", 'Invalide Email format'), 1);
                        }
                        $chat_id_data['user_email'] = $email;

                    }

                    $chat_id_data['value_id'] = $value_id;

                    $chat_id_obj         = new Migachat_Model_ModelChatIds();
                    $chat_id_data_exists = $chat_id_obj->find(['value_id' => $value_id, 'chat_id' => $chat_id]);
                    if (! $chat_id_data_exists->getId()) {
                        $chat_id_data['created_at'] = date('Y-m-d H:i:s');
                        $chat_id_data_exists        = null;
                        $chat_id_data_exists        = (new Migachat_Model_ModelChatIds())->addData($chat_id_data)->save();
                    } else {
                        $chat_id_data['id'] = $chat_id_data_exists->getId();

                        $chat_id_data_exists = (new Migachat_Model_ModelChatIds())->addData($chat_id_data)->save();
                    }
                    // get thread id if not exists create a new thread only if chatbot setting is enabled for assistant
                    $thread_id = $chat_id_data_exists->getThreadId();
                    if (empty($thread_id) && $setting_obj->getUseAssistant() == "1") {
                        $meta_data               = [];
                        $meta_data['value_id']   = (string) $value_id;
                        $meta_data['chat_id']    = (string) $chat_id;
                        $meta_data['created_at'] = date('Y-m-d H:i:s');
                        $new_thread              = $openai->createThread($meta_data);
                        // dd($new_thread);
                        // {
                        //     "id": "thread_abc123",
                        //     "object": "thread",
                        //     "created_at": 1629470000,
                        //     "metadata": {"user_id": "12345"},
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
                            $chat_id_data_exists->setThreadId($thread_id)->save();
                        } else {
                            throw new Exception(p__("Migachat", 'Failed to create a new thread. Please try again later.'));
                        }

                    }

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

                        if ($gdpr_settings->getId() && $gdpr_settings->getGdprActive() == '1') {

                            $chat_history_string .= ' ' . $message;
                            // translate gdpr texts here
                            $chatAPI  = new Migachat_Model_ChatGPTAPI($apiUrl, $secret_key, $organization_id, $gpt_model);
                            $response = $chatAPI->generateResponse($chat_history_string, $two_chat_history_conversation, 'admin', null);
                            $language = $this->languages_list[$gdpr_settings->getDefaultLanguage()];

                            if ($response[0] === true && $response[1]) {
                                $language              = str_ireplace("\n", ' ', $response[1]);
                                $prepend_translate     = "Just give the translation of given text.No explainations, no other text. if the language of text is same than don't translate.Tranlate the text in $language: ";
                                $GdprWelcomeText       = $gdpr_settings->getGdprWelcomeText();
                                $CommercialWelcomeText = $gdpr_settings->getCommercialWelcomeText();
                                $GdprSuccessText       = $gdpr_settings->getGdprSuccessText();
                                $GdprFailureText       = $gdpr_settings->getGdprFailureText();
                            } else {
                                $GdprWelcomeText       = $gdpr_settings->getGdprWelcomeText();
                                $CommercialWelcomeText = $gdpr_settings->getCommercialWelcomeText();
                                $GdprSuccessText       = $gdpr_settings->getGdprSuccessText();
                                $GdprFailureText       = $gdpr_settings->getGdprFailureText();
                            }
                            if ($chat_id_consent->getGdprConsent() == 2) {
                                // save current user message for later responce
                                $chatlogs_obj = new Migachat_Model_BridgeAPI();
                                $chatlog_data = [
                                    'value_id'           => $value_id,
                                    'chat_id'            => $chat_id,
                                    'chatbot_setting_id' => $setting_obj->getId(),
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

                                if ($language != $this->languages_list[$gdpr_settings->getDefaultLanguage()]) {
                                    $GdprWelcomeText_response = $chatAPI->generateResponse($prepend_translate . $GdprWelcomeText, $translate_system_prompt, 'admin', null);
                                    $GdprWelcomeText          = $GdprWelcomeText_response[1];
                                }
                                // ask for consent and update flag in db
                                $chat_id_consent->setGdprConsent(3)->setCreatedAt(date('Y-m-d H:i:s'))->save();
                                $response            = [];
                                $response['success'] = true;
                                $response['chat_id'] = $chat_id;
                                $response['message'] = $GdprWelcomeText . '<a href="' . $gdpr_settings->getGdprLink() . '">' . $gdpr_settings->getGdprLink() . '</a>';
                                return $this->_sendJson($response);
                            } elseif ($chat_id_consent->getGdprConsent() == 3) {
                                if ($this->checkPositiveResponce($gdpr_settings->getGdprWelcomeText(), $message, $secret_key, $organization_id)) {
                                    # if comercial is enable than send that otherwise success message
                                    $chat_id_consent->setGdprConsent(1)->setGdprConsentTimestamp(date('Y-m-d H:i:s'))->save();
                                    if ($gdpr_settings->getCommercialActive() == '1') {

                                        if ($language != $this->languages_list[$gdpr_settings->getDefaultLanguage()]) {
                                            $CommercialWelcomeText_response = $chatAPI->generateResponse($prepend_translate . $CommercialWelcomeText, $translate_system_prompt, 'admin', null);
                                            $CommercialWelcomeText          = $CommercialWelcomeText_response[1];
                                        }
                                        $chat_id_consent->setCommercialConsent(3)->save();
                                        $response            = [];
                                        $response['success'] = true;
                                        $response['chat_id'] = $chat_id;
                                        $response['message'] = $CommercialWelcomeText;
                                        return $this->_sendJson($response);
                                    } else {
                                        if ($language != $this->languages_list[$gdpr_settings->getDefaultLanguage()]) {
                                            $GdprSuccessText_response = $chatAPI->generateResponse($prepend_translate . $GdprSuccessText, $translate_system_prompt, 'admin', null);
                                            $GdprSuccessText          = $GdprSuccessText_response[1];
                                        }
                                        // $response = array();
                                        // $response['success'] = true;
                                        // $response['chat_id'] = $chat_id;
                                        // $response['message'] = $GdprSuccessText;
                                        // return $this->_sendJson($response);
                                        $ai_awnser_prepend = $GdprSuccessText . ' ';
                                    }
                                } else {
                                    if ($language != $this->languages_list[$gdpr_settings->getDefaultLanguage()]) {
                                        $GdprFailureText_response = $chatAPI->generateResponse($prepend_translate . $GdprFailureText, $translate_system_prompt, 'admin', null);
                                        $GdprFailureText          = $GdprFailureText_response[1];
                                    }
                                    $response            = [];
                                    $response['success'] = true;
                                    $response['chat_id'] = $chat_id;
                                    $response['message'] = $GdprFailureText;
                                    return $this->_sendJson($response);
                                }
                            } elseif ($chat_id_consent->getGdprConsent() == 1) {
                                if ($gdpr_settings->getCommercialActive() == '1' && $chat_id_consent->getCommercialConsent() == 3) {
                                    if ($this->checkPositiveResponce($gdpr_settings->getCommercialWelcomeText(), $message, $secret_key, $organization_id)) {
                                        $chat_id_consent->setCommercialConsent(1)->setCommercialConsentTimestamp(date('Y-m-d H:i:s'))->save();
                                    } else {
                                        $chat_id_consent->setCommercialConsent(0)->save();
                                    }
                                    if ($language != $this->languages_list[$gdpr_settings->getDefaultLanguage()]) {
                                        $GdprSuccessText_response = $chatAPI->generateResponse($prepend_translate . $GdprSuccessText, $translate_system_prompt, 'admin', null);
                                        $GdprSuccessText          = $GdprSuccessText_response[1];
                                    }
                                    // $response = array();
                                    // $response['success'] = true;
                                    // $response['chat_id'] = $chat_id;
                                    // $response['message'] = $GdprSuccessText;
                                    // return $this->_sendJson($response);
                                    $ai_awnser_prepend = $GdprSuccessText . ' ';
                                }
                            }

                        }
                        // gdpr and commercial consent endpoint
                        // =============================
                        // chackpoint for last two same messages
                        $last_two_messages_check_obj = new Migachat_Model_BridgeAPI();
                        $last_two_messages_check     = $last_two_messages_check_obj->lastTwoMessagesCheck($value_id, $chat_id, $message);
                        if ($last_two_messages_check) {

                            $error_array                      = [];
                            $error_array['value_id']          = $value_id;
                            $error_array['has_error']         = 1;
                            $error_array['customer_id']       = $chat_id;
                            $error_array['error_description'] = p__("Migachat", 'Repeating messages more than 2 times is not allowed!');
                            $error_array['platform']          = 'Bridge API';
                            $error_array['message']           = $message;
                            $error_array['customer_id']       = $chat_id;
                            $error_array['created_at']        = date("Y-m-d");
                            (new Migachat_Model_Webservicelogs())->addData($error_array)->save();
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
                        $operator_settings          = (new Migachat_Model_OperatorSettings)->find(['value_id' => $value_id]);
                        $last_asked_for_operator_at = $chat_id_consent->getLastAskedForOperatorAt();
                        // =====================================================
// Operator request flow with translation (like GDPR)
// =====================================================

// setup langs (use operator_settings default_language as baseline, fallback to 'it')
                        $op_default_lang  = strtolower($operator_settings->getDefaultLanguage() ?: 'it');
                        $op_detected_lang = strtolower($global_lang ?: $op_default_lang);

// helper inline translation (same style as GDPR)
                        $__op_translate = function (string $raw, string $targetLang) use ($chatAPI, $translate_system_prompt) {
                            $prompt = "Just give the translation no other text and if the language of text is same than don't translate.Tranlate the text in {$targetLang}: " . $raw;
                            $resp   = $chatAPI->generateResponse($prompt, $translate_system_prompt, 'admin', null);
                            return ($resp[0] ?? false) ? (string) $resp[1] : $raw;
                        };

// compute timing
                        $last_asked_for_operator_at = strtotime($last_asked_for_operator_at);
                        $current_time               = strtotime(date('Y-m-d H:i:s'));
                        $last_asked_diff            = $current_time - $last_asked_for_operator_at;

                        if ($operator_settings->getIsEnabledBridgeApi()) {

                            // Build last interactions transcript (same as your code)
                            $oper_five_chat_history = (new Migachat_Model_BridgeAPI())->getHistoryMessages($value_id, $chat_id, 10);
                            $fivw_conversation      = [];
                            $fivw_conversation_temp = [];
                            $rev_five_conversation  = "";
                            foreach ($oper_five_chat_history as $key => $value) {
                                if ($value['role'] == 'user') {
                                    $fivw_conversation[] = [
                                        'role'      => 'user',
                                        'content'   => urldecode($value['message_content']),
                                        'date_time' => $value['created_at'],
                                    ];
                                } else {
                                    $fivw_conversation[] = [
                                        'role'      => 'assistant',
                                        'content'   => $value['message_content'],
                                        'date_time' => $value['created_at'],
                                    ];
                                }
                            }
                            $fivw_conversation_temp = array_reverse($fivw_conversation);
                            foreach ($fivw_conversation_temp as $key => $value) {
                                $rev_five_conversation .= 'TIMESTAMP = ' . $value['date_time'] . '<br>' .
                                    'ROLE = ' . $value['role'] . '<br>' .
                                    'TEXT = ' . $value['content'] . '<br>----------------<br>';
                            }

                            if ($chat_id_consent->getAskedForOperator() != 1 && $last_asked_diff > 3600) {

                                $operatorprompt = $operator_settings->getOperatorSystemPrompt()
                                    ?: "Analyze this text string that a user wrote on our support chat (user prompt), reply with 1 if it is sufficiently probable tha it means that the user wants to speak to an operator. If it is not clear enough and in all other cases reply with a 0";

                                $operatorprompt = str_replace('@@last_five_history@@', 'last five interactions with user : ' . $rev_five_conversation, $operatorprompt);

                                $operator = $this->checkOperator($operatorprompt, $message, $secret_key, $organization_id);

                                if ($operator) {
                                    $chat_id_consent
                                        ->setAskedForOperator(1)
                                        ->setAskedForOperatorSt(date('Y-m-d H:i:s'))
                                        ->setAskedForOperatorCount(1)
                                        ->setCreatedAt(date('Y-m-d H:i:s'))
                                        ->setLastAskedForOperatorAt(date('Y-m-d H:i:s'))
                                        ->save();

                                    if ($lastInsertId->getId()) {
                                        $chatlog_data                       = [];
                                        $chatlog_data['asked_for_operator'] = 1;
                                        $chatlog_data['updated_at']         = date("Y-m-d H:i:s");
                                        $chatlogs_obj->addData($chatlog_data)->save();
                                    }

                                    // translate AskCallFromOperatorMsg (only if detected != default)
                                    $AskCallFromOperatorMsg = $operator_settings->getAskCallFromOperatorMsg();
                                    if ($op_detected_lang && $op_detected_lang !== $op_default_lang) {
                                        $AskCallFromOperatorMsg = $__op_translate($AskCallFromOperatorMsg, strtoupper($op_detected_lang));
                                    }

                                    $response            = [];
                                    $response['success'] = true;
                                    $response['chat_id'] = $chat_id;
                                    $response['message'] = $AskCallFromOperatorMsg;
                                    return $this->_sendJson($response);
                                }

                            } elseif ($chat_id_consent->getAskedForOperator() == 1) {

                                $asked_for_operator_at = strtotime($chat_id_consent->getAskedForOperatorSt());
                                $current_time          = strtotime(date('Y-m-d H:i:s'));
                                $diff                  = $current_time - $asked_for_operator_at;

                                if ($diff < 600) {

                                    // Stage-2 confirmation via keywords (kept as-is)
                                    $isPositiveResponceForOperator = false;
                                    foreach ($this->positiveResponses as $positiveWord) {
                                        if (strpos(strtolower($message), $positiveWord) !== false) {
                                            $isPositiveResponceForOperator = true;
                                            break;
                                        }
                                    }

                                    if ($isPositiveResponceForOperator) {

                                        // Save operator request, send webhook/email (your existing code)
                                        $app_id                            = (new Migachat_Model_Setting())->getAppIdByValueId($value_id);
                                        $operator_reqested                 = [];
                                        $operator_reqested['app_id']       = $app_id;
                                        $operator_reqested['value_id']     = $value_id;
                                        $operator_reqested['bot_type']     = 'bridge_api';
                                        $operator_reqested['user_id']      = $chat_id;
                                        $operator_reqested['status']       = 'pending';
                                        $operator_reqested['request_data'] = $message;
                                        $operator_reqested['user_email']   = $chat_id_consent->getUserEmail();
                                        $operator_reqested['user_mobile']  = $chat_id_consent->getUserMobile();
                                        $operator_reqested['user_name']    = $chat_id_consent->getUserName();
                                        $operator_reqested['created_at']   = date('Y-m-d H:i:s');
                                        $operator_reqested['updated_at']   = date('Y-m-d H:i:s');
                                        $operator_saved                    = (new Migachat_Model_OperatorRequests())->addData($operator_reqested)->save();
                                        $operator_id                       = $operator_saved->getId();

                                        $this->sendOperatorWebhook($operator_reqested, $chat_id_consent, $operator_id, $operator_settings, $rev_five_conversation);
                                        $this->sendOperatorEmail($operator_reqested, $chat_id_consent, $operator_id, $operator_settings, $rev_five_conversation);

                                        $chat_id_consent->setAskedForOperator(0)->setAskedForOperatorCount(0)->setCreatedAt(date('Y-m-d H:i:s'))->save();

                                        // translate ConfirmCallFromOperatorMsg
                                        $ConfirmCallFromOperatorMsg = $operator_settings->getConfirmCallFromOperatorMsg();
                                        if ($op_detected_lang && $op_detected_lang !== $op_default_lang) {
                                            $ConfirmCallFromOperatorMsg = $__op_translate($ConfirmCallFromOperatorMsg, strtoupper($op_detected_lang));
                                        }

                                        $response            = [];
                                        $response['success'] = true;
                                        $response['chat_id'] = $chat_id;
                                        $response['message'] = $ConfirmCallFromOperatorMsg;
                                        return $this->_sendJson($response);

                                    } else {

                                        if (in_array(strtolower($message), $this->negativeResponses)) {

                                            $chat_id_consent->setAskedForOperator(0)->setAskedForOperatorCount(0)->setCreatedAt(date('Y-m-d H:i:s'))->save();

                                            // translate DeclinedCallFromOperatorMsg
                                            $DeclinedCallFromOperatorMsg = $operator_settings->getDeclinedCallFromOperatorMsg();
                                            if ($op_detected_lang && $op_detected_lang !== $op_default_lang) {
                                                $DeclinedCallFromOperatorMsg = $__op_translate($DeclinedCallFromOperatorMsg, strtoupper($op_detected_lang));
                                            }

                                            $response            = [];
                                            $response['success'] = true;
                                            $response['chat_id'] = $chat_id;
                                            $response['message'] = $DeclinedCallFromOperatorMsg;
                                            return $this->_sendJson($response);

                                        } else {

                                            if ($chat_id_consent->getAskedForOperatorCount() < 2) {

                                                $chat_id_consent->setAskedForOperator(1)->setAskedForOperatorCount(2)->setCreatedAt(date('Y-m-d H:i:s'))->save();

                                                // translate InvalidAskCallFromOperatorMsg
                                                $InvalidAskCallFromOperatorMsg = $operator_settings->getInvalidAskCallFromOperatorMsg();
                                                if ($op_detected_lang && $op_detected_lang !== $op_default_lang) {
                                                    $InvalidAskCallFromOperatorMsg = $__op_translate($InvalidAskCallFromOperatorMsg, strtoupper($op_detected_lang));
                                                }

                                                $response            = [];
                                                $response['success'] = true;
                                                $response['chat_id'] = $chat_id;
                                                $response['message'] = $InvalidAskCallFromOperatorMsg;
                                                return $this->_sendJson($response);

                                            } else {

                                                $chat_id_consent->setAskedForOperator(0)->setAskedForOperatorCount(0)->setCreatedAt(date('Y-m-d H:i:s'))->save();

                                                // translate DeclinedCallFromOperatorMsg
                                                $DeclinedCallFromOperatorMsg = $operator_settings->getDeclinedCallFromOperatorMsg();
                                                if ($op_detected_lang && $op_detected_lang !== $op_default_lang) {
                                                    $DeclinedCallFromOperatorMsg = $__op_translate($DeclinedCallFromOperatorMsg, strtoupper($op_detected_lang));
                                                }

                                                $response            = [];
                                                $response['success'] = true;
                                                $response['chat_id'] = $chat_id;
                                                $response['message'] = $DeclinedCallFromOperatorMsg;
                                                return $this->_sendJson($response);
                                            }
                                        }
                                    }

                                } else {
                                    // (line after anchor will follow this)
                                    $chat_id_consent->setAskedForOperator(0)->setAskedForOperatorCount(0)->setCreatedAt(date('Y-m-d H:i:s'))->save();
                                }
                            }
                        }

                        // bridge api chat limit overall + chat id
                        $chat_id_limit_obj = new Migachat_Model_BridgrapiChatLimits();
                        $is_ai_turned_off  = $chat_id_limit_obj->find(['value_id' => $value_id, 'chat_id' => $chat_id, 'is_limit' => 0]);

                        if ($is_ai_turned_off->getId()) {
                            throw new Exception(p__("Migachat", 'AI is turned off for this chat id.'), 1);
                        }

                        $complete_prompt = "You are a helpful assistant. if the answer include a link add always the complete url starting with https://. Use the name of customer sending in prompt of role user.";
                        if ($app_setting_obj->getSystemPrompt()) {
                            $complete_prompt = $app_setting_obj->getSystemPrompt() . ' if the answer include a link add always the complete url starting with https://. Use the name of customer sending in prompt of role user.';
                        }
                        $complete_prompt .= ' ' . $message . ' ';

                        $system_prompt_token_limit = round((new Migachat_Model_ModelTokens())->getSystemPromptTokens($value_id), 0);

                        $history_tokens       = (new Migachat_Model_ModelTokens())->getHistoryTokens($value_id);
                        $history_tokens_limit = $history_tokens[0] + $system_prompt_token_limit;

                        $history_messages_limit = $history_tokens[1];

                        // Fetch chat history and prepare conversation
                        $message_sent = 0;
                        $chat_history = (new Migachat_Model_BridgeAPI())->getHistoryMessages($value_id, $chat_id, $history_messages_limit);

                        $all_conversation             = [];
                        $bu_all_conversation          = [];
                        $last_message_max_token_exeed = false;
                        foreach ($chat_history as $key => $value) {
                            $complete_prompt .= $value['message_content'];
                            if ((new Migachat_Model_Setting)->countTokens($complete_prompt) > ($history_tokens_limit - ((5 * $history_tokens_limit) / 100))) {
                                break;
                            }
                            $pattern = '/[^a-zA-Z0-9]+/';
                            if ($value['role'] == 'user') {
                                if ($name) {
                                    $all_conversation[] = [
                                        'role'    => 'user',
                                        'name'    => preg_replace($pattern, '-', $name),
                                        'content' => urldecode($value['message_content']),
                                    ];
                                } else {

                                    $all_conversation[] = [
                                        'role'    => 'user',
                                        'content' => urldecode($value['message_content']),
                                    ];
                                }
                            } else {
                                $message_content = $value['message_content'];
                                if ($key == 0 && $value['max_token_exeed']) {
                                    $message_content              = str_replace($value['max_token_responce'], ' ', $message_content);
                                    $last_message_max_token_exeed = true;
                                }
                                $all_conversation[] = [
                                    'role'    => 'assistant',
                                    'content' => $message_content,
                                ];
                            }
                        }

                        if ($app_setting_obj->getId()) {
                            if ($app_setting_obj->getPromptChatgptActive()) {
                                $token_limit = (new Migachat_Model_ModelTokens())->find(['model_name' => $gpt_model])->getTokens();
                                if (! $token_limit) {
                                    $k8   = ['gpt-4', 'gpt-4-0613', 'gpt-4-0314', 'code-davinci-002'];
                                    $k16  = ['gpt-3.5-turbo-16k', 'gpt-3.5-turbo-16k-0613'];
                                    $k32  = ['gpt-4-32k', 'gpt-4-32k-0613', 'gpt-4-32k-0314', 'code-davinci-002'];
                                    $k128 = ['gpt-4-1106-preview', 'gpt-4-vision-preview', 'chatgpt-4o-latest', 'gpt-4o-mini-2024-07-18', 'gpt-4o-mini', 'gpt-4o-2024-08-06', 'gpt-4o-2024-05-13', 'gpt-4o'];
                                    if (in_array($gpt_model, $k8)) {
                                        $token_limit = 8000;
                                    } elseif (in_array($gpt_model, $k16)) {
                                        $token_limit = 16000;
                                    } elseif (in_array($gpt_model, $k32)) {
                                        $token_limit = 32000;
                                    } elseif (in_array($gpt_model, $k128)) {
                                        $token_limit = 128000;
                                    } else {
                                        $token_limit = 4000;
                                    }
                                }
                                $system_prompt = $app_setting_obj->getSystemPrompt();
                                if ((new Migachat_Model_Setting)->countTokens($system_prompt) < $system_prompt_token_limit) {
                                    $all_conversation[] = [
                                        'role'    => 'system',
                                        'content' => $system_prompt . '. ' . $system_channel_prompt,
                                    ];
                                } else {
                                    $all_conversation[] = [
                                        'role'    => 'system',
                                        'content' => 'You are a helpful assistant. if the answer include a link add always the complete url starting with https://. Use the name of customer sending in prompt of role user.' . $system_channel_prompt,
                                    ];
                                }
                            } else {
                                $all_conversation[] = [
                                    'role'    => 'system',
                                    'content' => 'You are a helpful assistant. if the answer include a link add always the complete url starting with https://. Use the name of customer sending in prompt of role user.' . $system_channel_prompt,
                                ];
                            }
                        } else {
                            $all_conversation[] = [
                                'role'    => 'system',
                                'content' => 'You are a helpful assistant. if the answer include a link add always the complete url starting with https://. Use the name of customer sending in prompt of role user.' . $system_channel_prompt,
                            ];
                        }

                        $all_conversation_r = array_reverse($all_conversation);
                        // Initialize and use the ChatGPT API
                        //getting the responce max_tokens
                        $max_tokens               = $bridge_obj->getAiAnswerTokenLimit();
                        $user_max_tokens_responce = 'vuoi che continui?';
                        $user_max_tokens_responce = $bridge_obj->getAiAnswerTokenLimitMsg();
                        if ($this->checkPositiveResponce($user_max_tokens_responce, $message, $secret_key, $organization_id) && $last_message_max_token_exeed) {
                            $message = "continue the responce from where it stoped.";
                        }
                        $chatAPI = new Migachat_Model_ChatGPTAPI($apiUrl, $secret_key, $organization_id, $gpt_model);

                        // for assistant / threads or simple chat will manage here
                        $response = [];
                        if ($setting_obj->getUseAssistant() == "1") {
                            $message_to_thread = $openai->addMessageToThread($thread_id, 'user', $message);
                            // dd($message_to_thread);
                            if (! isset($message_to_thread['id'])) {
                                throw new Exception("Failed to add message to thread");
                            }
                            $assistant_id = $app_setting_obj->getAssistantId();
                            if (empty($assistant_id)) {
                                throw new Exception("Assistant ID is not set in chatbot settings");
                            }

                            // Migachat_Model_Assistants
                            $assistant = (new Migachat_Model_Assistants())->find(['assistant_id' => $assistant_id]);
                            if (! $assistant->getId()) {
                                throw new Exception("Assistant not found with ID: " . $assistant_id);
                            }
                            $file_ids = $assistant->getOpenaiFileIds();
                            // 3. Run the assistant (with vector store if needed)
                            $options = is_string($file_ids) ? json_decode($file_ids, true) : ($file_ids ? $file_ids : false);
                            $extra_instructions = "Language policy: Look at the last 8–10 messages and detect the user’s language, prioritizing the most recent user message. Reply entirely in that language. If messages contain multiple languages, use the language of the latest user message for your main reply and keep any quoted text in its original language. Do not translate code blocks, URLs, error messages, product/brand names, file paths, or quoted snippets.
                                                    Ambiguity rule: If the latest user message is too short (e.g., fewer than 3 words, or only emojis/stickers/“ok”/“yes”), or if prior history is unreliable (conflicting languages or insufficient context), respond in Italian (IT). If the user later switches language, switch accordingly without comment.";
                            $opts = [
                                'truncation_strategy' => [
                                    'type'          => 'last_messages', // or 'auto'
                                    'last_messages' => 10,              // keep only the last 8–10 thread messages
                                ],
                            ];

                            // If you’re attaching a vector store, include it as you already do:
                            if ($options) {
                                $opts['tool_resources'] = [
                                    'file_search' => [
                                        'vector_store_ids' => is_string($file_ids) ? json_decode($file_ids, true) : ($file_ids ? $file_ids : [])
                                    ],
                                ];
                            }
                            $run = $openai->runThread($thread_id, $assistant_id, $opts);

                            // dd($run);
                            if (! isset($run['id'])) {
                                // dd($run);
                                throw new Exception("Failed to initiate assistant run");
                            }
                            $run_id = $run['id'];

                                                        // --- 4) Poll for run completion (with requires_action handling) ---
                            $deadline   = time() + 120; // up to 120s
                            $run_status = null;

                            while (true) {
                                                // small backoff
                                usleep(600000); // 0.6s

                                $status = $openai->getRunStatus($thread_id, $run_id);
                                if (! isset($status['status'])) {
                                    throw new Exception("Failed to get run status");
                                }

                                $run_status = $status['status'];

                                // Handle tool-calls if your assistant can request them
                                if ($run_status === 'requires_action' && ! empty($status['required_action']['submit_tool_outputs'])) {
                                    // TODO: build $tool_outputs based on your tools (search, custom, etc.)
                                    // Example skeleton:
                                    /*
        $tool_outputs = [
            [
                'tool_call_id' => $status['required_action']['submit_tool_outputs']['tool_calls'][0]['id'],
                'output'       => '...your tool output here...',
            ],
        ];
        $openai->submitToolOutputs($thread_id, $run_id, ['tool_outputs' => $tool_outputs]);
        */
                                    // If you don't support tools yet:
                                    throw new Exception("Run requires tool outputs, but no tool handler is implemented.");
                                }

                                if ($run_status === 'completed' || $run_status === 'failed' || $run_status === 'cancelled' || $run_status === 'expired') {
                                    break;
                                }

                                if (time() > $deadline) {
                                    throw new Exception("Run did not complete in time (last status: {$run_status})");
                                }
                            }
                            $promptTokens     = $status['usage']['prompt_tokens'] ?? 0;
                            $completionTokens = $status['usage']['completion_tokens'] ?? 0;
                            $totalTokens      = $status['usage']['total_tokens'] ?? 0;
                            // 5. Retrieve final messages
                            // 5. Retrieve final messages

                            $messages = $openai->getThreadMessages($thread_id, ['order' => 'desc', 'limit' => 1]);
                            if (! isset($messages['data'][0])) {
                                throw new Exception("No messages found in thread");
                            }

                            $assistant_response = $messages['data'][0]['content'][0]['text']['value'] ?? '[No response content]';

                            $response_msg = '';
                            $response_msg = str_ireplace("\n", '<br>', $assistant_response);
                            $response_msg = $this->removeEmojis($response_msg);
                            $response[0]  = true;
                            $response[1]  = $response_msg;
                            $response[2]  = $promptTokens;
                            $response[3]  = $completionTokens;
                            $response[4]  = $totalTokens;
                        } else {
                            $response = $chatAPI->generateResponse($message, $all_conversation_r, $name, $max_tokens);
                        }

                        if ($response[0] === true) {
                            // Log the response and update chat logs

                            $response_msg = str_ireplace("\n", '<br>', $response[1]);
                            if ($lastInsertId->getId()) {
                                $chatlog_data                       = [];
                                $chatlog_data['migachat_bridge_id'] = $lastInsertId->getId();
                                $chatlog_data['is_sent']            = 1;
                                $chatlog_data['prompt_tokens']      = $response[2];
                                $chatlog_data['updated_at']         = date("Y-m-d H:i:s");
                                $chatlogs_obj->addData($chatlog_data)->save();
                            }
                            // check if the message is cut off due to max token limit
                            $max_token_exeed    = 0;
                            $max_token_responce = null;

                            if ($max_tokens == $response[3]) {
                                $chat_history_string .= $message . ' ' . $response_msg;

                                $trl_response = $chatAPI->generateResponse($chat_history_string, $two_chat_history_conversation, 'admin', null);

                                if ($trl_response[0] === true) {
                                    $language = str_ireplace("\n", '<br>', $trl_response[1]);

                                    $user_max_tokens_responce = "Just give the translation no other text and if the language of text is same than don't translate.Tranlate the text in $language: " . ' ' . $user_max_tokens_responce;
                                    $translate_response       = $chatAPI->generateResponse($user_max_tokens_responce, $translate_system_prompt, 'admin', null);

                                    $user_max_tokens_responce = str_ireplace("\n", '<br>', $translate_response[1]);
                                }
                                $response_msg .= " <br> <br>" . $user_max_tokens_responce;
                                $max_token_exeed    = 1;
                                $max_token_responce = $user_max_tokens_responce;
                            }

                            $chatlog_data = [
                                'value_id'           => $value_id,
                                'chat_id'            => $chat_id,
                                'chatbot_setting_id' => $setting_obj->getId(),
                                'role'               => 'agent',
                                'message_content'    => $this->removeEmojis($response_msg),
                                'completion_tokens'  => $response[3],
                                'total_tokens'       => $response[4],
                                'user_email'         => $email,
                                'user_name'          => $name,
                                'user_mobile'        => $mobile,
                                'is_sent'            => 1,
                                'channel'            => $channel,
                                'has_error'          => 0,
                                'is_read'            => 1,
                                'error_description'  => "",
                                'max_token_exeed'    => $max_token_exeed,
                                'max_token_responce' => $max_token_responce,
                                'created_at'         => date("Y-m-d H:i:s"),
                                'updated_at'         => date("Y-m-d H:i:s"),
                            ];

                            $chatlogs_obj = new Migachat_Model_BridgeAPI();
                            $chatlogs_obj->addData($chatlog_data)->save();

                            $payload = [
                                'success' => true,
                                'message' => ($ai_awnser_prepend) ? $ai_awnser_prepend . '<br><br><br><br>' . $this->removeEmojis($response_msg) : $this->removeEmojis($response_msg),
                                'chat_id' => $chat_id,
                            ];

                            $error_array                      = [];
                            $error_array['value_id']          = $value_id;
                            $error_array['has_error']         = 0;
                            $error_array['error_description'] = p__("Migachat", 'Get reply successfully.');
                            $error_array['platform']          = 'Bridge API';
                            $error_array['message']           = $message;
                            $error_array['customer_id']       = $chat_id;
                            $error_array['request']           = serialize($all_conversation_r);
                            $error_array['responce']          = $response_msg;
                            $error_array['message_id']        = $lastInsertId->getId();
                            $error_array['created_at']        = date("Y-m-d");
                            (new Migachat_Model_Webservicelogs())->addData($error_array)->save();

                        } else {
                            $payload = [
                                'success' => false,
                                'message' => $this->removeEmojis($response[1]),
                                'chat_id' => $chat_id,
                            ];
                            $error_array                      = [];
                            $error_array['value_id']          = $value_id;
                            $error_array['has_error']         = 1;
                            $error_array['error_description'] = $response[1];
                            $error_array['message']           = $message;
                            $error_array['customer_id']       = $chat_id;
                            $error_array['message_id']        = $lastInsertId->getId();
                            $error_array['platform']          = 'Bridge API';
                            $error_array['created_at']        = date("Y-m-d");
                            (new Migachat_Model_Webservicelogs())->addData($error_array)->save();
                        }

                        return $this->_sendJson($payload);
                        exit;
                    }
                } else {
                    $error_array                      = [];
                    $error_array['value_id']          = $value_id;
                    $error_array['has_error']         = 1;
                    $error_array['error_description'] = p__("Migachat", 'Application settings mismatch');
                    $error_array['platform']          = 'Bridge API';
                    $error_array['created_at']        = date("Y-m-d");
                    (new Migachat_Model_Webservicelogs())->addData($error_array)->save();
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
            $gpt_model          = 'gpt-4-1106-preview';
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

}
