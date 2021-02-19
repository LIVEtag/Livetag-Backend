<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
declare(strict_types=1);

namespace common\models\Analytics;

/**
 * @author Roman Oriekhov orekhov.ry@gbksoft.com
 */
interface StreamSessionEventInterface
{

    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * @return int|null
     */
    public function getStreamSessionId(): ?int;


    /**
     * @return int|null
     */
    public function getUserId(): ?int;

    /**
     * @return string|null
     */
    public function getType(): ?string;

    /**
     * @return array
     */
    public function getPayload(): array;

    /**
     * @return int|null
     */
    public function getCreatedAt(): ?int;
}
