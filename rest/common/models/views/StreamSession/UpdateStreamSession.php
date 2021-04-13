<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\models\views\StreamSession;

use common\models\Stream\StreamSession;
use yii\base\Model;

class UpdateStreamSession extends Model
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
            ['rotate', 'default', 'value' => StreamSession::ROTATE_0],
            ['rotate', 'in', 'range' => array_keys(StreamSession::ROTATIONS)],
        ];
    }

    /**
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function update()
    {
        if (!$this->validate()) {
            return false;
        }

        $this->streamSession->rotate = $this->rotate;
        if ($this->streamSession->update() === false) {
            return false;
        }

        return true;
    }
}
