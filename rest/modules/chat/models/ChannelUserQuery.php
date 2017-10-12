<?php
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
            ChannelUser::tableName() . '.user_id' => $userId,
            ChannelUser::tableName() . '.channel_id' => $channelId,
        ]);
        return $this;
    }
}
