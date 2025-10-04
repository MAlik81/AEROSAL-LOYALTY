<?php

class Aerosalloyalty_ApplicationController extends Application_Controller_Default
{
    public function viewAction()
    {
        $this->loadPartials();
    }

    /**
     * Prepare campaign payload ensuring optional columns are present.
     *
     * @param mixed $rows
     * @return array
     */
    protected function _formatCampaignRows($rows)
    {
        if ($rows instanceof Traversable) {
            $rows = iterator_to_array($rows, false);
        } elseif (!is_array($rows)) {
            $rows = [];
        }

        return array_map(function ($row) {
            if ($row instanceof Core_Model_Default) {
                $data = $row->getData();
            } elseif ($row instanceof Zend_Db_Table_Row_Abstract) {
                $data = $row->toArray();
            } elseif (is_array($row)) {
                $data = $row;
            } else {
                $data = [];
            }

            if (!array_key_exists('last_operation', $data)) {
                $data['last_operation'] = null;
            }

            if (!array_key_exists('last_operation_at', $data)) {
                $data['last_operation_at'] = null;
            }

            if (!array_key_exists('webhook_logs', $data)) {
                $data['webhook_logs'] = null;
            }

            if (!array_key_exists('webhook_log_summary', $data)) {
                $data['webhook_log_summary'] = null;
            }

            return $data;
        }, $rows);
    }

    /**
     * Retrieve campaigns ordered by name.
     *
     * @param int $value_id
     * @param string|null $card_number
     * @return Zend_Db_Table_Rowset_Abstract
     */
    protected function _fetchCampaignRows($value_id, $card_number = null)
    {
        $db = new Aerosalloyalty_Model_Db_Table_Campaign();
        $sel = $db->select()->from($db->info('name'))
            ->where('value_id = ?', (int)$value_id)
            ->order('name ASC');

        if ($card_number !== null) {
            $sel->where('card_number LIKE ?', $card_number . '%');
        }

        return $db->fetchAll($sel);
    }

    /** INIT: returns all data needed by the editor (settings + types); campaigns optional */
    public function initAction()
    {
        try {
            $value_id = (int)$this->getRequest()->getParam('value_id');
            if (!$value_id) throw new Exception('Missing value_id');
            $settings = (new Aerosalloyalty_Model_Settings())->findAll(['value_id' => $value_id])->toArray();
            $types    = (new Aerosalloyalty_Model_CampaignType())->findAll(['value_id' => $value_id])->toArray();
            $campaigns = $this->_formatCampaignRows($this->_fetchCampaignRows($value_id));
            $campaigns = $this->_attachLatestWebhookLogs($value_id, $campaigns);
            return $this->_sendJson([
                'success' => 1,
                'settings' => $settings ? $settings[0] : [],
                'types' => $types,
                'campaigns' => $campaigns
            ]);
        } catch (Exception $e) {
            return $this->_sendJson(['error' => 1, 'message' => $e->getMessage()]);
        }
    }

