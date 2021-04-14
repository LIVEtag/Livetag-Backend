<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\models\views\StreamSession;

use common\helpers\LogHelper;
use common\models\Stream\StreamSession;
use common\models\Stream\StreamSessionToken;
use Yii;
use yii\base\Model;
use yii\web\BadRequestHttpException;

class StartStreamSession extends Model
{
    /** @var int|string */
    public $rotate;

    /** @var StreamSession */
    private $streamSession;

    /**
     * UpdateStreamSession constructor
     *
     * @param StreamSession $streamSession
     * @param array $config
     */
    public function __construct(StreamSession $streamSession, array $config = [])
    {
        parent::__construct($config);
        $this->streamSession = $streamSession;
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['rotate', 'integer'],
            ['rotate', 'default', 'value' => StreamSession::ROTATE_0],
            ['rotate', 'in', 'range' => array_keys(StreamSession::ROTATIONS)],
        ];
    }

    /**
     * @return bool|StreamSessionToken
     * @throws BadRequestHttpException
     * @throws \yii\db\Exception
     */
    public function start()
    {
        if (!$this->validate()) {
            return false;
        }

        if (!$this->streamSession->isNew()) {
            throw new BadRequestHttpException('This translation already started');
        }
        // 1. Touch startedAt to generate expired time for token
        $this->streamSession->touch('startedAt');

        // 2. Create publisher token
        $token = $this->streamSession->createPublisherToken();

        //Save token and update session status
        $transaction = Yii::$app->db->beginTransaction();

        // 3. Save token
        if (!$token->save()) {
            $transaction->rollBack();
            LogHelper::error('Session start failed. Session Token not saved', StreamSession::LOG_CATEGORY, LogHelper::extraForModelError($token));
            throw new BadRequestHttpException(Yii::t('app', 'Failed to start session for unknown reason'));
        }

        // 4. Update status and save session
        $this->streamSession->status = StreamSession::STATUS_ACTIVE;
        $this->streamSession->rotate = $this->rotate;
        if (!$this->streamSession->save(true, ['status', 'startedAt', 'rotate'])) {
            $transaction->rollBack();
            LogHelper::error('Session start failed. Session not saved', StreamSession::LOG_CATEGORY, LogHelper::extraForModelError($this));
            throw new BadRequestHttpException(Yii::t('app', 'Failed to start session for unknown reason'));
        }
        $transaction->commit();
        return $token;
    }
}
