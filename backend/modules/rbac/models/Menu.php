<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace backend\modules\rbac\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * Class Menu
 *
 * @property integer $id
 * @property string $name
 * @property integer $parent
 * @property string $route
 * @property integer $order
 * @property string $data
 * @property Menu $menuParent read-only menuParent
 * @property Menu[] $menus read-only menus
 */
class Menu extends ActiveRecord
{
    /**
     * @var string
     */
    public $parentName;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%rbac_menu}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['parentName'], 'filterParent'],
            [['parentName'], 'in',
                'range' => static::find()->select(['name'])->column(),
                'message' => 'Menu "{value}" not found.'],
            [['parent', 'route', 'data', 'order'], 'default'],
            [['order'], 'integer'],
            [['route'], 'in',
                'range' => static::getSavedRoutes(),
                'message' => 'Route "{value}" not found.']
        ];
    }

    /**
     * Use to loop detected.
     */
    public function filterParent()
    {
        $value = $this->parentName;
        $parent = self::findOne(['name' => $value]);
        if ($parent) {
            $id = $this->id;
            $parentId = $parent->id;
            while ($parent) {
                if ($parent->id == $id) {
                    $this->addError('parentName', 'Loop detected.');

                    return;
                }
                $parent = $parent->menuParent;
            }
            $this->parent = $parentId;
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rbac-backend', 'ID'),
            'name' => Yii::t('rbac-backend', 'Name'),
            'parent' => Yii::t('rbac-backend', 'Parent'),
            'parentName' => Yii::t('rbac-backend', 'Parent Name'),
            'route' => Yii::t('rbac-backend', 'Route'),
            'order' => Yii::t('rbac-backend', 'Order'),
            'data' => Yii::t('rbac-backend', 'Data'),
        ];
    }

    /**
     * Get menu parent
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMenuParent()
    {
        return $this->hasOne(Menu::class, ['id' => 'parent']);
    }

    /**
     * Get menu children
     *
     * @return Menu[]
     */
    public function getMenus()
    {
        return $this->hasMany(Menu::class, ['parent' => 'id'])->all();
    }

    /**
     * Get saved routes
     *
     * @return array
     */
    public static function getSavedRoutes()
    {
        $result = [];
        foreach (Yii::$app->getAuthManager()->getPermissions() as $name => $value) {
            if ($name[0] === '/' && substr($name, -1) != '*') {
                $result[] = $name;
            }
        }

        return $result;
    }
}
