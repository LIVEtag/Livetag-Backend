<?php

namespace rest\tests;

use Codeception\Scenario;
use Codeception\Util\HttpCode;
use common\models\AccessToken;
use common\models\User;
use Faker\Generator;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

/**
 * @SuppressWarnings(PHPMD)
 */
class ApiTester extends \Codeception\Actor
{
    use _generated\ApiTesterActions;

    const TO_BIG = 9999999999;

    /** @var Generator */
    public $generator;

    public function __construct(Scenario $scenario)
    {
        parent::__construct($scenario);
        $this->generator = \Yii::createObject(Generator::class);
    }

    /**
     * Authorizes user on a site using exists access token
     * @param int $userId
     * @throws \RuntimeException
     */
    public function amLoggedInApiAs(int $userId)
    {
        /** var User $user */
        $user = $this->grabFixture('users', $userId);
        if (!$user) {
            throw new \RuntimeException('User has not exist');
        }

        switch ($user->role) {
            case User::ROLE_ADMIN:
            case User::ROLE_SELLER:
                //NOTE: this is bug and works only if userid==accessTokenId
                /** @var AccessToken $accessToken */
                $accessToken = $this->grabFixture('accessTokens', $userId);
                if (!isset($accessToken)) {
                    throw new \RuntimeException('User has no asses token');
                }
                $this->amBearerAuthenticated($accessToken->token);
                $this->haveHttpHeader('User-Agent', $accessToken->userAgent);
                break;
            case User::ROLE_BUYER:
                $this->amHttpAuthenticated($user->uuid, '');
                break;
        }
    }

    /**
     * Send method request
     * @param string $method
     * @param string $url
     * @param array|null $params
     * @param array|null $files
     * @return mixed
     */
    public function send(string $method, string $url, ...$args)
    {
        UploadedFile::reset(); // need for file uploading in several steps
        array_unshift($args, $url);
        return call_user_func_array([$this, 'send' . strtoupper($method)], $args);
    }

    public function seeResponseResultIsOk()
    {
        $this->seeResponseCodeIs(HttpCode::OK);
        $this->seeResponseMatchesJsonType(
            [
                'code' => 'integer:=200',
                'status' => 'string',
                'result' => 'array|null'
            ]
        );
    }

    public function seeResponseResultIsCreated()
    {
        $this->seeResponseCodeIs(HttpCode::CREATED);
        $this->seeResponseMatchesJsonType(
            [
                'code' => 'integer:=201',
                'status' => 'string:=success',
                'result' => 'array'
            ]
        );
    }

    public function seeResponseResultIsUnprocessableEntity()
    {
        $this->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
        $this->seeResponseMatchesJsonType(
            [
                'code' => 'integer:=422',
                'status' => 'string:=error',
                'result' => 'array'
            ]
        );
    }

    public function seeResponseResultIsUnauthorized()
    {
        $this->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
        $this->seeResponseMatchesJsonType(
            [
                'code' => 'integer:=401',
                'name' => 'string:=Unauthorized',
                'message' => 'string:=Your request was made with invalid credentials.',
                'status' => 'string:=error'
            ]
        );
    }

    public function seeResponseResultIsNotFound()
    {
        $this->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $this->seeResponseMatchesJsonType(
            [
                'code' => 'integer:=404',
                'name' => 'string:=Not Found',
                'status' => 'string:=error',
                'message' => 'string',
            ]
        );
    }

    public function seeResponseResultIsForbidden()
    {
        $this->seeResponseCodeIs(HttpCode::FORBIDDEN);
        $this->seeResponseMatchesJsonType(
            [
                'code' => 'integer:=403',
                'name' => 'string:=Forbidden',
                'status' => 'string:=error',
                'message' => 'string',
            ]
        );
    }

    public function seeResponseResultIsBadRequest()
    {
        $this->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $this->seeResponseMatchesJsonType(
            [
                'code' => 'integer:=400',
                'name' => 'string:=Bad Request',
                'status' => 'string:=error',
                'message' => 'string',
            ]
        );
    }

    public function seeResponseResultIsNoContent()
    {
        $this->seeResponseCodeIs(HttpCode::NO_CONTENT);
    }


    public function seeResponseResultMatches(array $jsonType)
    {
        $this->seeResponseMatchesJsonType(
            [
                'result' => $jsonType,
            ]
        );
    }

    public function seePaginationHeaders()
    {
        $this->amGoingTo('Check pagination headers');
        $this->seeHttpHeader('x-pagination-current-page');
        $this->seeHttpHeader('x-pagination-page-count');
        $this->seeHttpHeader('x-pagination-per-page');
        $this->seeHttpHeader('x-pagination-total-count');
    }

