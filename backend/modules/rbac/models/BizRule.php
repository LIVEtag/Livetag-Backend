<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace backend\modules\rbac\models;

use yii\base\Model;
use yii\rbac\Item;
use yii\rbac\Rule;
use Yii;

/**
 * Class BizRule
 */
class BizRule extends Model
{
    /**
     * Name of the rule
     *
     * @var string
     */
    public $name;

    /**
     * UNIX timestamp representing the rule creation time
     *
     * @var int
     */
    public $createdAt;

    /**
     * UNIX timestamp representing the rule updating time
     *
     * @var int
     */
    public $updatedAt;

    /**
     * Rule class name
     *
     * @var string
     */
    public $className;

    /**
     * @var Rule
     */
    private $_item;

    /**
     * Constructor
     *
     * @param Rule $item
     * @param array $config
     */
    public function __construct(Rule $item, $config = [])
    {
        $this->_item = $item;
        if ($item !== null) {
            $this->name = $item->name;
            $this->className = get_class($item);
        }
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'className'], 'required'],
            [['className'], 'string'],
            [['className'], 'classExists']
        ];
    }

    /**
     * Validate class exists
     */
    public function classExists()
    {
        if (!class_exists($this->className) || !is_subclass_of($this->className, Rule::className())) {
            $this->addError('className', "Unknown Class: {$this->className}");
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('rbac-backend', 'Name'),
            'className' => Yii::t('rbac-backend', 'Class Name'),
        ];
    }

    /**
     * Check if new record.
     * @return boolean
     */
    public function getIsNewRecord()
    {
        return $this->_item === null;
    }

    /**
     * Find model by id
     *
     * @param int $id
     * @return null|static
     */
    public static function find($id)
    {
        $item = Yii::$app->authManager->getRule($id);
        if ($item !== null) {
            return new static($item);
        }

        return null;
    }

    /**
     * Save model to authManager
     *
     * @return bool
     */
    public function save()
    {
        if ($this->validate()) {
            $manager = Yii::$app->authManager;
            $class = $this->className;
            $oldName = null;
            if ($this->_item === null) {
                $this->_item = new $class();
                $isNew = true;
            } else {
                $isNew = false;
                $oldName = $this->_item->name;
            }
            $this->_item->name = $this->name;

            if ($isNew) {
                $manager->add($this->_item);
            } else {
                $manager->update($oldName, $this->_item);
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * Get item
     *
     * @return Item
     */
    public function getItem()
    {
        return $this->_item;
    }
}
