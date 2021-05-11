<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace common\models\forms\User;

use common\models\User;
use Yii;
use yii\base\Model;

/**
 * User Profile form
 */
class UserProfileForm extends Model
{
    private $user;
    
    public function __construct(User $user, $config = [])
    {
        parent::__construct($config);
        $this->user = $user;
    }
    
    /**
     * @var string
     */
    public $name;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => User::NAME_MAX_LENGTH],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => Yii::t('app', 'Seller Name'),
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return $this;
        }
    
        $this->user->name = $this->name;
        $this->user->save();
        return $this->user;
    }
}
