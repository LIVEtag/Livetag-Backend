<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\components\filters;

use Yii;
use yii\base\ActionFilter;
use yii\caching\CacheInterface;
use rest\components\filters\RateLimiter\Interfaces\RateLimitRuleInterface;
use rest\components\filters\RateLimiter\Interfaces\RateLimitRestrictionInterface;
use rest\components\filters\RateLimiter\RateLimitAllowance;
use InvalidArgumentException;
use yii\web\Request;
use yii\web\Response;
use yii\web\User as WebUser;
use yii\web\TooManyRequestsHttpException;

class RateLimiter extends ActionFilter
{
    /**
     * @var bool Enable or disable rate limit filter
     */
    public $isActive = true;

    /**
     * @var bool whether to include rate limit headers in the response
     */
    public $enableHeaders = true;

    /**
     * @var bool whether to use request user agent for guest identity
     */
    public $useUserAgentInIdentity = true;

    /**
     * @var RateLimitRuleInterface[] list of rate request rule
     */
    public $rules = [];

    /**
     * @var string the message to be displayed when rate limit exceeds
     */
    public $errorMessage = 'Rate limit exceeded.';

    /** @var CacheInterface */
    protected $cache;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $config = [])
    {
        $this->cache = Yii::$app->cache;
        parent::__construct($config);
    }


    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        foreach ($this->rules as &$rule) {
            if (is_array($rule)) {
                $rule = Yii::createObject($rule);
            }
            if (!($rule instanceof RateLimitRuleInterface)) {
                throw new InvalidArgumentException('Invalid rule configuration');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function isActive($action)
    {
        return $this->isActive && parent::isActive($action);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {
        $user = Yii::$app->getUser();
        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();
        foreach ($this->rules as $rule) {
            $this->recalculateRuleLimit($rule, $user, $request, $response, $action);
        }
        return true;
    }

    /**
     * @param $rule
     * @param $user
     * @param $request
     * @param $response
     * @param $action
     * @throws TooManyRequestsHttpException
     */
    protected function recalculateRuleLimit($rule, $user, $request, $response, $action)
    {
        if ($rule->match($request, $action, $user)) {
            $restriction = $rule->getRestriction();

            $identity = array_merge(
                $this->getUserIdentity($request, $user),
                $rule->getRuleIdentity($request, $action, $user)
            );
            $allowance = $this->loadAllowance($identity, $restriction);
            $current = time();

            $allowance->quantity += (int)(($current - $allowance->timestamp) *
                $restriction->getMaxCount() / $restriction->getInterval());
            $allowance->timestamp = $current;

            if ($allowance->quantity > $restriction->getMaxCount()) {
                $allowance->quantity = $restriction->getMaxCount();
            }

            if ($allowance->quantity <= 0) {
                $allowance->quantity = 0;
                $this->saveAllowance($identity, $allowance, $restriction);
                $this->addRateLimitHeaders($response, $restriction->getMaxCount(), 0, $restriction->getInterval());
                throw new TooManyRequestsHttpException($this->errorMessage);
            }

            $allowance->quantity--;
            $this->saveAllowance($identity, $allowance, $restriction);
            $this->addRateLimitHeaders(
                $response,
                $restriction->getMaxCount(),
                $allowance->quantity,
                (int)(($restriction->getMaxCount() - $allowance->quantity) *
                    $restriction->getInterval() / $restriction->getMaxCount())
            );
        }
    }

    /**
     * Generate identity for any user type
     * @param Request $request
     * @param WebUser $user
     * @return array
     */
    protected function getUserIdentity(Request $request, WebUser $user): array
    {
        return $user->getIsGuest() === false ?
            ['user', $user->getId()] :
            [
                'guest',
                $request->getUserIP() ?? 'localhost',
                $this->useUserAgentInIdentity ? $request->getUserAgent() : 'UA'
            ];
    }

    /**
     * Loads the number of allowed requests and the corresponding timestamp
     * @param array $identity
     * @param RateLimitRestrictionInterface $restriction
     * @return RateLimitAllowance
     */
    protected function loadAllowance(
        array $identity,
        RateLimitRestrictionInterface $restriction
    ): RateLimitAllowance {
        $data = $this->cache->get($identity);
        if ($data === false) {
            $rateLimitAllowance = new RateLimitAllowance([
                'quantity' => $restriction->getMaxCount(),
                'timestamp' => time()
            ]);
        } else {
            $rateLimitAllowance = new RateLimitAllowance($data);
        }
        return $rateLimitAllowance;
    }

    /**
     * Saves the number of allowed requests and the corresponding timestamp
     * @param array $identity
     * @param RateLimitAllowance $allowance
     * @param RateLimitRestrictionInterface $restriction
     */
    protected function saveAllowance(
        array $identity,
        RateLimitAllowance $allowance,
        RateLimitRestrictionInterface $restriction
    ): void {
        $this->cache->set(
            $identity,
            $allowance->toArray(),
            $restriction->getInterval()
        );
    }

    /**
     * Adds the rate limit headers to the response.
     * @param Response $response
     * @param int $limit the maximum number of allowed requests during a period
     * @param int $remaining the remaining number of allowed requests within the current period
     * @param int $reset the number of seconds to wait before having maximum number of allowed requests again
     */
    protected function addRateLimitHeaders(Response $response, int $limit, int $remaining, int $reset): void
    {
        if ($this->enableHeaders) {
            $response->getHeaders()
                ->set('X-Rate-Limit-Limit', $limit)
                ->set('X-Rate-Limit-Remaining', $remaining)
                ->set('X-Rate-Limit-Reset', $reset);
        }
    }
}
