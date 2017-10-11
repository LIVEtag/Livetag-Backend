<?php

namespace rest\modules\chat\models;

/**
 * This is the ActiveQuery class for [[ChannelAccess]].
 *
 * @see ChannelAccess
 */
class ChannelAccessQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ChannelAccess[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ChannelAccess|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