    public function dontSeePaginationHeaders()
    {
        $this->dontSeeHttpHeader('x-pagination-current-page');
        $this->dontSeeHttpHeader('x-pagination-page-count');
        $this->dontSeeHttpHeader('x-pagination-per-page');
        $this->dontSeeHttpHeader('x-pagination-total-count');
    }

    //Some Basic Responses
    /**
     * @return array
     */
    public function getStreamSessionResponse(): array
    {
        return [
            'id' => 'integer',
            'shopUri' => 'string',
            'sessionId' => 'string',
            'status' => 'integer',
            'createdAt' => 'integer',
            'startedAt' => 'integer|null',
            'stoppedAt' => 'integer|null',
        ];
    }

    /**
     * @return array
     */
    public function getStreamSessionTokenResponse(): array
    {
        return [
            'streamSessionId' => 'integer',
            'token' => 'string',
            'expiredAt' => 'integer',
        ];
    }

    /**
     * @return array
     */
    public function getStreamSessionProductResponse(): array
    {
        return [
            'productId' => 'integer',
            'status' => 'integer',
            'product' => $this->getProductResponse(),
        ];
    }

    /**
     * @return array
     */
    public function getStreamSessionSnapshotResponse(): array
    {
        $product = $this->getStreamSessionProductResponse();
        unset($product['product']);
        return [
            'timestamp' => 'integer',
            'products' => [$product],
        ];
    }

    /**
     * @return array
     */
    public function getStreamSessionCommentResponse(): array
    {
        return [
            'userId' => 'integer',
            'message' => 'string',
            'user' => $this->getUserResponse(),
        ];
    }

    /**
     * @return array
     */
    public function getLikes(): array
    {
        return [
            'timestamp' => 'integer',
            'count' => 'integer',
        ];
    }

    /**
     * @return array
     */
    public function getProductResponse(): array
    {
        return [
            'id' => 'integer',
            'externalId' => 'string',
            'title' => 'string',
            'photo' => 'string',
            'link' => 'string',
            'options' => 'array'
        ];
    }

    /**
     * @return array
     */
    public function getShopResponse(): array
    {
        return [
            'uri' => 'string',
            'name' => 'string',
            'website' => 'string',
            'logo' => 'string|null',
        ];
    }

    /**
     * @return array
     */
    public function getUserEditResponse(): array
    {
        return [
            'name' => 'string',
            'role' => 'string',
        ];
    }

    /**
     * @return array
     */
    public function getUserResponse(): array
    {
        return [
            'name' => 'string|null',
            'role' => 'string',
        ];
    }

    /**
     * @param array $array
     * @param string|null $on
     * @param int $order
     * @return array
     */
    public function arraySort(array $array, string $on = null, int $order = SORT_ASC): array
    {
        $stringComparer = new \Collator('en_EN');
        $comparer = static function ($a, $b) use ($stringComparer): int {
            if (is_string($a) || is_string($b)) {
                $res = $stringComparer->compare($a, $b);
            } else {
                if ($a < $b) {
                    $res = -1;
                } else if ($a > $b) {
                    $res = 1;
                } else {
                    $res = 0;
                }
            }
            return $res;
        };
        uasort($array, static function ($a, $b) use ($on, $order, $comparer) {
            if ($on !== null) {
                $a = ArrayHelper::getValue($a, $on);
                $b = ArrayHelper::getValue($b, $on);
            }
            $res = $comparer($a, $b);
            return $order === SORT_ASC ? $res : $res * -1;
        });
        return array_values($array); //reset key numbering in sorted array
    }

    /**
     * Creates a copy of the $array and sorts by the $sortByKey field & $sortDirection.
     * Compares the original $array and sorted array using the function assertEquals().
     *
     * @param string $sortByKey - name of field in the $array
     * @param int $sortDirection - SORT_ASC, SORT_DESC
     * @param array $array
     */
    public function checkArraySort(string $sortByKey, int $sortDirection, array $array): void
    {
        $sortedArray = $this->arraySort($array, $sortByKey, $sortDirection);
        $this->compareArray($sortByKey, $sortedArray, $array);
    }

    /**
     * @param string $compareKey
     * @param array $sortArray
     * @param array $comparedWith
     */
    public function compareArray(string $compareKey, array $sortArray, array $comparedWith): void
    {
        foreach ($sortArray as $key => $data) {
            // Different sort algorithms do not guarantee order for items with same value
            $this->assertEquals(
                ArrayHelper::getValue($data, $compareKey),
                ArrayHelper::getValue($comparedWith[$key], $compareKey)
            );
        }
    }
}
