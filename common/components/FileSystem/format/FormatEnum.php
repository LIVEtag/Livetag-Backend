<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\components\FileSystem\format;

/**
 * Class FormatEnum
 */
class FormatEnum
{
    const SMALL = 'small';
    const LARGE = 'large';

    /** @var string[] */
    const ALLOWED_FORMAT_LIST = [
        self::SMALL,
        self::LARGE,
    ];
}