    /** SETTINGS — save (POST) */
    public function saveSettingsAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->_sendJson(['error' => 1, 'message' => 'Invalid request']);
        }
        try {
            $p = $this->getRequest()->getPost();
            $value_id = (int)($p['value_id'] ?? 0);
            if (!$value_id) throw new Exception('Missing value_id');

            $data = [
                'value_id'              => $value_id,
                'app_id'                => (int)($p['app_id'] ?? 0),
                'default_ean_encoding'  => $p['default_ean_encoding'] ?? 'EAN13',
                'enable_check_benefits' => !empty($p['enable_check_benefits']) ? 1 : 0,
                'webhook_url'           => strlen(trim($p['webhook_url'] ?? '')) ? trim($p['webhook_url']) : null,
            ];

            (new Aerosalloyalty_Model_Settings())->upsert($data);
            return $this->_sendJson(['success' => 1, 'message' => p__('Aerosalloyalty', 'Settings saved')]);
        } catch (Exception $e) {
            return $this->_sendJson(['error' => 1, 'message' => $e->getMessage()]);
        }
    }

    /** API TOKEN — fetch active token (GET) */
    public function getApiTokenAction()
    {
        try {
            $value_id = (int)$this->getRequest()->getParam('value_id');
            if (!$value_id) throw new Exception('Missing value_id');

            $token = (new Aerosalloyalty_Model_ApiToken())->ensureToken($value_id);

            return $this->_sendJson([
                'success' => 1,
                'token' => $token->getToken(),
                'last_used_at' => $token->getLastUsedAt()
            ]);
        } catch (Exception $e) {
            return $this->_sendJson(['error' => 1, 'message' => $e->getMessage()]);
        }
    }

    /** API TOKEN — regenerate (POST) */
    public function regenerateApiTokenAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->_sendJson(['error' => 1, 'message' => 'Invalid request']);
        }

        try {
            $value_id = (int)$this->getRequest()->getPost('value_id');
            if (!$value_id) throw new Exception('Missing value_id');

            $token = (new Aerosalloyalty_Model_ApiToken())->regenerateToken($value_id);

            return $this->_sendJson([
                'success' => 1,
                'token' => $token->getToken(),
                'last_used_at' => $token->getLastUsedAt()
            ]);
        } catch (Exception $e) {
            return $this->_sendJson(['error' => 1, 'message' => $e->getMessage()]);
        }
    }

    /** CAMPAIGN TYPES — list (GET) */
    public function listTypesAction()
    {
        try {
            $value_id = (int)$this->getRequest()->getParam('value_id');
            if (!$value_id) throw new Exception('Missing value_id');

            $types = (new Aerosalloyalty_Model_CampaignType())->allForValue($value_id);
            return $this->_sendJson([
                'success' => 1,
                'types' => array_map(function ($t) {
                    return $t->getData();
                }, iterator_to_array($types ?: []))
            ]);
        } catch (Exception $e) {
            return $this->_sendJson(['error' => 1, 'message' => $e->getMessage()]);
        }
    }

    /** CAMPAIGN TYPE — save (POST) create/update by (value_id, code) */
    public function saveTypeAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->_sendJson(['error' => 1, 'message' => 'Invalid request']);
        }
        try {
            $p = $this->getRequest()->getPost();
            $value_id = (int)($p['value_id'] ?? 0);
            if (!$value_id) throw new Exception('Missing value_id');

            $data = [
                'value_id' => $value_id,
                'code'     => trim($p['code'] ?? ''),
                'name'     => trim($p['name'] ?? ''),
                'icon'     => strlen(trim($p['icon'] ?? '')) ? trim($p['icon']) : null
            ];
            if (!$data['code'] || !$data['name']) throw new Exception('Code and Name are required');

            (new Aerosalloyalty_Model_CampaignType())->upsert($data);
            return $this->_sendJson(['success' => 1, 'message' => p__('Aerosalloyalty', 'Type saved')]);
        } catch (Exception $e) {
            return $this->_sendJson(['error' => 1, 'message' => $e->getMessage()]);
        }
    }

    /** CAMPAIGN TYPE — delete (POST by id) */
    public function deleteTypeAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->_sendJson(['error' => 1, 'message' => 'Invalid request']);
        }
        try {
            $id = (int)($this->getRequest()->getPost('id') ?? 0);
            if (!$id) throw new Exception('Missing id');

            $db = new Aerosalloyalty_Model_Db_Table_CampaignType();
            $db->delete(['aerosalloyalty_campaign_type_id = ?' => $id]);

            return $this->_sendJson(['success' => 1, 'message' => p__('Aerosalloyalty', 'Type deleted')]);
        } catch (Exception $e) {
            return $this->_sendJson(['error' => 1, 'message' => $e->getMessage()]);
        }
    }

    /** CAMPAIGNS — list (GET, optional card_number filter) */
    public function listCampaignsAction()
    {
        try {
            $value_id = (int)$this->getRequest()->getParam('value_id');
            if (!$value_id) throw new Exception('Missing value_id');

            $card_number = trim($this->getRequest()->getParam('card_number', ''));

            $campaigns = $this->_formatCampaignRows(
                $this->_fetchCampaignRows(
                    $value_id,
                    $card_number !== '' ? $card_number : null
                )
            );
            $campaigns = $this->_attachLatestWebhookLogs($value_id, $campaigns);

            return $this->_sendJson([
                'success' => 1,
                'campaigns' => $campaigns
            ]);
        } catch (Exception $e) {
            return $this->_sendJson(['error' => 1, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Enrich campaign payload with latest webhook log metadata.
     *
     * @param int   $valueId
     * @param array $campaigns
     * @return array
     */
    protected function _attachLatestWebhookLogs($valueId, array $campaigns)
    {
        if (empty($campaigns)) {
            return $campaigns;
        }

        $logs = $this->_fetchLatestWebhookLogs($valueId, $campaigns);

        foreach ($campaigns as &$campaign) {
            $uid = isset($campaign['campaign_uid']) ? (string)$campaign['campaign_uid'] : '';
            if ($uid !== '' && isset($logs[$uid])) {
                $campaign['webhook_logs'] = [
                    'latest' => $logs[$uid]['latest'] ?? null,
                    'inbound' => $logs[$uid]['inbound'] ?? null,
                    'outbound' => $logs[$uid]['outbound'] ?? null,
                ];
                $campaign['webhook_log_summary'] = $logs[$uid]['summary'] ?? null;
            } else {
                $campaign['webhook_logs'] = null;
                $campaign['webhook_log_summary'] = null;
            }
        }
        unset($campaign);

        return $campaigns;
    }

    /**
     * Retrieve most recent webhook logs for given campaigns.
     *
     * @param int   $valueId
     * @param array $campaigns
     * @return array
     */
    protected function _fetchLatestWebhookLogs($valueId, array $campaigns)
    {
        $uids = [];
        foreach ($campaigns as $campaign) {
            $uid = isset($campaign['campaign_uid']) ? (string)$campaign['campaign_uid'] : '';
            if ($uid !== '') {
                $uids[$uid] = true;
            }
        }

        if (empty($uids)) {
            return [];
        }

        $allowed = array_fill_keys(array_keys($uids), true);
        $db = new Aerosalloyalty_Model_Db_Table_WebhookLog();
        $tableName = $db->info('name');

        $limit = count($allowed) * 6;
        $limit = max(50, $limit);
        $limit = min(500, $limit);

        $select = $db->select()
            ->from($tableName)
            ->where('value_id = ?', (int)$valueId)
            ->order('created_at DESC')
            ->limit($limit);

        $rows = $db->fetchAll($select);
        if (!$rows) {
            return [];
        }

        $results = [];

        foreach ($rows as $row) {
            if ($row instanceof Zend_Db_Table_Row_Abstract) {
                $row = $row->toArray();
            }

            if (!is_array($row)) {
                continue;
            }

            $candidateUids = $this->_resolveCampaignUidsFromPayload($row['payload'] ?? '', $allowed);
            if (empty($candidateUids)) {
                continue;
            }

            $entry = [
                'direction' => isset($row['direction']) ? (string)$row['direction'] : null,
                'http_status' => array_key_exists('http_status', $row) && $row['http_status'] !== null
                    ? (int)$row['http_status']
                    : null,
                'endpoint' => isset($row['endpoint']) ? (string)$row['endpoint'] : null,
                'created_at' => isset($row['created_at']) ? (string)$row['created_at'] : null,
            ];

            foreach ($candidateUids as $uid) {
                if (!isset($allowed[$uid])) {
                    continue;
                }

                if (!isset($results[$uid]['latest'])) {
                    $results[$uid]['latest'] = $entry;
                }

                $direction = $entry['direction'] ?: '';
                if ($direction && !isset($results[$uid][$direction])) {
                    $results[$uid][$direction] = $entry;
                }
            }
        }

        foreach ($results as $uid => &$data) {
            if (!isset($data['latest'])) {
                if (isset($data['outbound'])) {
                    $data['latest'] = $data['outbound'];
                } elseif (isset($data['inbound'])) {
                    $data['latest'] = $data['inbound'];
                }
            }

            $data['summary'] = $this->_composeLogSummary($data);
        }
        unset($data);

        return $results;
    }

    /**
     * Extract campaign UIDs referenced within a webhook log payload.
     *
     * @param string $payloadRaw
     * @param array  $allowed
     * @return array
     */
    protected function _resolveCampaignUidsFromPayload($payloadRaw, array $allowed)
    {
        $matches = [];

        if (is_string($payloadRaw) && $payloadRaw !== '') {
            $decoded = json_decode($payloadRaw, true);

            if (is_array($decoded)) {
                $paths = [
                    ['campaign', 'campaign_uid'],
                    ['campaign', 'uid'],
                    ['payload', 'campaign_uid'],
                    ['payload', 'uid'],
                    ['campaign_uid'],
                    ['uid'],
                ];

                foreach ($paths as $path) {
                    $value = $decoded;
                    foreach ($path as $segment) {
                        if (!is_array($value) || !array_key_exists($segment, $value)) {
                            $value = null;
                            break;
                        }
                        $value = $value[$segment];
                    }

                    if ($value !== null && $value !== '') {
                        if (is_scalar($value)) {
                            $matches[(string)$value] = true;
                        }
                    }
                }
            }

            if (empty($matches)) {
                foreach ($allowed as $uid => $flag) {
                    if (strpos($payloadRaw, (string)$uid) !== false) {
                        $matches[(string)$uid] = true;
                    }
                }
            }
        }

        return array_values(array_filter(array_keys($matches), function ($uid) use ($allowed) {
            return isset($allowed[$uid]);
        }));
    }

    /**
     * Compose human readable summary from captured log data.
     *
     * @param array $data
     * @return string|null
     */
    protected function _composeLogSummary(array $data)
    {
        $parts = [];

        if (isset($data['inbound'])) {
            $parts[] = $this->_formatLogLine($data['inbound']);
        }

        if (isset($data['outbound'])) {
            $parts[] = $this->_formatLogLine($data['outbound']);
        }

        if ($parts) {
            return implode(' → ', $parts);
        }

        if (isset($data['latest'])) {
            return $this->_formatLogLine($data['latest']);
        }

        return null;
    }

    /**
     * Format a single log line fragment.
     *
     * @param array $log
     * @return string
     */
    protected function _formatLogLine(array $log)
    {
        $direction = isset($log['direction']) ? (string)$log['direction'] : '';
        $directionLabel = $direction === 'outbound'
            ? p__('Aerosalloyalty', 'Outbound')
            : p__('Aerosalloyalty', 'Inbound');

        $statusLabel = isset($log['http_status']) && $log['http_status'] !== null
            ? (string)$log['http_status']
            : p__('Aerosalloyalty', 'N/A');

        $timestamp = isset($log['created_at']) && $log['created_at']
            ? (string)$log['created_at']
            : p__('Aerosalloyalty', 'Unknown time');

        return sprintf('%s (%s) · %s', $directionLabel, $statusLabel, $timestamp);
    }

    /** CAMPAIGN — delete (POST by value_id, card_number, campaign_uid) */
    public function deleteCampaignAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->_sendJson(['error' => 1, 'message' => 'Invalid request']);
        }
        try {
            $p = $this->getRequest()->getPost();
            $value_id    = (int)($p['value_id'] ?? 0);
            $card_number = trim($p['card_number'] ?? '');
            $uid         = trim($p['campaign_uid'] ?? '');

            if (!$value_id || !$card_number || !$uid) {
                throw new Exception('Missing required parameters');
            }

            (new Aerosalloyalty_Model_Campaign())->deleteByUid($value_id, $card_number, $uid);
            return $this->_sendJson(['success' => 1, 'message' => p__('Aerosalloyalty', 'Campaign deleted')]);
        } catch (Exception $e) {
            return $this->_sendJson(['error' => 1, 'message' => $e->getMessage()]);
        }
    }
}
