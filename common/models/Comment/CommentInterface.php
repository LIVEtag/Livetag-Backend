<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
declare(strict_types=1);

namespace common\models\Comment;

/**
 * @author Roman Oriekhov orekhov.ry@gbksoft.com
 */
interface CommentInterface
{

    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * @return int|null
     */
    public function getUserId(): ?int;

    /**
     * @return int|null
     */
    public function getStreamSessionId(): ?int;

    /**
     * @return string|null
     */
    public function getMessage(): ?string;

    /**
     * @return int|null
     */
    public function getCreatedAt(): ?int;

    /**
     * @return int|null
     */
    public function getUpdatedAt(): ?int;
}
