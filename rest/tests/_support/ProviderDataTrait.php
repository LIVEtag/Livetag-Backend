<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\tests;

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

trait ProviderDataTrait
{
    /**
     * Get test class suffix
     * @return string
     */
    protected function getTestClassSuffix(): string
    {
        return 'Cest';
    }

    /**
     * Return providers data for testing (ApiTester $I allowed during file generation)
     * @param \Codeception\Actor $I
     * @param string|null $type
     * @return array
     * @throws \ReflectionException
     * @SuppressWarnings("PMD.UnusedFormalParameter")
     */
    protected function getProviderData(\Codeception\Actor $I, ?string $type = null): array
    {
        $callClass = static::class;
        $basePath = dirname((new \ReflectionClass(static::class))->getFileName());

        $baseName = StringHelper::basename($callClass);
        $testClassSuffix = $this->getTestClassSuffix();
        if (StringHelper::endsWith($baseName, $testClassSuffix)) {
            $baseName = substr($baseName, 0, - strlen($testClassSuffix));
        }
        $data = require $basePath . '/providers/' . Inflector::camel2id($baseName) . '.php';
        return $type !== null ? $data[$type] : $data;
    }

    /**
     * Generate comment to response
     * @param ApiTester $I
     * @param array $data
     */
    protected function dataComment(\Codeception\Actor $I, array $data)
    {
        if (isset($data['dataComment'])) {
            $I->amGoingTo($data['dataComment']);
        }
    }
}
