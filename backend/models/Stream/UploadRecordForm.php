<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace backend\models\Stream;

use Throwable;
use Yii;
use yii\base\Model;

/**
 * Class UploadRecordForm
 * @package backend\models\Stream
 */
class UploadRecordForm extends Model implements UploadArchiveInterface
{
    use UploadArchiveTrait;
    /**
     * @var StreamSession
     */
    public $streamSession;

    /**
     * @return StreamSession
     */
    public function getStreamSession(): StreamSession
    {
        return $this->streamSession;
    }

    /**
     * @param StreamSession|null $streamSession
     * @param array $config
     */
    public function __construct(StreamSession $streamSession, $config = array())
    {
        $this->streamSession = $streamSession;
        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return $this->getArchiveValidationRules();
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'videoFile' => Yii::t('app', 'File'),
            'directUrl' => Yii::t('app', 'Direct URL link'),
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
        // Transaction is needed, because when saving the archive, the video rotation is determined and the session is saved there.
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$this->saveArchive()) {
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
