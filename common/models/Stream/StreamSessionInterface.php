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
     * @return string|null
     */
    public function getSessionId(): ?string;

    /**
     * @return string|null
     */
    public function getPublisherToken(): ?string;

    /**
     * @return int|null
     */
    public function getCreatedAt(): ?int;

    /**
     * @return int|null
     */
    public function getUpdatedAt(): ?int;

    /**
     * @return int|null
     */
    public function getExpiredAt(): ?int;
}
