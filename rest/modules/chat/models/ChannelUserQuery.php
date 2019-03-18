<?php
/**
 * Copyright © 2017 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

declare(strict_types = 1);

namespace rest\modules\chat\models;

/**
 * This is the ActiveQuery class for [[ChannelUser]].
 *
 * @see ChannelUser
 */
class ChannelUserQuery extends \yii\db\ActiveQuery
{

    /**
     * find by channel and user
     *
     * @param int $channelId
     * @param int $userId
     * @return $this
     */
    public function byChannelAndUser(int $channelId, int $userId)
    {
        $this->andWhere([
            ChannelUser::tableName() . '.userId' => $userId,
            ChannelUser::tableName() . '.channelId' => $channelId,
        ]);
        return $this;
    }
}
