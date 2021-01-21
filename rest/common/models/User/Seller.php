<?php
/*
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\models\User;

use rest\common\models\User;

/**
 * @author Roman Oriekhov orekhov.ry@gbksoft.com
 */
class Seller extends User
{

    /**
     * For Seller display shop name
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->shop->name ?? null;
    }
}
