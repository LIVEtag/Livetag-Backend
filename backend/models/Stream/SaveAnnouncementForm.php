<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace backend\models\Stream;

use Yii;
use yii\base\Model;

/**
 * Description of SaveAnnouncementForm
 *
 * @author Roman Oriekhov orekhov.ry@gbksoft.com
 */
class SaveAnnouncementForm extends Model
{
    /**
     * Format of input date
     */
    const DATETIME_FORMAT = 'php:Y-m-d H:i';

    /**
     * @var int
     */
    public $shopId;

    /**
     * @var string
     */
    public $name;

    /**
     * Display format of announcedAt
     * @var string
     */
    public $announcedAtDatetime;

    /**
     *  @var int - timestamp
     */
    public $announcedAt;

    /**
     * @var int
     */
    public $duration = StreamSession::DEFAULT_DURATION;

    /**
     * @var array
     */
    public $productIds;

    /**
     * @var StreamSession
     */
    public $streamSession;

    /**
     * @param StreamSession $streamSession
     * @param array $config
     */
    public function __construct(StreamSession $streamSession = null, $config = array())
    {
        if ($streamSession) {
            $this->setAttributes($streamSession->getAttributes());
            $this->productIds = $streamSession->getProductIds();
            if ($streamSession->announcedAt) {
                $this->announcedAtDatetime = Yii::$app->formatter->asDatetime($streamSession->announcedAt, self::DATETIME_FORMAT);
            }
        }
        $this->streamSession = $streamSession ?: new StreamSession();
        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['name', 'shopId', 'announcedAtDatetime', 'duration'], 'required'],
            ['productIds', 'safe'], //validate in main model
            ['name', 'string', 'max' => StreamSession::MAX_NAME_LENGTH],
            [
                'announcedAtDatetime',
                'datetime',
                'format' => self::DATETIME_FORMAT,
                'timeZone' => Yii::$app->formatter->timeZone,
                'timestampAttribute' => 'announcedAt',
                'skipOnEmpty' => false
            ],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'name' => 'Name of livestream',
            'announcedAtDatetime' => 'Start At',
            'duration' => 'Maximum duration of this show',
            'productIds' => 'Products'
        ];
    }

    /**
     * @return bool
     */
    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }
        $this->streamSession->setAttributes($this->getAttributes());
        if (!$this->streamSession->save()) {
            $this->addErrors($this->streamSession->getErrors());
            if ($this->streamSession->hasErrors('announcedAt')) {
                $this->addError('announcedAtDatetime', $this->streamSession->getFirstError('announcedAt'));
            }
            return false;
        }
        return true;
    }
}
