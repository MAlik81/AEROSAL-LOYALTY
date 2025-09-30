<?php
declare(strict_types=1);

function expect($condition, $message)
{
    if (! $condition) {
        throw new RuntimeException($message);
    }
}

if (! function_exists('p__')) {
    function p__($domain, $text)
    {
        return $text;
    }
}

if (! class_exists('Core_Controller_Default')) {
    class Core_Controller_Default
    {
        protected function _sendJson($payload)
        {
            return $payload;
        }
    }
}

if (! class_exists('Migachat_Controller_Default')) {
    class Migachat_Controller_Default extends Core_Controller_Default
    {
    }
}

if (! class_exists('Migachat_Model_ChatGPTAPI')) {
    class Migachat_Model_ChatGPTAPI
    {
    }
}

if (! class_exists('Migachat_Model_BridgeAPI')) {
    class Migachat_Model_BridgeAPI
    {
        public $saved = [];

        public function addData($data)
        {
            $this->saved[] = $data;

            return $this;
        }

        public function save()
        {
            return new class
            {
                public function getId()
                {
                    return 101;
                }
            };
        }
    }
}

if (! class_exists('Migachat_Model_Webservicelogs')) {
    class Migachat_Model_Webservicelogs
    {
        public $entries = [];

        public function addData($data)
        {
            $this->entries[] = $data;

            return $this;
        }

        public function save()
        {
            return $this;
        }
    }
}

class StubSetting
{
    public function getId()
    {
        return 555;
    }
}

class StubLastInsertId
{
    public function getId()
    {
        return 777;
    }
}

class StubChatlogs
{
    public $updates = [];

    public function addData($data)
    {
        $this->updates[] = $data;

        return $this;
    }

    public function save()
    {
        return $this;
    }
}

class StubOpenAI
{
    public $addedMessages = [];
    public $runThreadCalls = [];
    private $statusCalls = 0;

    public function addMessageToThread($threadId, $role, $content)
    {
        $this->addedMessages[] = [
            'thread_id' => $threadId,
            'role'      => $role,
            'content'   => $content,
        ];

        return ['id' => 'msg_test'];
    }

    public function runThread($threadId, $assistantId, $options = [])
    {
        $this->runThreadCalls[] = [
            'thread_id'    => $threadId,
            'assistant_id' => $assistantId,
            'options'      => $options,
        ];

        return ['id' => 'run_test'];
    }

    public function getRunStatus($threadId, $runId)
    {
        $this->statusCalls++;

        if ($this->statusCalls < 2) {
            return ['status' => 'in_progress'];
        }

        return [
            'status' => 'completed',
            'usage'  => [
                'prompt_tokens'     => 12,
                'completion_tokens' => 8,
                'total_tokens'      => 20,
            ],
        ];
    }

    public function getThreadMessages($threadId, $params = [])
    {
        return [
            'data' => [
                [
                    'content' => [
                        [
                            'text' => [
                                'value' => 'Risposta di prova',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}

require_once __DIR__ . '/../controllers/Public/BridgeapiController.php';

function invokeGenerate(array $conversationOverrides, array $executionOverrides, StubOpenAI $openai)
{
    $baseConversation = [
        'chat_api'          => new Migachat_Model_ChatGPTAPI(),
        'prepared_message'  => 'Ciao, puoi aiutarmi con il menu del ristorante? ',
        'message'           => 'Ciao, puoi aiutarmi con il menu del ristorante? ',
        'assistant_context' => [
            'use_assistant'      => true,
            'thread_id'          => 'thread_base',
            'assistant_id'       => 'assistant_base',
            'assistant_run_opts' => [
                'truncation_strategy' => [
                    'type'          => 'last_messages',
                    'last_messages' => 10,
                ],
                'tool_resources'      => [
                    'file_search' => [
                        'vector_store_ids' => ['vs_base'],
                    ],
                ],
            ],
        ],
        'global_lang'      => 'IT',
        'global_lang_name' => 'Italian',
        'conversation'     => [],
        'name'             => 'Mario',
        'channel'          => 'web',
    ];

    $baseExecution = [
        'openai'            => $openai,
        'value_id'          => 1,
        'chat_id'           => 2,
        'setting_obj'       => new StubSetting(),
        'email'             => 'user@example.com',
        'name'              => 'Mario',
        'mobile'            => '12345',
        'channel'           => 'web',
        'last_insert_id'    => new StubLastInsertId(),
        'chatlogs_obj'      => new StubChatlogs(),
        'ai_answer_prepend' => '',
    ];

    $conversation = array_replace_recursive($baseConversation, $conversationOverrides);
    $execution    = array_replace_recursive($baseExecution, $executionOverrides);

    $controller = new Migachat_Public_BridgeapiController();
    $reflection = new ReflectionClass($controller);
    $method     = $reflection->getMethod('generateAndLogAiResponse');
    $method->setAccessible(true);

    return $method->invoke($controller, $conversation, $execution);
}

$openai1  = new StubOpenAI();
$payload1 = invokeGenerate([], [], $openai1);

expect($payload1['success'] === true, 'Expected successful payload');
expect(count($openai1->runThreadCalls) === 1, 'runThread should be called once');
$opts1 = $openai1->runThreadCalls[0]['options'];
expect(isset($opts1['additional_instructions']), 'Expected additional instructions to be present');
expect(strpos($opts1['additional_instructions'], 'Always respond in Italian') !== false, 'Expected Italian directive');
expect(strpos($opts1['additional_instructions'], 'Mirror the user\'s tone') !== false, 'Expected mirror directive');
expect(strpos($opts1['additional_instructions'], 'menu del ristorante') !== false, 'Expected snippet from prepared message');
expect(isset($opts1['truncation_strategy']), 'truncation strategy must be preserved');
expect(isset($opts1['tool_resources']), 'tool resources must be preserved');

$openai2  = new StubOpenAI();
$payload2 = invokeGenerate([
    'prepared_message' => 'ok',
    'message'          => 'ok',
], [], $openai2);

$opts2 = $openai2->runThreadCalls[0]['options'];
expect(isset($opts2['additional_instructions']), 'Expected language directive even for short message');
expect(strpos($opts2['additional_instructions'], 'Mirror the user\'s tone') === false, 'Mirror directive should be skipped for short message');

$openai3  = new StubOpenAI();
$payload3 = invokeGenerate([
    'global_lang'      => '',
    'global_lang_name' => '',
], [], $openai3);

$opts3 = $openai3->runThreadCalls[0]['options'];
expect(! isset($opts3['additional_instructions']), 'No additional instructions expected when metadata missing');

fwrite(STDOUT, "Assistant context tests passed\n");
