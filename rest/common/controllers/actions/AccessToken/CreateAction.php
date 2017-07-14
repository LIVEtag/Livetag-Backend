<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\controllers\actions\AccessToken;

use common\components\user\SearchService;
use rest\common\models\views\AccessToken\CreateToken;
use rest\components\api\actions\Action;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\rest\Controller;
use yii\web\ServerErrorHttpException;

/**
 * Class CreateAction
 */
class CreateAction extends Action
{
    /**
     * @var SearchService
     */
    private $searchService;

    /**
     * CreateAction constructor.
     * @param string $id
     * @param Controller $controller
     * @param SearchService $searchService
     * @param array $config
     */
    public function __construct(
        $id,
        Controller $controller,
        SearchService $searchService,
        array $config = []
    ) {
        parent::__construct($id, $controller, $config);
        $this->searchService = $searchService;
    }


    /**
     * Create access token
     *
     * @throws ServerErrorHttpException
     * @throws InvalidParamException
     * @throws InvalidConfigException
     */
    public function run()
    {
        /** @var CreateToken $accessTokenCreate */
        $accessTokenCreate = \Yii::createObject(CreateToken::class);

        $accessTokenCreate->setAttributes($this->request->getBodyParams());

        $accessTokenCreate->isRememberMe = filter_var($this->request->getBodyParam('is_remember_me'), FILTER_VALIDATE_BOOLEAN)
            ? CreateToken::YES_VALUE
            : CreateToken::NO_VALUE;

        $accessTokenCreate->userAgent = $this->request->getUserAgent();
        $accessTokenCreate->userIp = $this->request->getUserIP();

        $accessToken = $accessTokenCreate->create();

        if ($accessToken === null && !$accessTokenCreate->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create access token.');
        }

        if ($accessTokenCreate->hasErrors()) {
            return $accessTokenCreate;
        }

        $this->response->setStatusCode(201);

        return $accessToken;
    }
}
