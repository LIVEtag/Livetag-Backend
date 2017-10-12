<?php
namespace rest\modules\chat\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\helpers\Inflector;
use rest\common\models\User;
use rest\modules\chat\exception\AfterSaveException;

/**
 * This is the model class for table "chat_channel".
 *
 * @property integer $id
 * @property string $url
 * @property string $name
 * @property string $description
 * @property integer $type
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property ChannelUser[] $users
 * @property Message[] $messages
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
    public static function tableName()
    {
        return 'channel';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class
        ];
    }

    /**
     * use transactions for all scenarios
     * @return type
     */
    public function transactions()
    {
        $transactions = [];
        foreach ($this->scenarios() as $scenario => $fields) {
            $transactions[$scenario] = self::OP_ALL;
        }
        return $transactions;
    }

    /**
     * @inheritdoc
     * @return ChannelQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ChannelQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function rules()
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
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'url' => Yii::t('app', 'Url'),
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
            'type' => Yii::t('app', 'Type'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        return [
            'id',
            'url',
            'name',
            'description',
            'type',
            'created_at'
        ];
    }

    /**
     * @inheritdoc
     */
    public function extraFields()
    {
        return [
            'users',
            'messages'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(ChannelUser::className(), ['channel_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessages()
    {
        return $this->hasMany(Message::className(), ['channel_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->generateUniqueChannelUrl();
        }
        return parent::beforeSave($insert);
    }

    /**
     * Scenario with transaction rollback, if error in afterSave
     * catch thrown exception
     * @param boolean $runValidation
     * @param array $attributeNames
     * @return boolean
     */
    public function save($runValidation = true, $attributeNames = null)
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
            $this->createChannelUserRecord($this->created_by, ChannelUser::ROLE_ADMIN);
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
        $model->channel_id = $this->id;
        $model->user_id = $userId;
        $model->role = $role;
        if (!$model->save()) {
            $this->addErrors($model->getErrors());
            return false;
        }
        return true;
    }

    /**
     * generate uniq url for channel
     * @return string
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
     * @return boolean
     */
    private function checkUniqueUrl($url)
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
     * @param type $user
     * @return type
     */
    public function getUserRoleInChannel($user)
    {
        $role = $this->getUsers()
            ->select('role')
            ->andWhere([
                ChannelUser::tableName() . '.user_id' => $user->id,
            ])
            ->scalar();
        if (!$role) {
            $role = $this->type == self::TYPE_PUBLIC ? ChannelUser::ROLE_USER : ChannelUser::ROLE_NOBODY;
        }
        return $role;
    }

    /**
     * check if selected user can manage current channel
     *
     * @param User $user
     */
    public function canManage(User $user)
    {
        return $this->getUserRoleInChannel($user) == ChannelUser::ROLE_ADMIN;
    }

    /**
     * check if selected user can access to current channel
     *
     * @param User $user
     */
    public function canAccess(User $user)
    {
        return in_array($this->getUserRoleInChannel($user), [
            ChannelUser::ROLE_USER,
            ChannelUser::ROLE_ADMIN
        ]);
    }

    /**
     * try to join selected user to channel
     *
     * @param User $user
     * @return type
     */
    public function joinUserToChannel(User $user)
    {
        return $this->createChannelUserRecord($user->id);
    }

    /**
     * try to leave selected user from channel
     *
     * @param User $user
     * @return type
     */
    public function leaveUserFromChannel(User $user)
    {
        $channelUser = ChannelUser::find()
            ->byChannelAndUser($this->id, $user->id)
            ->one();
        if (!$channelUser) {
            $this->addError('channel_id', Yii::t('app', 'User not in channel'));
            return false;
        }
        return $channelUser->delete();
    }
}
