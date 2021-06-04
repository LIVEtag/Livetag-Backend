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
interface ProductInterface
{

    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * @return string|null
     */
    public function getExternalId(): ?string;

    /**
     * @return int|null
     */
    public function getShopId(): ?int;

    /**
     * @return string|null
     */
    public function getTitle(): ?string;

    /**
     * @return string|null
     */
    public function getDescription(): ?string;

    /**
     * @return string|null
     */
    public function getPhoto(): ?string;

    /**
     * @return string|null
     */
    public function getLink(): ?string;

    /**
     * @return int|null
     */
    public function getStatus(): ?int;

    /**
     * @return array
     */
    public function getOptions(): array;

    /**
     * @return int|null
     */
    public function getCreatedAt(): ?int;

    /**
     * @return int|null
     */
    public function getUpdatedAt(): ?int;
}
