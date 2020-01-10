<?php
/**
 * Copyright Â© 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\components\test;

use Faker\Generator;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\base\Security;
use yii\db\ActiveQuery;

/**
 * Class ActiveFixture
 *
 * @property-read Generator $generator Faker generator
 * @property-read Security $security The security application component
 */
abstract class ActiveFixture extends \yii\test\ActiveFixture
{
    /** @var Generator */
    private $generator;
    /** @var Security */
    private $security;

    /**
     * Array of required attributes
     * @var array
     */
    public $requiredAttributes = [];

    /**
     * Template for "basic" structure of item
     * @return array
     */
    abstract protected function getTemplate(): array;

    public function __construct(Generator $generator, array $config = [])
    {
        $this->generator = $generator;
        $this->security = \Yii::$app->security;
        parent::__construct($config);
    }

    /**
     * Faker generator
     * @return Generator
     */
    public function getGenerator(): Generator
    {
        return $this->generator;
    }

    /**
     * The security application component
     * @return Security
     */
    public function getSecurity(): Security
    {
        return $this->security;
    }

    /**
     * Generate fixture data item
     * @param array $values Directly set attribute values
     * @param array|null $return Attribute list to generate
     * @return array
     */
    public function generateItem(array $values = [], ?array $return = null): array
    {
        $required = array_keys(array_diff_key(array_flip($this->requiredAttributes), $values));
        if ($required) {
            throw new InvalidArgumentException('Set attribute(s): ' . implode(', ', $required));
        }
        $data = array_merge(
            $this->getTemplate(),
            $values
        );
        if ($return) {
            $data = array_intersect_key($data, array_flip($return));
        }
        return $data;
    }

    /** @inheritdoc */
    protected function getData()
    {
        $data = [];
        foreach (parent::getData() as $key => $attributes) {
            $data[$key] = $this->generateItem($attributes);
        }
        return $data;
    }

    /**
     * Generate model
     * @param array $attributes
     * @return null|\yii\db\ActiveRecord
     * @throws InvalidConfigException
     */
    public function createModel(array $attributes = [])
    {
        if ($this->modelClass === null) {
            throw new InvalidConfigException('"modelClass" must be set.');
        }
        $table = $this->getTableSchema();
        $item = $this->generateItem($attributes);
        $primaryKeys = $this->db->schema->insert($table->fullName, $item);

        /** @var ActiveQuery $activeQuery */
        $activeQuery = $this->modelClass::find();
        return $activeQuery->andWhere($primaryKeys)->one();
    }
}
