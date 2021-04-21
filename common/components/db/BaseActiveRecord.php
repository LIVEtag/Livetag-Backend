<?php
/*
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\components\db;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Connection;

/**
 * @inheritdoc
 */
class BaseActiveRecord extends ActiveRecord
{

    /**
     * @event AfterCommitEvent an event that is triggered after a record is inserted and all transactions commited.
     */
    const EVENT_AFTER_COMMIT_INSERT = 'afterCommitInsert';

    /**
     * @event AfterCommitEvent an event that is triggered after a record is updated and all transactions commited.
     */
    const EVENT_AFTER_COMMIT_UPDATE = 'afterCommitUpdate';


    /**
     * Needs to throw into afterCommit method
     */
    private $insert;
    private $changedAttributes;

    /**
     * @SuppressWarnings("BooleanArgumentFlag")
     * @inheritdoc
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        if (!parent::save($runValidation, $attributeNames)) {
            return false;
        }

        list($insert, $changedAttributes) = $this->popAfterSaveParams();
        if (Yii::$app->db->transaction) {
            Yii::$app->db->on(Connection::EVENT_COMMIT_TRANSACTION, function () use ($insert, $changedAttributes) {
                Yii::$app->db->off(Connection::EVENT_COMMIT_TRANSACTION);
                $this->afterCommit($insert, $changedAttributes);
            });
        } else {
            $this->afterCommit($insert, $changedAttributes);
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        $this->pushAfterSaveParams($insert, $changedAttributes);
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * Some logic that should be executed right after antity saved (and all transactions commit)
     *
     * First of all, it should be used to start queue handlers (with the current entity)
     * @param $insert
     * @param $changedAttributes
     */
    public function afterCommit($insert, $changedAttributes)
    {
        $this->trigger($insert ? self::EVENT_COMMIT_AFTER_INSERT : self::EVENT_AFTER_COMMIT_UPDATE, new AfterCommitEvent([
            'changedAttributes' => $changedAttributes,
        ]));
    }

    /**
     * Store after save params to use it later inside afterCommit logic
     */
    protected function pushAfterSaveParams($insert, $changedAttributes)
    {
        $this->insert = $insert;
        $this->changedAttributes = $changedAttributes;
    }

    /**
     * Extract and wipe after save params
     * Parameters cease to be relevant once used
     */
    protected function popAfterSaveParams()
    {
        $params = [$this->insert, $this->changedAttributes];
        $this->insert = null;
        $this->changedAttributes = null;
        return $params;
    }
}
