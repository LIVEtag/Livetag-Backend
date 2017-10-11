<?php
namespace rest\modules\chat\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\helpers\Inflector;

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
 * @property ChatChannelAccess[] $chatChannelAccesses
 * @property ChatMessage[] $chatMessages
 */
class Channel extends \yii\db\ActiveRecord
{

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
            'id' => Yii::t('chat', 'ID'),
            'url' => Yii::t('chat', 'Url'),
            'name' => Yii::t('chat', 'Name'),
            'description' => Yii::t('chat', 'Description'),
            'type' => Yii::t('chat', 'Type'),
            'created_by' => Yii::t('chat', 'Created By'),
            'updated_by' => Yii::t('chat', 'Updated By'),
            'created_at' => Yii::t('chat', 'Created At'),
            'updated_at' => Yii::t('chat', 'Updated At'),
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
            'created_at'
        ];
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

    //add transaction
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert) {
            //add user as channel admin
            //move
            $acc = new ChannelAccess();
            $acc->channel_id = $this->id;
            $acc->user_id = $this->created_by;
            $acc->role = ChannelAccess::ROLE_ADMIN;
            $acc->save();
        }
    }

    /**
     * generate uniq url for channel
     * @return string
     */
    public function generateUniqueChannelUrl()
    {
        $url = Inflector::slug($this->name, '_', true);
        if ($this->type === self::TYPE_PRIVATE) {
            $url = PRIVATE_PREFFIX . $url;
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
     * @return \yii\db\ActiveQuery
     */
    public function getChatChannelAccesses()
    {
        return $this->hasMany(ChatChannelAccess::className(), ['channel_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChatMessages()
    {
        return $this->hasMany(ChatMessage::className(), ['channel_id' => 'id']);
    }
}
