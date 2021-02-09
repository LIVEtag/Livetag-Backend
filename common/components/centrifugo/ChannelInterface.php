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
interface ChannelInterface
{
    /**
     * Get name of channel
     * @return string
     */
    public function getName(): string;
}
