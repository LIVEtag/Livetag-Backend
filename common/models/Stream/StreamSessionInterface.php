<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
declare(strict_types=1);

namespace common\models\Stream;

/**
 * @author Roman Oriekhov orekhov.ry@gbksoft.com
 */
interface StreamSessionInterface
{

    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * @return int|null
     */
    public function getShopId(): ?int;

    /**
     * @return int|null
     */
    public function getStatus(): ?int;

    /**
     * @return bool
     */
    public function getCommentsEnabled(): bool;

    /**
     * @return string|null
     */
    public function getSessionId(): ?string;

    /**
     * @return StreamSessionToken|null
     */
    public function getPublisherToken(): ?StreamSessionToken;

    /**
     * @return int|null
     */
    public function getCreatedAt(): ?int;

    /**
     * @return int|null
     */
    public function getStartedAt(): ?int;

    /**
     * @return int|null
     */
    public function getStoppedAt(): ?int;

    /**
     * @return int|null
     */
    public function getExpiredAt(): ?int;
}
