<?php
/**
 * Copyright © 2017 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

declare(strict_types = 1);

namespace rest\modules\chat\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\helpers\Inflector;
use rest\modules\chat\models\User;
use rest\modules\chat\exception\AfterSaveException;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "channel".
 *
 * @property integer $id
 * @property string $url
 * @property string $name
 * @property string $description
 * @property integer $type
 * @property integer $createdBy
 * @property integer $updatedBy
 * @property integer $createdAt
 * @property integer $updatedAt
 *
 * @property ChannelUser[] $users
 * @property ChannelMessage[] $messages
 */
class Channel extends \yii\db\ActiveRecord
{

    /**
     * create new channel
     */
    const SCENARIO_CREATE = 'create';

    /**
     * update channel info
     */
    const SCENARIO_UPDATE = 'update';

    /**
     * preffix, that determinate private channel (in centrifugo)
     */
    const PRIVATE_PREFFIX = '$';

    /**
     * public chat
     */
    const TYPE_PUBLIC = 1;

    /**
     * private chat
     */
    const TYPE_PRIVATE = 2;

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return 'channel';
    }

    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
            [
                'class' => BlameableBehavior::class,
                'value' => function () {
                    $user = Yii::$app->getModule('chat')->get('user', false);
                    return $user && !$user->isGuest ? $user->id : null;
                }
            ],
        ];
    }

    /**
     * use transactions for all scenarios
     * @return array
     */
    public function transactions(): array
    {
        $transactions = [];
        foreach (array_keys($this->scenarios()) as $scenario) {
            $transactions[$scenario] = self::OP_ALL;
        }
        return $transactions;
    }

    /**
     * @inheritdoc
     * @return ChannelQuery the active query used by this AR class.
     */
    public static function find(): ChannelQuery
    {
        return new ChannelQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['name', 'description', 'type'], 'required'],
            [['type'], 'integer'],
            ['type', 'in', 'range' => [self::TYPE_PUBLIC, self::TYPE_PRIVATE]],
            [['name', 'description'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios(): array
    {
        return array_merge(parent::scenarios(), [
            self::SCENARIO_CREATE => ['name', 'description', 'type'],
            self::SCENARIO_UPDATE => ['name', 'description'], //do not allow change type
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'url' => Yii::t('app', 'Url'),
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
            'type' => Yii::t('app', 'Type'),
            'createdBy' => Yii::t('app', 'Created By'),
            'updatedBy' => Yii::t('app', 'Updated By'),
            'createdAt' => Yii::t('app', 'Created At'),
            'updatedAt' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function fields(): array
    {
        return [
            'id',
            'url',
            'name',
            'description',
            'type',
            'createdAt'
        ];
    }

    /**
     * @inheritdoc
     */
    public function extraFields(): array
    {
        return [
            'inside',
            'users',
            'messages',
            'roleInChannel'
        ];
    }

    /**
     * role of current user in channel
     *
     * @return int
     */
    public function getRoleInChannel(): int
    {
        $userId = Yii::$app->getModule('chat')->user->id;
        return $this->getUserRoleInChannel($userId);
    }

    /**
     * is current user inside channel
     *
     * @return bool
     */
    public function getInside(): bool
    {
        $userId = Yii::$app->getModule('chat')->user->id;
        return $userId ? $this->canPost($userId) : false;
    }

    /**
     * @return ActiveQuery
     */
    public function getUsers(): ?ActiveQuery
    {
        return $this->hasMany(ChannelUser::className(), ['channelId' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getMessages(): ?ActiveQuery
    {
        return $this->hasMany(ChannelMessage::className(), ['channelId' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert): bool
    {
        if ($insert) {
            $this->generateUniqueChannelUrl();
        }
        return parent::beforeSave($insert);
    }

    /**
     * Scenario with transaction rollback, if error in afterSave
     * catch thrown exception
     * @param bool $runValidation
     * @param array $attributeNames
     * @return bool
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function save($runValidation = true, $attributeNames = null): bool
    {
        try {
            return parent::save($runValidation, $attributeNames);
        } catch (AfterSaveException $ex) {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert) {
            //add user as channel admin
            $this->createChannelUserRecord($this->createdBy, ChannelUser::ROLE_ADMIN);
            if ($this->hasErrors()) {
                throw new AfterSaveException();
            }
        }
    }

    /**
     * create new channel access record
     *
     * @param int $userId
     * @param int $role
     * @return bool
     */
    public function createChannelUserRecord(int $userId, int $role = ChannelUser::ROLE_USER): bool
    {
        $model = new ChannelUser();
        $model->channelId = $this->id;
        $model->userId = $userId;
        $model->role = $role;
        if (!$model->save()) {
            $this->addErrors($model->getErrors());
            return false;
        }
        return true;
    }

    /**
     * generate uniq url for channel
     */
    public function generateUniqueChannelUrl()
    {
        $url = Inflector::slug($this->name, '_', true);
        if ($this->type == self::TYPE_PRIVATE) {
            $url = self::PRIVATE_PREFFIX . $url;
        }
        if ($this->checkUniqueUrl($url)) {
            $this->url = $url;
        } else {
            $suffix = 1;
            do {
                $newUrl = $url . '_' . $suffix;
                $suffix++;
            } while (!$this->checkUniqueUrl($newUrl));
            $this->url = $newUrl;
        }
    }

    /**
     * check name for uniq
     * @param type $url
     * @return bool
     */
    private function checkUniqueUrl($url): bool
    {
        $query = static::find()->andWhere(['url' => $url]);
        if ($this->id) {
            $query->andWhere(['<>', 'id', $this->id]);
        }
        if (!$query->exists()) {
            return true;
        }
        return false;
    }

    /**
     * get role of selected user in channel
     * if channel public->user can access it as user
     *
     * @param int $userId
     * @return int
     */
    public function getUserRoleInChannel(int $userId): int
    {
        $role = $this->getUsers()
            ->select('role')
            ->andWhere([
                ChannelUser::tableName() . '.userId' => $userId,
            ])
            ->scalar();
        if (!$role) {
            $role = ChannelUser::ROLE_NOBODY;
        }
        return (int) $role;
    }

    /**
     * check if selected user can manage current channel
     *
     * @param int $userId
     */
    public function canManage(int $userId): bool
    {
        return $this->getUserRoleInChannel($userId) == ChannelUser::ROLE_ADMIN;
    }

    /**
     * check if selected user can access(see) to current channel
     *
     * @param int $userId
     */
    public function canAccess(int $userId): bool
    {
        $role = $this->getUserRoleInChannel($userId);
        //allow to view public channels vithout join
        if (!$role && $this->type == self::TYPE_PUBLIC) {
            return true;
        }
        return in_array($role, [
            ChannelUser::ROLE_USER,
            ChannelUser::ROLE_ADMIN
        ]);
    }

    /**
     * check if selected user can write to current channel
     *
     * @param int $userId
     */
    public function canPost(int $userId): bool
    {
        return in_array($this->getUserRoleInChannel($userId), [
            ChannelUser::ROLE_USER,
            ChannelUser::ROLE_ADMIN
        ]);
    }

    /**
     * try to join selected user to channel
     *
     * @param User $user
     * @return bool
     */
    public function joinUserToChannel(User $user): bool
    {
        return $this->createChannelUserRecord($user->id);
    }

    /**
     * try to leave selected user from channel
     *
     * @param User $user
     * @return bool
     */
    public function leaveUserFromChannel(User $user): bool
    {
        $channelUser = ChannelUser::find()
            ->byChannelAndUser($this->id, $user->id)
            ->one();
        if (!$channelUser) {
            $this->addError('channelId', Yii::t('app', 'User not in channel'));
            return false;
        }
        return $channelUser->delete() ? true : false;
    }
}
