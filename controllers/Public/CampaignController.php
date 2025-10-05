<?php

class Aerosalloyalty_Public_CampaignController extends Application_Controller_Default
{
    protected function authenticateToken()
    {
        $header = trim((string)$this->getRequest()->getHeader('Authorization'));
        if (!$header || stripos($header, 'Bearer ') !== 0) {
            throw new Exception('Unauthorized', 401);
        }

        $token = trim(substr($header, 7));
        if ($token === '') {
            throw new Exception('Unauthorized', 401);
        }

        $model = new Aerosalloyalty_Model_ApiToken();
        if (!$model->validate($token)) {
            throw new Exception('Unauthorized', 401);
        }

        return $model;
    }

    protected function resolveSettings($appId)
    {
        $appId = (int)$appId;
        if ($appId <= 0) {
            throw new Exception('Missing app_id', 400);
        }

        $settings = new Aerosalloyalty_Model_Settings();
        $result   = $settings->findByAppId($appId);
        if (!$result || !$settings->getId()) {
            throw new Exception('Feature not configured for provided app_id', 404);
        }

        return $settings;
    }

    protected function parseJsonBody()
    {
        $raw = $this->getRequest()->getRawBody();
        if (!strlen(trim($raw))) {
            return [];
        }

        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }

    protected function safeLog($valueId, $direction, $status, ?array $payload = null)
    {
        try {
            $json = $payload !== null
                ? json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
                : null;
            (new Aerosalloyalty_Model_WebhookLog())->log(
                (int)$valueId,
                $direction,
                (string)$this->getRequest()->getRequestUri(),
                $status,
                $json
            );
        } catch (Exception $e) {
            // Swallow logging issues to avoid breaking main flow
        }
    }

    protected function resolvePrizesToRedeem(array $body, $default = null)
    {
        $params = $this->getRequest()->getParams();
        $sources = [
            ['params', 'prizes_to_redeem'],
            ['body', 'prizes_to_redeem'],
            ['params', 'prizes'],
            ['body', 'prizes'],
        ];

        foreach ($sources as [$type, $key]) {
            if ($type === 'params') {
                if (!array_key_exists($key, $params)) {
                    continue;
                }
                $value = $params[$key];
            } else {
                if (!array_key_exists($key, $body)) {
                    continue;
                }
                $value = $body[$key];
            }

            if ($value === null) {
                return null;
            }

            if (!is_scalar($value)) {
                return null;
            }

            $value = trim((string)$value);
            return $value !== '' ? $value : null;
        }

        if ($default === null) {
            return null;
        }

        if (!is_scalar($default)) {
            return null;
        }

        $default = (string)$default;
        $default = trim($default);

        return $default !== '' ? $default : null;
    }

    protected function resolveCampaignTypeCode(array $body, $default = null)
    {
        $params = $this->getRequest()->getParams();
        $sources = [
            ['params', 'campaign_type_code'],
            ['params', 'type_code'],
            ['body', 'campaign_type_code'],
            ['body', 'type_code'],
        ];

        foreach ($sources as [$type, $key]) {
            if ($type === 'params') {
                if (!array_key_exists($key, $params)) {
                    continue;
                }
                $value = $params[$key];
            } else {
                if (!array_key_exists($key, $body)) {
                    continue;
                }
                $value = $body[$key];
            }

            if ($value === null) {
                continue;
            }

            if (!is_scalar($value)) {
                continue;
            }

            $value = trim((string)$value);
            if ($value !== '') {
                return $value;
            }
        }

        if ($default === null) {
            return null;
        }

        if (!is_scalar($default)) {
            return null;
        }

        $default = trim((string)$default);

        return $default !== '' ? $default : null;
    }

