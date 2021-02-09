<?php
/*
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\components\centrifugo;

/**
 * @author Roman Oriekhov orekhov.ry@gbksoft.com
 */
interface MessageInterface
{
    /**
     * Get message body
     * @return array
     */
    public function getBody(): array;
}
