<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\controllers\actions\Stream;

use common\models\Stream\StreamSession;
use rest\common\models\Analytics\ProductEventForm;
use rest\common\models\Product\Product;
use Yii;
use yii\rest\Action;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class ProductEventAction extends Action
{

    /**
     * @param int $id
     * @param int $productId
     */
    public function run(int $id, int $productId)
    {
        /** @var StreamSession $streamSession */
        $streamSession = $this->findModel($id);
        if ($this->checkAccess) {
            // phpcs:disable
            call_user_func($this->checkAccess, $this->id, $streamSession);
            // phpcs:enable
        }
        $product = Product::findOne($productId);
        if (!$product) {
            throw NotFoundHttpException("Product not found: $productId");
        }

        //check that session and product belongs to one shop
        if ($product->shopId !== $streamSession->shopId) {
            throw new ForbiddenHttpException('You cannot perform this action');
        }

        $form = new ProductEventForm($streamSession, $product, Yii::$app->user->identity);
        $form->setAttributes(Yii::$app->request->getBodyParams());
        $event = $form->create();
        if ($event->hasErrors()) {
            return $event;
        }
        Yii::$app->response->setStatusCode(204);
    }
}