    public function postAction()
    {
        $valueId = null;

        try {
            $tokenModel = $this->authenticateToken();
            $appId      = $this->getRequest()->getParam('app_id');
            $settings   = $this->resolveSettings($appId);
            $valueId    = (int)$settings->getValueId();

            if ((int)$tokenModel->getValueId() !== $valueId) {
                throw new Exception('Token does not grant access to this feature', 403);
            }

            $body = $this->parseJsonBody();

            $cardNumber = trim((string)($this->getRequest()->getParam('card_number', $body['card_number'] ?? '')));
            if ($cardNumber === '') {
                throw new Exception('Missing card_number', 400);
            }

            $card = (new Aerosalloyalty_Model_Card())->findByValueAndNumber($valueId, $cardNumber);
            if (!$card->getId()) {
                throw new Exception('Card not found for provided value_id', 404);
            }

            $typeCode = $this->resolveCampaignTypeCode($body);
            if ($typeCode === null) {
                throw new Exception('Missing campaign_type_code or type_code', 400);
            }

            $type = (new Aerosalloyalty_Model_CampaignType())->findByCode($valueId, $typeCode);
            if (!$type->getId()) {
                throw new Exception('Unknown campaign_type_code', 404);
            }

            $name = trim((string)($this->getRequest()->getParam('name', $body['name'] ?? '')));
            if ($name === '') {
                throw new Exception('Missing name', 400);
            }

            $points = $this->getRequest()->getParam('points_balance', $body['points_balance'] ?? 0);
            $points = (int)$points;

            $prizesToRedeem = $this->resolvePrizesToRedeem($body);

            $this->safeLog($valueId, 'inbound', null, [
                'method' => 'POST',
                'payload' => [
                    'app_id'             =>(int)$appId,
                    'card_number'        =>$cardNumber,
                    'campaign_type_code' =>$typeCode,
                    'name'               =>$name,
                    'points_balance'     =>$points,
                    'prizes_to_redeem'   =>$prizesToRedeem,
                ],
            ]);

            $uid = Aerosalloyalty_Model_Campaign::generateUid($valueId, $cardNumber);

            $campaign = (new Aerosalloyalty_Model_Campaign())->upsert([
                'value_id'           => $valueId,
                'card_number'        => $cardNumber,
                'campaign_uid'       => $uid,
                'campaign_type_code' => $typeCode,
                'name'               => $name,
                'points_balance'     => $points,
                'prizes_to_redeem'   => $prizesToRedeem,
            ]);

            $response = [
                'success'  => 1,
                'campaign' => [
                    'value_id'           => $campaign->getValueId(),
                    'card_number'        => $campaign->getCardNumber(),
                    'campaign_uid'       => $campaign->getCampaignUid(),
                    'campaign_type_code' => $campaign->getCampaignTypeCode(),
                    'name'               => $campaign->getName(),
                    'points_balance'     => (int)$campaign->getPointsBalance(),
                    'prizes_to_redeem'   => $campaign->getPrizesToRedeem(),
                ],
            ];

            $this->safeLog($valueId, 'outbound', 201, $response);
            $this->getResponse()->setHttpResponseCode(201);
            return $this->_sendJson($response);
        } catch (Exception $e) {
            $status = ($e->getCode() >= 400 && $e->getCode() < 600) ? $e->getCode() : 500;
            if ($valueId !== null) {
                $this->safeLog($valueId, 'outbound', $status, ['error' => 1, 'message' => $e->getMessage()]);
            }
            $this->getResponse()->setHttpResponseCode($status);
            return $this->_sendJson(['error' => 1, 'message' => $e->getMessage()]);
        }
    }

