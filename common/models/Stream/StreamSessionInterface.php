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
    public function id(): ?int;

    /**
     * @return int|null
     */
    public function shopId(): ?int;

    /**
     * @return int|null
     */
    public function status(): ?int;

    /**
     * @return string|null
     */
    public function sessionId(): ?string;

    /**
     * @return string|null
     */
    public function publisherToken(): ?string;

    /**
     * @return int|null
     */
    public function expiredAt(): ?int;
}
