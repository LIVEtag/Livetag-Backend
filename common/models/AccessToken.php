<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace common\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "access_token".
 *
 * @property int $id
 * @property int $userId
 * @property string $token
 * @property string $userIp
 * @property string $userAgent
 * @property int $createdAt
 * @property int $expiredAt
 */
class AccessToken extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'access_token';
    }
    
    /**
     * Invalidate access token
     */
    public function invalidate(): void
    {
        $this->expiredAt = time();
        $this->save(false, ['expiredAt']);
    }
}
