<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace backend\models\Stream;

use common\models\Stream\StreamSessionCover;
use Throwable;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class UploadRecordedShowForm
 * @package backend\models\Stream
 */
class UploadRecordedShowForm extends SaveAnnouncementForm implements UploadArchiveInterface
{
    use UploadArchiveTrait;

    /**
     * @return StreamSession
     */
    public function getStreamSession(): StreamSession
    {
        return $this->streamSession;
    }

    /**
     * @param array $config
     */
    public function __construct($config = array())
    {
        // If the specified property is not set,
        // the default value will be overwritten via setAttributes in the parent constructor
        $this->streamSession = new StreamSession([
            'status' => StreamSession::STATUS_STOPPED,
            'internalCart' => StreamSession::INTERNAL_CART_FALSE,
        ]);
        $this->streamSession->scenario = StreamSession::SCENARIO_UPLOAD_SHOW;
        parent::__construct($this->streamSession, $config);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        $archiveRules = $this->getArchiveValidationRules();
        return ArrayHelper::merge(
            [
                [['name', 'shopId', 'internalCart'], 'required'],
                [['internalCart'], 'boolean'],
                ['name', 'string', 'max' => StreamSession::MAX_NAME_LENGTH],
                [
                    'productIds', //validate in main model. select2 do not return null on empty select
                    'filter',
                    'filter' => function ($value) {
                        return $value == '' ? null : $value;
                    }
                ],
                [
                    'file',
                    'file',
                    'mimeTypes' => StreamSessionCover::getMimeTypes(),
                    'maxSize' => Yii::$app->params['maxUploadCoverSize'],
                ],
            ],
            $archiveRules
        );
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'name' => Yii::t('app', 'Name of livestream'),
            'videoFile' => Yii::t('app', 'Upload File'),
            'directUrl' => Yii::t('app', 'Direct URL link'),
            'productIds' => Yii::t('app', 'Products'),
            'file' => Yii::t('app', 'Cover (can be image or video)'),
            'internalCart' => Yii::t('app', 'Product details view'),
        ];
    }

    /**
     * @return bool
     * @throws Throwable
     */
    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            //1. entity
            $this->streamSession->setAttributes($this->getAttributes());
            $this->streamSession->announcedAt = time();
            if (!$this->streamSession->save()) {
                $this->addErrors($this->streamSession->getErrors());
                $transaction->rollBack();
                return false;
            }
            //2. archive
            if (!$this->saveArchive()) {
                $transaction->rollBack();
                return false;
            }
            //3. cover
            if (!$this->uploadCover()) {
                $transaction->rollBack();
                return false;
            }
            $transaction->commit();
            return true;
        } catch (Throwable $ex) {
            $transaction->rollBack();
            throw $ex;
        }
    }
}