    public function putAction()
    {
        $valueId = null;

        try {
            $tokenModel = $this->authenticateToken();
            $appId      = $this->getRequest()->getParam('app_id');
            $settings   = $this->resolveSettings($appId);
            $valueId    = (int)$settings->getValueId();

            if ((int)$tokenModel->getValueId() !== $valueId) {
                throw new Exception('Token does not grant access to this feature', 403);
            }

            $uid = trim((string)$this->getRequest()->getParam('uid'));
            if ($uid === '') {
                throw new Exception('Missing campaign uid', 400);
            }

            $body = $this->parseJsonBody();

            $cardNumber = $this->getRequest()->getParam('card_number', $body['card_number'] ?? null);
            $cardNumber = $cardNumber !== null ? trim((string)$cardNumber) : null;

            $campaignModel = new Aerosalloyalty_Model_Campaign();
            $campaign      = $campaignModel->findByUid($valueId, $uid, $cardNumber);
            if (!$campaign->getId()) {
                $campaign = $campaignModel->findByUid($valueId, $uid);
            }

            if (!$campaign->getId()) {
                throw new Exception('Campaign not found', 404);
            }

            $cardNumber = $cardNumber ?: $campaign->getCardNumber();

            $card = (new Aerosalloyalty_Model_Card())->findByValueAndNumber($valueId, $cardNumber);
            if (!$card->getId()) {
                throw new Exception('Card not found for provided value_id', 404);
            }

            $typeCode = $this->resolveCampaignTypeCode($body, $campaign->getCampaignTypeCode());
            if ($typeCode === null) {
                throw new Exception('Missing campaign_type_code or type_code', 400);
            }

            $type = (new Aerosalloyalty_Model_CampaignType())->findByCode($valueId, $typeCode);
            if (!$type->getId()) {
                throw new Exception('Unknown campaign_type_code', 404);
            }

            $name = $this->getRequest()->getParam('name', $body['name'] ?? $campaign->getName());
            $name = trim((string)$name);
            if ($name === '') {
                throw new Exception('Missing name', 400);
            }

            $points = $this->getRequest()->getParam('points_balance', $body['points_balance'] ?? $campaign->getPointsBalance());
            $points = (int)$points;

            $prizesToRedeem = $this->resolvePrizesToRedeem($body, $campaign->getPrizesToRedeem());

            $this->safeLog($valueId, 'inbound', null, [
                'method' => 'PUT',
                'payload' => [
                    'app_id'             => (int)$appId,
                    'uid'                => $uid,
                    'card_number'        => $cardNumber,
                    'campaign_type_code' => $typeCode,
                    'name'               => $name,
                    'points_balance'     => $points,
                    'prizes_to_redeem'   => $prizesToRedeem,
                ],
            ]);

            $campaign = $campaignModel->upsert([
                'value_id'           => $valueId,
                'card_number'        => $cardNumber,
                'campaign_uid'       => $uid,
                'campaign_type_code' => $typeCode,
                'name'               => $name,
                'points_balance'     => $points,
                'prizes_to_redeem'   => $prizesToRedeem,
            ]);

            $response = [
                'success'  => 1,
                'campaign' => [
                    'value_id'           => $campaign->getValueId(),
                    'card_number'        => $campaign->getCardNumber(),
                    'campaign_uid'       => $campaign->getCampaignUid(),
                    'campaign_type_code' => $campaign->getCampaignTypeCode(),
                    'name'               => $campaign->getName(),
                    'points_balance'     => (int)$campaign->getPointsBalance(),
                    'prizes_to_redeem'   => $campaign->getPrizesToRedeem(),
                ],
            ];

            $this->safeLog($valueId, 'outbound', 200, $response);
            $this->getResponse()->setHttpResponseCode(200);
            return $this->_sendJson($response);
        } catch (Exception $e) {
            $status = ($e->getCode() >= 400 && $e->getCode() < 600) ? $e->getCode() : 500;
            if ($valueId !== null) {
                $this->safeLog($valueId, 'outbound', $status, ['error' => 1, 'message' => $e->getMessage()]);
            }
            $this->getResponse()->setHttpResponseCode($status);
            return $this->_sendJson(['error' => 1, 'message' => $e->getMessage()]);
        }
    }

    public function deleteAction()
    {
        $valueId = null;

        try {
            $tokenModel = $this->authenticateToken();
            $appId      = $this->getRequest()->getParam('app_id');
            $settings   = $this->resolveSettings($appId);
            $valueId    = (int)$settings->getValueId();

            if ((int)$tokenModel->getValueId() !== $valueId) {
                throw new Exception('Token does not grant access to this feature', 403);
            }

            $uid = trim((string)$this->getRequest()->getParam('uid'));
            if ($uid === '') {
                throw new Exception('Missing campaign uid', 400);
            }

            $body = $this->parseJsonBody();

            $cardNumber = $this->getRequest()->getParam('card_number', $body['card_number'] ?? null);
            $cardNumber = $cardNumber !== null ? trim((string)$cardNumber) : null;

            $campaignModel = new Aerosalloyalty_Model_Campaign();
            $campaign      = $campaignModel->findByUid($valueId, $uid, $cardNumber);
            if (!$campaign->getId()) {
                $campaign = $campaignModel->findByUid($valueId, $uid);
            }

            if (!$campaign->getId()) {
                throw new Exception('Campaign not found', 404);
            }

            $cardNumber = $campaign->getCardNumber();

            $this->safeLog($valueId, 'inbound', null, [
                'method' => 'DELETE',
                'payload' => [
                    'app_id'      => (int)$appId,
                    'uid'         => $uid,
                    'card_number' => $cardNumber,
                ],
            ]);

            $campaignModel->deleteByUid($valueId, $cardNumber, $uid);

            $this->safeLog($valueId, 'outbound', 204, ['success' => 1]);
            $this->getResponse()->setHttpResponseCode(204);
            $this->getResponse()->setBody('');
            return;
        } catch (Exception $e) {
            $status = ($e->getCode() >= 400 && $e->getCode() < 600) ? $e->getCode() : 500;
            if ($valueId !== null) {
                $this->safeLog($valueId, 'outbound', $status, ['error' => 1, 'message' => $e->getMessage()]);
            }
            $this->getResponse()->setHttpResponseCode($status);
            return $this->_sendJson(['error' => 1, 'message' => $e->getMessage()]);
        }
    }
}
