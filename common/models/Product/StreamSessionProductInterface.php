<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
declare(strict_types=1);

namespace common\models\Product;

/**
 * @author Roman Oriekhov orekhov.ry@gbksoft.com
 */
interface StreamSessionProductInterface
{

    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     *
     * @return int|null
     */
    public function getStreamSessionId(): ?int;

    /**
     * @return int|null
     */
    public function getProductId(): ?int;

    /**
     * @return int|null
     */
    public function getStatus(): ?int;

    /**
     * @return int|null
     */
    public function getCreatedAt(): ?int;

    /**
     * @return int|null
     */
    public function getUpdatedAt(): ?int;
}
