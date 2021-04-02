<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
return [
    'user.passwordResetTokenExpire' => 3600,
    'supportEmail' => getenv('SUPPORT_EMAIL'),
    'adminEmail' => getenv('ADMIN_EMAIL'),
    'maxUploadLogoSize' => 102400, // 1024 * 100 -> 100 Kb
    'maxUploadImageSize' => 15728640, //1024 * 1024 * 15 -> 15 Mb
];
