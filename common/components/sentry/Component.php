<?php
declare(strict_types=1);

namespace common\components\sentry;

use Sentry\Breadcrumb;
use Sentry\State\Scope;
use yii\base\ActionEvent;
use yii\base\Controller;
use yii\base\Event;
use yii\base\InlineAction;

class Component extends \OlegTsvetkov\Yii2\Sentry\Component
{
    /**
     * This method overrides a bootstrap method of component and adds User context data during EVENT_BEFORE_ACTION
     * instead of EVENT_AFTER_LOGIN used in a base method (because it was not working correctly in admin panel)
     * @inheritDoc
     */
    public function bootstrap($app)
    {
        Event::on(
            Controller::class,
            Controller::EVENT_BEFORE_ACTION,
            function (ActionEvent $event) use ($app) {
                $route = $event->action->getUniqueId();

                $metadata = [];
                // Retrieve action's function
                if ($app->requestedAction instanceof InlineAction) {
                    $metadata['action'] = get_class($app->requestedAction->controller) . '::'
                        . $app->requestedAction->actionMethod . '()';
                } else {
                    $metadata['action'] = get_class($app->requestedAction) . '::run()';
                }

                // Set breadcrumb
                $this->hub->addBreadcrumb(new Breadcrumb(
                    Breadcrumb::LEVEL_INFO,
                    Breadcrumb::TYPE_NAVIGATION,
                    'route',
                    $route,
                    $metadata
                ));

                // Set "route" tag
                $this->hub->configureScope(function (Scope $scope) use ($route): void {
                    $scope->setTag('route', $route);
                });

                // Added user data
                $user = \Yii::$app->has('user', true) ? \Yii::$app->get('user', false) : null;
                if ($user) {
                    $identity = $user->getIdentity(false);
                    if (!empty($identity)) {
                        $this->hub->configureScope(function (Scope $scope) use ($identity): void {
                            $scope->setUser([
                                'id' => $identity->getId(),
                            ]);
                        });
                    }
                }
            }
        );
    }
}
