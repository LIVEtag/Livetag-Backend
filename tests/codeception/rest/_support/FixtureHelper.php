<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace tests\codeception\rest\_support;

use Codeception\Module;
use tests\codeception\rest\fixtures\AccessTokenFixture;
use tests\codeception\rest\fixtures\UserFixture;
use yii\test\FixtureTrait;
use yii\test\InitDbFixture;

/**
 * Class FixtureHelper
 */
class FixtureHelper extends Module
{
    /**
     * Test trait
     */
    use FixtureTrait {
        loadFixtures as public;
        fixtures as public;
        globalFixtures as public;
        createFixtures as public;
        unloadFixtures as public;
        getFixtures as public;
        getFixture as public;
    }

    /**
     * Method called before any suite tests run. Loads User fixture login user
     * to use in acceptance and functional tests.
     *
     * @param array $settings
     */
    public function _beforeSuite($settings = [])
    {
        $this->loadFixtures();
    }

    /**
     * Method is called after all suite tests run
     */
    public function _afterSuite()
    {
        $this->unloadFixtures();
    }

    /**
     * @inheritdoc
     */
    public function globalFixtures()
    {
        return [
            InitDbFixture::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => '@tests/codeception/rest/fixtures/data/test_user.php',
            ],
            'access_token' => [
                'class' => AccessTokenFixture::class,
                'dataFile' => '@tests/codeception/rest/fixtures/data/test_access_token.php',
            ],
        ];
    }
}
