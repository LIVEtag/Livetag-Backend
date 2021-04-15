<?php
/**
 * Copyright Â© 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\components\test;

use Faker\Generator;
use RuntimeException;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\base\Security;
use yii\db\ActiveQuery;
use yii\helpers\FileHelper;

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

    /**
     * Upload file to S3 and return origin path
     * @param string $relativePath
     * @param string $path
     * @return string
     * @throws InvalidConfigException
     */
    public function createS3UploadedData(string $relativePath, string $path): string
    {
        #$content = @file_get_contents($path);
        // phpcs:disable
        $stream = fopen($path, 'r+');
        // phpcs:enable
        if ($stream === false) {
            throw new RuntimeException("Can't get content from resource: {$path}");
        }
        // phpcs:disable
        $pathParts = pathinfo($path);
        // phpcs:enable
        $uniqId = str_replace('.', '', uniqid('', true));
        if (empty($pathParts['extension'])) {
            #$finfo = new finfo(FILEINFO_MIME_TYPE);
            #$mimeType = $finfo->buffer($content);
            $mimeType = FileHelper::getMimeType($path);
            $extensions = FileHelper::getExtensionsByMimeType($mimeType);
            if (!empty($extensions)) {
                $extension = end($extensions);
            }
        } else {
            $extension = $pathParts['extension'];
        }
        $relativePath = 'fixture/' . $relativePath;
        $remotePath = "{$relativePath}/{$uniqId}.{$extension}";
        \Yii::$app->fs->putStream(
            $remotePath,
            $stream
        );
        if (is_resource($stream)) {
            // phpcs:disable
            fclose($stream);
            // phpcs:enable
        }
        return $remotePath;
    }
}
