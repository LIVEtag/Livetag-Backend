<?php
/*
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\models\User;

use rest\common\models\AccessToken;
use rest\common\models\User;
use yii\base\NotSupportedException;
use yii\helpers\ArrayHelper;

/**
 * @author Roman Oriekhov orekhov.ry@gbksoft.com
 */
class Buyer extends User
{

    /**
     * @return BuyerQuery
     */
    public static function find()
    {
        return parent::find()->byRole(self::ROLE_BUYER);
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                ['role', 'default', 'value' => self::ROLE_BUYER],
            ]
        );
    }

    /**
     * @return AccessToken
     */
    public function getAccessToken(): AccessToken
    {
        throw new NotSupportedException('"getAccessToken" is not implemented.');
    }

    /**
     * Get existing record or create new one
     * @param string $uuid
     * @return self|null
     */
    public static function getOrCreate($uuid): ?self
    {
        $user = self::find()->byUuid($uuid)->one();
        if ($user) {
            return $user->status == self::STATUS_ACTIVE ? $user : null;
        }
        $user = new self([
            'uuid' => $uuid,
            'role' => self::ROLE_BUYER
        ]);
        return $user->save() ? $user : null;
    }
}
