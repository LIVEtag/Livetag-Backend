<?php
/**
 * Copyright © 2017 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace rest\modules\chat\models;

use rest\modules\chat\models\User;

/**
 * This is the ActiveQuery class for [[Channel]].
 *
 * @see Channel
 */
class ChannelQuery extends \yii\db\ActiveQuery
{

    /**
     * get records only avaliable for current user
     * @param User $user
     */
    public function avaliableForUser(User $user)
    {
        return $this->joinWith('users')
                ->andWhere(
                    ['OR',
                        [
                            Channel::tableName() . '.type' => Channel::TYPE_PUBLIC
                        ],
                        [
                            Channel::tableName() . '.type' => Channel::TYPE_PRIVATE,
                            ChannelUser::tableName() . '.user_id' => $user->id
                        ],
                    ]
                );
    }

    /**
     * filter by url
     * @param string $url
     */
    public function byUrl(string $url)
    {
        $this->andWhere([
            Channel::tableName() . '.url' => $url,
        ]);
        return $this;
    }
}
